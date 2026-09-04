<?php

declare(strict_types=1);

const TAASCOR_ROOT = __DIR__ . '/..';

/**
 * Return a same-origin asset URL with a content-derived cache key.
 *
 * Hostinger serves public assets with a long browser cache lifetime. Content
 * hashes keep coordinated HTML/CSS/JS releases from mixing old and new files
 * without weakening that cache policy.
 */
function taascor_asset_url(string $path): string
{
    $urlPath = '/' . ltrim($path, '/');
    if (
        preg_match('#^/assets/[A-Za-z0-9][A-Za-z0-9._/-]*$#', $urlPath) !== 1
        || str_contains($urlPath, '..')
    ) {
        throw new InvalidArgumentException('Asset paths must remain inside /assets/.');
    }

    static $versions = [];
    if (!isset($versions[$urlPath])) {
        $filePath = TAASCOR_ROOT . str_replace('/', DIRECTORY_SEPARATOR, $urlPath);
        if (!is_file($filePath)) {
            throw new RuntimeException('Referenced asset does not exist: ' . $urlPath);
        }
        $hash = hash_file('sha256', $filePath);
        if (!is_string($hash)) {
            throw new RuntimeException('Unable to fingerprint asset: ' . $urlPath);
        }
        $versions[$urlPath] = substr($hash, 0, 12);
    }

    return $urlPath . '?v=' . $versions[$urlPath];
}

function env_value(string $name, ?string $default = null): ?string
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

function env_bool(string $name, bool $default = false): bool
{
    $value = env_value($name);
    if ($value === null) {
        return $default;
    }

    return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
}

function app_url_is_origin(string $url, bool $requireHttps = false): bool
{
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }
    $parts = parse_url($url);
    if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
        return false;
    }
    $scheme = mb_strtolower((string) $parts['scheme']);
    if (!in_array($scheme, ['http', 'https'], true) || ($requireHttps && $scheme !== 'https')) {
        return false;
    }
    foreach (['user', 'pass', 'query', 'fragment'] as $disallowedPart) {
        if (array_key_exists($disallowedPart, $parts)) {
            return false;
        }
    }
    $path = (string) ($parts['path'] ?? '');
    return $path === '' || $path === '/';
}

/** @return array<string, mixed> */
function app_config(): array
{
    static $config;
    if (is_array($config)) {
        return $config;
    }

    $temporaryRoot = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
        . DIRECTORY_SEPARATOR . 'taascor-recruitment';
    $defaultEnvironment = in_array(PHP_SAPI, ['cli', 'cli-server'], true) ? 'local' : 'production';
    $environment = mb_strtolower(trim(env_value('APP_ENV', $defaultEnvironment) ?? $defaultEnvironment));
    if (!in_array($environment, ['local', 'test', 'production'], true)) {
        throw new RuntimeException('APP_ENV must be one of: local, test, production.');
    }
    if (!in_array(PHP_SAPI, ['cli', 'cli-server'], true)
        && $environment !== 'production'
        && !env_bool('ALLOW_NON_PRODUCTION_WEB', false)) {
        throw new RuntimeException(
            'Non-production web execution requires the explicit ALLOW_NON_PRODUCTION_WEB gate.'
        );
    }
    $configuredDsn = env_value('DB_DSN');
    $configuredDatabaseUser = env_value('DB_USER', '') ?? '';
    $configuredDatabasePassword = env_value('DB_PASSWORD', '') ?? '';
    $configuredUploadDirectory = env_value('UPLOAD_DIR');
    $configuredKey = env_value('APP_KEY', '') ?? '';
    $configuredUrl = env_value('APP_URL');
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
    $cookieSecure = env_bool('SESSION_COOKIE_SECURE', $isHttps);
    $idleTimeout = max(300, (int) (env_value('SESSION_IDLE_SECONDS', '1800') ?? '1800'));
    $absoluteTimeout = max($idleTimeout, (int) (env_value('SESSION_ABSOLUTE_SECONDS', '28800') ?? '28800'));
    $trustedProxyHttps = env_bool('TRUSTED_PROXY_HTTPS', false);
    $trustedProxyIps = array_values(array_filter(array_map(
        static fn (string $value): string => trim($value),
        explode(',', env_value('TRUSTED_PROXY_IPS', '') ?? '')
    )));
    foreach ($trustedProxyIps as $trustedProxyIp) {
        if (filter_var($trustedProxyIp, FILTER_VALIDATE_IP) === false) {
            throw new RuntimeException('TRUSTED_PROXY_IPS must contain only comma-separated IP addresses.');
        }
    }
    if ($trustedProxyHttps && $trustedProxyIps === []) {
        throw new RuntimeException('TRUSTED_PROXY_HTTPS requires at least one exact TRUSTED_PROXY_IPS address.');
    }

    if ($environment === 'production') {
        if (strlen($configuredKey) < 32) {
            throw new RuntimeException('Production APP_KEY must be a non-empty secret of at least 32 characters.');
        }
        if ($configuredDsn === null) {
            throw new RuntimeException('Production DB_DSN must be explicitly configured.');
        }
        if (!str_starts_with(mb_strtolower($configuredDsn), 'mysql:')) {
            throw new RuntimeException('Production DB_DSN must use the supported MySQL driver.');
        }
        if (!str_contains(mb_strtolower($configuredDsn), 'charset=utf8mb4')) {
            throw new RuntimeException('Production DB_DSN must explicitly use charset=utf8mb4.');
        }
        if (trim($configuredDatabaseUser) === '' || $configuredDatabasePassword === '') {
            throw new RuntimeException('Production DB_USER and DB_PASSWORD must be explicitly configured.');
        }
        if ($configuredUploadDirectory === null) {
            throw new RuntimeException('Production UPLOAD_DIR must be explicitly configured outside the document root.');
        }
        if (!$cookieSecure) {
            throw new RuntimeException('Production SESSION_COOKIE_SECURE must be true.');
        }
        if ($configuredUrl === null || !app_url_is_origin($configuredUrl, true)) {
            throw new RuntimeException(
                'Production APP_URL must be an explicit HTTPS origin without credentials, path, query, or fragment.'
            );
        }
        if (env_bool('APP_DEBUG', false)) {
            throw new RuntimeException('Production APP_DEBUG must be false.');
        }
    }

    $config = [
        'environment' => $environment,
        'url' => $configuredUrl ?? 'http://127.0.0.1:8080',
        'key' => $configuredKey,
        'timezone' => env_value('APP_TIMEZONE', 'Asia/Manila'),
        'debug' => env_bool('APP_DEBUG', false),
        'database_dsn' => $configuredDsn
            ?? 'sqlite:' . $temporaryRoot . DIRECTORY_SEPARATOR . 'taascor.sqlite',
        'database_user' => $configuredDatabaseUser,
        'database_password' => $configuredDatabasePassword,
        'session_cookie_name' => env_value('SESSION_COOKIE_NAME', 'taascor_recruitment'),
        'session_cookie_secure' => $cookieSecure,
        'session_idle_seconds' => $idleTimeout,
        'session_absolute_seconds' => $absoluteTimeout,
        'upload_dir' => $configuredUploadDirectory
            ?? $temporaryRoot . DIRECTORY_SEPARATOR . 'uploads',
        'max_upload_bytes' => max(1, (int) (env_value('MAX_UPLOAD_BYTES', '5242880') ?? '5242880')),
        'max_private_storage_bytes' => max(
            1,
            (int) (env_value('MAX_PRIVATE_STORAGE_BYTES', '1073741824') ?? '1073741824')
        ),
        'resume_retention_days' => max(0, (int) (env_value('RESUME_RETENTION_DAYS', '0') ?? '0')),
        'resume_upload_enabled' => env_bool('RESUME_UPLOAD_ENABLED', false),
        'staff_workflows_enabled' => env_bool('STAFF_WORKFLOWS_ENABLED', false),
        'job_publication_enabled' => env_bool('JOB_PUBLICATION_ENABLED', false),
        'trusted_proxy_https' => $trustedProxyHttps,
        'trusted_proxy_ips' => $trustedProxyIps,
        'privacy_notice_version' => env_value('PRIVACY_NOTICE_VERSION', 'draft-2026-09-01'),
        'privacy_notice_url' => env_value('PRIVACY_NOTICE_URL', '/apply/privacy.php'),
        'applicant_collection_enabled' => env_bool('APPLICANT_COLLECTION_ENABLED', false),
        'workforce_privacy_notice_version' => env_value(
            'WORKFORCE_PRIVACY_NOTICE_VERSION',
            'draft-workforce-2026-09-01'
        ),
        'workforce_privacy_notice_url' => env_value(
            'WORKFORCE_PRIVACY_NOTICE_URL',
            '/legal/privacy/'
        ),
        'workforce_collection_enabled' => env_bool('WORKFORCE_COLLECTION_ENABLED', false),
    ];

    return $config;
}

function config_value(string $key, mixed $default = null): mixed
{
    $config = app_config();
    return $config[$key] ?? $default;
}

function is_production(): bool
{
    return config_value('environment') === 'production';
}

function now_utc(): string
{
    return gmdate('Y-m-d H:i:s');
}

function utc_minutes_ago(int $minutes): string
{
    return gmdate('Y-m-d H:i:s', time() - ($minutes * 60));
}

function privacy_notice_is_draft(string $scope): bool
{
    $key = match ($scope) {
        'applicant' => 'privacy_notice_version',
        'workforce' => 'workforce_privacy_notice_version',
        default => throw new InvalidArgumentException('Unknown privacy notice scope.'),
    };
    $version = mb_strtolower(trim((string) config_value($key, '')));
    return $version === '' || str_starts_with($version, 'draft');
}

function privacy_collection_is_enabled(string $scope): bool
{
    if (config_value('environment') === 'test') {
        return true;
    }
    $enableKey = match ($scope) {
        'applicant' => 'applicant_collection_enabled',
        'workforce' => 'workforce_collection_enabled',
        default => throw new InvalidArgumentException('Unknown privacy notice scope.'),
    };
    if (!(bool) config_value($enableKey, false)) {
        return false;
    }
    if (is_production() && !production_capability_is_qualified($enableKey)) {
        return false;
    }
    return !is_production() || !privacy_notice_is_draft($scope);
}

function require_approved_privacy_notice(string $scope): void
{
    if (!privacy_collection_is_enabled($scope)) {
        throw new DomainException('This collection route is unavailable until its approved privacy notice is published.');
    }
}

function resume_upload_is_enabled(): bool
{
    if (config_value('environment') === 'test') {
        return true;
    }
    return (bool) config_value('resume_upload_enabled', false)
        && (!is_production() || production_capability_is_qualified('resume_upload_enabled'));
}

function staff_workflows_are_enabled(): bool
{
    if (config_value('environment') === 'test') {
        return true;
    }
    return (bool) config_value('staff_workflows_enabled', false)
        && (!is_production() || production_capability_is_qualified('staff_workflows_enabled'));
}

/**
 * Production capabilities that still depend on external approval or services
 * stay code-locked. Enabling an environment flag alone must never bypass the
 * corresponding release gate. A future reviewed release may change one value
 * only alongside the control implementation and closure evidence.
 */
function production_capability_is_qualified(string $capability): bool
{
    $qualified = [
        'applicant_collection_enabled' => false,
        'workforce_collection_enabled' => false,
        'resume_upload_enabled' => false,
        'staff_workflows_enabled' => false,
    ];
    if (!array_key_exists($capability, $qualified)) {
        throw new InvalidArgumentException('Unknown production capability gate.');
    }
    return $qualified[$capability];
}

function job_publication_is_enabled(): bool
{
    return config_value('environment') === 'test' || (bool) config_value('job_publication_enabled', false);
}

function business_today(): string
{
    $timezone = new DateTimeZone((string) config_value('timezone', 'Asia/Manila'));
    return (new DateTimeImmutable('now', $timezone))->format('Y-m-d');
}
