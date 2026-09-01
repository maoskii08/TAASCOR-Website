<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$staff = require_role('staff', '/staff/login.php');
$applicationId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
$application = application_by_id($applicationId);
if (!$application || $application['current_status'] === 'draft') {
    http_abort(404, 'Submitted application not found.');
}
$applicationIsTerminal = application_is_terminal((string) $application['current_status']);

if (is_post()) {
    verify_csrf();
    try {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'update_status') {
            staff_update_application_status(
                $applicationId,
                (string) ($_POST['status'] ?? ''),
                (string) ($_POST['public_note'] ?? ''),
                (int) $staff['id']
            );
            flash('success', 'Application status and public history updated.');
        } elseif ($action === 'add_task') {
            $dueDateRaw = trim((string) ($_POST['due_date'] ?? ''));
            staff_add_application_task(
                $applicationId,
                (string) ($_POST['title'] ?? ''),
                $dueDateRaw !== '' ? $dueDateRaw : null,
                isset($_POST['applicant_visible']),
                (int) $staff['id']
            );
            flash('success', 'Task created.');
        } elseif ($action === 'set_task_status') {
            staff_set_task_status(
                $applicationId,
                (int) ($_POST['task_id'] ?? 0),
                (int) $staff['id'],
                (string) ($_POST['status'] ?? '')
            );
            flash('success', 'Task status updated.');
        } else {
            throw new InvalidArgumentException('Unknown management action.');
        }
    } catch (InvalidArgumentException | DomainException $exception) {
        flash('error', $exception->getMessage());
    }
    redirect_to('/staff/application.php?id=' . $applicationId);
}

$history = application_history($applicationId);
$tasks = application_tasks($applicationId);
$documents = application_documents($applicationId);
$allowedStatusTransitions = allowed_staff_status_transitions((string) $application['current_status']);
$pageTitle = 'Review application #' . $applicationId;
$pageEyebrow = 'Staff workspace';
$pageDescription = $application['candidate_full_name'] . ' · ' . $application['job_title'];
$bodyClass = 'staff-theme';
require __DIR__ . '/../app/views/header.php';
?>
<section class="portal-card application-record">
    <header class="panel-heading">
        <div><p class="kicker"><?= e($application['job_company']) ?></p><h2><?= e($application['job_title']) ?></h2><p><?= e($application['job_location']) ?></p></div>
        <span class="status-chip status-<?= e($application['current_status']) ?>"><?= e(ucfirst((string) $application['current_status'])) ?></span>
    </header>
    <dl class="record-grid">
        <div><dt>Applicant</dt><dd><?= e($application['candidate_full_name']) ?></dd></div>
        <div><dt>Email</dt><dd><?= e($application['candidate_email']) ?></dd></div>
        <div><dt>Phone</dt><dd><?= e($application['candidate_phone']) ?></dd></div>
        <div><dt>Current city</dt><dd><?= e($application['current_city']) ?></dd></div>
        <div><dt>Eligibility confirmed</dt><dd><?= $application['eligibility_confirmed'] ? 'Yes' : 'No' ?></dd></div>
        <div><dt>Privacy notice</dt><dd><?= e($application['privacy_notice_version']) ?> at <?= e($application['privacy_accepted_at']) ?> UTC</dd></div>
    </dl>
    <h3>Relevant experience</h3>
    <p class="preserve-lines"><?= e($application['experience_summary'] ?: 'No experience summary provided.') ?></p>
</section>

<div class="portal-grid portal-grid-admin">
    <section class="portal-card">
        <p class="kicker">Human decision</p>
        <h2>Update status</h2>
        <?php if ($allowedStatusTransitions): ?>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $applicationId ?>">
            <input type="hidden" name="action" value="update_status">
            <label><span>New status</span><select name="status"><?php foreach ($allowedStatusTransitions as $status): ?><option value="<?= e($status) ?>"><?= e(ucfirst($status)) ?></option><?php endforeach; ?></select></label>
            <label><span>Applicant-visible note</span><textarea name="public_note" rows="4" minlength="3" maxlength="500" required></textarea><small>This becomes part of the applicant's permanent status timeline.</small></label>
            <button class="button button-primary" type="submit">Record status decision</button>
        </form>
        <?php else: ?>
            <p>This application is in a terminal state. Reopening it requires a separately governed correction workflow.</p>
        <?php endif; ?>
    </section>
    <section class="portal-card">
        <p class="kicker">Action management</p>
        <h2>Add task</h2>
        <?php if ($applicationIsTerminal): ?>
            <p>This application is in a terminal state. Its task history is read-only.</p>
        <?php else: ?>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= $applicationId ?>">
            <input type="hidden" name="action" value="add_task">
            <label><span>Task title</span><input name="title" maxlength="180" required></label>
            <label><span>Due date <small>Optional</small></span><input type="date" name="due_date"></label>
            <label class="check-row"><input type="checkbox" name="applicant_visible" value="1" checked><span>Show this task to the applicant</span></label>
            <button class="button button-secondary" type="submit">Add task</button>
        </form>
        <?php endif; ?>
    </section>
</div>

<div class="portal-grid portal-grid-admin">
    <section class="portal-card">
        <h2>Status history</h2>
        <ol class="timeline">
            <?php foreach ($history as $event): ?>
                <li><span class="timeline-dot" aria-hidden="true"></span><div><strong><?= e(ucfirst((string) $event['status'])) ?></strong><p><?= e($event['note_public']) ?></p><time><?= e($event['created_at']) ?> UTC</time></div></li>
            <?php endforeach; ?>
        </ol>
    </section>
    <section class="portal-card">
        <h2>Tasks</h2>
        <?php if (!$tasks): ?><p>No tasks created.</p><?php endif; ?>
        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li>
                    <div><strong><?= e($task['title']) ?></strong><small><?= $task['applicant_visible'] ? 'Applicant visible' : 'Staff only' ?><?= $task['due_date'] ? ' · Due ' . e($task['due_date']) : '' ?></small></div>
                    <?php if (!$applicationIsTerminal): ?>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $applicationId ?>">
                        <input type="hidden" name="action" value="set_task_status">
                        <input type="hidden" name="task_id" value="<?= (int) $task['id'] ?>">
                        <input type="hidden" name="status" value="<?= $task['status'] === 'completed' ? 'pending' : 'completed' ?>">
                        <button class="button button-small button-secondary" type="submit"><?= $task['status'] === 'completed' ? 'Reopen' : 'Complete' ?></button>
                    </form>
                    <?php else: ?><span class="status-chip"><?= e(ucfirst((string) $task['status'])) ?></span><?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

<section class="portal-card">
    <p class="kicker">Private documents</p>
    <h2>Quarantine inventory</h2>
    <?php if (!$documents): ?><p>No resume attached.</p><?php endif; ?>
    <?php foreach ($documents as $document): ?>
        <div class="document-summary">
            <p><strong><?= e($document['original_name']) ?></strong> · <?= e(number_format((int) $document['size_bytes'] / 1024, 1)) ?> KB · <?= e($document['mime_type']) ?></p>
            <span class="status-chip status-warning"><?= e(ucfirst((string) $document['scan_status'])) ?></span>
        </div>
    <?php endforeach; ?>
    <p class="small-copy">Quarantined files have no public or staff download route. A production malware-scanning and controlled-release service is required before document access is enabled.</p>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
