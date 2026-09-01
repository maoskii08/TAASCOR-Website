import { execFileSync } from 'node:child_process';

function isExtensionLoaded(extension) {
  try {
    return execFileSync(
      'php',
      ['-r', `exit(extension_loaded('${extension}') ? 0 : 1);`],
      { stdio: 'ignore' },
    ) === null;
  } catch {
    return false;
  }
}

export function phpRuntimeArgs() {
  const args = [];

  if (!isExtensionLoaded('pdo_sqlite')) {
    args.push('-d', 'extension=pdo_sqlite');
  }

  if (!isExtensionLoaded('sqlite3')) {
    args.push('-d', 'extension=sqlite3');
  }

  return args;
}
