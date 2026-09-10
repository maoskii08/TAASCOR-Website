<?php

declare(strict_types=1);

function hris_jobs_feed_enabled(): bool
{
    return env_bool('HRIS_JOBS_FEED_ENABLED', false);
}

/** @return list<array<string,mixed>> */
function hris_published_jobs(): array
{
    $endpoint = trim((string) env_value('HRIS_JOBS_FEED_URL', ''));
    if (preg_match('#^https://[^/]+(?:/.*)?$#i', $endpoint) !== 1) {
        throw new RuntimeException('The HRIS job integration requires an approved HTTPS feed URL.');
    }

    $cache = sys_get_temp_dir() . DIRECTORY_SEPARATOR
        . 'taascor-hris-jobs-' . substr(hash('sha256', $endpoint), 0, 16) . '.json';
    try {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 4,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\nUser-Agent: TAASCOR-Website-Job-Projection/1\r\n",
            ],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $raw = @file_get_contents($endpoint, false, $context);
        $statusLine = $http_response_header[0] ?? '';
        if ($raw === false || preg_match('/\s200\s/', $statusLine) !== 1) {
            throw new RuntimeException('The authoritative HRIS job feed is unavailable.');
        }
        $response = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (
            ($response['success'] ?? 0) !== 1
            || (int) ($response['meta']['schema_version'] ?? 0) !== 1
            || !is_array($response['data'] ?? null)
        ) {
            throw new RuntimeException('The HRIS job feed contract is invalid.');
        }
        $jobs = [];
        foreach ($response['data'] as $job) {
            if (
                !is_array($job)
                || preg_match('/^[0-9a-f-]{36}$/i', (string) ($job['public_id'] ?? '')) !== 1
                || preg_match('/\A[a-z0-9][a-z0-9-]{0,159}\z/', (string) ($job['slug'] ?? '')) !== 1
            ) {
                continue;
            }
            $jobs[] = hris_map_job($job);
        }
        @file_put_contents(
            $cache,
            json_encode(['stored_at' => time(), 'jobs' => $jobs], JSON_THROW_ON_ERROR),
            LOCK_EX
        );
        return $jobs;
    } catch (Throwable $error) {
        if (is_file($cache)) {
            $cached = json_decode((string) file_get_contents($cache), true);
            if (
                is_array($cached)
                && time() - (int) ($cached['stored_at'] ?? 0) <= 900
                && is_array($cached['jobs'] ?? null)
            ) {
                return $cached['jobs'];
            }
        }
        throw $error;
    }
}

/** @param array<string,mixed> $job @return array<string,mixed> */
function hris_map_job(array $job): array
{
    $publicId = (string) $job['public_id'];
    $slug = (string) $job['slug'];

    return [
        'id' => hexdec(substr(hash('sha256', $publicId), 0, 7)),
        'public_id' => $publicId,
        'slug' => $slug,
        'title' => (string) $job['title'],
        'company' => 'TAASCOR Management & General Services Corp.',
        'location' => (string) $job['location_label'],
        'employment_type' => ucwords(str_replace('_', ' ', (string) $job['employment_type'])),
        'function_area' => 'General services',
        'shift_pattern' => 'See role details',
        'summary' => (string) $job['summary'],
        'description' => trim(strip_tags((string) ($job['description_html'] ?? $job['summary']))),
        'requirements' => trim(strip_tags((string) ($job['requirements_html'] ?? 'Requirements are listed in the approved role details.'))),
        'openings' => null,
        'closing_date' => isset($job['closes_at']) ? substr((string) $job['closes_at'], 0, 10) : null,
        'is_demo' => false,
        'published_at' => (string) ($job['updated_at'] ?? gmdate('c')),
        'apply_url' => '/apply/' . rawurlencode($slug) . '/',
        'source' => 'hris',
    ];
}
