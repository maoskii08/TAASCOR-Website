import { expect, test } from '@playwright/test';
import {
  accountRoutes,
  careersCandidates,
  expectSuccessfulNavigation,
  firstAvailableRoute,
  industryContextRoutes,
  publicExperienceRoutes,
  sameOriginPath,
} from '../support/routes.mjs';

test('required public and account routes render meaningful pages', async ({ page, request }) => {
  const careersRoute = await firstAvailableRoute(request, careersCandidates, 'Careers');
  const routes = [...publicExperienceRoutes, careersRoute, ...accountRoutes];

  for (const route of routes) {
    await test.step(route, async () => {
      await expectSuccessfulNavigation(page, route);
      await expect(page.locator('body')).not.toBeEmpty();
      await expect(page.locator('h1').first()).toBeVisible();
      const bodyText = (await page.locator('body').innerText()).trim();
      expect(bodyText.length, `${route} contains too little meaningful text`).toBeGreaterThan(80);
    });
  }
});

test('first-party links on public pages do not resolve to errors', async ({ page, request, baseURL }) => {
  const routes = [...publicExperienceRoutes];
  const failures = [];
  const checked = new Set();

  for (const route of routes) {
    await expectSuccessfulNavigation(page, route);
    const hrefs = await page.locator('a[href]').evaluateAll((anchors) =>
      anchors.map((anchor) => anchor.getAttribute('href')).filter(Boolean),
    );

    for (const href of hrefs) {
      const localPath = sameOriginPath(href, baseURL);
      if (!localPath || localPath.startsWith('/account/logout.php')) {
        continue;
      }

      const requestPath = localPath.split('#')[0] || '/';
      if (checked.has(requestPath)) {
        continue;
      }
      checked.add(requestPath);

      const response = await request.get(requestPath, { maxRedirects: 0 });
      if (response.status() >= 400) {
        failures.push(`${route} -> ${requestPath} (${response.status()})`);
      }
    }
  }

  expect(failures, `Broken first-party links:\n${failures.join('\n')}`).toEqual([]);
  expect(checked.size, 'No first-party links were available to validate').toBeGreaterThan(0);
});

test('industry overview leads to four evidence-gated context lenses', async ({ page }) => {
  await expectSuccessfulNavigation(page, '/industries/');
  const overviewHrefs = await page.locator('a[href^="/industries/"]').evaluateAll((links) =>
    [...new Set(links.map((link) => link.getAttribute('href')).filter(Boolean))],
  );
  for (const route of industryContextRoutes) {
    expect(overviewHrefs, `Industry overview must link to ${route}`).toContain(route);
  }

  for (const route of industryContextRoutes) {
    await test.step(route, async () => {
      await expectSuccessfulNavigation(page, route);
      await expect(page.locator('.breadcrumbs')).toContainText('Industries');
      await expect(page.locator('.notice-panel').first()).toContainText('What this page does not establish');
      await expect(page.locator('.industry-context-grid > li')).toHaveCount(4);
      await expect(page.locator('.process-list > li')).toHaveCount(5);
      await expect(page.locator('.industry-solution-card')).toHaveCount(4);
      await expect(page.locator('.module-card')).toHaveCount(4);
      await expect(page.locator('a[href="/workforce/"]').first()).toBeVisible();
    });
  }
});

test('home remains readable when third-party animation and font dependencies fail', async ({ page, baseURL }) => {
  const localOrigin = new URL(baseURL).origin;
  await page.route('**/*', async (route) => {
    const requestURL = new URL(route.request().url());
    if (requestURL.origin !== localOrigin) {
      await route.abort('failed');
      return;
    }
    await route.continue();
  });

  await page.goto('/', { waitUntil: 'domcontentloaded' });
  const heading = page.locator('h1').first();
  await expect(heading).toBeVisible();
  await expect(heading).not.toBeEmpty();

  const visibility = await heading.evaluate((element) => {
    const style = getComputedStyle(element);
    const rect = element.getBoundingClientRect();
    return {
      display: style.display,
      opacity: Number(style.opacity),
      visibility: style.visibility,
      width: rect.width,
      height: rect.height,
    };
  });

  expect(visibility.display).not.toBe('none');
  expect(visibility.visibility).not.toBe('hidden');
  expect(visibility.opacity).toBeGreaterThan(0.01);
  expect(visibility.width).toBeGreaterThan(20);
  expect(visibility.height).toBeGreaterThan(10);
});

test('careers to job detail to apply preserves job context', async ({ page, request }) => {
  const careersRoute = await firstAvailableRoute(request, careersCandidates, 'Careers');
  await expectSuccessfulNavigation(page, careersRoute);

  const jobLinks = page.locator(
    'a[href^="/jobs/"]:not([href="/jobs/"]), a[href*="job.php"]:not([href*="/apply"]), a[data-job-link]',
  );
  await expect(jobLinks.first(), 'Careers must link to a dedicated job-detail page').toBeVisible();
  const jobHref = await jobLinks.first().getAttribute('href');
  expect(jobHref).toBeTruthy();

  await expectSuccessfulNavigation(page, jobHref);
  const jobTitle = (await page.locator('[data-job-title], main h1, main h2').first().innerText()).trim();
  expect(jobTitle.length).toBeGreaterThan(2);

  const applyLink = page.locator('a[href*="/apply"]').first();
  await expect(applyLink, 'Job detail must expose a contextual Apply action').toBeVisible();
  const applyHref = await applyLink.getAttribute('href');
  expect(applyHref).toBeTruthy();
  expect(
    /(?:[?&](?:job|job_id|slug)=|\/apply\/.+[^/])/.test(applyHref),
    `Apply link must carry job context: ${applyHref}`,
  ).toBeTruthy();

  await expectSuccessfulNavigation(page, applyHref);
  await expect(
    page.locator('form, a[href*="/account/login.php"], a[href*="/account/register.php"]').first(),
    'The contextual apply page must expose either its form or a secure authentication handoff',
  ).toBeVisible();

  const context = await page.evaluate(() => {
    const selectedOption = document.querySelector('select[name*="job" i] option:checked');
    const contextualField = document.querySelector(
      'input[name*="job" i], [data-job-title], [data-selected-job]',
    );
    return [
      document.querySelector('main')?.textContent || '',
      selectedOption?.textContent || '',
      contextualField?.getAttribute('value') || contextualField?.textContent || '',
    ].join(' ').replace(/\s+/g, ' ').trim().toLowerCase();
  });
  const meaningfulTitleWords = jobTitle
    .toLowerCase()
    .split(/\W+/)
    .filter((word) => word.length >= 4);
  expect(
    meaningfulTitleWords.some((word) => context.includes(word)),
    `Apply page did not preserve visible context for "${jobTitle}"`,
  ).toBeTruthy();
});
