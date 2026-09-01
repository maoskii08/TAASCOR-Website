<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$staff = require_role('staff', '/staff/login.php');
$filter = (string) ($_GET['status'] ?? 'all');
if ($filter !== 'all' && !in_array($filter, STAFF_MANAGED_APPLICATION_STATUSES, true)) {
    $filter = 'all';
}
$sql = "SELECT a.id, a.candidate_full_name, a.candidate_email, a.current_city, a.current_status,
               a.job_snapshot_json,
               a.submitted_at, a.updated_at, j.title AS job_title, j.company AS job_company
        FROM applications a INNER JOIN jobs j ON j.id = a.job_id
        WHERE a.current_status <> 'draft'";
$parameters = [];
if ($filter !== 'all') {
    $sql .= ' AND a.current_status = :status';
    $parameters['status'] = $filter;
}
$sql .= ' ORDER BY a.updated_at DESC';
$statement = db()->prepare($sql);
$statement->execute($parameters);
$applications = array_map('hydrate_application_job_snapshot', $statement->fetchAll());

$pageTitle = 'Application review queue';
$pageEyebrow = 'Staff workspace';
$pageDescription = 'Review only submitted applications. Every status change requires an applicant-visible note and creates an audit event.';
$bodyClass = 'staff-theme';
require __DIR__ . '/../app/views/header.php';
?>
<section class="portal-card filter-bar">
    <form method="get" class="inline-form">
        <label><span>Status</span><select name="status"><option value="all">All submitted</option><?php foreach (STAFF_MANAGED_APPLICATION_STATUSES as $status): ?><option value="<?= e($status) ?>" <?= $filter === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option><?php endforeach; ?></select></label>
        <button class="button button-secondary" type="submit">Filter</button>
    </form>
</section>
<section class="portal-card">
    <?php if (!$applications): ?><p>No applications match this filter.</p><?php endif; ?>
    <?php if ($applications): ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Applicant</th><th>Role</th><th>Location</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><strong><?= e($application['candidate_full_name']) ?></strong><small><?= e($application['candidate_email']) ?></small></td>
                        <td><?= e($application['job_title']) ?><small><?= e($application['job_company']) ?></small></td>
                        <td><?= e($application['current_city']) ?></td>
                        <td><span class="status-chip status-<?= e($application['current_status']) ?>"><?= e(ucfirst((string) $application['current_status'])) ?></span></td>
                        <td><?= e($application['submitted_at']) ?> UTC</td>
                        <td><a href="/staff/application.php?id=<?= (int) $application['id'] ?>">Review</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
