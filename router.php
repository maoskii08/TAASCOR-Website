<?php

declare(strict_types=1);

$projectRoot = __DIR__;
$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
header_remove('X-Powered-By');
if (preg_match('/[\x00-\x1F\x7F]/', $requestPath)) {
    http_response_code(400);
    require $projectRoot . '/site/404.php';
    return true;
}
$requestPath = '/' . ltrim(str_replace('\\', '/', $requestPath), '/');

$restrictedPrefixes = [
    '/.git', '/.claude', '/app', '/database', '/storage', '/Backups',
    '/Planning', '/Audit', '/tests', '/scripts', '/node_modules',
];
foreach ($restrictedPrefixes as $prefix) {
    if (strcasecmp($requestPath, $prefix) === 0 || str_starts_with(strtolower($requestPath), strtolower($prefix . '/'))) {
        http_response_code(404);
        require $projectRoot . '/site/404.php';
        return true;
    }
}

if (
    preg_match('#(?:^|/)\.#', $requestPath)
    || preg_match('#/[^/]*\.(?:sql|md|lock|toml|log|ini|example|zip|tar|gz|tgz|bz2|7z|rar|bak|old|orig|swp)$#i', $requestPath)
    || preg_match('#/[^/]*~$#', $requestPath)
    || preg_match('#/(?:composer|package)(?:-lock)?\.(?:json|lock)$#i', $requestPath)
    || preg_match('#/(?:playwright|vite|webpack|rollup|eslint|prettier)\.config\.[^/]+$#i', $requestPath)
) {
    http_response_code(404);
    require $projectRoot . '/site/404.php';
    return true;
}

if ($requestPath === '/robots.txt') {
    require $projectRoot . '/robots.php';
    return true;
}
if ($requestPath === '/sitemap.xml') {
    require $projectRoot . '/sitemap.php';
    return true;
}
if ($requestPath === '/' || strcasecmp($requestPath, '/index.html') === 0 || strcasecmp($requestPath, '/index.php') === 0) {
    require $projectRoot . '/index.php';
    return true;
}
if (strcasecmp($requestPath, '/jobs') === 0 || strcasecmp($requestPath, '/jobs/') === 0) {
    require $projectRoot . '/careers/index.php';
    return true;
}
if (preg_match('#^/jobs/([a-z0-9][a-z0-9-]{0,159})/?$#', $requestPath, $jobRoute) === 1) {
    $_GET['job'] = $jobRoute[1];
    require $projectRoot . '/careers/job.php';
    return true;
}
if (preg_match('#^/apply/([a-z0-9][a-z0-9-]{0,159})/?$#', $requestPath, $applicationRoute) === 1) {
    $_GET['job'] = $applicationRoute[1];
    require $projectRoot . '/apply/index.php';
    return true;
}
if (strcasecmp($requestPath, '/site/404.php') === 0) {
    require $projectRoot . '/site/404.php';
    return true;
}

$candidate = realpath($projectRoot . DIRECTORY_SEPARATOR . ltrim($requestPath, '/'));
$normalizedRoot = strtolower(str_replace('\\', '/', realpath($projectRoot) ?: $projectRoot));
$normalizedCandidate = $candidate === false ? '' : strtolower(str_replace('\\', '/', $candidate));
if ($candidate !== false && ($normalizedCandidate === $normalizedRoot || str_starts_with($normalizedCandidate, $normalizedRoot . '/'))) {
    $publicRouteDirectories = [
        'about', 'account', 'applicant', 'apply', 'careers', 'case-studies', 'clients',
        'contact', 'industries', 'insights', 'leadership', 'legal', 'locations', 'login', 'platform',
        'portal', 'proof', 'resources', 'solutions', 'staff', 'workforce', 'jobs',
    ];
    $allowed = false;
    $favicon = realpath($projectRoot . DIRECTORY_SEPARATOR . 'favicon.svg');
    if (is_file($candidate) && $favicon !== false && strcasecmp($candidate, $favicon) === 0) {
        $allowed = true;
    }

    $assetsRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . 'assets');
    if (!$allowed && $assetsRoot !== false) {
        $normalizedAssetsRoot = strtolower(str_replace('\\', '/', $assetsRoot));
        $insideAssets = $normalizedCandidate === $normalizedAssetsRoot
            || str_starts_with($normalizedCandidate, $normalizedAssetsRoot . '/');
        if ($insideAssets && is_file($candidate)
            && preg_match('/\.(?:css|js|svg|png|jpe?g|webp|avif|woff2?)$/i', $candidate)) {
            $allowed = true;
        }
    }

    foreach ($publicRouteDirectories as $directory) {
        $publicRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . $directory);
        if ($publicRoot === false) {
            continue;
        }
        $normalizedPublicRoot = strtolower(str_replace('\\', '/', $publicRoot));
        $insidePublicRoot = $normalizedCandidate === $normalizedPublicRoot
            || str_starts_with($normalizedCandidate, $normalizedPublicRoot . '/');
        if (!$insidePublicRoot) {
            continue;
        }
        if (is_file($candidate) && strtolower(pathinfo($candidate, PATHINFO_EXTENSION)) === 'php') {
            $allowed = true;
            break;
        }
        if (is_dir($candidate) && is_file($candidate . DIRECTORY_SEPARATOR . 'index.php')) {
            $allowed = true;
            break;
        }
    }

    if ($allowed) {
        if (is_file($candidate)) {
            return false;
        }
        if (is_dir($candidate)) {
            foreach (['index.php', 'index.html'] as $indexFile) {
                if (is_file($candidate . DIRECTORY_SEPARATOR . $indexFile)) {
                    return false;
                }
            }
        }
    }
}

http_response_code(404);
require $projectRoot . '/site/404.php';
return true;
