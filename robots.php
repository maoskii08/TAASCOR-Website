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

header('Content-Type: text/plain; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');

echo "User-agent: *\n";
if (filter_var(getenv('PUBLIC_INDEXING_ENABLED') ?: 'false', FILTER_VALIDATE_BOOL) !== true) {
    echo "Disallow: /\n";
    echo 'Sitemap: ' . $baseUrl . "/sitemap.xml\n";
    exit;
}
echo "Allow: /\n";
foreach ([
    '/account/',
    '/applicant/',
    '/apply/',
    '/staff/',
    '/app/',
    '/database/',
    '/storage/',
    '/Backups/',
    '/Planning/',
    '/Audit/',
    '/scripts/',
    '/tests/',
] as $path) {
    echo 'Disallow: ' . $path . "\n";
}
echo 'Sitemap: ' . $baseUrl . "/sitemap.xml\n";
