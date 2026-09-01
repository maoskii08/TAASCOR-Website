<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if ($existingUser = auth_user()) {
    redirect_to($existingUser['role'] === 'staff' ? '/staff/' : '/applicant/');
}

$email = trim((string) ($_POST['email'] ?? ''));
$errors = [];
if (is_post()) {
    verify_csrf();
    try {
        $user = authenticate_user($email, (string) ($_POST['password'] ?? ''), 'staff');
        if (!$user) {
            $errors[] = 'Email or password is incorrect.';
        } else {
            login_user($user);
            flash('success', 'Staff access confirmed.');
            redirect_to('/staff/');
        }
    } catch (PublicRateLimitException $exception) {
        $errors[] = $exception->getMessage();
    } catch (Throwable $exception) {
        $errors[] = config_value('debug') ? $exception->getMessage() : 'Staff sign-in is temporarily unavailable.';
    }
}

$pageTitle = 'Staff sign in';
$pageEyebrow = 'Restricted workspace';
$pageDescription = 'Recruitment and administrative accounts are provisioned privately. There is no public staff registration.';
$bodyClass = 'staff-theme';
require __DIR__ . '/../app/views/header.php';
?>
<div class="portal-grid portal-grid-auth">
    <section class="portal-card">
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <label>
                <span>Staff email</span>
                <input type="email" name="email" value="<?= e($email) ?>" autocomplete="username" maxlength="190" required autofocus>
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" autocomplete="current-password" maxlength="128" required>
            </label>
            <button class="button button-primary" type="submit">Enter staff workspace</button>
        </form>
    </section>
    <aside class="portal-card portal-card-soft">
        <p class="kicker">Access control</p>
        <h2>Provisioned accounts only</h2>
        <p>Staff identities are created through the controlled command-line provisioning script. Failed sign-ins are rate-limited and recorded without raw IP addresses.</p>
        <p>Applicant? <a href="/account/login.php">Use applicant sign in</a>.</p>
    </aside>
</div>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
