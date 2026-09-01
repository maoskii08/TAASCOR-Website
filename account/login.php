<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if ($existingUser = auth_user()) {
    redirect_to($existingUser['role'] === 'staff' ? '/staff/' : '/applicant/');
}

$next = safe_local_path($_POST['next'] ?? $_GET['next'] ?? null, '/applicant/');
$email = trim((string) ($_POST['email'] ?? ''));
$errors = [];

if (is_post()) {
    verify_csrf();
    try {
        $user = authenticate_user($email, (string) ($_POST['password'] ?? ''), 'applicant');
        if (!$user) {
            $errors[] = 'Email or password is incorrect.';
        } else {
            login_user($user);
            flash('success', 'Welcome back.');
            redirect_to($next);
        }
    } catch (PublicRateLimitException $exception) {
        $errors[] = $exception->getMessage();
    } catch (Throwable $exception) {
        $errors[] = config_value('debug') ? $exception->getMessage() : 'Sign-in is temporarily unavailable.';
    }
}

$pageTitle = 'Applicant sign in';
$pageEyebrow = 'Candidate gateway';
$pageDescription = 'Continue an application or review your current recruitment status.';
require __DIR__ . '/../app/views/header.php';
?>
<div class="portal-grid portal-grid-auth">
    <section class="portal-card">
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <label>
                <span>Email address</span>
                <input type="email" name="email" value="<?= e($email) ?>" autocomplete="email" maxlength="190" required autofocus>
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" autocomplete="current-password" maxlength="128" required>
            </label>
            <button class="button button-primary" type="submit">Sign in securely</button>
        </form>
    </section>
    <aside class="portal-card portal-card-soft">
        <p class="kicker">New to TAASCOR?</p>
        <h2>Start with a secure applicant profile</h2>
        <p>Create an account, choose a published role, and complete a short two-stage application.</p>
        <a class="button button-secondary" href="/account/register.php?next=<?= e(urlencode($next)) ?>">Create an account</a>
        <p class="small-copy">TAASCOR staff use a separate restricted sign-in.</p>
    </aside>
</div>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
