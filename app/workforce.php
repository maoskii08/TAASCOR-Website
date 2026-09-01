<?php

declare(strict_types=1);

function workforce_brief_rate_limited(): bool
{
    $statement = db()->prepare(
        "SELECT COUNT(*) FROM audit_events
         WHERE event_type = 'workforce_brief.submitted'
           AND ip_hash = :ip_hash
           AND created_at >= :window_start"
    );
    $statement->execute([
        'ip_hash' => request_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
        'window_start' => utc_minutes_ago(60),
    ]);
    return (int) $statement->fetchColumn() >= 5;
}

/**
 * @param array<string, mixed> $input
 * @return array{
 *   organization:string,contact_name:string,contact_email:string,contact_phone:string,
 *   sites:string,roles_needed:string,estimated_headcount:?int,shift_pattern:string,
 *   target_start_date:?string,service_needs:string,notes:string,privacy_accepted:bool
 * }
 */
function validate_workforce_brief(array $input): array
{
    $normalized = [
        'organization' => trim((string) ($input['organization'] ?? '')),
        'contact_name' => trim((string) ($input['contact_name'] ?? '')),
        'contact_email' => mb_strtolower(trim((string) ($input['contact_email'] ?? ''))),
        'contact_phone' => trim((string) ($input['contact_phone'] ?? '')),
        'sites' => trim((string) ($input['sites'] ?? '')),
        'roles_needed' => trim((string) ($input['roles_needed'] ?? '')),
        'estimated_headcount' => null,
        'shift_pattern' => trim((string) ($input['shift_pattern'] ?? '')),
        'target_start_date' => null,
        'service_needs' => trim((string) ($input['service_needs'] ?? '')),
        'notes' => trim((string) ($input['notes'] ?? '')),
        'privacy_accepted' => filter_var($input['privacy_accepted'] ?? false, FILTER_VALIDATE_BOOL),
    ];

    $headcountRaw = trim((string) ($input['estimated_headcount'] ?? ''));
    if ($headcountRaw !== '') {
        $headcount = filter_var($headcountRaw, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 100000],
        ]);
        if ($headcount === false) {
            throw new InvalidArgumentException('Estimated headcount must be between 1 and 100,000.');
        }
        $normalized['estimated_headcount'] = $headcount;
    }

    $targetStartRaw = trim((string) ($input['target_start_date'] ?? ''));
    if ($targetStartRaw !== '') {
        $targetStart = validate_date_input($targetStartRaw);
        if ($targetStart === null) {
            throw new InvalidArgumentException('Target start date must use a valid calendar date.');
        }
        $normalized['target_start_date'] = $targetStart;
    }

    $lengthRules = [
        'organization' => [2, 160, 'Organization'],
        'contact_name' => [2, 120, 'Contact name'],
        'sites' => [2, 1000, 'Sites or locations'],
        'roles_needed' => [2, 1500, 'Roles needed'],
        'service_needs' => [2, 1500, 'Service needs'],
    ];
    foreach ($lengthRules as $field => [$minimum, $maximum, $label]) {
        $length = mb_strlen($normalized[$field]);
        if ($length < $minimum || $length > $maximum) {
            throw new InvalidArgumentException("{$label} must be between {$minimum} and {$maximum} characters.");
        }
    }

    if (!filter_var($normalized['contact_email'], FILTER_VALIDATE_EMAIL)
        || mb_strlen($normalized['contact_email']) > 190) {
        throw new InvalidArgumentException('Enter a valid contact email address.');
    }
    if ($normalized['contact_phone'] !== '') {
        $phoneLength = mb_strlen($normalized['contact_phone']);
        if ($phoneLength < 7 || $phoneLength > 30) {
            throw new InvalidArgumentException('Contact phone must be between 7 and 30 characters when provided.');
        }
    }
    if (mb_strlen($normalized['shift_pattern']) > 500) {
        throw new InvalidArgumentException('Shift pattern may not exceed 500 characters.');
    }
    if (mb_strlen($normalized['notes']) > 2000) {
        throw new InvalidArgumentException('Additional notes may not exceed 2,000 characters.');
    }
    if (!$normalized['privacy_accepted']) {
        throw new InvalidArgumentException('Review and acknowledge the workforce enquiry privacy notice.');
    }

    return $normalized;
}

/** @param array<string, mixed> $input */
function create_workforce_brief(array $input): int
{
    require_approved_privacy_notice('workforce');
    $brief = validate_workforce_brief($input);
    return db_transaction(function () use ($brief): int {
        $timestamp = now_utc();
        $statement = db()->prepare(
            'INSERT INTO workforce_briefs
                (organization, contact_name, contact_email, contact_phone, sites, roles_needed,
                 estimated_headcount, shift_pattern, target_start_date, service_needs, notes,
                 privacy_notice_version, privacy_accepted_at, status, submitted_at, updated_at)
             VALUES
                (:organization, :contact_name, :contact_email, :contact_phone, :sites, :roles_needed,
                 :estimated_headcount, :shift_pattern, :target_start_date, :service_needs, :notes,
                 :privacy_notice_version, :privacy_accepted_at, :status, :submitted_at, :updated_at)'
        );
        $statement->execute([
            'organization' => $brief['organization'],
            'contact_name' => $brief['contact_name'],
            'contact_email' => $brief['contact_email'],
            'contact_phone' => $brief['contact_phone'],
            'sites' => $brief['sites'],
            'roles_needed' => $brief['roles_needed'],
            'estimated_headcount' => $brief['estimated_headcount'],
            'shift_pattern' => $brief['shift_pattern'],
            'target_start_date' => $brief['target_start_date'],
            'service_needs' => $brief['service_needs'],
            'notes' => $brief['notes'],
            'privacy_notice_version' => (string) config_value('workforce_privacy_notice_version'),
            'privacy_accepted_at' => $timestamp,
            'status' => 'submitted',
            'submitted_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $briefId = last_inserted_id();
        audit_event('workforce_brief.submitted', 'workforce_brief', $briefId, [
            'estimated_headcount' => $brief['estimated_headcount'],
            'has_target_start_date' => $brief['target_start_date'] !== null ? 1 : 0,
        ]);
        return $briefId;
    });
}
