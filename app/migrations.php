<?php

declare(strict_types=1);

function migrate_database(): void
{
    $driver = db_driver();
    preflight_database_migration($driver);
    $schemaFile = match ($driver) {
        'sqlite' => TAASCOR_ROOT . '/database/schema.sqlite.sql',
        'mysql' => TAASCOR_ROOT . '/database/schema.mysql.sql',
        default => throw new RuntimeException('Unsupported PDO driver: ' . $driver),
    };
    $schema = file_get_contents($schemaFile);
    if ($schema === false) {
        throw new RuntimeException('Unable to read database schema.');
    }

    if ($driver === 'sqlite') {
        db()->exec($schema);
    } else {
        $statements = preg_split('/;\s*(?:\r?\n|$)/', $schema, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($statements ?: [] as $statement) {
            if (trim($statement) !== '') {
                db()->exec($statement);
            }
        }
    }

    apply_compatibility_migrations($driver);
    verify_database_migration($driver);
    record_database_migration($driver);
}

function migration_table_exists(string $table, string $driver): bool
{
    if (!preg_match('/^[a-z_]+$/', $table)) {
        throw new InvalidArgumentException('Unsupported migration table name.');
    }
    if ($driver === 'sqlite') {
        $statement = db()->prepare(
            "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = :table"
        );
    } else {
        $statement = db()->prepare(
            'SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = :table'
        );
    }
    $statement->execute(['table' => $table]);
    return (int) $statement->fetchColumn() > 0;
}

function migration_column_exists(string $table, string $column, string $driver): bool
{
    if ($driver === 'sqlite') {
        $rows = db()->query('PRAGMA table_info(' . $table . ')')->fetchAll();
        foreach ($rows as $row) {
            if ((string) $row['name'] === $column) {
                return true;
            }
        }
        return false;
    }
    $statement = db()->prepare(
        'SELECT COUNT(*) FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = :table AND column_name = :column'
    );
    $statement->execute(['table' => $table, 'column' => $column]);
    return (int) $statement->fetchColumn() > 0;
}

function migration_snapshot_content_hash(array $snapshot): string
{
    return hash('sha256', json_encode(
        migration_job_snapshot_payload($snapshot),
        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    ));
}

function validate_persisted_application_snapshots(string $driver): void
{
    if (!migration_table_exists('applications', $driver)
        || !migration_column_exists('applications', 'job_snapshot_json', $driver)) {
        return;
    }

    $statement = db()->query('SELECT id, job_snapshot_json FROM applications ORDER BY id');
    while ($row = $statement->fetch()) {
        $raw = $row['job_snapshot_json'] ?? null;
        try {
            $snapshot = is_string($raw) && trim($raw) !== ''
                ? json_decode($raw, true, 32, JSON_THROW_ON_ERROR)
                : null;
        } catch (JsonException) {
            $snapshot = null;
        }
        $storedHash = is_array($snapshot) ? ($snapshot['content_sha256'] ?? null) : null;
        if (!is_string($storedHash)
            || preg_match('/\A[a-f0-9]{64}\z/', $storedHash) !== 1
            || !hash_equals(migration_snapshot_content_hash($snapshot), $storedHash)) {
            throw new RuntimeException(
                'Migration integrity verification failed: an application snapshot requires approved reconciliation.'
            );
        }
    }
}

function validate_persisted_document_hashes(string $driver): void
{
    if (!migration_table_exists('application_documents', $driver)
        || !migration_column_exists('application_documents', 'content_sha256', $driver)) {
        return;
    }

    $statement = db()->query('SELECT id, content_sha256 FROM application_documents ORDER BY id');
    while ($row = $statement->fetch()) {
        $hash = $row['content_sha256'] ?? null;
        if (!is_string($hash) || preg_match('/\A[a-f0-9]{64}\z/', $hash) !== 1) {
            throw new RuntimeException(
                'Migration integrity verification failed: a document hash requires approved reconciliation.'
            );
        }
    }
}

function preflight_database_migration(string $driver): void
{
    if (!in_array($driver, ['sqlite', 'mysql'], true)) {
        throw new RuntimeException('Unsupported migration preflight driver.');
    }
    if (is_production() && migration_table_exists('jobs', $driver)) {
        $publishedJobs = (int) db()->query(
            "SELECT COUNT(*) FROM jobs WHERE status = 'published'"
        )->fetchColumn();
        if ($publishedJobs > 0 && !job_publication_is_enabled()) {
            throw new RuntimeException(
                'Production migration stopped before DDL: published jobs exist while the publication gate is disabled.'
            );
        }
        if (migration_column_exists('jobs', 'is_demo', $driver)) {
            $demoJobs = (int) db()->query('SELECT COUNT(*) FROM jobs WHERE is_demo = 1')->fetchColumn();
            if ($demoJobs > 0) {
                throw new RuntimeException(
                    'Production migration stopped before DDL: demonstration jobs must not exist on a production target.'
                );
            }
        }
    }
    if (migration_table_exists('applications', $driver)) {
        $applicationCount = (int) db()->query('SELECT COUNT(*) FROM applications')->fetchColumn();
        $hasSnapshotColumn = migration_column_exists('applications', 'job_snapshot_json', $driver);
        if (is_production() && $applicationCount > 0 && !$hasSnapshotColumn) {
            throw new RuntimeException(
                'Production migration stopped before DDL: existing applications require approved historical snapshot reconciliation.'
            );
        }
        if (is_production() && $hasSnapshotColumn) {
            $missing = (int) db()->query(
                "SELECT COUNT(*) FROM applications
                 WHERE job_snapshot_json IS NULL OR TRIM(job_snapshot_json) = ''"
            )->fetchColumn();
            if ($missing > 0) {
                throw new RuntimeException(
                    'Production migration stopped before DDL: existing applications require approved historical snapshot reconciliation.'
                );
            }
            validate_persisted_application_snapshots($driver);
        }
    }

    if (!migration_table_exists('application_documents', $driver)) {
        return;
    }
    $duplicate = db()->query(
        'SELECT application_id, COUNT(*) AS document_count
         FROM application_documents GROUP BY application_id HAVING COUNT(*) > 1 LIMIT 1'
    )->fetch();
    if ($duplicate) {
        throw new RuntimeException(
            'Migration stopped before DDL: duplicate application documents require approved reconciliation.'
        );
    }
    $documentCount = (int) db()->query('SELECT COUNT(*) FROM application_documents')->fetchColumn();
    $hasContentHash = migration_column_exists('application_documents', 'content_sha256', $driver);
    if (is_production() && $documentCount > 0 && !$hasContentHash) {
        throw new RuntimeException(
            'Production migration stopped before DDL: existing documents require approved content-hash reconciliation.'
        );
    }
    if (is_production() && $hasContentHash) {
        $missing = (int) db()->query(
            "SELECT COUNT(*) FROM application_documents
             WHERE content_sha256 IS NULL OR content_sha256 = ''"
        )->fetchColumn();
        if ($missing > 0) {
            throw new RuntimeException(
                'Production migration stopped before DDL: existing documents require approved content-hash reconciliation.'
            );
        }
        validate_persisted_document_hashes($driver);
    }
}

function verify_database_migration(string $driver): void
{
    foreach (['jobs', 'job_change_history', 'applications', 'application_documents', 'application_job_snapshot_history', 'private_storage_usage'] as $table) {
        if (!migration_table_exists($table, $driver)) {
            throw new RuntimeException('Migration verification failed: required table is missing.');
        }
    }
    foreach (['content_sha256', 'scanned_at', 'scan_result', 'retention_expires_at'] as $column) {
        if (!migration_column_exists('application_documents', $column, $driver)) {
            throw new RuntimeException('Migration verification failed: a document-integrity column is missing.');
        }
    }
    ensure_unique_document_per_application();
    validate_persisted_application_snapshots($driver);
    validate_persisted_document_hashes($driver);
}

function record_database_migration(string $driver): void
{
    $parameters = [
        'migration_key' => '2026-09-02-integrated-experience-baseline',
        'applied_at' => now_utc(),
        'details_json' => json_encode(
            ['driver' => $driver, 'verified' => true],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        ),
    ];
    $sql = $driver === 'sqlite'
        ? 'INSERT INTO schema_migrations (migration_key, applied_at, details_json)
           VALUES (:migration_key, :applied_at, :details_json)
           ON CONFLICT(migration_key) DO UPDATE SET applied_at = excluded.applied_at, details_json = excluded.details_json'
        : 'INSERT INTO schema_migrations (migration_key, applied_at, details_json)
           VALUES (:migration_key, :applied_at, :details_json)
           ON DUPLICATE KEY UPDATE applied_at = VALUES(applied_at), details_json = VALUES(details_json)';
    db()->prepare($sql)->execute($parameters);
}

/** @return list<string> */
function database_table_columns(string $table): array
{
    $allowedTables = ['users', 'jobs', 'applications', 'application_documents'];
    if (!in_array($table, $allowedTables, true)) {
        throw new InvalidArgumentException('Unsupported migration table.');
    }

    if (db_driver() === 'sqlite') {
        $rows = db()->query('PRAGMA table_info(' . $table . ')')->fetchAll();
        return array_values(array_map(static fn (array $row): string => (string) $row['name'], $rows));
    }

    $rows = db()->query('SHOW COLUMNS FROM `' . $table . '`')->fetchAll();
    return array_values(array_map(static fn (array $row): string => (string) $row['Field'], $rows));
}

function ensure_database_column(
    string $table,
    string $column,
    string $sqliteDefinition,
    string $mysqlDefinition
): void {
    if (in_array($column, database_table_columns($table), true)) {
        return;
    }

    $allowed = [
        'users.session_version',
        'jobs.function_area',
        'jobs.shift_pattern',
        'jobs.requirements',
        'jobs.openings',
        'jobs.closing_date',
        'jobs.version',
        'applications.job_snapshot_json',
        'application_documents.content_sha256',
        'application_documents.scanned_at',
        'application_documents.scan_result',
        'application_documents.retention_expires_at',
    ];
    if (!in_array($table . '.' . $column, $allowed, true)) {
        throw new InvalidArgumentException('Unsupported compatibility migration.');
    }
    $definition = db_driver() === 'sqlite' ? $sqliteDefinition : $mysqlDefinition;
    db()->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
}

function apply_compatibility_migrations(string $driver): void
{
    if (!in_array($driver, ['sqlite', 'mysql'], true)) {
        throw new RuntimeException('Unsupported compatibility migration driver.');
    }

    ensure_database_column('users', 'session_version', 'INTEGER NOT NULL DEFAULT 1', 'INT UNSIGNED NOT NULL DEFAULT 1');
    ensure_database_column('jobs', 'function_area', "TEXT NOT NULL DEFAULT ''", "VARCHAR(120) NOT NULL DEFAULT ''");
    ensure_database_column('jobs', 'shift_pattern', "TEXT NOT NULL DEFAULT ''", "VARCHAR(120) NOT NULL DEFAULT ''");
    ensure_database_column('jobs', 'requirements', "TEXT NOT NULL DEFAULT ''", 'TEXT NULL');
    ensure_database_column('jobs', 'openings', 'INTEGER NULL', 'INT UNSIGNED NULL');
    ensure_database_column('jobs', 'closing_date', 'TEXT NULL', 'DATE NULL');
    ensure_database_column('jobs', 'version', 'INTEGER NOT NULL DEFAULT 1', 'INT UNSIGNED NOT NULL DEFAULT 1');
    ensure_database_column('applications', 'job_snapshot_json', 'TEXT NULL', 'TEXT NULL');
    ensure_database_column('application_documents', 'content_sha256', 'TEXT NULL', 'CHAR(64) NULL');
    ensure_database_column('application_documents', 'scanned_at', 'TEXT NULL', 'DATETIME NULL');
    ensure_database_column('application_documents', 'scan_result', 'TEXT NULL', 'VARCHAR(100) NULL');
    ensure_database_column('application_documents', 'retention_expires_at', 'TEXT NULL', 'DATETIME NULL');

    reconcile_legacy_application_snapshots();
    ensure_unique_document_per_application();
    reconcile_private_storage_usage($driver);
}

/** @param array<string, mixed> $job @return array<string, mixed> */
function migration_job_snapshot_payload(array $job): array
{
    $snapshot = [];
    foreach ([
        'id', 'slug', 'title', 'company', 'location', 'employment_type', 'function_area',
        'shift_pattern', 'summary', 'description', 'requirements', 'openings', 'closing_date', 'is_demo',
    ] as $field) {
        $snapshot[$field] = $job[$field] ?? null;
    }
    return $snapshot;
}

function reconcile_legacy_application_snapshots(): void
{
    $statement = db()->query(
        "SELECT a.id AS application_id,
                j.id AS id, j.slug, j.title, j.company, j.location, j.employment_type,
                j.function_area, j.shift_pattern, j.summary, j.description, j.requirements,
                j.openings, j.closing_date, j.is_demo
         FROM applications a
         INNER JOIN jobs j ON j.id = a.job_id
         WHERE a.job_snapshot_json IS NULL OR TRIM(a.job_snapshot_json) = ''"
    );
    $rows = $statement->fetchAll();
    if (!$rows) {
        return;
    }
    if (is_production()) {
        throw new RuntimeException(
            'Production migration stopped: existing applications need an approved historical job-snapshot reconciliation.'
        );
    }

    $update = db()->prepare('UPDATE applications SET job_snapshot_json = :snapshot WHERE id = :id');
    $history = db()->prepare(
        'INSERT INTO application_job_snapshot_history
            (application_id, event_type, previous_snapshot_json, accepted_snapshot_json,
             previous_content_sha256, accepted_content_sha256, applicant_reviewed_changes,
             acknowledged_at, actor_user_id, created_at)
         VALUES
            (:application_id, :event_type, NULL, :accepted_snapshot_json,
             NULL, :accepted_content_sha256, 0, NULL, NULL, :created_at)'
    );
    foreach ($rows as $row) {
        $payload = migration_job_snapshot_payload($row);
        $payload['content_sha256'] = hash('sha256', json_encode(
            $payload,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ));
        $payload['snapshotted_at'] = now_utc();
        $payload['backfill_source'] = 'current_job_at_local_migration';
        $payload['historical_reconciliation_required'] = true;
        $snapshotJson = json_encode(
            $payload,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        $update->execute([
            'snapshot' => $snapshotJson,
            'id' => (int) $row['application_id'],
        ]);
        $history->execute([
            'application_id' => (int) $row['application_id'],
            'event_type' => 'legacy_snapshot_backfilled',
            'accepted_snapshot_json' => $snapshotJson,
            'accepted_content_sha256' => (string) $payload['content_sha256'],
            'created_at' => now_utc(),
        ]);
    }
}

function ensure_unique_document_per_application(): void
{
    $duplicate = db()->query(
        'SELECT application_id, COUNT(*) AS document_count
         FROM application_documents GROUP BY application_id HAVING COUNT(*) > 1 LIMIT 1'
    )->fetch();
    if ($duplicate) {
        throw new RuntimeException(
            'Migration stopped: an application has multiple documents and needs approved reconciliation before uniqueness can be enforced.'
        );
    }

    if (db_driver() === 'sqlite') {
        db()->exec(
            'CREATE UNIQUE INDEX IF NOT EXISTS uq_document_application ON application_documents (application_id)'
        );
        assert_unique_document_index_shape('sqlite');
        return;
    }

    $indexes = db()->query(
        "SHOW INDEX FROM application_documents WHERE Key_name = 'uq_document_application'"
    )->fetchAll();
    if ($indexes === []) {
        db()->exec(
            'ALTER TABLE application_documents ADD UNIQUE INDEX uq_document_application (application_id)'
        );
    }
    assert_unique_document_index_shape('mysql');
}

function assert_unique_document_index_shape(string $driver): void
{
    if ($driver === 'sqlite') {
        $matchingIndex = null;
        foreach (db()->query("PRAGMA index_list('application_documents')")->fetchAll() as $index) {
            if ((string) ($index['name'] ?? '') === 'uq_document_application') {
                $matchingIndex = $index;
                break;
            }
        }
        if ($matchingIndex === null || (int) ($matchingIndex['unique'] ?? 0) !== 1) {
            throw new RuntimeException(
                'Migration verification failed: the application-document uniqueness index is missing or non-unique.'
            );
        }
        $columns = db()->query("PRAGMA index_info('uq_document_application')")->fetchAll();
        if (count($columns) !== 1
            || (int) ($columns[0]['seqno'] ?? -1) !== 0
            || (string) ($columns[0]['name'] ?? '') !== 'application_id') {
            throw new RuntimeException(
                'Migration verification failed: the application-document uniqueness index has an unexpected column shape.'
            );
        }
        return;
    }

    if ($driver !== 'mysql') {
        throw new RuntimeException('Migration verification failed: unsupported index-verification driver.');
    }
    $indexes = db()->query(
        "SHOW INDEX FROM application_documents WHERE Key_name = 'uq_document_application'"
    )->fetchAll();
    if (count($indexes) !== 1) {
        throw new RuntimeException(
            'Migration verification failed: the application-document uniqueness index has an unexpected column shape.'
        );
    }
    $index = $indexes[0];
    $nonUnique = $index['Non_unique'] ?? $index['non_unique'] ?? null;
    $sequence = $index['Seq_in_index'] ?? $index['seq_in_index'] ?? null;
    $column = $index['Column_name'] ?? $index['column_name'] ?? null;
    if ((int) $nonUnique !== 0 || (int) $sequence !== 1 || (string) $column !== 'application_id') {
        throw new RuntimeException(
            'Migration verification failed: the application-document uniqueness index is not a unique application_id key.'
        );
    }
}

function reconcile_private_storage_usage(string $driver): void
{
    if ($driver === 'sqlite') {
        $statement = db()->prepare(
            'INSERT OR IGNORE INTO private_storage_usage (scope, used_bytes, updated_at)
             VALUES (:scope, 0, :updated_at)'
        );
    } else {
        $statement = db()->prepare(
            'INSERT IGNORE INTO private_storage_usage (scope, used_bytes, updated_at)
             VALUES (:scope, 0, :updated_at)'
        );
    }
    $statement->execute(['scope' => 'application_documents', 'updated_at' => now_utc()]);
    db()->prepare(
        'UPDATE private_storage_usage
         SET used_bytes = (SELECT COALESCE(SUM(size_bytes), 0) FROM application_documents), updated_at = :updated_at
         WHERE scope = :scope'
    )->execute(['updated_at' => now_utc(), 'scope' => 'application_documents']);

    $unverified = (int) db()->query(
        "SELECT COUNT(*) FROM application_documents
         WHERE content_sha256 IS NULL OR content_sha256 = ''"
    )->fetchColumn();
    if ($unverified > 0 && is_production()) {
        throw new RuntimeException(
            'Production migration stopped: existing documents require an approved content-hash reconciliation.'
        );
    }
}

function seed_demo_jobs(): int
{
    $jobs = [
        [
            'slug' => 'sample-operations-associate',
            'title' => 'Sample Operations Associate',
            'company' => 'TAASCOR Demo Workforce Partner',
            'location' => 'CALABARZON',
            'employment_type' => 'Full-time',
            'function_area' => 'Operations',
            'shift_pattern' => 'Schedule confirmed during role review',
            'summary' => 'Synthetic vacancy used to demonstrate the local application workflow.',
            'description' => 'Support a sample client operation while following documented safety, attendance, and quality standards. This is demonstration data only.',
            'requirements' => 'Synthetic demonstration requirement: review the role context and confirm availability during the local test flow.',
            'openings' => 3,
            'closing_date' => null,
        ],
        [
            'slug' => 'sample-warehouse-coordinator',
            'title' => 'Sample Warehouse Coordinator',
            'company' => 'TAASCOR Demo Workforce Partner',
            'location' => 'Laguna',
            'employment_type' => 'Full-time',
            'function_area' => 'Logistics',
            'shift_pattern' => 'Sample rotating schedule',
            'summary' => 'Synthetic warehouse coordination role for local validation.',
            'description' => 'Coordinate a fictional warehouse shift, track handoffs, and escalate exceptions. This is demonstration data only.',
            'requirements' => 'Synthetic demonstration requirement: familiarity with organized shift handoffs.',
            'openings' => 2,
            'closing_date' => null,
        ],
        [
            'slug' => 'sample-office-support-specialist',
            'title' => 'Sample Office Support Specialist',
            'company' => 'TAASCOR Demo Workforce Partner',
            'location' => 'Cavite',
            'employment_type' => 'Full-time',
            'function_area' => 'Administrative support',
            'shift_pattern' => 'Sample day schedule',
            'summary' => 'Synthetic office-support vacancy for local validation.',
            'description' => 'Provide administrative support in a fictional client environment. This is demonstration data only.',
            'requirements' => 'Synthetic demonstration requirement: clear written communication and document organization.',
            'openings' => 1,
            'closing_date' => null,
        ],
    ];

    $exists = db()->prepare('SELECT id FROM jobs WHERE slug = :slug LIMIT 1');
    $insert = db()->prepare(
        'INSERT INTO jobs
            (slug, title, company, location, employment_type, function_area, shift_pattern,
             summary, description, requirements, openings, closing_date,
             status, is_demo, published_at, created_at, updated_at)
         VALUES
            (:slug, :title, :company, :location, :employment_type, :function_area, :shift_pattern,
             :summary, :description, :requirements, :openings, :closing_date,
             :status, 1, :published_at, :created_at, :updated_at)'
    );
    $created = 0;
    foreach ($jobs as $job) {
        $exists->execute(['slug' => $job['slug']]);
        if ($exists->fetchColumn()) {
            continue;
        }
        $timestamp = now_utc();
        $insert->execute($job + [
            'status' => 'published',
            'published_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
        $created++;
    }

    return $created;
}
