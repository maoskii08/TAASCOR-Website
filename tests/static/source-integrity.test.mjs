import assert from 'node:assert/strict';
import path from 'node:path';
import test from 'node:test';
import {
  existingRouteTarget,
  projectRoot,
  readUtf8,
  walkProjectFiles,
} from '../support/project.mjs';

const markupExtensions = new Set(['.html', '.htm', '.php']);
const textualExtensions = new Set([
  '.css',
  '.env',
  '.example',
  '.htaccess',
  '.html',
  '.htm',
  '.ini',
  '.js',
  '.json',
  '.jsx',
  '.md',
  '.mjs',
  '.php',
  '.sql',
  '.svg',
  '.ts',
  '.tsx',
  '.txt',
  '.xml',
  '.yaml',
  '.yml',
]);

function extractAttributeValues(source, attributeNames) {
  const joinedNames = attributeNames.join('|');
  const matcher = new RegExp(`\\b(?:${joinedNames})\\s*=\\s*(["'])(.*?)\\1`, 'gis');
  return [...source.matchAll(matcher)].map((match) => match[2].trim());
}

function isStaticLocalReference(reference) {
  if (!reference || reference.startsWith('#')) {
    return false;
  }

  if (/^(?:https?:|data:|mailto:|tel:|javascript:|blob:|\/\/)/i.test(reference)) {
    return false;
  }

  return !/[<>{}$]/.test(reference)
    && !reference.includes('<?')
    && !/%(?:\d+\$)?[bcdeEfFgGosuxX]/.test(reference);
}

test('static local href, src, and action targets resolve to project files or routes', async (context) => {
  const markupFiles = (await walkProjectFiles()).filter(({ absolutePath }) =>
    markupExtensions.has(path.extname(absolutePath).toLowerCase()),
  );
  const unresolved = [];
  let referencesChecked = 0;

  for (const file of markupFiles) {
    const source = await readUtf8(file.absolutePath);
    const references = extractAttributeValues(source, ['href', 'src', 'action'])
      .filter(isStaticLocalReference);

    for (const reference of references) {
      referencesChecked += 1;
      if (!(await existingRouteTarget(reference, file.absolutePath))) {
        unresolved.push(`${file.relativePath} -> ${reference}`);
      }
    }
  }

  assert.deepEqual(
    unresolved,
    [],
    `Unresolved local source references:\n${unresolved.join('\n')}`,
  );
  context.diagnostic(`Resolved ${referencesChecked} static local reference(s).`);
});

test('same-document fragments have unique, existing targets', async (context) => {
  const markupFiles = (await walkProjectFiles()).filter(({ absolutePath }) =>
    markupExtensions.has(path.extname(absolutePath).toLowerCase()),
  );
  const failures = [];
  let fragmentsChecked = 0;

  for (const file of markupFiles) {
    const source = await readUtf8(file.absolutePath);
    if (!/<main\b/i.test(source)) {
      continue;
    }
    const ids = extractAttributeValues(source, ['id']);
    const idCounts = new Map();

    for (const id of ids) {
      idCounts.set(id, (idCounts.get(id) || 0) + 1);
    }

    const isStaticDocument = ['.html', '.htm'].includes(path.extname(file.absolutePath).toLowerCase());
    for (const [id, count] of idCounts) {
      // PHP templates can contain mutually exclusive response branches. Runtime
      // duplicate IDs are covered by the browser assertions below.
      if (isStaticDocument && count > 1) {
        failures.push(`${file.relativePath}: duplicate id="${id}" (${count} occurrences)`);
      }
    }

    const fragments = extractAttributeValues(source, ['href'])
      .filter((reference) => /^#[^#]+$/.test(reference))
      .map((reference) => decodeURIComponent(reference.slice(1)));

    for (const fragment of fragments) {
      fragmentsChecked += 1;
      if (!idCounts.has(fragment)) {
        failures.push(`${file.relativePath}: missing target for #${fragment}`);
      }
    }
  }

  assert.deepEqual(failures, [], `Fragment/id failures:\n${failures.join('\n')}`);
  context.diagnostic(`Checked ${fragmentsChecked} same-document fragment link(s).`);
});

test('repository content contains no recognizable committed secret signatures', async (context) => {
  const files = (await walkProjectFiles()).filter(({ absolutePath, relativePath }) => {
    const extension = path.extname(absolutePath).toLowerCase();
    return textualExtensions.has(extension)
      && relativePath !== 'package-lock.json'
      && !relativePath.startsWith('Audit/');
  });

  const directPatterns = [
    ['private key', /-----BEGIN (?:RSA |EC |OPENSSH |DSA )?PRIVATE KEY-----/g],
    ['AWS access key', /\bAKIA[0-9A-Z]{16}\b/g],
    ['GitHub token', /\b(?:gh[pousr]_[A-Za-z0-9]{30,}|github_pat_[A-Za-z0-9_]{40,})\b/g],
    ['OpenAI-style key', /\bsk-(?:proj-)?[A-Za-z0-9_-]{20,}\b/g],
    ['Stripe live secret', /\bsk_live_[A-Za-z0-9]{20,}\b/g],
  ];
  const genericAssignment = /\b(password|passwd|secret|api[_-]?key|access[_-]?token)\b\s*[:=]\s*(["'])([^\n"']{8,})\2/gi;
  const safeValue = /(?:example|placeholder|change[-_ ]?me|dummy|synthetic|qa[-_ ]?only|test[-_ ]?only|do[-_ ]?not[-_ ]?use|your[-_ ])/i;
  const findings = [];

  for (const file of files) {
    const source = await readUtf8(file.absolutePath);

    for (const [label, pattern] of directPatterns) {
      pattern.lastIndex = 0;
      if (pattern.test(source)) {
        findings.push(`${file.relativePath}: ${label}`);
      }
    }

    genericAssignment.lastIndex = 0;
    for (const match of source.matchAll(genericAssignment)) {
      const value = match[3];
      if (!safeValue.test(value)) {
        findings.push(`${file.relativePath}: non-placeholder ${match[1]} assignment`);
      }
    }
  }

  assert.deepEqual(
    findings,
    [],
    `Potential secrets found (values intentionally omitted):\n${findings.join('\n')}`,
  );
  context.diagnostic(`Scanned ${files.length} textual file(s) below ${projectRoot}.`);
});
