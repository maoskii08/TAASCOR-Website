<?php

declare(strict_types=1);

require_once __DIR__ . '/app/early.php';
require_once __DIR__ . '/app/config.php';

$baseUrl = rtrim((string) config_value('url', ''), '/');
if (!app_url_is_origin($baseUrl, is_production())) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "A valid APP_URL is required.\n";
    exit;
}

$routes = filter_var(getenv('PUBLIC_INDEXING_ENABLED') ?: 'false', FILTER_VALIDATE_BOOL) === true ? [
    '/' => __DIR__ . '/index.html',
    '/solutions/' => __DIR__ . '/solutions/index.php',
    '/solutions/workforce-staffing/' => __DIR__ . '/solutions/workforce-staffing/index.php',
    '/solutions/recruitment-sourcing/' => __DIR__ . '/solutions/recruitment-sourcing/index.php',
    '/solutions/payroll-coordination/' => __DIR__ . '/solutions/payroll-coordination/index.php',
    '/solutions/hr-administration/' => __DIR__ . '/solutions/hr-administration/index.php',
    '/solutions/facility-support/' => __DIR__ . '/solutions/facility-support/index.php',
    '/solutions/hris-enabled-operations/' => __DIR__ . '/solutions/hris-enabled-operations/index.php',
    '/industries/' => __DIR__ . '/industries/index.php',
    '/industries/production-throughput/' => __DIR__ . '/industries/production-throughput/index.php',
    '/industries/distribution-fulfilment/' => __DIR__ . '/industries/distribution-fulfilment/index.php',
    '/industries/office-service-support/' => __DIR__ . '/industries/office-service-support/index.php',
    '/industries/facilities-site-support/' => __DIR__ . '/industries/facilities-site-support/index.php',
    '/platform/' => __DIR__ . '/platform/index.php',
    '/proof/' => __DIR__ . '/proof/index.php',
    '/clients/' => __DIR__ . '/clients/index.php',
    '/case-studies/' => __DIR__ . '/case-studies/index.php',
    '/about/' => __DIR__ . '/about/index.php',
    '/leadership/' => __DIR__ . '/leadership/index.php',
    '/locations/' => __DIR__ . '/locations/index.php',
    '/contact/' => __DIR__ . '/contact/index.php',
    '/insights/' => __DIR__ . '/insights/index.php',
    '/resources/' => __DIR__ . '/resources/index.php',
    '/jobs/' => __DIR__ . '/careers/index.php',
] : [];

if ($routes !== []) {
    try {
        require_once __DIR__ . '/app/database.php';
        require_once __DIR__ . '/app/recruitment.php';
        foreach (list_published_jobs() as $job) {
            $slug = (string) ($job['slug'] ?? '');
            if ((bool) ($job['is_demo'] ?? false)
                || preg_match('/\A[a-z0-9][a-z0-9-]{0,159}\z/', $slug) !== 1) {
                continue;
            }
            $routes['/jobs/' . $slug . '/'] = __DIR__ . '/careers/job.php';
        }
    } catch (Throwable $exception) {
        error_log('TAASCOR sitemap job catalogue unavailable: ' . $exception->getMessage());
    }
}

header('Content-Type: application/xml; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('X-Robots-Tag: noindex');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($routes as $route => $sourceFile) {
    $lastModified = is_file($sourceFile) ? filemtime($sourceFile) : false;
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($baseUrl . $route, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n";
    if ($lastModified !== false) {
        echo '    <lastmod>' . gmdate('Y-m-d', $lastModified) . "</lastmod>\n";
    }
    echo "  </url>\n";
}
echo "</urlset>\n";
