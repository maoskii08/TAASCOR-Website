<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$collectionEnabled = privacy_collection_is_enabled('applicant');
$jobs = list_published_jobs();
$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
$pathSlug = preg_match('#^/apply/([a-z0-9][a-z0-9-]{0,159})/?$#', $requestPath, $routeMatch) === 1
    ? $routeMatch[1]
    : null;
$slug = trim((string) ($pathSlug ?? $_POST['job'] ?? $_GET['job'] ?? ''));
$job = $slug !== '' ? find_published_job_by_slug($slug) : null;
$user = auth_user();
$values = [
    'full_name' => trim((string) ($_POST['full_name'] ?? ($user['full_name'] ?? ''))),
    'phone' => trim((string) ($_POST['phone'] ?? ($user['phone'] ?? ''))),
    'current_city' => trim((string) ($_POST['current_city'] ?? '')),
];
$existingApplication = $job && $user && $user['role'] === 'applicant'
    ? application_by_user_and_job((int) $user['id'], (int) $job['id'])
    : null;
if (!is_post() && $existingApplication && $existingApplication['current_status'] === 'draft') {
    $values = [
        'full_name' => (string) $existingApplication['candidate_full_name'],
        'phone' => (string) $existingApplication['candidate_phone'],
        'current_city' => (string) $existingApplication['current_city'],
    ];
}
$errors = [];

if (is_post()) {
    verify_csrf();
    if (!$job) {
        $errors[] = 'Choose an available job before starting an application.';
    } elseif (!$user) {
        redirect_to('/account/login.php?next=' . urlencode('/apply/' . rawurlencode((string) $job['slug']) . '/'));
    } elseif ($user['role'] !== 'applicant') {
        $errors[] = 'Use an applicant account to apply for a role.';
    } else {
        try {
            $applicationId = create_application_draft((int) $user['id'], (int) $job['id'], [
                'full_name' => $values['full_name'],
                'phone' => $values['phone'],
                'current_city' => $values['current_city'],
                'eligibility_confirmed' => isset($_POST['eligibility_confirmed']),
                'privacy_accepted' => isset($_POST['privacy_accepted']),
            ]);
            flash('success', 'Stage one is saved. Add an optional resume, then review and submit.');
            redirect_to('/apply/step2.php?id=' . $applicationId);
        } catch (InvalidArgumentException | DomainException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $exception) {
            $errors[] = config_value('debug') ? $exception->getMessage() : 'We could not save your application. Try again.';
        }
    }
}

$pageTitle = $job ? 'Apply for ' . $job['title'] : 'Choose a role to apply for';
$pageEyebrow = $job ? 'Application · Stage 1 of 2' : 'TAASCOR opportunities';
$pageDescription = $job
    ? 'Confirm your job context and share only the contact details needed for first review.'
    : 'Select a published opportunity. Your selected job remains attached throughout registration and application.';
require __DIR__ . '/../app/views/header.php';
?>
<?php if ($slug !== '' && !$job): ?>
    <div class="alert alert-error" role="alert">That role is unavailable or no longer accepting applications.</div>
<?php endif; ?>

<?php if (!$job): ?>
    <section class="job-grid" aria-label="Available jobs">
        <?php if (!$jobs): ?>
            <article class="portal-card empty-state">
                <h2>No published roles right now</h2>
                <p>Check back later or return to the careers experience.</p>
                <a class="button button-secondary" href="/jobs/">Explore careers</a>
            </article>
        <?php endif; ?>
        <?php foreach ($jobs as $availableJob): ?>
            <article class="portal-card job-card">
                <?php if ($availableJob['is_demo']): ?><p class="status-chip status-warning">Demonstration role</p><?php endif; ?>
                <p class="kicker"><?= e($availableJob['company']) ?></p>
                <h2><?= e($availableJob['title']) ?></h2>
                <p><?= e($availableJob['summary']) ?></p>
                <dl class="meta-list">
                    <div><dt>Location</dt><dd><?= e($availableJob['location']) ?></dd></div>
                    <div><dt>Type</dt><dd><?= e($availableJob['employment_type']) ?></dd></div>
                    <div><dt>Function</dt><dd><?= e($availableJob['function_area']) ?></dd></div>
                    <div><dt>Shift</dt><dd><?= e($availableJob['shift_pattern']) ?></dd></div>
                </dl>
                <a class="button button-primary" href="/apply/<?= e(rawurlencode((string) $availableJob['slug'])) ?>/">Review and apply</a>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <div class="portal-grid portal-grid-application">
        <aside class="portal-card portal-card-sticky">
            <?php if ($job['is_demo']): ?><p class="status-chip status-warning">Synthetic demonstration data</p><?php endif; ?>
            <p class="kicker">Selected role</p>
            <h2><?= e($job['title']) ?></h2>
            <p><?= e($job['summary']) ?></p>
            <dl class="meta-list">
                <div><dt>Hiring organization</dt><dd><?= e($job['company']) ?></dd></div>
                <div><dt>Location</dt><dd><?= e($job['location']) ?></dd></div>
                <div><dt>Type</dt><dd><?= e($job['employment_type']) ?></dd></div>
                <div><dt>Function</dt><dd><?= e($job['function_area']) ?></dd></div>
                <div><dt>Shift</dt><dd><?= e($job['shift_pattern']) ?></dd></div>
            </dl>
        </aside>
        <section class="portal-card">
            <?php if (!$collectionEnabled): ?>
                <div class="alert alert-error" role="status">Applications are unavailable until the approved applicant privacy notice and explicit collection enablement are configured.</div>
            <?php elseif (!$user): ?>
                <p class="kicker">Secure application</p>
                <h2>Sign in before sharing applicant details</h2>
                <p>Your role selection will be preserved while you sign in or create an account.</p>
                <?php $nextPath = '/apply/' . rawurlencode((string) $job['slug']) . '/'; ?>
                <div class="button-row">
                    <a class="button button-primary" href="/account/login.php?next=<?= e(urlencode($nextPath)) ?>">Sign in</a>
                    <a class="button button-secondary" href="/account/register.php?next=<?= e(urlencode($nextPath)) ?>">Create account</a>
                </div>
            <?php elseif ($user['role'] !== 'applicant'): ?>
                <div class="alert alert-error">Staff accounts cannot submit applicant records.</div>
            <?php else: ?>
                <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <?php if ($existingApplication && $existingApplication['current_status'] !== 'draft'): ?>
                    <p>You already started an application for this role.</p>
                    <a class="button button-primary" href="/applicant/">View application status</a>
                <?php else: ?>
                    <?php if ($existingApplication): ?>
                        <div class="alert alert-info" role="status">Stage one is editable while this application remains a draft. Saving here updates the applicant snapshot, then returns you to stage two.</div>
                    <?php endif; ?>
                    <form method="post" class="portal-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="job" value="<?= e($job['slug']) ?>">
                        <label>
                            <span>Full legal name</span>
                            <input type="text" name="full_name" value="<?= e($values['full_name']) ?>" autocomplete="name" maxlength="120" required>
                        </label>
                        <label>
                            <span>Email address</span>
                            <input type="email" value="<?= e($user['email']) ?>" readonly aria-describedby="email-help">
                            <small id="email-help">This comes from your secure account.</small>
                        </label>
                        <label>
                            <span>Phone number</span>
                            <input type="tel" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel" maxlength="30" required>
                        </label>
                        <label>
                            <span>Current city or municipality</span>
                            <input type="text" name="current_city" value="<?= e($values['current_city']) ?>" autocomplete="address-level2" maxlength="120" required>
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="eligibility_confirmed" value="1" required>
                            <span>I confirm that I am eligible to work in the Philippines.</span>
                        </label>
                        <label class="check-row">
                            <input type="checkbox" name="privacy_accepted" value="1" required>
                            <span>I reviewed the <a href="<?= e(config_value('privacy_notice_url')) ?>" target="_blank" rel="noopener">applicant privacy notice</a> (<?= e(config_value('privacy_notice_version')) ?>).</span>
                        </label>
                        <button class="button button-primary" type="submit"><?= $existingApplication ? 'Update and continue' : 'Save and continue' ?></button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
