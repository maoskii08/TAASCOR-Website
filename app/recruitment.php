<?php

declare(strict_types=1);

const APPLICATION_STATUSES = [
    'draft',
    'submitted',
    'reviewing',
    'shortlisted',
    'requirements',
    'scheduled',
    'hired',
    'declined',
    'withdrawn',
];

const STAFF_MANAGED_APPLICATION_STATUSES = [
    'submitted',
    'reviewing',
    'shortlisted',
    'requirements',
    'scheduled',
    'hired',
    'declined',
];

const STAFF_APPLICATION_STATUS_TRANSITIONS = [
    'submitted' => ['reviewing', 'shortlisted', 'declined'],
    'reviewing' => ['shortlisted', 'requirements', 'scheduled', 'declined'],
    'shortlisted' => ['requirements', 'scheduled', 'declined'],
    'requirements' => ['scheduled', 'declined'],
    'scheduled' => ['requirements', 'hired', 'declined'],
    'hired' => [],
    'declined' => [],
    'withdrawn' => [],
];

const JOB_STATUSES = ['draft', 'published', 'closed'];
const TERMINAL_APPLICATION_STATUSES = ['hired', 'declined', 'withdrawn'];

function application_is_terminal(string $status): bool
{
    return in_array($status, TERMINAL_APPLICATION_STATUSES, true);
}

/** @return list<string> */
function allowed_staff_status_transitions(string $currentStatus): array
{
    return STAFF_APPLICATION_STATUS_TRANSITIONS[$currentStatus] ?? [];
}

/** @return list<array<string, mixed>> */
function list_published_jobs(): array
{
    if (!job_publication_is_enabled()) {
        return [];
    }
    $statement = db()->prepare(
        "SELECT id, slug, title, company, location, employment_type, function_area, shift_pattern,
                summary, description, requirements, openings, closing_date, is_demo, published_at
         FROM jobs
         WHERE status = 'published' AND (closing_date IS NULL OR closing_date >= :today)
         ORDER BY published_at DESC, created_at DESC"
    );
    $statement->execute(['today' => business_today()]);
    return $statement->fetchAll();
}

/** @return array<string, mixed>|null */
function find_published_job_by_slug(string $slug): ?array
{
    if (!job_publication_is_enabled()) {
        return null;
    }
    $statement = db()->prepare(
        "SELECT id, slug, title, company, location, employment_type, function_area, shift_pattern,
                summary, description, requirements, openings, closing_date, is_demo, published_at
         FROM jobs
         WHERE status = 'published' AND slug = :slug
           AND (closing_date IS NULL OR closing_date >= :today)
         LIMIT 1"
    );
    $statement->execute(['slug' => trim($slug), 'today' => business_today()]);
    $job = $statement->fetch();
    return is_array($job) ? $job : null;
}

/** @return list<array<string, mixed>> */
function related_published_jobs(int $jobId, string $functionArea, int $limit = 3): array
{
    if (!job_publication_is_enabled()) {
        return [];
    }
    $safeLimit = max(1, min($limit, 6));
    $statement = db()->prepare(
        "SELECT id, slug, title, company, location, employment_type, function_area, shift_pattern,
                summary, description, requirements, openings, closing_date, is_demo, published_at
         FROM jobs
         WHERE status = 'published' AND id <> :job_id
           AND (closing_date IS NULL OR closing_date >= :today)
         ORDER BY CASE WHEN function_area = :function_area THEN 0 ELSE 1 END,
                  published_at DESC, created_at DESC
         LIMIT " . $safeLimit
    );
    $statement->execute([
        'job_id' => $jobId,
        'today' => business_today(),
        'function_area' => trim($functionArea),
    ]);
    return $statement->fetchAll();
}

/** @return array<string, mixed>|null */
function job_by_id(int $id): ?array
{
    $statement = db()->prepare('SELECT * FROM jobs WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $job = $statement->fetch();
    return is_array($job) ? $job : null;
}

/** @return array<string, mixed>|null */
function job_by_id_for_update(int $id): ?array
{
    $sql = 'SELECT * FROM jobs WHERE id = :id LIMIT 1';
    if (db_driver() === 'mysql') {
        $sql .= ' FOR UPDATE';
    }
    $statement = db()->prepare($sql);
    $statement->execute(['id' => $id]);
    $job = $statement->fetch();
    return is_array($job) ? $job : null;
}

/** @param array<string, mixed> $job */
function serialize_job_history_state(array $job): string
{
    return json_encode(
        $job,
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
}

/** @param array<string, mixed>|null $previousJob @param array<string, mixed> $newJob */
function record_job_change_history(
    int $jobId,
    string $eventType,
    ?array $previousJob,
    array $newJob,
    int $actorUserId
): int {
    $statement = db()->prepare(
        'INSERT INTO job_change_history
            (job_id, event_type, previous_job_json, new_job_json, actor_user_id, created_at)
         VALUES
            (:job_id, :event_type, :previous_job_json, :new_job_json, :actor_user_id, :created_at)'
    );
    $statement->execute([
        'job_id' => $jobId,
        'event_type' => mb_substr($eventType, 0, 64),
        'previous_job_json' => $previousJob === null ? null : serialize_job_history_state($previousJob),
        'new_job_json' => serialize_job_history_state($newJob),
        'actor_user_id' => $actorUserId,
        'created_at' => now_utc(),
    ]);
    return last_inserted_id();
}

/** @param array<string, mixed> $job @return array<string, mixed> */
function job_snapshot_payload(array $job): array
{
    $snapshot = [];
    foreach ([
        'id', 'slug', 'title', 'company', 'location', 'employment_type', 'function_area',
        'shift_pattern', 'summary', 'description', 'requirements', 'openings', 'closing_date', 'is_demo',
    ] as $field) {
        $snapshot[$field] = $job[$field] ?? null;
    }
    return $snapshot;
}

/** @param array<string, mixed> $job */
function job_snapshot_content_hash(array $job): string
{
    return hash('sha256', json_encode(
        job_snapshot_payload($job),
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    ));
}

/** @param array<string, mixed> $job */
function serialize_job_snapshot(array $job): string
{
    $snapshot = job_snapshot_payload($job);
    $snapshot['content_sha256'] = job_snapshot_content_hash($job);
    $snapshot['snapshotted_at'] = now_utc();
    return json_encode($snapshot, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/** @param array<string, mixed> $application @return array<string, mixed> */
function hydrate_application_job_snapshot(array $application): array
{
    $snapshot = application_job_snapshot_data($application);
    if ($snapshot === null) {
        $application['job_snapshot_integrity'] = 'missing_or_invalid';
        $application['job_title'] = 'Historical role context requires reconciliation';
        $application['job_company'] = 'Unavailable';
        $application['job_location'] = 'Unavailable';
        return $application;
    }
    $application['job_snapshot_integrity'] = 'verified';
    foreach ([
        'slug', 'title', 'company', 'location', 'employment_type', 'function_area',
        'shift_pattern', 'summary', 'description', 'requirements', 'openings', 'closing_date', 'is_demo',
    ] as $field) {
        if (array_key_exists($field, $snapshot)) {
            $application['job_' . $field] = $snapshot[$field];
        }
    }
    return $application;
}

/** @param array<string, mixed> $application @return array<string, mixed>|null */
function application_job_snapshot_data(array $application): ?array
{
    $rawSnapshot = $application['job_snapshot_json'] ?? null;
    if (!is_string($rawSnapshot) || trim($rawSnapshot) === '') {
        return null;
    }
    try {
        $snapshot = json_decode($rawSnapshot, true, 32, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        return null;
    }
    if (!is_array($snapshot) || !isset($snapshot['content_sha256']) || !is_string($snapshot['content_sha256'])) {
        return null;
    }
    $expectedHash = job_snapshot_content_hash($snapshot);
    if (!hash_equals($expectedHash, $snapshot['content_sha256'])) {
        return null;
    }
    return $snapshot;
}

/** @param array<string, mixed> $application @param array<string, mixed> $currentJob */
function application_job_snapshot_changed(array $application, array $currentJob): bool
{
    $snapshot = application_job_snapshot_data($application);
    if ($snapshot === null || !isset($snapshot['content_sha256'])) {
        return true;
    }
    return !hash_equals((string) $snapshot['content_sha256'], job_snapshot_content_hash($currentJob));
}

function slugify_job_title(string $value): string
{
    $value = mb_strtolower(trim($value));
    $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-');
}

/** @return array<string, mixed>|null */
function application_by_user_and_job(int $userId, int $jobId): ?array
{
    $statement = db()->prepare(
        'SELECT * FROM applications WHERE applicant_user_id = :user_id AND job_id = :job_id LIMIT 1'
    );
    $statement->execute(['user_id' => $userId, 'job_id' => $jobId]);
    $application = $statement->fetch();
    return is_array($application) ? $application : null;
}

/** @return array<string, mixed>|null */
function application_by_user_and_job_for_update(int $userId, int $jobId): ?array
{
    $sql = 'SELECT * FROM applications WHERE applicant_user_id = :user_id AND job_id = :job_id LIMIT 1';
    if (db_driver() === 'mysql') {
        $sql .= ' FOR UPDATE';
    }
    $statement = db()->prepare($sql);
    $statement->execute(['user_id' => $userId, 'job_id' => $jobId]);
    $application = $statement->fetch();
    return is_array($application) ? $application : null;
}

/** @return array<string, mixed>|null */
function application_by_id(int $id): ?array
{
    $statement = db()->prepare(
        'SELECT a.*, j.slug AS job_slug, j.title AS job_title, j.company AS job_company,
                j.location AS job_location, j.status AS job_status, j.closing_date AS job_closing_date,
                j.status AS job_current_status, j.closing_date AS job_current_closing_date,
                u.full_name AS applicant_account_name
         FROM applications a
         INNER JOIN jobs j ON j.id = a.job_id
         INNER JOIN users u ON u.id = a.applicant_user_id
         WHERE a.id = :id LIMIT 1'
    );
    $statement->execute(['id' => $id]);
    $application = $statement->fetch();
    return is_array($application) ? hydrate_application_job_snapshot($application) : null;
}

/** @return array<string, mixed>|null */
function application_row_for_update(int $id): ?array
{
    $sql = 'SELECT * FROM applications WHERE id = :id LIMIT 1';
    if (db_driver() === 'mysql') {
        $sql .= ' FOR UPDATE';
    }
    $statement = db()->prepare($sql);
    $statement->execute(['id' => $id]);
    $application = $statement->fetch();
    return is_array($application) ? $application : null;
}

function record_application_job_snapshot_history(
    int $applicationId,
    string $eventType,
    ?string $previousSnapshotJson,
    string $acceptedSnapshotJson,
    bool $applicantReviewedChanges,
    ?string $acknowledgedAt,
    ?int $actorUserId
): int {
    $previous = $previousSnapshotJson !== null
        ? json_decode($previousSnapshotJson, true)
        : null;
    $accepted = json_decode($acceptedSnapshotJson, true, 32, JSON_THROW_ON_ERROR);
    if (!is_array($accepted) || !isset($accepted['content_sha256'])) {
        throw new RuntimeException('Accepted role snapshot is invalid.');
    }
    $statement = db()->prepare(
        'INSERT INTO application_job_snapshot_history
            (application_id, event_type, previous_snapshot_json, accepted_snapshot_json,
             previous_content_sha256, accepted_content_sha256, applicant_reviewed_changes,
             acknowledged_at, actor_user_id, created_at)
         VALUES
            (:application_id, :event_type, :previous_snapshot_json, :accepted_snapshot_json,
             :previous_content_sha256, :accepted_content_sha256, :applicant_reviewed_changes,
             :acknowledged_at, :actor_user_id, :created_at)'
    );
    $statement->execute([
        'application_id' => $applicationId,
        'event_type' => mb_substr($eventType, 0, 64),
        'previous_snapshot_json' => $previousSnapshotJson,
        'accepted_snapshot_json' => $acceptedSnapshotJson,
        'previous_content_sha256' => is_array($previous) && isset($previous['content_sha256'])
            ? (string) $previous['content_sha256']
            : null,
        'accepted_content_sha256' => (string) $accepted['content_sha256'],
        'applicant_reviewed_changes' => $applicantReviewedChanges ? 1 : 0,
        'acknowledged_at' => $acknowledgedAt,
        'actor_user_id' => $actorUserId,
        'created_at' => now_utc(),
    ]);
    return last_inserted_id();
}

/** @param array{full_name:string,phone:string,current_city:string,eligibility_confirmed:bool,privacy_accepted:bool} $data */
function create_application_draft(int $applicantUserId, int $jobId, array $data): int
{
    require_approved_privacy_notice('applicant');
    $user = user_by_id($applicantUserId);
    $job = job_by_id($jobId);
    if (!$user || $user['role'] !== 'applicant' || !(bool) $user['is_active']) {
        throw new DomainException('Only an active applicant account can apply.');
    }
    if (!$job || $job['status'] !== 'published'
        || ($job['closing_date'] !== null && $job['closing_date'] < business_today())) {
        throw new DomainException('This job is not accepting applications.');
    }
    $fullName = trim($data['full_name']);
    $phone = trim($data['phone']);
    $city = trim($data['current_city']);
    if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120) {
        throw new InvalidArgumentException('Enter your full legal name.');
    }
    if (mb_strlen($phone) < 7 || mb_strlen($phone) > 30) {
        throw new InvalidArgumentException('Enter a valid phone number.');
    }
    if (mb_strlen($city) < 2 || mb_strlen($city) > 120) {
        throw new InvalidArgumentException('Enter your current city or municipality.');
    }
    if (!$data['eligibility_confirmed']) {
        throw new InvalidArgumentException('Confirm that you are eligible to work in the Philippines.');
    }
    if (!$data['privacy_accepted']) {
        throw new InvalidArgumentException('Review and acknowledge the applicant privacy notice.');
    }

    return db_transaction(function () use ($applicantUserId, $jobId, $user, $fullName, $phone, $city): int {
        $timestamp = now_utc();
        $currentJob = job_by_id_for_update($jobId);
        if (!$currentJob || $currentJob['status'] !== 'published'
            || ($currentJob['closing_date'] !== null && $currentJob['closing_date'] < business_today())) {
            throw new DomainException('This job is not accepting applications.');
        }
        $existing = application_by_user_and_job_for_update($applicantUserId, $jobId);
        if ($existing) {
            if ($existing['current_status'] !== 'draft') {
                throw new DomainException('An application for this role has already been submitted.');
            }
            $update = db()->prepare(
                "UPDATE applications
                 SET candidate_full_name = :candidate_full_name, candidate_email = :candidate_email,
                     candidate_phone = :candidate_phone, current_city = :current_city,
                     eligibility_confirmed = 1, privacy_notice_version = :privacy_notice_version,
                     privacy_accepted_at = :privacy_accepted_at, updated_at = :updated_at
                 WHERE id = :id AND applicant_user_id = :applicant_user_id AND current_status = 'draft'"
            );
            $update->execute([
                'candidate_full_name' => $fullName,
                'candidate_email' => (string) $user['email'],
                'candidate_phone' => $phone,
                'current_city' => $city,
                'privacy_notice_version' => (string) config_value('privacy_notice_version'),
                'privacy_accepted_at' => $timestamp,
                'updated_at' => $timestamp,
                'id' => (int) $existing['id'],
                'applicant_user_id' => $applicantUserId,
            ]);
            if ($update->rowCount() !== 1) {
                throw new DomainException('This draft changed while stage one was being saved. Reload and try again.');
            }
            audit_event(
                'application.draft_stage_one_updated',
                'application',
                (int) $existing['id'],
                ['privacy_notice_version' => (string) config_value('privacy_notice_version')],
                $applicantUserId
            );
            return (int) $existing['id'];
        }
        $jobSnapshot = serialize_job_snapshot($currentJob);
        $statement = db()->prepare(
            'INSERT INTO applications
                (applicant_user_id, job_id, job_snapshot_json, candidate_full_name, candidate_email, candidate_phone,
                 current_city, eligibility_confirmed, current_status, privacy_notice_version,
                 privacy_accepted_at, created_at, updated_at)
             VALUES
                (:applicant_user_id, :job_id, :job_snapshot_json, :candidate_full_name, :candidate_email, :candidate_phone,
                 :current_city, 1, :current_status, :privacy_notice_version,
                 :privacy_accepted_at, :created_at, :updated_at)'
        );
        $statement->execute([
            'applicant_user_id' => $applicantUserId,
            'job_id' => $jobId,
            'job_snapshot_json' => $jobSnapshot,
            'candidate_full_name' => $fullName,
            'candidate_email' => (string) $user['email'],
            'candidate_phone' => $phone,
            'current_city' => $city,
            'current_status' => 'draft',
            'privacy_notice_version' => (string) config_value('privacy_notice_version'),
            'privacy_accepted_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $applicationId = last_inserted_id();
        record_application_job_snapshot_history(
            $applicationId,
            'draft_terms_captured',
            null,
            $jobSnapshot,
            false,
            null,
            $applicantUserId
        );
        add_status_history($applicationId, 'draft', 'Application started.', $applicantUserId);
        audit_event('application.draft_created', 'application', $applicationId, ['job_id' => $jobId], $applicantUserId);
        return $applicationId;
    });
}

function update_application_stage_two(int $applicationId, int $applicantUserId, string $experienceSummary): void
{
    require_approved_privacy_notice('applicant');
    $application = application_by_id($applicationId);
    if (!$application || (int) $application['applicant_user_id'] !== $applicantUserId) {
        throw new DomainException('Application not found.');
    }
    if ($application['current_status'] !== 'draft') {
        throw new DomainException('This application has already been submitted.');
    }
    $experienceSummary = trim($experienceSummary);
    if (mb_strlen($experienceSummary) > 1500) {
        throw new InvalidArgumentException('Experience summary may not exceed 1,500 characters.');
    }
    db_transaction(function () use ($applicationId, $applicantUserId, $experienceSummary): void {
        $statement = db()->prepare(
            "UPDATE applications SET experience_summary = :summary, updated_at = :updated_at
             WHERE id = :id AND applicant_user_id = :applicant_user_id AND current_status = 'draft'"
        );
        $statement->execute([
            'summary' => $experienceSummary,
            'updated_at' => now_utc(),
            'id' => $applicationId,
            'applicant_user_id' => $applicantUserId,
        ]);
        if ($statement->rowCount() !== 1) {
            $current = application_by_id($applicationId);
            if (!$current || (int) $current['applicant_user_id'] !== $applicantUserId || $current['current_status'] !== 'draft') {
                throw new DomainException('This application changed while the draft was being saved. Reload and review its current state.');
            }
        }
        audit_event('application.draft_saved', 'application', $applicationId, [], $applicantUserId);
    });
}

function submit_application(
    int $applicationId,
    int $applicantUserId,
    bool $certified,
    bool $jobChangeReviewed = false,
    ?string $reviewedJobVersion = null
): void
{
    require_approved_privacy_notice('applicant');
    if (!$certified) {
        throw new InvalidArgumentException('Certify that the submitted information is accurate.');
    }
    db_transaction(function () use (
        $applicationId,
        $applicantUserId,
        $jobChangeReviewed,
        $reviewedJobVersion
    ): void {
        $applicationLookup = application_by_id($applicationId);
        if (!$applicationLookup || (int) $applicationLookup['applicant_user_id'] !== $applicantUserId) {
            throw new DomainException('Application not found.');
        }
        $currentJob = job_by_id_for_update((int) $applicationLookup['job_id']);
        $application = application_row_for_update($applicationId);
        if (!$application || (int) $application['applicant_user_id'] !== $applicantUserId) {
            throw new DomainException('Application not found.');
        }
        if ($application['current_status'] !== 'draft') {
            throw new DomainException('This application has already been submitted.');
        }
        if (!$currentJob || $currentJob['status'] !== 'published'
            || ($currentJob['closing_date'] !== null && $currentJob['closing_date'] < business_today())) {
            throw new DomainException('This role is no longer accepting applications. Your draft remains available for reference.');
        }
        $jobTermsChanged = application_job_snapshot_changed($application, $currentJob);
        if ($jobTermsChanged) {
            $currentVersion = job_snapshot_content_hash($currentJob);
            if (!$jobChangeReviewed || $reviewedJobVersion === null || !hash_equals($currentVersion, $reviewedJobVersion)) {
                throw new DomainException('This role changed after you started. Review the current role details shown on this page, then confirm them before submitting.');
            }
        }
        $timestamp = now_utc();
        $previousSnapshotJson = is_string($application['job_snapshot_json'] ?? null)
            ? (string) $application['job_snapshot_json']
            : null;
        $acceptedSnapshotJson = serialize_job_snapshot($currentJob);
        $statement = db()->prepare(
            "UPDATE applications
             SET current_status = 'submitted', certification_at = :certification_at,
                 submitted_at = :submitted_at, job_snapshot_json = :job_snapshot_json, updated_at = :updated_at
             WHERE id = :id AND current_status = 'draft'"
        );
        $statement->execute([
            'certification_at' => $timestamp,
            'submitted_at' => $timestamp,
            'job_snapshot_json' => $acceptedSnapshotJson,
            'updated_at' => $timestamp,
            'id' => $applicationId,
        ]);
        if ($statement->rowCount() !== 1) {
            throw new DomainException('This application changed while it was being submitted. Reload and review its current state.');
        }
        record_application_job_snapshot_history(
            $applicationId,
            'submission_terms_accepted',
            $previousSnapshotJson,
            $acceptedSnapshotJson,
            $jobTermsChanged && $jobChangeReviewed,
            $timestamp,
            $applicantUserId
        );
        add_status_history($applicationId, 'submitted', 'Application submitted for review.', $applicantUserId);
        $previousSnapshot = application_job_snapshot_data($application);
        $acceptedSnapshot = json_decode($acceptedSnapshotJson, true, 32, JSON_THROW_ON_ERROR);
        audit_event('application.submitted', 'application', $applicationId, [
            'job_terms_changed' => $jobTermsChanged,
            'applicant_reviewed_changes' => $jobTermsChanged && $jobChangeReviewed,
            'previous_job_content_sha256' => is_array($previousSnapshot)
                ? (string) ($previousSnapshot['content_sha256'] ?? '')
                : null,
            'accepted_job_content_sha256' => (string) ($acceptedSnapshot['content_sha256'] ?? ''),
        ], $applicantUserId);
    });
}

function add_status_history(int $applicationId, string $status, string $publicNote, ?int $changedBy): int
{
    if (!in_array($status, APPLICATION_STATUSES, true)) {
        throw new InvalidArgumentException('Unknown application status.');
    }
    $statement = db()->prepare(
        'INSERT INTO application_status_history
            (application_id, status, note_public, changed_by_user_id, created_at)
         VALUES
            (:application_id, :status, :note_public, :changed_by_user_id, :created_at)'
    );
    $statement->execute([
        'application_id' => $applicationId,
        'status' => $status,
        'note_public' => mb_substr(trim($publicNote), 0, 500),
        'changed_by_user_id' => $changedBy,
        'created_at' => now_utc(),
    ]);
    return last_inserted_id();
}

/** @return list<array<string, mixed>> */
function applicant_applications(int $applicantUserId): array
{
    $statement = db()->prepare(
        'SELECT a.*, j.slug AS job_slug, j.title AS job_title, j.company AS job_company,
                j.location AS job_location, j.is_demo AS job_is_demo
         FROM applications a
         INNER JOIN jobs j ON j.id = a.job_id
         WHERE a.applicant_user_id = :applicant_user_id
         ORDER BY a.updated_at DESC'
    );
    $statement->execute(['applicant_user_id' => $applicantUserId]);
    return array_map('hydrate_application_job_snapshot', $statement->fetchAll());
}

/** @return list<array<string, mixed>> */
function application_history(int $applicationId): array
{
    $statement = db()->prepare(
        'SELECT * FROM application_status_history WHERE application_id = :application_id ORDER BY created_at ASC, id ASC'
    );
    $statement->execute(['application_id' => $applicationId]);
    return $statement->fetchAll();
}

/** @return list<array<string, mixed>> */
function application_tasks(int $applicationId, bool $applicantOnly = false): array
{
    $sql = 'SELECT * FROM application_tasks WHERE application_id = :application_id';
    if ($applicantOnly) {
        $sql .= ' AND applicant_visible = 1';
    }
    $sql .= ' ORDER BY CASE status WHEN \'pending\' THEN 0 ELSE 1 END, due_date ASC, created_at ASC';
    $statement = db()->prepare($sql);
    $statement->execute(['application_id' => $applicationId]);
    return $statement->fetchAll();
}

/** @return list<array<string, mixed>> */
function application_documents(int $applicationId): array
{
    $statement = db()->prepare(
        'SELECT id, application_id, original_name, mime_type, size_bytes, content_sha256,
                scan_status, scanned_at, scan_result, retention_expires_at, uploaded_at
         FROM application_documents WHERE application_id = :application_id ORDER BY uploaded_at DESC'
    );
    $statement->execute(['application_id' => $applicationId]);
    return $statement->fetchAll();
}

function record_application_document(int $applicationId, int $applicantUserId, array $document): int
{
    require_approved_privacy_notice('applicant');
    if (!resume_upload_is_enabled()) {
        throw new DomainException('Resume upload is not enabled for this environment.');
    }
    $sizeBytes = (int) ($document['size_bytes'] ?? 0);
    $contentHash = mb_strtolower((string) ($document['content_sha256'] ?? ''));
    if ($sizeBytes <= 0 || preg_match('/^[a-f0-9]{64}$/', $contentHash) !== 1) {
        throw new InvalidArgumentException('The quarantined document metadata is incomplete.');
    }

    try {
        return db_transaction(function () use ($applicationId, $applicantUserId, $document, $sizeBytes, $contentHash): int {
            $application = application_row_for_update($applicationId);
            if (!$application || (int) $application['applicant_user_id'] !== $applicantUserId) {
                throw new DomainException('Application not found.');
            }
            if ($application['current_status'] !== 'draft') {
                throw new DomainException('Documents may be added only to an active application draft.');
            }
            $existing = db()->prepare(
                'SELECT id FROM application_documents WHERE application_id = :application_id LIMIT 1'
            );
            $existing->execute(['application_id' => $applicationId]);
            if ($existing->fetchColumn()) {
                throw new DomainException('A resume is already attached to this application.');
            }

            $maximumStorage = (int) config_value('max_private_storage_bytes', 1073741824);
            if ($sizeBytes > $maximumStorage) {
                throw new DomainException('Private resume storage has reached its configured limit.');
            }
            $reserve = db()->prepare(
                'UPDATE private_storage_usage
                 SET used_bytes = used_bytes + :increment, updated_at = :updated_at
                 WHERE scope = :scope AND used_bytes <= :remaining_capacity'
            );
            $reserve->execute([
                'increment' => $sizeBytes,
                'updated_at' => now_utc(),
                'scope' => 'application_documents',
                'remaining_capacity' => $maximumStorage - $sizeBytes,
            ]);
            if ($reserve->rowCount() !== 1) {
                throw new DomainException('Private resume storage has reached its configured limit.');
            }

            $retentionDays = (int) config_value('resume_retention_days', 0);
            $retentionExpiresAt = $retentionDays > 0
                ? gmdate('Y-m-d H:i:s', time() + ($retentionDays * 86400))
                : null;
            $statement = db()->prepare(
                'INSERT INTO application_documents
                    (application_id, original_name, storage_name, mime_type, size_bytes, content_sha256,
                     scan_status, scanned_at, scan_result, retention_expires_at, uploaded_at)
                 VALUES
                    (:application_id, :original_name, :storage_name, :mime_type, :size_bytes, :content_sha256,
                     :scan_status, NULL, NULL, :retention_expires_at, :uploaded_at)'
            );
            $statement->execute([
                'application_id' => $applicationId,
                'original_name' => $document['original_name'],
                'storage_name' => $document['storage_name'],
                'mime_type' => $document['mime_type'],
                'size_bytes' => $sizeBytes,
                'content_sha256' => $contentHash,
                'scan_status' => 'quarantine',
                'retention_expires_at' => $retentionExpiresAt,
                'uploaded_at' => now_utc(),
            ]);
            $documentId = last_inserted_id();
            audit_event('application.document_quarantined', 'application_document', $documentId, [
                'application_id' => $applicationId,
                'size_bytes' => $sizeBytes,
                'content_sha256' => $contentHash,
                'retention_expires_at' => $retentionExpiresAt,
            ], $applicantUserId);
            return $documentId;
        });
    } catch (PDOException $exception) {
        $detail = mb_strtolower($exception->getMessage());
        if (str_contains($detail, 'uq_document_application')
            || str_contains($detail, 'application_documents.application_id')) {
            throw new DomainException('A resume is already attached to this application.', 0, $exception);
        }
        throw $exception;
    }
}

function staff_update_application_status(
    int $applicationId,
    string $newStatus,
    string $publicNote,
    int $staffUserId
): void {
    $staff = user_by_id($staffUserId);
    if (!$staff || $staff['role'] !== 'staff' || !(bool) $staff['is_active']) {
        throw new DomainException('Staff authorization is required.');
    }
    if (!in_array($newStatus, STAFF_MANAGED_APPLICATION_STATUSES, true)) {
        throw new InvalidArgumentException('Select a supported staff-managed status.');
    }
    $publicNote = trim($publicNote);
    if (mb_strlen($publicNote) < 3 || mb_strlen($publicNote) > 500) {
        throw new InvalidArgumentException('Provide a 3 to 500 character applicant-visible note.');
    }

    db_transaction(function () use ($applicationId, $newStatus, $publicNote, $staffUserId): void {
        $application = application_row_for_update($applicationId);
        if (!$application || $application['current_status'] === 'draft') {
            throw new DomainException('Only submitted applications can be managed by staff.');
        }
        $oldStatus = (string) $application['current_status'];
        if (!in_array($newStatus, allowed_staff_status_transitions($oldStatus), true)) {
            throw new DomainException('That status transition is not allowed from the application’s current state.');
        }
        $statement = db()->prepare(
            'UPDATE applications SET current_status = :status, updated_at = :updated_at
             WHERE id = :id AND current_status = :expected_status'
        );
        $statement->execute([
            'status' => $newStatus,
            'updated_at' => now_utc(),
            'id' => $applicationId,
            'expected_status' => $oldStatus,
        ]);
        if ($statement->rowCount() !== 1) {
            throw new DomainException('This application changed while the status was being updated. Reload and review its current state.');
        }
        add_status_history($applicationId, $newStatus, $publicNote, $staffUserId);
        audit_event('application.status_changed', 'application', $applicationId, [
            'from' => $oldStatus,
            'to' => $newStatus,
        ], $staffUserId);
    });
}

function withdraw_application(int $applicationId, int $applicantUserId, string $reason): void
{
    $reason = trim($reason);
    if (mb_strlen($reason) < 3 || mb_strlen($reason) > 500) {
        throw new InvalidArgumentException('Provide a 3 to 500 character withdrawal note.');
    }

    db_transaction(function () use ($applicationId, $applicantUserId, $reason): void {
        $application = application_row_for_update($applicationId);
        if (!$application || (int) $application['applicant_user_id'] !== $applicantUserId) {
            throw new DomainException('Application not found.');
        }
        if (!in_array($application['current_status'], ['submitted', 'reviewing', 'shortlisted', 'requirements', 'scheduled'], true)) {
            throw new DomainException('This application can no longer be withdrawn through the portal.');
        }

        $oldStatus = (string) $application['current_status'];
        $statement = db()->prepare(
            "UPDATE applications SET current_status = 'withdrawn', updated_at = :updated_at
             WHERE id = :id AND applicant_user_id = :applicant_user_id AND current_status = :expected_status"
        );
        $statement->execute([
            'updated_at' => now_utc(),
            'id' => $applicationId,
            'applicant_user_id' => $applicantUserId,
            'expected_status' => $oldStatus,
        ]);
        if ($statement->rowCount() !== 1) {
            throw new DomainException('This application changed while it was being withdrawn. Reload and review its current state.');
        }
        add_status_history($applicationId, 'withdrawn', 'Application withdrawn by applicant: ' . $reason, $applicantUserId);
        audit_event('application.withdrawn', 'application', $applicationId, [], $applicantUserId);
    });
}

function staff_add_application_task(
    int $applicationId,
    string $title,
    ?string $dueDate,
    bool $applicantVisible,
    int $staffUserId
): int {
    $staff = user_by_id($staffUserId);
    if (!$staff || $staff['role'] !== 'staff' || !(bool) $staff['is_active']) {
        throw new DomainException('Staff authorization is required.');
    }
    $application = application_by_id($applicationId);
    if (!$application) {
        throw new DomainException('Application not found.');
    }
    if ($application['current_status'] === 'draft' || application_is_terminal((string) $application['current_status'])) {
        throw new DomainException('Tasks can be added only while a submitted application remains active.');
    }
    $title = trim($title);
    if (mb_strlen($title) < 3 || mb_strlen($title) > 180) {
        throw new InvalidArgumentException('Task title must be between 3 and 180 characters.');
    }
    if ($dueDate !== null && validate_date_input($dueDate) === null) {
        throw new InvalidArgumentException('Enter a valid task due date.');
    }
    return db_transaction(function () use ($applicationId, $title, $dueDate, $applicantVisible, $staffUserId): int {
        $currentApplication = application_row_for_update($applicationId);
        if (!$currentApplication || $currentApplication['current_status'] === 'draft'
            || application_is_terminal((string) $currentApplication['current_status'])) {
            throw new DomainException('This application no longer accepts task changes.');
        }
        $timestamp = now_utc();
        $statement = db()->prepare(
            'INSERT INTO application_tasks
                (application_id, title, due_date, status, applicant_visible, created_by_user_id, created_at, updated_at)
             VALUES
                (:application_id, :title, :due_date, :status, :applicant_visible, :created_by_user_id, :created_at, :updated_at)'
        );
        $statement->execute([
            'application_id' => $applicationId,
            'title' => $title,
            'due_date' => $dueDate,
            'status' => 'pending',
            'applicant_visible' => $applicantVisible ? 1 : 0,
            'created_by_user_id' => $staffUserId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $taskId = last_inserted_id();
        audit_event('application.task_created', 'application_task', $taskId, [
            'application_id' => $applicationId,
            'applicant_visible' => $applicantVisible ? 1 : 0,
        ], $staffUserId);
        return $taskId;
    });
}

function set_task_status_for_applicant(int $taskId, int $applicantUserId, string $status): void
{
    if (!in_array($status, ['pending', 'completed'], true)) {
        throw new InvalidArgumentException('Unknown task status.');
    }
    db_transaction(function () use ($taskId, $applicantUserId, $status): void {
        $statement = db()->prepare(
            'SELECT t.*, a.applicant_user_id, a.current_status AS application_status
             FROM application_tasks t
             INNER JOIN applications a ON a.id = t.application_id
             WHERE t.id = :id AND t.applicant_visible = 1 LIMIT 1'
        );
        $statement->execute(['id' => $taskId]);
        $task = $statement->fetch();
        if (!$task || (int) $task['applicant_user_id'] !== $applicantUserId) {
            throw new DomainException('Task not found.');
        }
        $currentApplication = application_row_for_update((int) $task['application_id']);
        if (!$currentApplication || (int) $currentApplication['applicant_user_id'] !== $applicantUserId) {
            throw new DomainException('Task not found.');
        }
        if ($currentApplication['current_status'] === 'draft'
            || application_is_terminal((string) $currentApplication['current_status'])) {
            throw new DomainException('Tasks cannot change after the application reaches a terminal state.');
        }
        $oldStatus = (string) $task['status'];
        if ($oldStatus === $status) {
            return;
        }
        $update = db()->prepare(
            'UPDATE application_tasks SET status = :status, updated_at = :updated_at
             WHERE id = :id AND status = :expected_status'
        );
        $update->execute([
            'status' => $status,
            'updated_at' => now_utc(),
            'id' => $taskId,
            'expected_status' => $oldStatus,
        ]);
        if ($update->rowCount() !== 1) {
            throw new DomainException('This task changed while it was being updated. Reload and review its current state.');
        }
        audit_event('application.task_updated_by_applicant', 'application_task', $taskId, ['status' => $status], $applicantUserId);
    });
}

function staff_set_task_status(int $applicationId, int $taskId, int $staffUserId, string $status): void
{
    $staff = user_by_id($staffUserId);
    if (!$staff || $staff['role'] !== 'staff' || !(bool) $staff['is_active']) {
        throw new DomainException('Staff authorization is required.');
    }
    if (!in_array($status, ['pending', 'completed'], true)) {
        throw new InvalidArgumentException('Unknown task status.');
    }
    db_transaction(function () use ($applicationId, $taskId, $staffUserId, $status): void {
        $statement = db()->prepare(
            'SELECT t.*, a.current_status AS application_status
             FROM application_tasks t INNER JOIN applications a ON a.id = t.application_id
             WHERE t.id = :id AND t.application_id = :application_id LIMIT 1'
        );
        $statement->execute(['id' => $taskId, 'application_id' => $applicationId]);
        $task = $statement->fetch();
        if (!$task) {
            throw new DomainException('Task not found for this application.');
        }
        $currentApplication = application_row_for_update($applicationId);
        if (!$currentApplication) {
            throw new DomainException('Application not found.');
        }
        if ($currentApplication['current_status'] === 'draft'
            || application_is_terminal((string) $currentApplication['current_status'])) {
            throw new DomainException('Tasks cannot change after the application reaches a terminal state.');
        }
        $oldStatus = (string) $task['status'];
        if ($oldStatus === $status) {
            return;
        }
        $update = db()->prepare(
            'UPDATE application_tasks SET status = :status, updated_at = :updated_at
             WHERE id = :id AND application_id = :application_id AND status = :expected_status'
        );
        $update->execute([
            'status' => $status,
            'updated_at' => now_utc(),
            'id' => $taskId,
            'application_id' => $applicationId,
            'expected_status' => $oldStatus,
        ]);
        if ($update->rowCount() !== 1) {
            throw new DomainException('This task changed while it was being updated. Reload and review its current state.');
        }
        audit_event('application.task_updated_by_staff', 'application_task', $taskId, [
            'application_id' => $applicationId,
            'status' => $status,
        ], $staffUserId);
    });
}
