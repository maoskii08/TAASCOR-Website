<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$collectionEnabled = privacy_collection_is_enabled('applicant');

if ($existingUser = auth_user()) {
    redirect_to($existingUser['role'] === 'staff' ? '/staff/' : '/applicant/');
}

$next = safe_local_path($_POST['next'] ?? $_GET['next'] ?? null, '/applicant/');
$values = [
    'full_name' => trim((string) ($_POST['full_name'] ?? '')),
    'email' => trim((string) ($_POST['email'] ?? '')),
    'phone' => trim((string) ($_POST['phone'] ?? '')),
];
$errors = [];

if (is_post()) {
    verify_csrf();
    $password = (string) ($_POST['password'] ?? '');
    if (!hash_equals($password, (string) ($_POST['password_confirmation'] ?? ''))) {
        $errors[] = 'Password confirmation does not match.';
    }
    if (!$errors) {
        try {
            $userId = register_applicant(
                $values['full_name'],
                $values['email'],
                $values['phone'],
                $password,
                isset($_POST['privacy_acknowledged'])
            );
            $user = user_by_id($userId);
            if (!$user) {
                throw new RuntimeException('Unable to load the new account.');
            }
            login_user($user);
            flash('success', 'Your applicant account is ready.');
            redirect_to($next);
        } catch (InvalidArgumentException | DomainException | PublicRateLimitException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $exception) {
            if (config_value('debug')) {
                $errors[] = $exception->getMessage();
            } else {
                $errors[] = 'We could not create the account. Try again or contact recruitment support.';
            }
        }
    }
}

$pageTitle = 'Create an applicant account';
$pageEyebrow = 'Candidate gateway';
$pageDescription = 'Use one secure account to apply, track status changes, and complete recruitment tasks.';
require __DIR__ . '/../app/views/header.php';
?>
<div class="portal-grid portal-grid-auth">
    <section class="portal-card">
        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert">
                <strong>Check the form.</strong>
                <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <?php if (!$collectionEnabled): ?>
            <div class="alert alert-error" role="status">Applicant account creation is unavailable until the approved privacy notice and explicit collection enablement are configured.</div>
        <?php else: ?>
        <form method="post" class="portal-form" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <label>
                <span>Full name</span>
                <input type="text" name="full_name" value="<?= e($values['full_name']) ?>" autocomplete="name" maxlength="120" required>
            </label>
            <label>
                <span>Email address</span>
                <input type="email" name="email" value="<?= e($values['email']) ?>" autocomplete="email" maxlength="190" required>
            </label>
            <label>
                <span>Phone number <small>Optional until you apply</small></span>
                <input type="tel" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel" maxlength="30">
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" autocomplete="new-password" minlength="12" maxlength="128" aria-describedby="password-help" required>
                <small id="password-help">Use at least 12 characters. A memorable passphrase works well.</small>
            </label>
            <label>
                <span>Confirm password</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" minlength="12" maxlength="128" required>
            </label>
            <label class="check-row">
                <input type="checkbox" name="privacy_acknowledged" value="1" required>
                <span>I reviewed the <a href="<?= e((string) config_value('privacy_notice_url')) ?>" target="_blank" rel="noopener">applicant privacy notice</a> (<?= e((string) config_value('privacy_notice_version')) ?>).</span>
            </label>
            <button class="button button-primary" type="submit">Create secure account</button>
        </form>
        <?php endif; ?>
    </section>
    <aside class="portal-card portal-card-soft">
        <p class="kicker">Privacy by stages</p>
        <h2>Only what recruitment needs now</h2>
        <p>Account creation asks for basic contact details. Government identifiers, medical information, religion, family data, and similar sensitive fields are not collected here.</p>
        <p>Already registered? <a href="/account/login.php?next=<?= e(urlencode($next)) ?>">Sign in</a>.</p>
    </aside>
</div>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
