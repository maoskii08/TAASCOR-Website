<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$user = require_role('applicant', '/account/login.php');
$applicationId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
$application = application_by_id($applicationId);
if (!$application || (int) $application['applicant_user_id'] !== (int) $user['id']) {
    http_abort(404, 'Application not found.');
}
if ($application['current_status'] !== 'draft') {
    flash('info', 'This application has already been submitted.');
    redirect_to('/applicant/');
}

$currentJob = job_by_id((int) $application['job_id']);
$jobSnapshotChanged = $currentJob !== null && application_job_snapshot_changed($application, $currentJob);
$resumeUploadEnabled = resume_upload_is_enabled();

$experienceSummary = (string) ($_POST['experience_summary'] ?? $application['experience_summary']);
$errors = [];

if (is_post()) {
    verify_csrf();
    $storedDocument = null;
    try {
        update_application_stage_two($applicationId, (int) $user['id'], $experienceSummary);
        $existingDocuments = application_documents($applicationId);
        $hasNewUpload = isset($_FILES['resume']) && (int) ($_FILES['resume']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($hasNewUpload && $existingDocuments) {
            throw new DomainException('A resume is already attached to this application.');
        }
        if ($hasNewUpload && !$resumeUploadEnabled) {
            throw new DomainException('Resume upload is unavailable until its production safeguards are approved.');
        }
        if ($hasNewUpload) {
            $storedDocument = store_resume_upload($_FILES['resume']);
            try {
                record_application_document($applicationId, (int) $user['id'], $storedDocument);
            } catch (Throwable $exception) {
                remove_stored_upload($storedDocument);
                throw $exception;
            }
        }

        if (($_POST['action'] ?? '') === 'submit') {
            submit_application(
                $applicationId,
                (int) $user['id'],
                isset($_POST['certified']),
                isset($_POST['job_change_reviewed']),
                isset($_POST['reviewed_job_version']) ? (string) $_POST['reviewed_job_version'] : null
            );
            flash('success', 'Your application was submitted. Status updates will appear in your dashboard.');
            redirect_to('/applicant/?submitted=' . $applicationId);
        }
        flash('success', 'Draft saved.');
        redirect_to('/apply/step2.php?id=' . $applicationId);
    } catch (InvalidArgumentException | DomainException $exception) {
        $errors[] = $exception->getMessage();
    } catch (Throwable $exception) {
        $errors[] = config_value('debug') ? $exception->getMessage() : 'We could not save this stage. Try again.';
    }
}

$documents = application_documents($applicationId);
$pageTitle = 'Complete your application';
$pageEyebrow = 'Application · Stage 2 of 2';
$pageDescription = 'Add only relevant experience and an optional resume, then certify and submit.';
require __DIR__ . '/../app/views/header.php';
?>
<div class="portal-grid portal-grid-application">
    <aside class="portal-card portal-card-sticky">
        <p class="kicker">Application <?= e('#' . $applicationId) ?></p>
        <h2><?= e($application['job_title']) ?></h2>
        <p><?= e($application['job_company']) ?> · <?= e($application['job_location']) ?></p>
        <ol class="stage-list">
            <li class="is-complete">Contact and eligibility <span>Saved</span></li>
            <li class="is-current">Experience and review <span>Current</span></li>
        </ol>
        <a class="text-link" href="/apply/<?= e(rawurlencode((string) $application['job_slug'])) ?>/">Edit stage-one details</a>
    </aside>
    <section class="portal-card">
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <?php if ($jobSnapshotChanged && $currentJob): ?>
            <div class="alert alert-info" role="status">
                <strong>The role changed after you started.</strong>
                <p>Review the current terms before submitting: <?= e($currentJob['title']) ?> · <?= e($currentJob['location']) ?> · <?= e($currentJob['shift_pattern']) ?>.</p>
                <p><?= e($currentJob['requirements']) ?></p>
                <p><a href="/jobs/<?= e(rawurlencode((string) $currentJob['slug'])) ?>/" target="_blank" rel="noopener">Open the current role detail</a></p>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $applicationId ?>">
            <?php if ($jobSnapshotChanged && $currentJob): ?>
                <input type="hidden" name="reviewed_job_version" value="<?= e(job_snapshot_content_hash($currentJob)) ?>">
            <?php endif; ?>
            <label>
                <span>Relevant experience <small>Optional</small></span>
                <textarea name="experience_summary" rows="7" maxlength="1500" data-character-count="experience-count"><?= e($experienceSummary) ?></textarea>
                <small id="experience-count">Up to 1,500 characters.</small>
            </label>
            <?php if ($documents): ?>
                <div class="document-summary">
                    <p class="kicker">Resume attached</p>
                    <p><?= e($documents[0]['original_name']) ?> · <?= e(number_format((int) $documents[0]['size_bytes'] / 1024, 1)) ?> KB</p>
                    <p class="status-chip status-warning">Private quarantine · scanning required before staff access</p>
                </div>
            <?php elseif ($resumeUploadEnabled): ?>
                <label>
                    <span>Resume <small>Optional · PDF, DOC, or DOCX · maximum 5 MB</small></span>
                    <input type="file" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    <small>Files are renamed, stored outside the public web root, and marked quarantine.</small>
                </label>
            <?php else: ?>
                <div class="alert alert-info" role="status">Optional resume upload is unavailable until approved scanning, retention, and private-storage controls are enabled. You can still submit the application summary without a file.</div>
            <?php endif; ?>
            <label class="check-row certification-row">
                <input type="checkbox" name="certified" value="1">
                <span>I certify that the information I am submitting is accurate to the best of my knowledge.</span>
            </label>
            <?php if ($jobSnapshotChanged && $currentJob): ?>
                <label class="check-row certification-row">
                    <input type="checkbox" name="job_change_reviewed" value="1">
                    <span>I reviewed the current role detail and understand that these updated terms will be attached to my submitted application.</span>
                </label>
            <?php endif; ?>
            <div class="button-row">
                <button class="button button-secondary" type="submit" name="action" value="save">Save draft</button>
                <button class="button button-primary" type="submit" name="action" value="submit">Submit application</button>
            </div>
        </form>
    </section>
</div>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
