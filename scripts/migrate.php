<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

if (getenv('APP_ENV') === false || trim((string) getenv('APP_ENV')) === '') {
    fwrite(STDERR, "Migration refused. Set APP_ENV explicitly to local, test, or production before selecting a database target.\n");
    exit(2);
}

require_once dirname(__DIR__) . '/app/bootstrap.php';

$productionConfirmed = in_array('--confirm-production-migration', $argv, true);
if (is_production() && !$productionConfirmed) {
    fwrite(STDERR, "Production migration refused. Use the reviewed release procedure and pass --confirm-production-migration only after separate approval and backup.\n");
    exit(2);
}

try {
    fwrite(STDOUT, 'Migration target: environment=' . config_value('environment') . ', driver=' . db_driver() . ".\n");
    migrate_database();
    fwrite(STDOUT, 'Database schema is current for ' . db_driver() . ".\n");
} catch (Throwable $exception) {
    fwrite(STDERR, 'Migration failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
