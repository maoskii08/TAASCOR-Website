import { expect, test } from '@playwright/test';
import {
  accountRoutes,
  careersCandidates,
  expectSuccessfulNavigation,
  firstAvailableRoute,
  publicExperienceRoutes,
} from '../support/routes.mjs';

async function coreRoutes(request) {
  const careersRoute = await firstAvailableRoute(request, careersCandidates, 'Careers');
  return [
    ...publicExperienceRoutes,
    careersRoute,
    '/jobs/sample-operations-associate/',
    '/apply/sample-operations-associate/',
    '/apply/privacy.php',
    ...accountRoutes,
  ];
}

test('core pages provide skip navigation, one main landmark, labels, and no hidden focus traps', async ({ page, request }) => {
  for (const route of await coreRoutes(request)) {
    await test.step(route, async () => {
      await expectSuccessfulNavigation(page, route);
      await expect(page.locator('html')).toHaveAttribute('lang', /\S+/);
      await expect(page.locator('main')).toHaveCount(1);
      await expect(page.locator('main')).toHaveAttribute('id', 'main-content');

      const duplicateIds = await page.locator('[id]').evaluateAll((elements) => {
        const counts = new Map();
        for (const element of elements) {
          counts.set(element.id, (counts.get(element.id) || 0) + 1);
        }
        return [...counts.entries()]
          .filter(([, count]) => count > 1)
          .map(([id, count]) => `${id} (${count})`);
      });
      expect(duplicateIds, `${route} has duplicate rendered IDs`).toEqual([]);

      const skipLink = page.locator('a[href="#main-content"]').first();
      await expect(skipLink, `${route} requires a skip-to-content link`).toHaveCount(1);
      await skipLink.focus();
      await expect(skipLink).toBeVisible();

      const hiddenFocusable = await page.locator('[aria-hidden="true"]').evaluateAll((containers) => {
        const focusableSelector = [
          'a[href]',
          'button',
          'input:not([type="hidden"])',
          'select',
          'textarea',
          '[tabindex]:not([tabindex="-1"])',
          '[contenteditable="true"]',
        ].join(',');

        return containers.flatMap((container) => {
          const candidates = [
            ...(container.matches(focusableSelector) ? [container] : []),
            ...container.querySelectorAll(focusableSelector),
          ];
          return candidates
            .filter((element) => !element.disabled && element.getAttribute('tabindex') !== '-1')
            .map((element) => element.outerHTML.slice(0, 180));
        });
      });
      expect(
        hiddenFocusable,
        `${route} has focusable controls inside aria-hidden containers`,
      ).toEqual([]);

      const unlabeledControls = await page.locator('input:not([type="hidden"]), select, textarea').evaluateAll((controls) =>
        controls
          .filter((control) => {
            if (control.disabled || control.type === 'submit' || control.type === 'button') return false;
            if (control.getAttribute('aria-label') || control.getAttribute('aria-labelledby')) return false;
            if (control.closest('label')) return false;
            const id = control.getAttribute('id');
            return !id || !document.querySelector(`label[for="${CSS.escape(id)}"]`);
          })
          .map((control) => control.outerHTML.slice(0, 180)),
      );
      expect(unlabeledControls, `${route} has unlabeled form controls`).toEqual([]);
    });
  }
});

test('core pages do not overflow a 390px viewport', async ({ page, request }) => {
  await page.setViewportSize({ width: 390, height: 844 });

  for (const route of await coreRoutes(request)) {
    await test.step(route, async () => {
      await expectSuccessfulNavigation(page, route);
      await page.waitForTimeout(75);

      const overflow = await page.evaluate(() => {
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
          .slice(0, 8);
        return { documentWidth, scrollWidth, offenders };
      });

      expect(
        overflow.scrollWidth,
        `${route} overflows ${overflow.documentWidth}px: ${JSON.stringify(overflow.offenders)}`,
      ).toBeLessThanOrEqual(overflow.documentWidth + 1);
    });
  }
});

test('reduced-motion preference disables smooth scrolling and long-running page motion', async ({ page, request }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });

  for (const route of ['/', await firstAvailableRoute(request, careersCandidates, 'Careers')]) {
    await test.step(route, async () => {
      await expectSuccessfulNavigation(page, route);
      await page.waitForTimeout(150);

      const motion = await page.evaluate(() => {
        const parseDurations = (value) => value
          .split(',')
          .map((duration) => duration.trim())
          .map((duration) => duration.endsWith('ms')
            ? Number.parseFloat(duration)
            : Number.parseFloat(duration) * 1000)
          .filter(Number.isFinite);
        let maximumAnimationMs = 0;
        let maximumTransitionMs = 0;

        for (const element of document.querySelectorAll('body *')) {
          const style = getComputedStyle(element);
          maximumAnimationMs = Math.max(maximumAnimationMs, ...parseDurations(style.animationDuration), 0);
          maximumTransitionMs = Math.max(maximumTransitionMs, ...parseDurations(style.transitionDuration), 0);
        }

        const activeAnimations = document.getAnimations()
          .map((animation) => Number(animation.effect?.getTiming?.().duration || 0))
          .filter((duration) => duration > 100);

        return {
          preferenceMatches: matchMedia('(prefers-reduced-motion: reduce)').matches,
          scrollBehavior: getComputedStyle(document.documentElement).scrollBehavior,
          maximumAnimationMs,
          maximumTransitionMs,
          activeAnimations,
        };
      });

      expect(motion.preferenceMatches).toBeTruthy();
      expect(motion.scrollBehavior).not.toBe('smooth');
      expect(motion.maximumAnimationMs).toBeLessThanOrEqual(100);
      expect(motion.maximumTransitionMs).toBeLessThanOrEqual(100);
      expect(motion.activeAnimations).toEqual([]);
    });
  }
});

test('home mobile navigation is keyboard-usable and ambient motion can be paused', async ({ page }) => {
  await page.setViewportSize({ width: 390, height: 844 });
  await page.emulateMedia({ reducedMotion: 'no-preference' });
  await expectSuccessfulNavigation(page, '/');

  const menu = page.locator('details.home-menu');
  const summary = menu.locator('summary');
  await expect(summary).toBeVisible();
  await summary.focus();
  await page.keyboard.press('Enter');
  await expect(menu).toHaveAttribute('open', '');
  await expect(menu.getByRole('link', { name: 'Access TAASCOR' })).toBeVisible();

  await page.waitForFunction(() =>
    document.documentElement.classList.contains('motion-ready')
      || document.documentElement.classList.contains('motion-unavailable')
      || document.documentElement.classList.contains('mobile-layout'));
  await expect(page.locator('html')).toHaveClass(/mobile-layout/);
  await expect(page.locator('#film .stage')).toHaveCSS('position', 'static');
  await expect(page.locator('#film .beat')).toHaveCount(5);
  await expect(page.locator('#film .beat').nth(1)).toBeVisible();
  const motionIsReady = await page.locator('html').evaluate((element) =>
    element.classList.contains('motion-ready'));
  if (motionIsReady) {
    const toggle = page.locator('#motion-toggle');
    await expect(toggle).toBeVisible();
    await expect(toggle).toHaveAccessibleName('Pause motion');
    await toggle.click();
    await expect(toggle).toHaveAttribute('aria-pressed', 'true');
    await expect(toggle).toHaveText('Resume motion');
    await expect(page.locator('html')).toHaveClass(/motion-paused/);
  }
});

test('theme control is accessible and persists across public routes', async ({ page }) => {
  await page.emulateMedia({ colorScheme: 'dark' });
  await expectSuccessfulNavigation(page, '/solutions/');
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'light');

  const toggle = page.locator('[data-theme-toggle]').first();
  await expect(toggle).toHaveAttribute('aria-pressed', 'true');
  await expect(toggle).toHaveAttribute('aria-label', 'Use dark theme');
  await toggle.click();
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');
  await expect(toggle).toHaveAttribute('aria-pressed', 'false');

  await expectSuccessfulNavigation(page, '/proof/');
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');
  await expect(page.locator('[data-theme-toggle]').first()).toHaveAttribute('aria-label', 'Use light theme');

  await expectSuccessfulNavigation(page, '/');
  const homeToggle = page.locator('[data-theme-toggle]').first();
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');
  await expect(homeToggle).toHaveAttribute('aria-label', 'Use light theme');
  await homeToggle.click();
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'light');
  await expect(page.locator('meta[name="theme-color"]')).toHaveAttribute('content', '#f4f7fb');

  await expectSuccessfulNavigation(page, '/portal/');
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'light');
});
