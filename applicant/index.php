<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$user = require_role('applicant', '/account/login.php');
$applications = applicant_applications((int) $user['id']);
$submittedApplicationId = max(0, (int) ($_GET['submitted'] ?? 0));
$submittedApplication = null;
foreach ($applications as $candidateApplication) {
    if ((int) $candidateApplication['id'] === $submittedApplicationId
        && (string) $candidateApplication['current_status'] !== 'draft') {
        $submittedApplication = $candidateApplication;
        break;
    }
}
$pageTitle = 'Your application journey';
$pageEyebrow = 'Applicant workspace';
$pageDescription = 'See the current status, recruitment history, and next actions for every application.';
require __DIR__ . '/../app/views/header.php';
?>
<section class="dashboard-summary portal-card">
    <div><span>Applicant</span><strong><?= e($user['full_name']) ?></strong></div>
    <div><span>Applications</span><strong><?= count($applications) ?></strong></div>
    <div><span>Account email</span><strong><?= e($user['email']) ?></strong></div>
</section>

<?php if ($submittedApplication): ?>
    <section class="portal-card submission-confirmation" role="status" aria-labelledby="submission-confirmation-title">
        <p class="kicker">Application receipt</p>
        <h2 id="submission-confirmation-title">TAASCOR-APP-<?= str_pad((string) $submittedApplicationId, 6, '0', STR_PAD_LEFT) ?> submitted</h2>
        <p>Your application for <strong><?= e((string) $submittedApplication['job_title']) ?></strong> is now recorded. Keep this reference with your applicant account.</p>
        <ul>
            <li>Status changes and assigned actions will appear in this workspace.</li>
            <li>No response-time commitment is published in this pre-release experience.</li>
            <li>Use the published <a href="/contact/">contact-routing page</a> to verify any public support option before sharing information elsewhere.</li>
        </ul>
    </section>
<?php endif; ?>

<?php if (!$applications): ?>
    <section class="portal-card empty-state">
        <h2>No applications yet</h2>
        <p>Explore published opportunities and choose the role that fits your experience.</p>
        <a class="button button-primary" href="/jobs/">Explore careers</a>
    </section>
<?php endif; ?>

<?php foreach ($applications as $application): ?>
    <?php
    $history = application_history((int) $application['id']);
    $tasks = application_tasks((int) $application['id'], true);
    $documents = application_documents((int) $application['id']);
    ?>
    <article class="portal-card application-panel">
        <header class="panel-heading">
            <div>
                <p class="kicker"><?= e($application['job_company']) ?><?php if ($application['job_is_demo']): ?> · Demonstration role<?php endif; ?></p>
                <h2><?= e($application['job_title']) ?></h2>
                <p><?= e($application['job_location']) ?> · Application #<?= (int) $application['id'] ?></p>
            </div>
            <span class="status-chip status-<?= e($application['current_status']) ?>"><?= e(ucfirst((string) $application['current_status'])) ?></span>
        </header>

        <?php if ($application['current_status'] === 'draft'): ?>
            <div class="alert alert-info">Your application is still private and has not been submitted.</div>
            <a class="button button-primary" href="/apply/step2.php?id=<?= (int) $application['id'] ?>">Continue application</a>
        <?php else: ?>
            <div class="panel-columns">
                <section>
                    <h3>Status history</h3>
                    <ol class="timeline">
                        <?php foreach ($history as $event): ?>
                            <li>
                                <span class="timeline-dot" aria-hidden="true"></span>
                                <div><strong><?= e(ucfirst((string) $event['status'])) ?></strong><p><?= e($event['note_public']) ?></p><time><?= e($event['created_at']) ?> UTC</time></div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>
                <section>
                    <h3>Next actions</h3>
                    <?php if (!$tasks): ?><p>No applicant actions are currently assigned.</p><?php endif; ?>
                    <ul class="task-list">
                        <?php foreach ($tasks as $task): ?>
                            <li>
                                <div><strong><?= e($task['title']) ?></strong><?php if ($task['due_date']): ?><small>Due <?= e($task['due_date']) ?></small><?php endif; ?></div>
                                <?php if (!application_is_terminal((string) $application['current_status'])): ?>
                                <form method="post" action="/applicant/task.php">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">
                                    <input type="hidden" name="status" value="<?= $task['status'] === 'completed' ? 'pending' : 'completed' ?>">
                                    <button class="button button-small <?= $task['status'] === 'completed' ? 'button-secondary' : 'button-primary' ?>" type="submit">
                                        <?= $task['status'] === 'completed' ? 'Reopen' : 'Mark complete' ?>
                                    </button>
                                </form>
                                <?php else: ?><span class="status-chip"><?= e(ucfirst((string) $task['status'])) ?></span><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        <?php endif; ?>

        <?php if ($documents): ?>
            <div class="document-summary">
                <p><strong>Attached resume:</strong> <?= e($documents[0]['original_name']) ?></p>
                <span class="status-chip status-warning"><?= e(ucfirst((string) $documents[0]['scan_status'])) ?></span>
            </div>
        <?php endif; ?>

        <?php if (in_array($application['current_status'], ['submitted', 'reviewing', 'shortlisted', 'requirements', 'scheduled'], true)): ?>
            <details class="withdraw-panel">
                <summary>Withdraw this application</summary>
                <form method="post" action="/applicant/withdraw.php" class="portal-form" data-confirm="Withdraw this application? This status change will be visible to recruitment staff.">
                    <?= csrf_field() ?>
                    <input type="hidden" name="application_id" value="<?= (int) $application['id'] ?>">
                    <label><span>Withdrawal note</span><textarea name="reason" rows="3" minlength="3" maxlength="500" required></textarea><small>Use a short operational reason. Do not add medical or other sensitive details.</small></label>
                    <button class="button button-secondary" type="submit">Confirm withdrawal</button>
                </form>
            </details>
        <?php endif; ?>
    </article>
<?php endforeach; ?>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
