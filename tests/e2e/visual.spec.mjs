import { mkdir } from 'node:fs/promises';
import path from 'node:path';
import { expect, test } from '@playwright/test';
import {
  accountRoutes,
  careersCandidates,
  expectSuccessfulNavigation,
  firstAvailableRoute,
} from '../support/routes.mjs';
import { projectRoot } from '../support/project.mjs';

const screenshotDirectory = path.join(projectRoot, 'tests', '.artifacts', 'screenshots');

function routeSlug(route) {
  return route === '/'
    ? 'home'
    : route.replace(/^\/+|\/+$/g, '').replace(/[^a-z0-9]+/gi, '-').toLowerCase();
}

function requireLoopback(baseURL) {
  const host = new URL(baseURL).hostname;
  expect(
    ['127.0.0.1', 'localhost', '::1'],
    'Synthetic authenticated visual checks are allowed only on loopback.',
  ).toContain(host);
}

async function setThemePreference(page, theme) {
  await page.goto('/');
  await page.evaluate((value) => {
    window.localStorage.setItem('taascor-color-theme', value);
  }, theme);
}

async function captureReviewRoute(page, route, prefix, testInfo) {
  await expectSuccessfulNavigation(page, route);
  await expect(page.locator('body')).toBeVisible();
  await page.evaluate(() => window.scrollTo(0, 0));
  const dimensions = await page.evaluate(() => ({
    clientWidth: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
  }));
  expect(dimensions.scrollWidth, `${route} overflows ${prefix}`).toBeLessThanOrEqual(
    dimensions.clientWidth + 1,
  );
  const screenshotPath = path.join(screenshotDirectory, `${prefix}-${routeSlug(route)}.png`);
  await page.screenshot({ path: screenshotPath, fullPage: false, animations: 'disabled' });
  await testInfo.attach(`${prefix}-${routeSlug(route)}`, {
    path: screenshotPath,
    contentType: 'image/png',
  });
}

async function inspectHorizontalOverflow(page) {
  return page.evaluate(() => {
    const documentWidth = document.documentElement.clientWidth;
    const scrollWidth = document.documentElement.scrollWidth;
    const offenders = [...document.querySelectorAll('body *')]
      .map((element) => {
        const rect = element.getBoundingClientRect();
        return {
          element: `${element.tagName.toLowerCase()}${element.id ? `#${element.id}` : ''}${
            element.classList.length ? `.${[...element.classList].slice(0, 3).join('.')}` : ''
          }`,
          left: Math.round(rect.left),
          right: Math.round(rect.right),
          width: Math.round(rect.width),
        };
      })
      .filter(({ left, right, width }) => width > 0 && (left < -2 || right > documentWidth + 2))
      .slice(0, 12);

    return { documentWidth, scrollWidth, offenders };
  });
}

test('capture review screenshots across the final responsive and theme matrix', async ({ page, request }, testInfo) => {
  test.setTimeout(90_000);
  await mkdir(screenshotDirectory, { recursive: true });
  const careersRoute = await firstAvailableRoute(request, careersCandidates, 'Careers');
  const routes = [
    '/',
    '/solutions/',
    '/industries/',
    '/platform/',
    '/proof/',
    careersRoute,
    '/jobs/sample-warehouse-coordinator/',
    '/workforce/',
    '/portal/',
    '/login/',
    ...accountRoutes,
  ];

  for (const viewport of [
    { name: 'desktop-dark', width: 1440, height: 1000, colorScheme: 'dark' },
    { name: 'tablet-light', width: 768, height: 1024, colorScheme: 'light' },
    { name: 'mobile-390-dark', width: 390, height: 844, colorScheme: 'dark' },
    { name: 'mobile-360-light', width: 360, height: 800, colorScheme: 'light' },
  ]) {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    await page.emulateMedia({ reducedMotion: 'reduce', colorScheme: viewport.colorScheme });
    await setThemePreference(page, viewport.name.includes('dark') ? 'dark' : 'light');

    for (const route of routes) {
      await expectSuccessfulNavigation(page, route);
      await expect(page.locator('body')).toBeVisible();
      await page.evaluate(() => window.scrollTo(0, 0));
      const dimensions = await inspectHorizontalOverflow(page);
      expect(
        dimensions.scrollWidth,
        `${route} overflows the ${viewport.name} viewport: ${JSON.stringify(dimensions.offenders)}`,
      ).toBeLessThanOrEqual(dimensions.documentWidth + 1);
      const screenshotPath = path.join(
        screenshotDirectory,
        `${viewport.name}-${routeSlug(route)}.png`,
      );
      await page.screenshot({ path: screenshotPath, fullPage: false, animations: 'disabled' });
      await testInfo.attach(`${viewport.name}-${routeSlug(route)}`, {
        path: screenshotPath,
        contentType: 'image/png',
      });
    }
  }
});

test('capture authenticated applicant and staff surfaces at desktop and mobile', async ({ browser, page, baseURL }, testInfo) => {
  test.setTimeout(90_000);
  requireLoopback(baseURL);
  await mkdir(screenshotDirectory, { recursive: true });

  await page.setViewportSize({ width: 1440, height: 1000 });
  await page.emulateMedia({ reducedMotion: 'reduce', colorScheme: 'dark' });
  await setThemePreference(page, 'dark');
  await page.goto('/account/register.php');
  await page.locator('[name="full_name"]').fill('Synthetic Visual Applicant');
  await page.locator('[name="email"]').fill('qa.visual.applicant@example.test');
  await page.locator('[name="phone"]').fill('+639170000601');
  await page.locator('[name="password"]').fill('Synthetic-visual-password-61!');
  await page.locator('[name="password_confirmation"]').fill('Synthetic-visual-password-61!');
  await page.locator('[name="privacy_acknowledged"]').check();
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.getByRole('button', { name: 'Create secure account' }).click(),
  ]);
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)/);

  for (const viewport of [
    { prefix: 'authenticated-desktop-dark', width: 1440, height: 1000, colorScheme: 'dark' },
    { prefix: 'authenticated-mobile-390-light', width: 390, height: 844, colorScheme: 'light' },
  ]) {
    await page.setViewportSize({ width: viewport.width, height: viewport.height });
    await page.emulateMedia({ reducedMotion: 'reduce', colorScheme: viewport.colorScheme });
    await setThemePreference(page, viewport.colorScheme);
    for (const route of ['/applicant/', '/account/settings.php', '/apply/sample-warehouse-coordinator/']) {
      await captureReviewRoute(page, route, viewport.prefix, testInfo);
    }
  }

  const staffContext = await browser.newContext({ viewport: { width: 1440, height: 1000 }, colorScheme: 'dark', reducedMotion: 'reduce' });
  const staffPage = await staffContext.newPage();
  try {
    await setThemePreference(staffPage, 'dark');
    await staffPage.goto('/staff/login.php');
    await staffPage.locator('[name="email"]').fill('qa.staff@example.test');
    await staffPage.locator('[name="password"]').fill('QA-only-password-43!');
    await Promise.all([
      staffPage.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      staffPage.getByRole('button', { name: 'Enter staff workspace' }).click(),
    ]);
    await expect(staffPage).toHaveURL(/\/staff\/(?:index\.php)?(?:[?#]|$)/);

    for (const viewport of [
      { prefix: 'staff-desktop-dark', width: 1440, height: 1000, colorScheme: 'dark' },
      { prefix: 'staff-mobile-390-light', width: 390, height: 844, colorScheme: 'light' },
    ]) {
      await staffPage.setViewportSize({ width: viewport.width, height: viewport.height });
      await staffPage.emulateMedia({ reducedMotion: 'reduce', colorScheme: viewport.colorScheme });
      await setThemePreference(staffPage, viewport.colorScheme);
      for (const route of ['/staff/', '/staff/jobs.php', '/staff/applications.php']) {
        await captureReviewRoute(staffPage, route, viewport.prefix, testInfo);
      }
    }
  } finally {
    await staffContext.close();
  }
});
