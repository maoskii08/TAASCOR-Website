import { mkdir, rm } from 'node:fs/promises';
import { execFileSync, spawn } from 'node:child_process';
import { existsSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { fileURLToPath } from 'node:url';
import { phpRuntimeArgs } from './php.mjs';
import { projectRoot } from './project.mjs';

const supportDirectory = path.dirname(fileURLToPath(import.meta.url));
const artifactDirectory = path.join(projectRoot, 'tests', '.artifacts');
const databasePath = path.join(artifactDirectory, 'qa.sqlite');
const uploadDirectory = path.join(artifactDirectory, 'uploads');
const host = '127.0.0.1';
const port = '4177';

const normalizedArtifacts = path.resolve(artifactDirectory);
const normalizedTestsDirectory = path.resolve(projectRoot, 'tests');
if (!normalizedArtifacts.startsWith(`${normalizedTestsDirectory}${path.sep}`)) {
  throw new Error(`Refusing to prepare unsafe QA artifact path: ${normalizedArtifacts}`);
}

await mkdir(artifactDirectory, { recursive: true });
await rm(databasePath, { force: true });
await rm(uploadDirectory, { recursive: true, force: true });
await mkdir(uploadDirectory, { recursive: true });

const inheritedEnvironment = Object.fromEntries(
  Object.entries(process.env).filter(([name]) => name.toUpperCase() !== 'PUBLIC_INDEXING_ENABLED'),
);

const qaEnvironment = {
  ...inheritedEnvironment,
  APP_ENV: 'test',
  APP_URL: `http://${host}:${port}`,
  APP_KEY: 'qa-only-local-key-do-not-use-in-production',
  DB_DSN: `sqlite:${databasePath}`,
  DB_USER: '',
  DB_PASSWORD: '',
  SESSION_COOKIE_NAME: 'taascor_qa_session',
  UPLOAD_DIR: uploadDirectory,
  PRIVACY_NOTICE_VERSION: '2026-09-01-qa',
  WORKFORCE_PRIVACY_NOTICE_VERSION: 'draft-workforce-2026-09-01-qa',
  STAFF_SEED_NAME: 'Synthetic QA Recruiter',
  STAFF_SEED_EMAIL: 'qa.staff@example.test',
  STAFF_SEED_PASSWORD: 'QA-only-password-43!',
};

const runtimeArguments = phpRuntimeArgs();
const setupCommands = [
  ['scripts/migrate.php', []],
  ['scripts/seed.php', []],
  ['scripts/create_staff.php', ['--confirm-local-staff']],
];

for (const [relativeScript, scriptArguments] of setupCommands) {
  const scriptPath = path.join(projectRoot, relativeScript);
  if (!existsSync(scriptPath)) {
    throw new Error(`Required isolated-QA setup script is missing: ${relativeScript}`);
  }
  execFileSync('php', [...runtimeArguments, scriptPath, ...scriptArguments], {
    cwd: projectRoot,
    env: qaEnvironment,
    stdio: 'inherit',
    windowsHide: true,
  });
}

const phpArguments = [
  ...runtimeArguments,
  '-S',
  `${host}:${port}`,
  '-t',
  projectRoot,
  path.join(supportDirectory, 'router.php'),
];

const server = spawn('php', phpArguments, {
  cwd: projectRoot,
  env: qaEnvironment,
  stdio: 'inherit',
  windowsHide: true,
});

const terminate = (signal) => {
  if (!server.killed) {
    server.kill(signal);
  }
};

process.on('SIGINT', () => terminate('SIGINT'));
process.on('SIGTERM', () => terminate('SIGTERM'));
process.on('exit', () => terminate('SIGTERM'));

server.on('error', (error) => {
  throw error;
});

server.on('exit', (code, signal) => {
  if (signal) {
    process.exit(0);
  }
  process.exit(code ?? 1);
});

await new Promise(() => {});
