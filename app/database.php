<?php

declare(strict_types=1);

function database_connection(): PDO
{
    static $connection;
    if ($connection instanceof PDO) {
        return $connection;
    }

    $dsn = (string) config_value('database_dsn');
    if ($dsn === '') {
        throw new RuntimeException('DB_DSN may not be empty.');
    }

    if (str_starts_with($dsn, 'sqlite:')) {
        $databasePath = substr($dsn, 7);
        if ($databasePath !== ':memory:') {
            if ($databasePath === '' || !str_starts_with($databasePath, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:[\\\\\/]/', $databasePath)) {
                throw new RuntimeException('SQLite DB_DSN must use an absolute path.');
            }
            if (path_is_within($databasePath, TAASCOR_ROOT) && config_value('environment') !== 'test') {
                throw new RuntimeException('SQLite database must be outside the public project root.');
            }
            $directory = dirname($databasePath);
            if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
                throw new RuntimeException('Unable to create the SQLite database directory.');
            }
            $canonicalDirectory = realpath($directory);
            if ($canonicalDirectory === false) {
                throw new RuntimeException('Unable to resolve the SQLite database directory.');
            }
            $canonicalDatabasePath = $canonicalDirectory . DIRECTORY_SEPARATOR . basename($databasePath);
            if (path_is_within($canonicalDatabasePath, TAASCOR_ROOT) && config_value('environment') !== 'test') {
                throw new RuntimeException('SQLite database resolved inside the public project root.');
            }
            $dsn = 'sqlite:' . $canonicalDatabasePath;
        }
    }

    $connection = new PDO(
        $dsn,
        (string) config_value('database_user', ''),
        (string) config_value('database_password', ''),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]
    );

    if ($connection->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $connection->exec('PRAGMA foreign_keys = ON');
        $connection->exec('PRAGMA busy_timeout = 5000');
    }

    return $connection;
}

function db(): PDO
{
    return database_connection();
}

function db_driver(): string
{
    return (string) db()->getAttribute(PDO::ATTR_DRIVER_NAME);
}

function db_transaction(callable $operation): mixed
{
    $connection = db();
    $connection->beginTransaction();

    try {
        $result = $operation($connection);
        $connection->commit();
        return $result;
    } catch (Throwable $exception) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        throw $exception;
    }
}

function last_inserted_id(): int
{
    return (int) db()->lastInsertId();
}
