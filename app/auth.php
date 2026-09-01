<?php

declare(strict_types=1);

const USER_ROLES = ['applicant', 'staff'];
const DUMMY_BCRYPT_HASH = '$2y$12$Z3g8K2776p/n3YivYwxK6OxA1O1D2SVWsbjdddPmI9pS9ULx7P2XK';
const DUMMY_ARGON2ID_HASH = '$argon2id$v=19$m=65536,t=4,p=1$YzBEdndXYnRYLndrRldJMA$kYnB+ZRR0cM4IvlVrJcT5aHSpGgmluLTa2W6lW8pHXI';

function preferred_password_algorithm(): string|int
{
    return defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
}

/** @return array<string, int> */
function preferred_password_options(): array
{
    if (preferred_password_algorithm() === PASSWORD_BCRYPT) {
        return ['cost' => 12];
    }
    return [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 1,
    ];
}

function hash_user_password(string $password): string
{
    $hash = password_hash($password, preferred_password_algorithm(), preferred_password_options());
    if (!is_string($hash) || $hash === '') {
        throw new RuntimeException('Unable to secure the password.');
    }
    return $hash;
}

function dummy_password_hash(): string
{
    return preferred_password_algorithm() === PASSWORD_BCRYPT
        ? DUMMY_BCRYPT_HASH
        : DUMMY_ARGON2ID_HASH;
}

/** @return array<string, mixed>|null */
function user_by_id(int $id): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $user = $statement->fetch();
    return is_array($user) ? $user : null;
}

/** @return array<string, mixed>|null */
function user_by_email(string $email): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => mb_strtolower(trim($email))]);
    $user = $statement->fetch();
    return is_array($user) ? $user : null;
}

/** @return array<string, mixed>|null */
function auth_user(): ?array
{
    $id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    if ($id <= 0) {
        return null;
    }

    $user = user_by_id($id);
    if (!$user || !(bool) $user['is_active']
        || (int) ($_SESSION['session_version'] ?? 0) !== (int) ($user['session_version'] ?? 1)) {
        logout_user(false);
        return null;
    }

    return $user;
}

function login_user(array $user): void
{
    db_transaction(function () use ($user): void {
        $statement = db()->prepare('UPDATE users SET last_login_at = :last_login_at WHERE id = :id');
        $statement->execute(['last_login_at' => now_utc(), 'id' => (int) $user['id']]);
        audit_event('auth.login_succeeded', 'user', (int) $user['id'], ['role' => (string) $user['role']], (int) $user['id']);
    });

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['role'] = (string) $user['role'];
    $_SESSION['session_version'] = (int) ($user['session_version'] ?? 1);
    $_SESSION['authenticated_at'] = time();
    $_SESSION['last_activity_at'] = time();
}

function logout_user(bool $recordAudit = true): void
{
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    if ($recordAudit && $userId) {
        audit_event('auth.logout', 'user', $userId, [], $userId, false);
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => (bool) $params['secure'],
            'httponly' => (bool) $params['httponly'],
            'samesite' => $params['samesite'] ?: 'Lax',
        ]);
    }
    session_regenerate_id(true);
}

function require_role(string $role, string $loginPath): array
{
    if ($role === 'staff' && !staff_workflows_are_enabled()) {
        http_abort(503, 'The staff workspace is unavailable until its production security gates are approved.');
    }
    $user = auth_user();
    if (!$user) {
        $next = urlencode(current_request_path());
        redirect_to($loginPath . '?next=' . $next);
    }
    if ($user['role'] !== $role) {
        http_abort(403, 'This account is not authorized for this area.');
    }

    return $user;
}

/** @return list<string> */
function validate_password(string $password): array
{
    $errors = [];
    if (mb_strlen($password) < 12) {
        $errors[] = 'Use at least 12 characters.';
    }
    if (strlen($password) > 128) {
        $errors[] = 'Password may not exceed 128 UTF-8 bytes.';
    }
    if (preferred_password_algorithm() === PASSWORD_BCRYPT && strlen($password) > 72) {
        $errors[] = 'This server accepts at most 72 UTF-8 bytes for a password.';
    }
    return $errors;
}

function register_applicant(
    string $fullName,
    string $email,
    string $phone,
    string $password,
    bool $privacyAcknowledged
): int
{
    require_approved_privacy_notice('applicant');
    $fullName = trim($fullName);
    $email = mb_strtolower(trim($email));
    $phone = trim($phone);

    if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120) {
        throw new InvalidArgumentException('Enter your full name.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        throw new InvalidArgumentException('Enter a valid email address.');
    }
    if ($phone !== '' && (mb_strlen($phone) < 7 || mb_strlen($phone) > 30)) {
        throw new InvalidArgumentException('Enter a valid phone number.');
    }
    $passwordErrors = validate_password($password);
    if ($passwordErrors) {
        throw new InvalidArgumentException(implode(' ', $passwordErrors));
    }
    if (!$privacyAcknowledged) {
        throw new InvalidArgumentException('Review and acknowledge the applicant privacy notice.');
    }
    if (registration_rate_limited($email)) {
        throw new PublicRateLimitException('Too many account-creation attempts. Wait before trying again.');
    }
    record_registration_attempt($email);
    if (user_by_email($email)) {
        throw new DomainException('We could not create an account with those details. Try signing in or use a different email.');
    }

    return db_transaction(function () use ($fullName, $email, $phone, $password): int {
        $timestamp = now_utc();
        $statement = db()->prepare(
            'INSERT INTO users
                (role, email, full_name, phone, password_hash, is_active, created_at, updated_at)
             VALUES
                (:role, :email, :full_name, :phone, :password_hash, 1, :created_at, :updated_at)'
        );
        $statement->execute([
            'role' => 'applicant',
            'email' => $email,
            'full_name' => $fullName,
            'phone' => $phone,
            'password_hash' => hash_user_password($password),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $id = last_inserted_id();
        $privacy = db()->prepare(
            'INSERT INTO privacy_acknowledgements
                (user_id, notice_scope, notice_version, acknowledged_at)
             VALUES (:user_id, :notice_scope, :notice_version, :acknowledged_at)'
        );
        $privacy->execute([
            'user_id' => $id,
            'notice_scope' => 'account',
            'notice_version' => (string) config_value('privacy_notice_version'),
            'acknowledged_at' => $timestamp,
        ]);
        audit_event('applicant.registered', 'user', $id, [
            'role' => 'applicant',
            'privacy_notice_version' => (string) config_value('privacy_notice_version'),
        ], $id);
        return $id;
    });
}

function update_applicant_profile(int $userId, string $fullName, string $phone): void
{
    require_approved_privacy_notice('applicant');
    $user = user_by_id($userId);
    if (!$user || $user['role'] !== 'applicant' || !(bool) $user['is_active']) {
        throw new DomainException('Applicant account not found.');
    }
    $fullName = trim($fullName);
    $phone = trim($phone);
    if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120) {
        throw new InvalidArgumentException('Enter your full name.');
    }
    if ($phone !== '' && (mb_strlen($phone) < 7 || mb_strlen($phone) > 30)) {
        throw new InvalidArgumentException('Enter a valid phone number.');
    }

    db_transaction(function () use ($userId, $fullName, $phone): void {
        $statement = db()->prepare(
            'UPDATE users SET full_name = :full_name, phone = :phone, updated_at = :updated_at WHERE id = :id'
        );
        $statement->execute([
            'full_name' => $fullName,
            'phone' => $phone,
            'updated_at' => now_utc(),
            'id' => $userId,
        ]);
        audit_event('applicant.profile_updated', 'user', $userId, [], $userId);
    });
}

function change_user_password(int $userId, string $currentPassword, string $newPassword): void
{
    $user = user_by_id($userId);
    if (!$user || !(bool) $user['is_active']) {
        throw new DomainException('Current password is incorrect.');
    }
    $scope = 'password_change:' . $userId;
    if (too_many_auth_attempts($scope, (string) $user['email'])) {
        throw new PublicRateLimitException('Too many password attempts. Wait 15 minutes and try again.');
    }
    if (!password_verify($currentPassword, (string) $user['password_hash'])) {
        record_auth_failure($scope, (string) $user['email']);
        throw new DomainException('Current password is incorrect.');
    }
    clear_auth_failures($scope, (string) $user['email']);
    if (hash_equals($currentPassword, $newPassword)) {
        throw new InvalidArgumentException('Choose a new password that differs from the current password.');
    }
    if ($errors = validate_password($newPassword)) {
        throw new InvalidArgumentException(implode(' ', $errors));
    }

    $newSessionVersion = db_transaction(function () use ($userId, $newPassword): int {
        $statement = db()->prepare(
            'UPDATE users SET password_hash = :password_hash, session_version = session_version + 1,
                updated_at = :updated_at WHERE id = :id'
        );
        $statement->execute([
            'password_hash' => hash_user_password($newPassword),
            'updated_at' => now_utc(),
            'id' => $userId,
        ]);
        $updatedUser = user_by_id($userId);
        if (!$updatedUser) {
            throw new RuntimeException('Unable to reload the updated account.');
        }
        audit_event('auth.password_changed', 'user', $userId, [], $userId);
        return (int) ($updatedUser['session_version'] ?? 1);
    });
    session_regenerate_id(true);
    $_SESSION['session_version'] = $newSessionVersion;
    $_SESSION['authenticated_at'] = time();
    $_SESSION['last_activity_at'] = time();
}

/** @return list<array<string, mixed>> */
function privacy_acknowledgements_for_user(int $userId): array
{
    $statement = db()->prepare(
        'SELECT notice_scope, notice_version, acknowledged_at
         FROM privacy_acknowledgements WHERE user_id = :user_id ORDER BY acknowledged_at DESC'
    );
    $statement->execute(['user_id' => $userId]);
    return $statement->fetchAll();
}

function provision_staff(string $fullName, string $email, string $password): int
{
    $fullName = trim($fullName);
    $email = mb_strtolower(trim($email));
    if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120) {
        throw new InvalidArgumentException('STAFF_SEED_NAME must be between 2 and 120 characters.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        throw new InvalidArgumentException('STAFF_SEED_EMAIL is invalid.');
    }
    if ($errors = validate_password($password)) {
        throw new InvalidArgumentException(implode(' ', $errors));
    }
    if (user_by_email($email)) {
        throw new DomainException('A user already exists for that email address.');
    }

    return db_transaction(function () use ($fullName, $email, $password): int {
        $timestamp = now_utc();
        $statement = db()->prepare(
            'INSERT INTO users
                (role, email, full_name, phone, password_hash, is_active, created_at, updated_at)
             VALUES
                (:role, :email, :full_name, :phone, :password_hash, 1, :created_at, :updated_at)'
        );
        $statement->execute([
            'role' => 'staff',
            'email' => $email,
            'full_name' => $fullName,
            'phone' => '',
            'password_hash' => hash_user_password($password),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $id = last_inserted_id();
        audit_event('staff.provisioned', 'user', $id, ['role' => 'staff'], $id);
        return $id;
    });
}

function auth_subject_hash(string $scope, string $email): string
{
    return request_fingerprint($scope . '|' . mb_strtolower(trim($email)));
}

function registration_rate_limited(string $email): bool
{
    $emailScope = 'register:email';
    $ipScope = 'register:ip';
    $windowStart = utc_minutes_ago(60);
    $statement = db()->prepare(
        'SELECT COUNT(*) FROM auth_attempts
         WHERE scope = :scope AND subject_hash = :subject_hash AND attempted_at >= :window_start'
    );
    $statement->execute([
        'scope' => $emailScope,
        'subject_hash' => auth_subject_hash($emailScope, $email),
        'window_start' => $windowStart,
    ]);
    $emailAttempts = (int) $statement->fetchColumn();
    $statement->execute([
        'scope' => $ipScope,
        'subject_hash' => request_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
        'window_start' => $windowStart,
    ]);
    return $emailAttempts >= 5 || (int) $statement->fetchColumn() >= 10;
}

function record_registration_attempt(string $email): void
{
    db_transaction(function () use ($email): void {
        $statement = db()->prepare(
            'INSERT INTO auth_attempts (scope, subject_hash, attempted_at)
             VALUES (:scope, :subject_hash, :attempted_at)'
        );
        $timestamp = now_utc();
        $emailScope = 'register:email';
        $statement->execute([
            'scope' => $emailScope,
            'subject_hash' => auth_subject_hash($emailScope, $email),
            'attempted_at' => $timestamp,
        ]);
        $statement->execute([
            'scope' => 'register:ip',
            'subject_hash' => request_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
            'attempted_at' => $timestamp,
        ]);
        db()->prepare('DELETE FROM auth_attempts WHERE attempted_at < :cutoff')
            ->execute(['cutoff' => utc_minutes_ago(1440)]);
        audit_event('auth.registration_attempted', 'user', null, [], null, false);
    });
}

function too_many_auth_attempts(string $scope, string $email): bool
{
    $statement = db()->prepare(
        'SELECT COUNT(*) FROM auth_attempts
         WHERE scope = :scope AND subject_hash = :subject_hash AND attempted_at >= :window_start'
    );
    $statement->execute([
        'scope' => $scope,
        'subject_hash' => auth_subject_hash($scope, $email),
        'window_start' => utc_minutes_ago(15),
    ]);
    $subjectAttempts = (int) $statement->fetchColumn();

    $ipStatement = db()->prepare(
        'SELECT COUNT(*) FROM auth_attempts
         WHERE scope = :scope AND subject_hash = :subject_hash AND attempted_at >= :window_start'
    );
    $ipStatement->execute([
        'scope' => $scope . ':ip',
        'subject_hash' => request_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
        'window_start' => utc_minutes_ago(15),
    ]);
    return $subjectAttempts >= 8 || (int) $ipStatement->fetchColumn() >= 20;
}

function record_auth_failure(string $scope, string $email): void
{
    $statement = db()->prepare(
        'INSERT INTO auth_attempts (scope, subject_hash, attempted_at)
         VALUES (:scope, :subject_hash, :attempted_at)'
    );
    $statement->execute([
        'scope' => $scope,
        'subject_hash' => auth_subject_hash($scope, $email),
        'attempted_at' => now_utc(),
    ]);
    $statement->execute([
        'scope' => $scope . ':ip',
        'subject_hash' => request_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
        'attempted_at' => now_utc(),
    ]);
    db()->prepare('DELETE FROM auth_attempts WHERE attempted_at < :cutoff')
        ->execute(['cutoff' => utc_minutes_ago(1440)]);
    audit_event('auth.login_failed', 'user', null, ['scope' => $scope], null, false);
}

function clear_auth_failures(string $scope, string $email): void
{
    $statement = db()->prepare('DELETE FROM auth_attempts WHERE scope = :scope AND subject_hash = :subject_hash');
    $statement->execute(['scope' => $scope, 'subject_hash' => auth_subject_hash($scope, $email)]);
}

/** @return array<string, mixed>|null */
function authenticate_user(string $email, string $password, string $expectedRole): ?array
{
    if (!in_array($expectedRole, USER_ROLES, true)) {
        throw new InvalidArgumentException('Unknown authentication role.');
    }
    if ($expectedRole === 'staff' && !staff_workflows_are_enabled()) {
        throw new DomainException('The staff workspace is not enabled for this environment.');
    }
    $scope = 'login:' . $expectedRole;
    if (too_many_auth_attempts($scope, $email)) {
        audit_event('auth.rate_limited', 'user', null, ['scope' => $scope], null, false);
        throw new PublicRateLimitException('Too many attempts. Wait 15 minutes and try again.');
    }

    $user = user_by_email($email);
    $candidateHash = $user ? (string) $user['password_hash'] : dummy_password_hash();
    $passwordValid = password_verify($password, $candidateHash);
    $valid = $user
        && (bool) $user['is_active']
        && $user['role'] === $expectedRole
        && $passwordValid;

    if (!$valid) {
        record_auth_failure($scope, $email);
        return null;
    }

    if (password_needs_rehash(
        (string) $user['password_hash'],
        preferred_password_algorithm(),
        preferred_password_options()
    )) {
        $statement = db()->prepare('UPDATE users SET password_hash = :hash, updated_at = :updated_at WHERE id = :id');
        $statement->execute([
            'hash' => hash_user_password($password),
            'updated_at' => now_utc(),
            'id' => (int) $user['id'],
        ]);
    }

    clear_auth_failures($scope, $email);
    return $user;
}
