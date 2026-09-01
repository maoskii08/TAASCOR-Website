import assert from 'node:assert/strict';
import { mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import path from 'node:path';
import { spawnSync } from 'node:child_process';
import test from 'node:test';
import { phpRuntimeArgs } from '../support/php.mjs';
import { projectRoot } from '../support/project.mjs';

function runIsolatedProbe(php, databasePath, overrides = {}) {
  const environment = {
    ...process.env,
    APP_ENV: 'local',
    APP_URL: 'http://127.0.0.1:4177',
    DB_DSN: `sqlite:${databasePath}`,
    JOB_PUBLICATION_ENABLED: 'false',
    ...overrides,
  };
  return spawnSync(
    'php',
    [
      ...phpRuntimeArgs(),
      '-r',
      php,
      path.join(projectRoot, 'app', 'config.php'),
      path.join(projectRoot, 'app', 'security.php'),
      path.join(projectRoot, 'app', 'database.php'),
      path.join(projectRoot, 'app', 'migrations.php'),
      path.join(projectRoot, 'app', 'recruitment.php'),
    ],
    {
      cwd: projectRoot,
      env: environment,
      encoding: 'utf8',
      windowsHide: true,
    },
  );
}

test('public job reads fail closed when publication is disabled', () => {
  const temporaryRoot = mkdtempSync(path.join(tmpdir(), 'taascor-publication-gate-'));
  const databasePath = path.join(temporaryRoot, 'gate.sqlite');
  try {
    const php = String.raw`
      require $argv[1];
      require $argv[2];
      require $argv[3];
      require $argv[5];
      db()->exec("CREATE TABLE jobs (
        id INTEGER PRIMARY KEY, slug TEXT, title TEXT, company TEXT, location TEXT,
        employment_type TEXT, function_area TEXT, shift_pattern TEXT, summary TEXT,
        description TEXT, requirements TEXT, openings INTEGER, closing_date TEXT,
        status TEXT, is_demo INTEGER, published_at TEXT, created_at TEXT
      )");
      db()->exec("INSERT INTO jobs VALUES (
        1, 'synthetic-hidden-role', 'Synthetic Hidden Role', 'Synthetic Organization',
        'Synthetic Site', 'Full-time', 'Operations', 'Synthetic shift',
        'Synthetic summary', 'Synthetic description', 'Synthetic requirements', 1,
        NULL, 'published', 0, '2026-09-02 00:00:00', '2026-09-02 00:00:00'
      )");
      echo json_encode([
        'list' => list_published_jobs(),
        'detail' => find_published_job_by_slug('synthetic-hidden-role'),
        'related' => related_published_jobs(2, 'Operations'),
      ], JSON_THROW_ON_ERROR);
    `;
    const result = runIsolatedProbe(php, databasePath);
    assert.equal(result.status, 0, result.stderr);
    assert.deepEqual(JSON.parse(result.stdout), { list: [], detail: null, related: [] });
  } finally {
    rmSync(temporaryRoot, { recursive: true, force: true });
  }
});

test('migration integrity rejects malformed snapshots, invalid hashes, and a misleading index', () => {
  const temporaryRoot = mkdtempSync(path.join(tmpdir(), 'taascor-migration-contract-'));
  const databasePath = path.join(temporaryRoot, 'integrity.sqlite');
  try {
    const php = String.raw`
      require $argv[1];
      require $argv[2];
      require $argv[3];
      require $argv[4];

      db()->exec('CREATE TABLE applications (id INTEGER PRIMARY KEY, job_snapshot_json TEXT)');
      db()->exec("INSERT INTO applications VALUES (1, '{not-json')");
      $malformedSnapshotRejected = false;
      try { validate_persisted_application_snapshots('sqlite'); }
      catch (RuntimeException) { $malformedSnapshotRejected = true; }

      $mismatch = ['id' => 1, 'slug' => 'synthetic-role', 'content_sha256' => str_repeat('0', 64)];
      $update = db()->prepare('UPDATE applications SET job_snapshot_json = :snapshot WHERE id = 1');
      $update->execute(['snapshot' => json_encode($mismatch, JSON_THROW_ON_ERROR)]);
      $mismatchedSnapshotRejected = false;
      try { validate_persisted_application_snapshots('sqlite'); }
      catch (RuntimeException) { $mismatchedSnapshotRejected = true; }

      db()->exec('CREATE TABLE application_documents (
        id INTEGER PRIMARY KEY, application_id INTEGER, content_sha256 TEXT
      )');
      db()->exec("INSERT INTO application_documents VALUES (1, 1, 'not-a-sha256')");
      $invalidDocumentHashRejected = false;
      try { validate_persisted_document_hashes('sqlite'); }
      catch (RuntimeException) { $invalidDocumentHashRejected = true; }

      db()->exec('CREATE INDEX uq_document_application ON application_documents (application_id)');
      $misleadingIndexRejected = false;
      try { ensure_unique_document_per_application(); }
      catch (RuntimeException) { $misleadingIndexRejected = true; }

      echo json_encode([
        'malformed_snapshot' => $malformedSnapshotRejected,
        'mismatched_snapshot' => $mismatchedSnapshotRejected,
        'invalid_document_hash' => $invalidDocumentHashRejected,
        'misleading_index' => $misleadingIndexRejected,
      ], JSON_THROW_ON_ERROR);
    `;
    const result = runIsolatedProbe(php, databasePath);
    assert.equal(result.status, 0, result.stderr);
    assert.deepEqual(JSON.parse(result.stdout), {
      malformed_snapshot: true,
      mismatched_snapshot: true,
      invalid_document_hash: true,
      misleading_index: true,
    });
  } finally {
    rmSync(temporaryRoot, { recursive: true, force: true });
  }
});

test('Apache clean-job rewrite keeps the path slug authoritative', async () => {
  const htaccess = await import('node:fs/promises').then(({ readFile }) =>
    readFile(path.join(projectRoot, '.htaccess'), 'utf8'));
  const detailRule = htaccess.split(/\r?\n/)
    .find((line) => line.includes('^jobs/([a-z0-9]'));
  assert.ok(detailRule, 'The clean job-detail rewrite must exist');
  assert.match(detailRule, /careers\/job\.php\s+\[L\]/);
  assert.doesNotMatch(detailRule, /\?job=/i, 'The path slug must be parsed from REQUEST_URI');
  assert.doesNotMatch(detailRule, /QSA/i, 'External job query values must not override the path slug');
});
