import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';
import test from 'node:test';
import { walkProjectFiles } from '../support/project.mjs';

test('every PHP source file passes php -l', async (context) => {
  const phpFiles = (await walkProjectFiles()).filter(({ relativePath }) =>
    relativePath.toLowerCase().endsWith('.php'),
  );

  if (phpFiles.length === 0) {
    context.diagnostic('No PHP files are present; syntax scan has no applicable inputs.');
    return;
  }

  const failures = [];
  for (const file of phpFiles) {
    try {
      execFileSync('php', ['-l', file.absolutePath], {
        encoding: 'utf8',
        stdio: ['ignore', 'pipe', 'pipe'],
      });
    } catch (error) {
      const detail = [error.stdout, error.stderr].filter(Boolean).join('\n').trim();
      failures.push(`${file.relativePath}: ${detail || error.message}`);
    }
  }

  assert.deepEqual(failures, [], `PHP syntax failures:\n${failures.join('\n')}`);
  context.diagnostic(`Linted ${phpFiles.length} PHP file(s).`);
});
