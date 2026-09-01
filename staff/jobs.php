<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$staff = require_role('staff', '/staff/login.php');
$errors = [];
$editId = (int) ($_GET['edit'] ?? 0);
$editJob = $editId > 0 ? job_by_id($editId) : null;

if (is_post()) {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'save_job') {
            $id = (int) ($_POST['id'] ?? 0);
            $expectedVersion = (int) ($_POST['expected_version'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $company = trim((string) ($_POST['company'] ?? ''));
            $location = trim((string) ($_POST['location'] ?? ''));
            $employmentType = trim((string) ($_POST['employment_type'] ?? ''));
            $functionArea = trim((string) ($_POST['function_area'] ?? ''));
            $shiftPattern = trim((string) ($_POST['shift_pattern'] ?? ''));
            $summary = trim((string) ($_POST['summary'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $requirements = trim((string) ($_POST['requirements'] ?? ''));
            $openingsRaw = trim((string) ($_POST['openings'] ?? ''));
            $openings = $openingsRaw === '' ? null : filter_var($openingsRaw, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 100000],
            ]);
            $closingDateRaw = trim((string) ($_POST['closing_date'] ?? ''));
            $closingDate = $closingDateRaw === '' ? null : validate_date_input($closingDateRaw);
            $slug = slugify_job_title((string) ($_POST['slug'] ?? $title));
            $status = (string) ($_POST['status'] ?? 'draft');
            if (mb_strlen($title) < 3 || mb_strlen($title) > 160) {
                throw new InvalidArgumentException('Job title must be between 3 and 160 characters.');
            }
            if (mb_strlen($company) < 2 || mb_strlen($company) > 160) {
                throw new InvalidArgumentException('Company must be between 2 and 160 characters.');
            }
            if (mb_strlen($location) < 2 || mb_strlen($location) > 160) {
                throw new InvalidArgumentException('Location must be between 2 and 160 characters.');
            }
            if (mb_strlen($employmentType) < 2 || mb_strlen($employmentType) > 80) {
                throw new InvalidArgumentException('Employment type must be between 2 and 80 characters.');
            }
            if (mb_strlen($functionArea) < 2 || mb_strlen($functionArea) > 120) {
                throw new InvalidArgumentException('Function area must be between 2 and 120 characters.');
            }
            if (mb_strlen($shiftPattern) < 2 || mb_strlen($shiftPattern) > 120) {
                throw new InvalidArgumentException('Shift pattern must be between 2 and 120 characters.');
            }
            if ($slug === '' || mb_strlen($slug) > 160) {
                throw new InvalidArgumentException('Enter a usable URL slug.');
            }
            if (mb_strlen($summary) < 10 || mb_strlen($summary) > 500) {
                throw new InvalidArgumentException('Summary must be between 10 and 500 characters.');
            }
            if (mb_strlen($description) < 10 || mb_strlen($description) > 5000) {
                throw new InvalidArgumentException('Description must be between 10 and 5,000 characters.');
            }
            if (mb_strlen($requirements) < 10 || mb_strlen($requirements) > 5000) {
                throw new InvalidArgumentException('Requirements must be between 10 and 5,000 characters.');
            }
            if ($openingsRaw !== '' && $openings === false) {
                throw new InvalidArgumentException('Openings must be between 1 and 100,000 when provided.');
            }
            if ($closingDateRaw !== '' && $closingDate === null) {
                throw new InvalidArgumentException('Enter a valid closing date.');
            }
            if (!in_array($status, JOB_STATUSES, true)) {
                throw new InvalidArgumentException('Select a valid job status.');
            }
            if ($status === 'published' && !job_publication_is_enabled()) {
                throw new DomainException(
                    'Production job publishing is disabled until the legal hiring organization, worksite, approver, and publication workflow are confirmed.'
                );
            }

            $timestamp = now_utc();
            if ($id > 0) {
                db_transaction(function () use (
                    $id, $slug, $title, $company, $location, $employmentType, $functionArea,
                    $shiftPattern, $summary, $description, $requirements, $openings, $closingDate,
                    $status, $timestamp, $staff, $expectedVersion
                ): void {
                    $existing = job_by_id_for_update($id);
                    if (!$existing) {
                        throw new DomainException('Job not found.');
                    }
                    if ($expectedVersion < 1 || (int) $existing['version'] !== $expectedVersion) {
                        throw new DomainException('This job changed in another staff session. Reload it before saving your edits.');
                    }
                    $publishedAt = $status === 'published'
                        ? ($existing['published_at'] ?: $timestamp)
                        : $existing['published_at'];
                    $statement = db()->prepare(
                        'UPDATE jobs SET slug = :slug, title = :title, company = :company, location = :location,
                            employment_type = :employment_type, function_area = :function_area,
                            shift_pattern = :shift_pattern, summary = :summary, description = :description,
                            requirements = :requirements, openings = :openings, closing_date = :closing_date,
                            status = :status, published_at = :published_at, version = version + 1,
                            updated_at = :updated_at WHERE id = :id AND version = :expected_version'
                    );
                    $statement->execute([
                        'slug' => $slug, 'title' => $title, 'company' => $company, 'location' => $location,
                        'employment_type' => $employmentType, 'function_area' => $functionArea,
                        'shift_pattern' => $shiftPattern, 'summary' => $summary, 'description' => $description,
                        'requirements' => $requirements, 'openings' => $openings, 'closing_date' => $closingDate,
                        'status' => $status, 'published_at' => $publishedAt, 'updated_at' => $timestamp,
                        'id' => $id, 'expected_version' => $expectedVersion,
                    ]);
                    if ($statement->rowCount() !== 1) {
                        throw new DomainException('This job changed in another staff session. Reload it before saving your edits.');
                    }
                    $updated = job_by_id_for_update($id);
                    if (!$updated) {
                        throw new RuntimeException('Unable to verify the updated job.');
                    }
                    record_job_change_history($id, 'job_updated', $existing, $updated, (int) $staff['id']);
                    audit_event('job.updated', 'job', $id, [
                        'status' => $status,
                        'from_version' => $expectedVersion,
                        'to_version' => (int) $updated['version'],
                    ], (int) $staff['id']);
                });
            } else {
                $id = db_transaction(function () use (
                    $slug, $title, $company, $location, $employmentType, $functionArea,
                    $shiftPattern, $summary, $description, $requirements, $openings, $closingDate,
                    $status, $timestamp, $staff
                ): int {
                    $statement = db()->prepare(
                        'INSERT INTO jobs
                            (slug, title, company, location, employment_type, function_area, shift_pattern,
                             summary, description, requirements, openings, closing_date, status, is_demo,
                             published_at, created_at, updated_at)
                         VALUES
                            (:slug, :title, :company, :location, :employment_type, :function_area, :shift_pattern,
                             :summary, :description, :requirements, :openings, :closing_date, :status, 0,
                             :published_at, :created_at, :updated_at)'
                    );
                    $statement->execute([
                        'slug' => $slug, 'title' => $title, 'company' => $company, 'location' => $location,
                        'employment_type' => $employmentType, 'function_area' => $functionArea,
                        'shift_pattern' => $shiftPattern, 'summary' => $summary, 'description' => $description,
                        'requirements' => $requirements, 'openings' => $openings, 'closing_date' => $closingDate,
                        'status' => $status, 'published_at' => $status === 'published' ? $timestamp : null,
                        'created_at' => $timestamp, 'updated_at' => $timestamp,
                    ]);
                    $newId = last_inserted_id();
                    $createdJob = job_by_id_for_update($newId);
                    if (!$createdJob) {
                        throw new RuntimeException('Unable to verify the created job.');
                    }
                    record_job_change_history($newId, 'job_created', null, $createdJob, (int) $staff['id']);
                    audit_event('job.created', 'job', $newId, ['status' => $status], (int) $staff['id']);
                    return $newId;
                });
            }
            flash('success', 'Job saved.');
            redirect_to('/staff/jobs.php');
        }

        if ($action === 'set_status') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = (string) ($_POST['status'] ?? '');
            $expectedVersion = (int) ($_POST['expected_version'] ?? 0);
            if (!in_array($status, JOB_STATUSES, true)) {
                throw new InvalidArgumentException('Job or status is invalid.');
            }
            if ($status === 'published' && !job_publication_is_enabled()) {
                throw new DomainException(
                    'Production job publishing is disabled until the legal hiring organization, worksite, approver, and publication workflow are confirmed.'
                );
            }
            db_transaction(function () use ($id, $status, $staff, $expectedVersion): void {
                $job = job_by_id_for_update($id);
                if (!$job) {
                    throw new InvalidArgumentException('Job or status is invalid.');
                }
                if ($expectedVersion < 1 || (int) $job['version'] !== $expectedVersion) {
                    throw new DomainException('This job changed in another staff session. Reload before changing its publication state.');
                }
                $timestamp = now_utc();
                $statement = db()->prepare(
                    'UPDATE jobs SET status = :status,
                        published_at = :published_at, version = version + 1, updated_at = :updated_at
                      WHERE id = :id AND version = :expected_version'
                );
                $statement->execute([
                    'status' => $status,
                    'published_at' => $status === 'published' ? ($job['published_at'] ?: $timestamp) : $job['published_at'],
                    'updated_at' => $timestamp,
                    'id' => $id,
                    'expected_version' => $expectedVersion,
                ]);
                if ($statement->rowCount() !== 1) {
                    throw new DomainException('This job changed in another staff session. Reload before changing its publication state.');
                }
                $updated = job_by_id_for_update($id);
                if (!$updated) {
                    throw new RuntimeException('Unable to verify the job status change.');
                }
                record_job_change_history($id, 'job_status_changed', $job, $updated, (int) $staff['id']);
                audit_event('job.status_changed', 'job', $id, [
                    'from' => (string) $job['status'],
                    'to' => $status,
                    'from_version' => $expectedVersion,
                    'to_version' => (int) $updated['version'],
                ], (int) $staff['id']);
            });
            flash('success', 'Job status updated.');
            redirect_to('/staff/jobs.php');
        }
    } catch (InvalidArgumentException | DomainException $exception) {
        $errors[] = $exception->getMessage();
    } catch (PDOException $exception) {
        error_log('TAASCOR job persistence error: ' . $exception->getMessage());
        $errors[] = str_contains(mb_strtolower($exception->getMessage()), 'slug')
            ? 'The job slug must be unique.'
            : 'The job change could not be recorded.';
    } catch (Throwable $exception) {
        error_log('TAASCOR job workflow error: ' . $exception->getMessage());
        $errors[] = config_value('debug') ? $exception->getMessage() : 'The job change could not be completed.';
    }
}

$jobs = db()->query('SELECT * FROM jobs ORDER BY updated_at DESC')->fetchAll();
$formJob = $editJob ?: [
    'id' => 0, 'slug' => '', 'title' => '', 'company' => '', 'location' => '',
    'employment_type' => 'Full-time', 'function_area' => '', 'shift_pattern' => '',
    'summary' => '', 'description' => '', 'requirements' => '', 'openings' => '',
    'closing_date' => '', 'status' => 'draft', 'version' => 0,
];
$pageTitle = $editJob ? 'Edit job' : 'Job publishing';
$pageEyebrow = 'Staff workspace';
$pageDescription = 'Draft, publish, or close governed roles. Publishing requires the actual legal hiring organization, worksite, accountable approver, and verified terms.';
$bodyClass = 'staff-theme';
require __DIR__ . '/../app/views/header.php';
?>
<div class="portal-grid portal-grid-admin">
    <section class="portal-card">
        <p class="kicker"><?= $editJob ? 'Editing job #' . (int) $editJob['id'] : 'Create role' ?></p>
        <?php if ($errors): ?><div class="alert alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <form method="post" class="portal-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save_job">
            <input type="hidden" name="id" value="<?= (int) $formJob['id'] ?>">
            <input type="hidden" name="expected_version" value="<?= (int) $formJob['version'] ?>">
            <label><span>Job title</span><input name="title" value="<?= e($formJob['title']) ?>" maxlength="160" required></label>
            <label><span>URL slug <small>Generated from the title if blank</small></span><input name="slug" value="<?= e($formJob['slug']) ?>" maxlength="160" pattern="[a-z0-9-]+"></label>
            <label><span>Legal hiring organization <small>Use the actual employer, not a client or worksite label</small></span><input name="company" value="<?= e($formJob['company']) ?>" maxlength="160" required></label>
            <div class="field-pair">
                <label><span>Location</span><input name="location" value="<?= e($formJob['location']) ?>" maxlength="160" required></label>
                <label><span>Employment type</span><input name="employment_type" value="<?= e($formJob['employment_type']) ?>" maxlength="80" required></label>
            </div>
            <div class="field-pair">
                <label><span>Function area</span><input name="function_area" value="<?= e($formJob['function_area']) ?>" maxlength="120" required></label>
                <label><span>Shift pattern</span><input name="shift_pattern" value="<?= e($formJob['shift_pattern']) ?>" maxlength="120" required></label>
            </div>
            <label><span>Card summary</span><textarea name="summary" rows="3" maxlength="500" required><?= e($formJob['summary']) ?></textarea></label>
            <label><span>Role description</span><textarea name="description" rows="8" maxlength="5000" required><?= e($formJob['description']) ?></textarea></label>
            <label><span>Essential requirements</span><textarea name="requirements" rows="6" maxlength="5000" required><?= e($formJob['requirements']) ?></textarea></label>
            <div class="field-pair">
                <label><span>Number of openings <small>Optional</small></span><input type="number" name="openings" value="<?= e((string) $formJob['openings']) ?>" min="1" max="100000"></label>
                <label><span>Closing date <small>Optional</small></span><input type="date" name="closing_date" value="<?= e((string) $formJob['closing_date']) ?>"></label>
            </div>
            <label><span>Status</span><select name="status"><?php foreach (JOB_STATUSES as $status): ?><option value="<?= e($status) ?>" <?= $formJob['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option><?php endforeach; ?></select></label>
            <div class="button-row">
                <button class="button button-primary" type="submit">Save job</button>
                <?php if ($editJob): ?><a class="button button-secondary" href="/staff/jobs.php">Cancel</a><?php endif; ?>
            </div>
        </form>
    </section>
    <section class="portal-card">
        <p class="kicker">Role inventory</p>
        <h2><?= count($jobs) ?> jobs</h2>
        <div class="admin-list">
            <?php foreach ($jobs as $job): ?>
                <article>
                    <div>
                        <p class="kicker"><?= e($job['company']) ?><?php if ($job['is_demo']): ?> · Demo<?php endif; ?></p>
                        <h3><?= e($job['title']) ?></h3>
                        <p><?= e($job['location']) ?> · <?= e($job['employment_type']) ?> · <?= e($job['function_area']) ?> · <?= e($job['shift_pattern']) ?></p>
                    </div>
                    <div class="admin-actions">
                        <span class="status-chip status-<?= e($job['status']) ?>"><?= e(ucfirst((string) $job['status'])) ?></span>
                        <a href="/staff/jobs.php?edit=<?= (int) $job['id'] ?>">Edit</a>
                        <form method="post">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="set_status">
                            <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                            <input type="hidden" name="expected_version" value="<?= (int) $job['version'] ?>">
                            <input type="hidden" name="status" value="<?= $job['status'] === 'published' ? 'closed' : 'published' ?>">
                            <button class="link-button" type="submit"><?= $job['status'] === 'published' ? 'Close' : 'Publish' ?></button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<?php require __DIR__ . '/../app/views/footer.php'; ?>
