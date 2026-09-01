<?php

declare(strict_types=1);

final class PublicRateLimitException extends RuntimeException
{
}

function request_uses_https(): bool
{
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if (!(bool) config_value('trusted_proxy_https', false)) {
        return false;
    }
    $remoteAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    $trustedProxyIps = config_value('trusted_proxy_ips', []);
    if (!is_array($trustedProxyIps) || !in_array($remoteAddress, $trustedProxyIps, true)) {
        return false;
    }
    $forwarded = mb_strtolower(trim(explode(',', (string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0]));
    return $forwarded === 'https';
}

function enforce_production_https(): void
{
    if (PHP_SAPI === 'cli' || !is_production() || request_uses_https()) {
        return;
    }

    $method = mb_strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if (in_array($method, ['GET', 'HEAD'], true)) {
        $target = rtrim((string) config_value('url'), '/') . current_request_path();
        header('Location: ' . $target, true, 308);
        exit;
    }
    http_abort(400, 'This operation requires a secure HTTPS connection.');
}

function apply_security_headers(): void
{
    if (PHP_SAPI === 'cli' || headers_sent()) {
        return;
    }

    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: DENY');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; connect-src 'self'; font-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    header('Cross-Origin-Opener-Policy: same-origin');

    if (config_value('session_cookie_secure') && request_uses_https()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}

/** @param array<string, mixed> $sessionState */
function authenticated_session_has_expired(
    array $sessionState,
    int $now,
    int $idleTimeout,
    int $absoluteTimeout
): bool {
    if (!isset($sessionState['user_id'])) {
        return false;
    }
    $authenticatedAt = isset($sessionState['authenticated_at'])
        ? (int) $sessionState['authenticated_at']
        : 0;
    $lastActivity = isset($sessionState['last_activity_at'])
        ? (int) $sessionState['last_activity_at']
        : $authenticatedAt;
    return ($authenticatedAt > 0 && $now - $authenticatedAt > $absoluteTimeout)
        || ($lastActivity > 0 && $now - $lastActivity > $idleTimeout);
}

function start_secure_session(): void
{
    if (PHP_SAPI === 'cli') {
        $_SESSION ??= [];
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $cookieName = (string) config_value('session_cookie_name', 'taascor_recruitment');
    if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', $cookieName)) {
        throw new RuntimeException('SESSION_COOKIE_NAME contains unsupported characters.');
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_name($cookieName);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (bool) config_value('session_cookie_secure', false),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    $now = time();
    $expired = authenticated_session_has_expired(
        $_SESSION,
        $now,
        (int) config_value('session_idle_seconds', 1800),
        (int) config_value('session_absolute_seconds', 28800)
    );
    if ($expired) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['_flash'] = [[
            'type' => 'info',
            'message' => 'Your secure session expired. Sign in again to continue.',
        ]];
        return;
    }
    if (isset($_SESSION['user_id'])) {
        $_SESSION['last_activity_at'] = $now;
    }
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function require_post(): void
{
    if (!is_post()) {
        http_abort(405, 'Method not allowed.');
    }
}

function redirect_to(string $path): never
{
    $safePath = safe_local_path($path, '/');
    header('Location: ' . $safePath, true, 303);
    exit;
}

function safe_local_path(?string $path, string $fallback = '/'): string
{
    if ($path === null || $path === '' || !str_starts_with($path, '/') || str_starts_with($path, '//')) {
        return $fallback;
    }

    if (str_contains($path, "\\") || preg_match('/[\x00-\x1F\x7F]/', $path)) {
        return $fallback;
    }

    $parts = parse_url($path);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['user'])) {
        return $fallback;
    }

    return $path;
}

function current_request_path(): string
{
    return safe_local_path($_SERVER['REQUEST_URI'] ?? '/', '/');
}

function http_abort(int $status, string $message): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $message;
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf'])
        && is_string($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}

function verify_csrf(): void
{
    if (!csrf_is_valid($_POST['_csrf'] ?? null)) {
        audit_event(
            'security.csrf_rejected',
            'request',
            null,
            ['path' => parse_url(current_request_path(), PHP_URL_PATH)],
            null,
            false
        );
        http_abort(419, 'Your secure form session expired. Refresh the page and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/** @return list<array{type: string, message: string}> */
function consume_flashes(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return is_array($messages) ? $messages : [];
}

function request_fingerprint(string $value): string
{
    $key = (string) config_value('key', '');
    return $key !== ''
        ? hash_hmac('sha256', $value, $key)
        : hash('sha256', $value);
}

function normalized_path(string $path): string
{
    $normalized = str_replace('\\', '/', $path);
    return rtrim($normalized, '/');
}

function canonical_path(string $path): string
{
    $resolved = realpath($path);
    if ($resolved !== false) {
        return normalized_path($resolved);
    }

    $segments = [];
    $cursor = $path;
    while (($resolvedParent = realpath($cursor)) === false) {
        $parent = dirname($cursor);
        if ($parent === $cursor || $parent === '') {
            throw new RuntimeException('Unable to resolve the configured filesystem path.');
        }
        array_unshift($segments, basename($cursor));
        $cursor = $parent;
    }

    $canonical = normalized_path($resolvedParent);
    foreach ($segments as $segment) {
        if ($segment === '' || $segment === '.') {
            continue;
        }
        if ($segment === '..') {
            $canonical = normalized_path(dirname($canonical));
            continue;
        }
        $canonical .= '/' . $segment;
    }

    return normalized_path($canonical);
}

function path_is_within(string $path, string $parent): bool
{
    $path = canonical_path($path);
    $parent = canonical_path($parent);

    if (DIRECTORY_SEPARATOR === '\\') {
        $path = strtolower($path);
        $parent = strtolower($parent);
    }

    return $path === $parent || str_starts_with($path, $parent . '/');
}

function validate_date_input(?string $value): ?string
{
    if ($value === null || $value === '') {
        return null;
    }

    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('UTC'));
    return $date && $date->format('Y-m-d') === $value ? $value : null;
}
