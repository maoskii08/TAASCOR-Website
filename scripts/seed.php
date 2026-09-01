<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

if (getenv('APP_ENV') === false || trim((string) getenv('APP_ENV')) === '') {
    fwrite(STDERR, "Synthetic seed refused. Set APP_ENV explicitly to local or test.\n");
    exit(2);
}

require_once dirname(__DIR__) . '/app/bootstrap.php';

if (!in_array((string) config_value('environment'), ['local', 'test'], true)) {
    fwrite(STDERR, "Synthetic job seeding is restricted to local and test environments.\n");
    exit(2);
}

try {
    fwrite(STDOUT, 'Synthetic seed target: environment=' . config_value('environment') . ', driver=' . db_driver() . ".\n");
    migrate_database();
    $created = seed_demo_jobs();
    fwrite(STDOUT, "Synthetic demonstration jobs created: {$created}.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, 'Synthetic seed failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
