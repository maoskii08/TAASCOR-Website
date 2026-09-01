<?php

declare(strict_types=1);

require_once __DIR__ . '/app/early.php';
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/security.php';
require_once __DIR__ . '/app/database.php';
require_once __DIR__ . '/app/recruitment.php';

enforce_production_https();

/** @param list<array<string, mixed>>|null $jobs */
function render_home_job_preview(?array $jobs): string
{
    if ($jobs === null) {
        return '<div class="opp-empty" role="status">The governed opportunity catalogue is temporarily unavailable. No vacancy status is implied; please try Careers again later.</div>';
    }
    if ($jobs === []) {
        return '<div class="opp-empty">No approved openings are published right now. Visit Careers to review the current governed listing.</div>';
    }

    $visibleJobs = array_slice($jobs, 0, 3);
    $hasDemoJob = array_reduce(
        $visibleJobs,
        static fn (bool $found, array $job): bool => $found || (bool) ($job['is_demo'] ?? false),
        false
    );
    $markup = '';

    if ($hasDemoJob) {
        $markup .= '<div class="opp-demo" role="note"><strong>Local demonstration</strong><span>Synthetic roles are shown only to validate the integrated experience. They are not real vacancies.</span></div>';
    }

    $markup .= '<div class="opp-grid">';
    foreach ($visibleJobs as $job) {
        $slug = rawurlencode((string) ($job['slug'] ?? ''));
        $title = e($job['title'] ?? 'Opportunity');
        $summary = e($job['summary'] ?? 'Review the governed role profile and application steps.');
        $location = e($job['location'] ?? 'Location to be confirmed');
        $employmentType = e($job['employment_type'] ?? 'Employment type to be confirmed');
        $functionArea = e($job['function_area'] ?? 'Workforce opportunity');
        $tag = (bool) ($job['is_demo'] ?? false) ? 'Demonstration role' : 'Open role';

        $markup .= '<a class="opp-card" href="/jobs/' . $slug . '/">';
        $markup .= '<span class="opp-tag">' . e($tag) . '</span>';
        $markup .= '<h3>' . $title . '</h3>';
        $markup .= '<p>' . $summary . '</p>';
        $markup .= '<span class="opp-meta"><span>' . $location . '</span><span>' . $employmentType . '</span><span>' . $functionArea . '</span></span>';
        $markup .= '</a>';
    }
    $markup .= '</div>';

    return $markup;
}

$sourcePath = __DIR__ . '/index.html';
$source = file_get_contents($sourcePath);
if ($source === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'TAASCOR homepage is temporarily unavailable.';
    exit;
}

$publicIndexingEnabled = filter_var(getenv('PUBLIC_INDEXING_ENABLED') ?: 'false', FILTER_VALIDATE_BOOL) === true;
if ($publicIndexingEnabled) {
    $source = str_replace(
        '<meta name="robots" content="noindex,nofollow">',
        '<meta name="robots" content="index,follow">',
        $source
    );
}

$configuredBaseUrl = rtrim((string) config_value('url', ''), '/');
$canonicalMarkup = '';
$organizationMarkup = '';
if (app_url_is_origin($configuredBaseUrl, is_production())) {
    $canonicalHome = htmlspecialchars($configuredBaseUrl . '/', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $canonicalMarkup = '<link rel="canonical" href="' . $canonicalHome . '">' . "\n" .
        '<meta property="og:url" content="' . $canonicalHome . '">';
    $organizationMarkup = '<script type="application/ld+json">' . json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'TAASCOR',
        'url' => $configuredBaseUrl . '/',
    ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>';
}
$source = str_replace('<!-- TAASCOR_CANONICAL_META -->', $canonicalMarkup, $source);
$source = str_replace('<!-- TAASCOR_ORGANIZATION_DATA -->', $organizationMarkup, $source);

try {
    $jobPreview = render_home_job_preview(list_published_jobs());
} catch (Throwable $exception) {
    error_log('TAASCOR homepage job preview unavailable: ' . $exception->getMessage());
    $jobPreview = render_home_job_preview(null);
}

$source = preg_replace(
    '#<!-- TAASCOR_JOB_PREVIEW_START -->.*?<!-- TAASCOR_JOB_PREVIEW_END -->#s',
    "<!-- TAASCOR_JOB_PREVIEW_START -->\n" . $jobPreview . "\n    <!-- TAASCOR_JOB_PREVIEW_END -->",
    $source,
    1
) ?? $source;

$scriptHashes = [];
if (preg_match_all('#<script(?:\s[^>]*)?>(.*?)</script>#si', $source, $matches)) {
    foreach ($matches[1] as $script) {
        if ($script !== '') {
            $scriptHashes[] = "'sha256-" . base64_encode(hash('sha256', $script, true)) . "'";
        }
    }
}

if (!headers_sent()) {
    header_remove('X-Powered-By');
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    header('X-Robots-Tag: ' . ($publicIndexingEnabled ? 'index,follow' : 'noindex,nofollow'));
    header(
        "Content-Security-Policy: default-src 'self'; " .
        "base-uri 'self'; object-src 'none'; frame-ancestors 'none'; " .
        "img-src 'self' data:; " .
        "font-src 'self' https://fonts.gstatic.com https://cdn.fontshare.com https://api.fontshare.com; " .
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://api.fontshare.com; " .
        "script-src 'self' https://cdn.jsdelivr.net " . implode(' ', array_unique($scriptHashes)) . "; " .
        "connect-src 'self'; form-action 'self'"
    );
    header('Cross-Origin-Opener-Policy: same-origin');

    if (request_uses_https()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}

echo $source;
