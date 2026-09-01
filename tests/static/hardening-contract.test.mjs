import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { tmpdir } from 'node:os';
import path from 'node:path';
import test from 'node:test';
import { phpRuntimeArgs } from '../support/php.mjs';
import { projectRoot } from '../support/project.mjs';

function cleanEnvironment(...removedNames) {
  const removed = new Set(removedNames.map((name) => name.toUpperCase()));
  return Object.fromEntries(
    Object.entries(process.env).filter(([name]) => !removed.has(name.toUpperCase())),
  );
}

function runPhpFile(relativePath, environment) {
  return spawnSync(
    'php',
    [...phpRuntimeArgs(), path.join(projectRoot, relativePath)],
    {
      cwd: projectRoot,
      env: environment,
      encoding: 'utf8',
      windowsHide: true,
    },
  );
}

function productionEnvironment(overrides = {}) {
  const removed = [
    'APP_ENV', 'APP_KEY', 'APP_URL', 'APP_DEBUG', 'DB_DSN', 'DB_USER', 'DB_PASSWORD',
    'UPLOAD_DIR', 'SESSION_COOKIE_SECURE', 'APPLICANT_COLLECTION_ENABLED',
    'WORKFORCE_COLLECTION_ENABLED', 'RESUME_UPLOAD_ENABLED', 'PRIVACY_NOTICE_VERSION',
    'WORKFORCE_PRIVACY_NOTICE_VERSION',
  ];
  return {
    ...cleanEnvironment(...removed),
    APP_ENV: 'production',
    APP_KEY: 'qa-production-contract-key-0000000000000000',
    APP_URL: 'https://taascor-qa.invalid',
    APP_DEBUG: 'false',
    DB_DSN: 'mysql:host=127.0.0.1;dbname=taascor_qa_contract;charset=utf8mb4',
    DB_USER: 'qa-contract',
    DB_PASSWORD: 'not-a-real-password',
    UPLOAD_DIR: path.join(tmpdir(), 'taascor-qa-production-uploads'),
    SESSION_COOKIE_SECURE: 'true',
    PRIVACY_NOTICE_VERSION: 'approved-applicant-qa-v1',
    WORKFORCE_PRIVACY_NOTICE_VERSION: 'approved-workforce-qa-v1',
    ...overrides,
  };
}

function productionPrivacyProbe(overrides = {}) {
  const php = String.raw`
    require $argv[1];
    require $argv[2];
    $result = [];
    foreach (['applicant', 'workforce'] as $scope) {
        $result[$scope] = ['enabled' => privacy_collection_is_enabled($scope), 'error' => null];
        try {
            require_approved_privacy_notice($scope);
        } catch (DomainException $exception) {
            $result[$scope]['error'] = $exception->getMessage();
        }
    }
    $result['resume_upload'] = ['enabled' => resume_upload_is_enabled(), 'error' => null];
    try {
        store_resume_upload([]);
    } catch (DomainException $exception) {
        $result['resume_upload']['error'] = $exception->getMessage();
    } catch (InvalidArgumentException $exception) {
        $result['resume_upload']['error'] = $exception->getMessage();
    }
    $result['staff_workflows'] = ['enabled' => staff_workflows_are_enabled()];
    echo json_encode($result, JSON_THROW_ON_ERROR);
  `;
  const result = spawnSync(
    'php',
    [
      ...phpRuntimeArgs(), '-r', php,
      path.join(projectRoot, 'app', 'config.php'),
      path.join(projectRoot, 'app', 'upload.php'),
    ],
    {
      cwd: projectRoot,
      env: productionEnvironment(overrides),
      encoding: 'utf8',
      windowsHide: true,
    },
  );
  assert.equal(result.status, 0, `Production privacy probe failed:\n${result.stderr}`);
  return JSON.parse(result.stdout);
}

test('production APP_URL accepts only a credential-free HTTPS origin', () => {
  const php = 'require $argv[1]; app_config(); echo "configured";';
  for (const appUrl of [
    'http://taascor.example',
    'https://user:pass@taascor.example',
    'https://taascor.example/subdirectory',
    'https://taascor.example/?campaign=test',
    'https://taascor.example/#fragment',
  ]) {
    const result = spawnSync(
      'php',
      [...phpRuntimeArgs(), '-r', php, path.join(projectRoot, 'app', 'config.php')],
      {
        cwd: projectRoot,
        env: productionEnvironment({ APP_URL: appUrl }),
        encoding: 'utf8',
        windowsHide: true,
      },
    );
    assert.notEqual(result.status, 0, `${appUrl} must fail production configuration`);
    assert.match(result.stderr, /HTTPS origin without credentials, path, query, or fragment/i);
  }
});

test('CLI mutation helpers refuse to infer APP_ENV', () => {
  const environment = cleanEnvironment('APP_ENV');
  const expectations = [
    ['scripts/migrate.php', /Migration refused\. Set APP_ENV explicitly/i],
    ['scripts/seed.php', /Synthetic seed refused\. Set APP_ENV explicitly/i],
    ['scripts/create_staff.php', /Staff provisioning refused\. Set APP_ENV explicitly/i],
  ];

  for (const [script, message] of expectations) {
    const result = runPhpFile(script, environment);
    assert.equal(result.status, 2, `${script} must exit 2 without APP_ENV:\n${result.stdout}${result.stderr}`);
    assert.match(result.stderr, message, `${script} must explain its explicit-environment gate`);
    assert.equal(result.stdout, '', `${script} must stop before selecting or mutating a target`);
  }
});

test('authenticated sessions enforce idle and absolute expiry boundaries', () => {
  const php = String.raw`
    require $argv[1];
    echo json_encode([
      'active' => authenticated_session_has_expired([
        'user_id' => 1, 'authenticated_at' => 100, 'last_activity_at' => 900,
      ], 1000, 300, 1200),
      'idle_expired' => authenticated_session_has_expired([
        'user_id' => 1, 'authenticated_at' => 100, 'last_activity_at' => 699,
      ], 1000, 300, 1200),
      'absolute_expired' => authenticated_session_has_expired([
        'user_id' => 1, 'authenticated_at' => 99, 'last_activity_at' => 1299,
      ], 1300, 300, 1200),
      'anonymous' => authenticated_session_has_expired([], 1000, 300, 1200),
    ], JSON_THROW_ON_ERROR);
  `;
  const result = spawnSync(
    'php',
    [...phpRuntimeArgs(), '-r', php, path.join(projectRoot, 'app', 'security.php')],
    {
      cwd: projectRoot,
      env: cleanEnvironment(),
      encoding: 'utf8',
      windowsHide: true,
    },
  );
  assert.equal(result.status, 0, result.stderr);
  assert.deepEqual(JSON.parse(result.stdout), {
    active: false,
    idle_expired: true,
    absolute_expired: true,
    anonymous: false,
  });
});

test('unqualified personal-data, upload, and staff capabilities stay code-locked in production', () => {
  const refusalMessage = 'This collection route is unavailable until its approved privacy notice is published.';
  const disabled = productionPrivacyProbe();
  assert.deepEqual(disabled, {
    applicant: { enabled: false, error: refusalMessage },
    workforce: { enabled: false, error: refusalMessage },
    resume_upload: {
      enabled: false,
      error: 'Resume upload is unavailable until malware scanning, retention, and private-storage controls are approved.',
    },
    staff_workflows: { enabled: false },
  });

  const draftNotice = productionPrivacyProbe({
    APPLICANT_COLLECTION_ENABLED: 'true',
    WORKFORCE_COLLECTION_ENABLED: 'true',
    PRIVACY_NOTICE_VERSION: 'draft-applicant-qa-v2',
    WORKFORCE_PRIVACY_NOTICE_VERSION: 'draft-workforce-qa-v2',
  });
  assert.deepEqual(draftNotice, disabled, 'Enable flags must not override draft privacy notices');

  const explicitFlagsWithoutQualifiedControls = productionPrivacyProbe({
    APPLICANT_COLLECTION_ENABLED: 'true',
    WORKFORCE_COLLECTION_ENABLED: 'true',
    RESUME_UPLOAD_ENABLED: 'true',
    STAFF_WORKFLOWS_ENABLED: 'true',
  });
  assert.deepEqual(
    explicitFlagsWithoutQualifiedControls,
    disabled,
    'Environment flags alone must not bypass code-locked production qualification',
  );
});

test('sitemap defaults empty and emits the governed public set only when explicitly enabled', () => {
  const baseEnvironment = {
    ...cleanEnvironment('APP_ENV', 'APP_URL', 'PUBLIC_INDEXING_ENABLED'),
    APP_ENV: 'test',
    APP_URL: 'http://127.0.0.1:4177',
  };
  const defaultResult = runPhpFile('sitemap.php', baseEnvironment);
  assert.equal(defaultResult.status, 0, defaultResult.stderr);
  assert.doesNotMatch(defaultResult.stdout, /<loc>/i);

  const enabledResult = runPhpFile('sitemap.php', {
    ...baseEnvironment,
    PUBLIC_INDEXING_ENABLED: 'true',
  });
  assert.equal(enabledResult.status, 0, enabledResult.stderr);
  const locations = [...enabledResult.stdout.matchAll(/<loc>([^<]+)<\/loc>/g)]
    .map((match) => new URL(match[1]).pathname);
  assert.deepEqual(new Set(locations), new Set([
    '/', '/solutions/', '/solutions/workforce-staffing/',
    '/solutions/recruitment-sourcing/', '/solutions/payroll-coordination/',
    '/solutions/hr-administration/', '/solutions/facility-support/',
    '/solutions/hris-enabled-operations/', '/industries/',
    '/industries/production-throughput/', '/industries/distribution-fulfilment/',
    '/industries/office-service-support/', '/industries/facilities-site-support/',
    '/platform/', '/proof/',
    '/clients/', '/case-studies/', '/about/', '/leadership/', '/locations/',
    '/contact/', '/insights/', '/resources/', '/jobs/',
  ]));
  assert.equal(new Set(locations).size, locations.length, 'Enabled sitemap routes must be unique');
});
