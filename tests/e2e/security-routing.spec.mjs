import { expect, test } from '@playwright/test';
import {
  accountRoutes,
  publicExperienceRoutes,
} from '../support/routes.mjs';

const requiredHeaders = [
  ['content-security-policy', /default-src/i],
  ['x-content-type-options', /^nosniff$/i],
  ['referrer-policy', /(?:no-referrer|same-origin|strict-origin)/i],
  ['permissions-policy', /\S/],
];

test('public and authentication responses include baseline security headers', async ({ request }) => {
  const failures = [];
  const routes = ['/', ...publicExperienceRoutes.slice(1), ...accountRoutes];

  for (const route of routes) {
    const response = await request.get(route, { maxRedirects: 0 });
    const headers = response.headers();

    if (response.status() >= 400) {
      failures.push(`${route}: HTTP ${response.status()}`);
      continue;
    }

    for (const [header, expectation] of requiredHeaders) {
      if (!expectation.test(headers[header] || '')) {
        failures.push(`${route}: missing/invalid ${header}`);
      }
    }

    const framingProtected = /(?:deny|sameorigin)/i.test(headers['x-frame-options'] || '')
      || /frame-ancestors\s+(?:'none'|'self')/i.test(headers['content-security-policy'] || '');
    if (!framingProtected) {
      failures.push(`${route}: no clickjacking protection`);
    }

    const cookies = response.headersArray()
      .filter(({ name }) => name.toLowerCase() === 'set-cookie')
      .map(({ value }) => value);
    for (const cookie of cookies) {
      if (!/;\s*httponly(?:;|$)/i.test(cookie)) {
        failures.push(`${route}: session cookie lacks HttpOnly`);
      }
      if (!/;\s*samesite=(?:lax|strict)(?:;|$)/i.test(cookie)) {
        failures.push(`${route}: session cookie lacks SameSite=Lax/Strict`);
      }
    }
  }

  expect(failures, `Security-header failures:\n${failures.join('\n')}`).toEqual([]);
});

test('default indexing policy fails closed and branded 404 behavior is present', async ({ request, baseURL }) => {
  const robots = await request.get('/robots.txt');
  expect(robots.status()).toBe(200);
  const robotsBody = await robots.text();
  expect(robotsBody).toMatch(/^User-agent:\s*\*$/im);
  expect(robotsBody).toMatch(/^Disallow:\s*\/$/im);
  expect(robotsBody).not.toMatch(/^Allow:/im);
  expect(robotsBody).toContain(`Sitemap: ${new URL('/sitemap.xml', baseURL).href}`);

  const sitemap = await request.get('/sitemap.xml');
  expect(sitemap.status()).toBe(200);
  expect(sitemap.headers()['content-type'] || '').toMatch(/(?:xml|text\/plain)/i);
  expect(sitemap.headers()['x-robots-tag'] || '').toMatch(/noindex/i);
  const sitemapBody = await sitemap.text();
  expect(sitemapBody).toMatch(/<urlset\b/i);
  expect(sitemapBody).not.toMatch(/<loc>/i);
  expect(sitemapBody).not.toMatch(/<lastmod>/i);

  for (const route of ['/', ...publicExperienceRoutes.slice(1)]) {
    const response = await request.get(route, { maxRedirects: 0 });
    expect(response.status(), `${route} must remain available while indexing is disabled`).toBe(200);
    expect(response.headers()['x-robots-tag'] || '', `${route} must send a noindex header`)
      .toMatch(/noindex/i);
    expect(await response.text(), `${route} must render a noindex robots directive`)
      .toMatch(/<meta\s+[^>]*name=["']robots["'][^>]*content=["'][^"']*noindex/i);
  }

  const missing = await request.get('/qa-intentionally-missing-7fd46f91');
  expect(missing.status()).toBe(404);
  expect(missing.headers()['content-type'] || '').toMatch(/text\/html/i);
  const missingBody = await missing.text();
  expect(missingBody).toMatch(/TAASCOR/i);
  expect(missingBody).toMatch(/(?:not found|page.*missing|404)/i);
  expect(missingBody).toMatch(/<main\b/i);
  expect(missingBody.length).toBeGreaterThan(500);
});

test('anonymous users cannot access applicant or staff workspaces', async ({ request }) => {
  for (const route of [
    '/applicant/index.php',
    '/staff/index.php',
    '/staff/jobs.php',
    '/staff/applications.php',
  ]) {
    const response = await request.get(route, { maxRedirects: 0 });
    expect([302, 303, 401, 403], `${route} must reject anonymous access`).toContain(response.status());
    if ([302, 303].includes(response.status())) {
      expect(response.headers().location || '', `${route} must redirect to an authentication route`)
        .toMatch(/\/(?:account|staff)\/login\.php/i);
    }
  }
});

test('state-changing authentication forms reject missing CSRF tokens', async ({ request, baseURL }) => {
  const host = new URL(baseURL).hostname;
  test.skip(!['127.0.0.1', 'localhost', '::1'].includes(host), 'Mutation probes are loopback-only.');

  await request.get('/account/register.php');
  const registerResponse = await request.post('/account/register.php', {
    form: {
      full_name: 'Synthetic CSRF Probe',
      email: 'csrf-probe@example.test',
      phone: '+639170000001',
      password: 'Synthetic-only-password-41!',
      password_confirmation: 'Synthetic-only-password-41!',
      privacy_acknowledged: '1',
    },
    maxRedirects: 0,
  });
  expect(registerResponse.status(), 'Registration without CSRF must be rejected').toBe(419);

  await request.get('/account/login.php');
  const loginResponse = await request.post('/account/login.php', {
    form: {
      email: 'nobody@example.test',
      password: 'Synthetic-only-password-41!',
    },
    maxRedirects: 0,
  });
  expect(loginResponse.status(), 'Login without CSRF must be rejected').toBe(419);
});

test('applicant sign-in throttles repeated failures without exposing account state', async ({ page, baseURL }) => {
  const host = new URL(baseURL).hostname;
  expect(
    ['127.0.0.1', 'localhost', '::1'],
    'Synthetic authentication abuse checks are allowed only on loopback.',
  ).toContain(host);

  await page.goto('/account/login.php');
  for (let attempt = 1; attempt <= 9; attempt += 1) {
    await page.locator('[name="email"]').fill('qa-rate-limit-subject@example.test');
    await page.locator('[name="password"]').fill(`Incorrect-synthetic-password-${attempt}!`);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.getByRole('button', { name: 'Sign in securely' }).click(),
    ]);
    const message = page.getByRole('alert');
    if (attempt <= 8) {
      await expect(message).toContainText('Email or password is incorrect.');
    } else {
      await expect(message).toContainText('Too many attempts. Wait 15 minutes and try again.');
    }
  }
});
