<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

if (getenv('APP_ENV') === false || trim((string) getenv('APP_ENV')) === '') {
    fwrite(STDERR, "Staff provisioning refused. Set APP_ENV explicitly to local or test.\n");
    exit(2);
}

require_once dirname(__DIR__) . '/app/bootstrap.php';

if (!in_array((string) config_value('environment'), ['local', 'test'], true)) {
    fwrite(STDERR, "This helper provisions staff only in local and test environments.\n");
    exit(2);
}
if (!in_array('--confirm-local-staff', $argv, true)) {
    fwrite(STDERR, "Staff provisioning requires --confirm-local-staff.\n");
    exit(2);
}

$name = trim((string) env_value('STAFF_SEED_NAME', ''));
$email = mb_strtolower(trim((string) env_value('STAFF_SEED_EMAIL', '')));
$password = (string) env_value('STAFF_SEED_PASSWORD', '');
if ($name === '' || $email === '' || $password === '') {
    fwrite(STDERR, "STAFF_SEED_NAME, STAFF_SEED_EMAIL, and STAFF_SEED_PASSWORD are required.\n");
    exit(2);
}

try {
    fwrite(STDOUT, 'Staff provisioning target: environment=' . config_value('environment') . ', driver=' . db_driver() . ".\n");
    migrate_database();
    $existing = user_by_email($email);
    if ($existing !== null) {
        if ($existing['role'] !== 'staff') {
            throw new DomainException('The configured email belongs to a non-staff account.');
        }
        fwrite(STDOUT, "Local staff account already exists.\n");
        exit(0);
    }

    $staffId = provision_staff($name, $email, $password);
    fwrite(STDOUT, "Local staff account created with ID {$staffId}. The password was not printed.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, 'Staff provisioning failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
