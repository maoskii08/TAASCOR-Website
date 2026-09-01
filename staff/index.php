<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$staff = require_role('staff', '/staff/login.php');
$counts = [
    'published_jobs' => (int) db()->query("SELECT COUNT(*) FROM jobs WHERE status = 'published'")->fetchColumn(),
    'submitted_applications' => (int) db()->query("SELECT COUNT(*) FROM applications WHERE current_status <> 'draft'")->fetchColumn(),
    'pending_tasks' => (int) db()->query("SELECT COUNT(*) FROM application_tasks WHERE status = 'pending'")->fetchColumn(),
    'quarantined_documents' => (int) db()->query("SELECT COUNT(*) FROM application_documents WHERE scan_status = 'quarantine'")->fetchColumn(),
];
$latest = db()->query(
    "SELECT a.id, a.current_status, a.updated_at, a.candidate_full_name, a.job_snapshot_json, j.title AS job_title
     FROM applications a INNER JOIN jobs j ON j.id = a.job_id
     WHERE a.current_status <> 'draft'
     ORDER BY a.updated_at DESC LIMIT 8"
)->fetchAll();
$latest = array_map('hydrate_application_job_snapshot', $latest);

$pageTitle = 'Recruitment operations';
$pageEyebrow = 'Staff workspace';
$pageDescription = 'Publish roles, review submitted applications, record human decisions, and assign applicant-visible tasks.';
$bodyClass = 'staff-theme';
require __DIR__ . '/../app/views/header.php';
?>
<section class="dashboard-summary stats-grid">
    <div><span>Published roles</span><strong><?= $counts['published_jobs'] ?></strong></div>
    <div><span>Submitted applications</span><strong><?= $counts['submitted_applications'] ?></strong></div>
    <div><span>Pending tasks</span><strong><?= $counts['pending_tasks'] ?></strong></div>
    <div><span>Quarantined files</span><strong><?= $counts['quarantined_documents'] ?></strong></div>
</section>
<div class="button-row workspace-nav">
    <a class="button button-primary" href="/staff/jobs.php">Manage jobs</a>
    <a class="button button-secondary" href="/staff/applications.php">Review applications</a>
</div>
<section class="portal-card">
    <div class="panel-heading"><div><p class="kicker">Review queue</p><h2>Recent submitted applications</h2></div></div>
    <?php if (!$latest): ?><p>No submitted applications yet.</p><?php endif; ?>
    <?php if ($latest): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Applicant</th><th>Role</th><th>Status</th><th>Updated</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($latest as $application): ?>
                    <tr>
                        <td><?= e($application['candidate_full_name']) ?></td>
                        <td><?= e($application['job_title']) ?></td>
                        <td><span class="status-chip status-<?= e($application['current_status']) ?>"><?= e(ucfirst((string) $application['current_status'])) ?></span></td>
                        <td><?= e($application['updated_at']) ?> UTC</td>
                        <td><a href="/staff/application.php?id=<?= (int) $application['id'] ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
