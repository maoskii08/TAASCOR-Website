<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$user = require_role('applicant', '/account/login.php');
$profileCollectionEnabled = privacy_collection_is_enabled('applicant');
$errors = [];

if (is_post()) {
    verify_csrf();
    try {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'profile') {
            if (!$profileCollectionEnabled) {
                throw new DomainException('Profile collection is unavailable until the approved applicant privacy notice is enabled.');
            }
            update_applicant_profile(
                (int) $user['id'],
                (string) ($_POST['full_name'] ?? ''),
                (string) ($_POST['phone'] ?? '')
            );
            flash('success', 'Account contact details updated. Existing submitted applications remain an auditable snapshot.');
        } elseif ($action === 'password') {
            $newPassword = (string) ($_POST['new_password'] ?? '');
            if (!hash_equals($newPassword, (string) ($_POST['new_password_confirmation'] ?? ''))) {
                throw new InvalidArgumentException('New password confirmation does not match.');
            }
            change_user_password(
                (int) $user['id'],
                (string) ($_POST['current_password'] ?? ''),
                $newPassword
            );
            flash('success', 'Password changed and the secure session was rotated.');
        } else {
            throw new InvalidArgumentException('Unknown account action.');
        }
        redirect_to('/account/settings.php');
    } catch (InvalidArgumentException | DomainException | PublicRateLimitException $exception) {
        $errors[] = $exception->getMessage();
    } catch (Throwable $exception) {
        $errors[] = config_value('debug') ? $exception->getMessage() : 'The account change could not be completed.';
    }
}

$user = user_by_id((int) $user['id']) ?? $user;
$privacyAcknowledgements = privacy_acknowledgements_for_user((int) $user['id']);
$pageTitle = 'Account and privacy controls';
$pageEyebrow = 'Applicant workspace';
$pageDescription = 'Maintain current contact details, rotate your password, and review the privacy notices recorded with this account.';
require __DIR__ . '/../app/views/header.php';
?>
<?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="portal-grid portal-grid-admin">
    <section class="portal-card">
        <p class="kicker">Account profile</p>
        <h2>Current contact identity</h2>
        <?php if ($profileCollectionEnabled): ?>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="profile">
            <label><span>Full name</span><input name="full_name" value="<?= e($user['full_name']) ?>" autocomplete="name" maxlength="120" required></label>
            <label><span>Email address</span><input value="<?= e($user['email']) ?>" autocomplete="email" readonly><small>Email-change verification requires an approved delivery provider and is not enabled in this local build.</small></label>
            <label><span>Phone number</span><input name="phone" type="tel" value="<?= e($user['phone']) ?>" autocomplete="tel" maxlength="30"></label>
            <button class="button button-primary" type="submit">Update profile</button>
        </form>
        <?php else: ?>
            <div class="alert alert-info" role="status">Profile editing is read-only until the approved applicant privacy notice and collection gate are enabled. Password and sign-out controls remain available.</div>
            <dl class="meta-list"><div><dt>Full name</dt><dd><?= e($user['full_name']) ?></dd></div><div><dt>Email</dt><dd><?= e($user['email']) ?></dd></div><div><dt>Phone</dt><dd><?= e($user['phone'] ?: 'Not recorded') ?></dd></div></dl>
        <?php endif; ?>
    </section>
    <section class="portal-card">
        <p class="kicker">Authentication</p>
        <h2>Change password</h2>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="password">
            <label><span>Current password</span><input name="current_password" type="password" autocomplete="current-password" maxlength="128" required></label>
            <label><span>New password</span><input name="new_password" type="password" autocomplete="new-password" minlength="12" maxlength="128" required><small>Use at least 12 characters.</small></label>
            <label><span>Confirm new password</span><input name="new_password_confirmation" type="password" autocomplete="new-password" minlength="12" maxlength="128" required></label>
            <button class="button button-secondary" type="submit">Change password</button>
        </form>
    </section>
</div>

<section class="portal-card">
    <p class="kicker">Recorded notices</p>
    <h2>Privacy acknowledgements</h2>
    <?php if (!$privacyAcknowledgements): ?><p>No account notice acknowledgement was found.</p><?php endif; ?>
    <?php if ($privacyAcknowledgements): ?>
        <div class="table-wrap"><table><thead><tr><th>Scope</th><th>Version</th><th>Recorded</th></tr></thead><tbody>
        <?php foreach ($privacyAcknowledgements as $acknowledgement): ?>
            <tr><td><?= e(ucfirst((string) $acknowledgement['notice_scope'])) ?></td><td><?= e($acknowledgement['notice_version']) ?></td><td><?= e($acknowledgement['acknowledged_at']) ?> UTC</td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    <?php endif; ?>
    <p class="small-copy">A production data-subject request channel, identity-verification process, retention decision, and accountable DPO contact remain privacy-owner approval gates. Do not send identity documents through ordinary email.</p>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
