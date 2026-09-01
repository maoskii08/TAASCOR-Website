import { readdir, readFile, stat } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

export const projectRoot = path.resolve(
  path.dirname(fileURLToPath(import.meta.url)),
  '..',
  '..',
);

const ignoredDirectoryNames = new Set([
  '.git',
  '.idea',
  '.vscode',
  'node_modules',
  'vendor',
]);

const ignoredRelativePrefixes = [
  'tests/.artifacts/',
  'storage/private/',
];

export function toPosix(value) {
  return value.split(path.sep).join('/');
}

export async function walkProjectFiles(directory = projectRoot) {
  const files = [];

  async function walk(currentDirectory) {
    const entries = await readdir(currentDirectory, { withFileTypes: true });

    for (const entry of entries) {
      if (entry.isDirectory() && ignoredDirectoryNames.has(entry.name)) {
        continue;
      }

      const absolutePath = path.join(currentDirectory, entry.name);
      const relativePath = toPosix(path.relative(projectRoot, absolutePath));

      if (ignoredRelativePrefixes.some((prefix) => `${relativePath}/`.startsWith(prefix))) {
        continue;
      }

      if (entry.isDirectory()) {
        await walk(absolutePath);
      } else if (entry.isFile()) {
        files.push({ absolutePath, relativePath });
      }
    }
  }

  await walk(directory);
  return files.sort((left, right) => left.relativePath.localeCompare(right.relativePath));
}

export async function existingRouteTarget(reference, sourceFile) {
  const cleanReference = reference.split('#')[0].split('?')[0];
  if (!cleanReference) {
    return true;
  }

  let decodedReference;
  try {
    decodedReference = decodeURIComponent(cleanReference);
  } catch {
    return false;
  }
  const absoluteTarget = decodedReference.startsWith('/')
    ? path.join(projectRoot, decodedReference.replace(/^\/+/, ''))
    : path.resolve(path.dirname(sourceFile), decodedReference);

  const candidates = [
    absoluteTarget,
    `${absoluteTarget}.php`,
    path.join(absoluteTarget, 'index.php'),
    path.join(absoluteTarget, 'index.html'),
  ];

  for (const candidate of candidates) {
    try {
      const targetStat = await stat(candidate);
      if (targetStat.isFile()) {
        return true;
      }
    } catch {
      // Continue through all route/file candidates.
    }
  }

  return false;
}

export async function readUtf8(file) {
  return readFile(file, 'utf8');
}
