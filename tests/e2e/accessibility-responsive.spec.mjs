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

async function contrastRatio(page, textSelector, surfaceSelector) {
  const colors = await page.evaluate(({ textSelector: text, surfaceSelector: surface }) => ({
    foreground: getComputedStyle(document.querySelector(text)).color,
    background: getComputedStyle(document.querySelector(surface)).backgroundColor,
  }), { textSelector, surfaceSelector });

  const channels = (value) => value.match(/[\d.]+/g).slice(0, 3).map((channel) => Number(channel) / 255);
  const luminance = (value) => channels(value)
    .map((channel) => channel <= 0.04045 ? channel / 12.92 : ((channel + 0.055) / 1.055) ** 2.4)
    .reduce((sum, channel, index) => sum + channel * [0.2126, 0.7152, 0.0722][index], 0);
  const foreground = luminance(colors.foreground);
  const background = luminance(colors.background);
  return (Math.max(foreground, background) + 0.05) / (Math.min(foreground, background) + 0.05);
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

test('home mobile keeps native flow, scroll-driven motion, and guarded tap-to-advance', async ({ page }) => {
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
    document.documentElement.classList.contains('mobile-motion-ready'));
  await expect(page.locator('html')).toHaveClass(/mobile-layout/);
  await expect(page.locator('html')).toHaveClass(/mobile-motion-ready/);
  await expect(page.locator('#film .stage')).toHaveCSS('position', 'static');
  await expect(page.locator('#film .beat')).toHaveCount(5);
  await expect(page.locator('#film .beat').nth(1)).toBeVisible();
  await expect(page.locator('#cue')).toContainText('Tap empty space or swipe to continue');
  await expect(page.locator('#motion-toggle')).toHaveCount(0);

  const expectedStop = await page.locator('#film .beat').nth(1).evaluate((element) => {
    const header = document.querySelector('#hdr');
    const offset = Math.min(90, (header?.offsetHeight || 64) + 12);
    return Math.max(0, Math.round(element.getBoundingClientRect().top + window.scrollY - offset));
  });
  await page.locator('#b-hero').dispatchEvent('pointerdown', {
    pointerId: 17, pointerType: 'touch', isPrimary: true, clientX: 20, clientY: 620, button: 0,
  });
  await page.locator('#b-hero').dispatchEvent('pointerup', {
    pointerId: 17, pointerType: 'touch', isPrimary: true, clientX: 20, clientY: 620, button: 0,
  });
  await expect.poll(async () => Math.round(await page.evaluate(() => window.scrollY)), {
    timeout: 2_500,
  }).toBeGreaterThan(expectedStop - 12);
  expect(Math.abs(Math.round(await page.evaluate(() => window.scrollY)) - expectedStop)).toBeLessThanOrEqual(18);
  await expect(page.locator('#film .beat').nth(1)).toHaveClass(/mobile-inview/);

  await page.evaluate(() => window.scrollTo(0, 0));
  await expect.poll(async () => Math.round(await page.evaluate(() => window.scrollY))).toBeLessThanOrEqual(2);
  const firstDoor = page.locator('.journey-door').first();
  await firstDoor.dispatchEvent('pointerdown', {
    pointerId: 18, pointerType: 'touch', isPrimary: true, clientX: 40, clientY: 500, button: 0,
  });
  await firstDoor.dispatchEvent('pointerup', {
    pointerId: 18, pointerType: 'touch', isPrimary: true, clientX: 40, clientY: 500, button: 0,
  });
  await page.waitForTimeout(150);
  expect(Math.round(await page.evaluate(() => window.scrollY))).toBeLessThanOrEqual(2);

  await page.locator('#b-hero').dispatchEvent('pointerdown', {
    pointerId: 19, pointerType: 'touch', isPrimary: true, clientX: 30, clientY: 650, button: 0,
  });
  await page.locator('#b-hero').dispatchEvent('pointermove', {
    pointerId: 19, pointerType: 'touch', isPrimary: true, clientX: 30, clientY: 590, button: 0,
  });
  await page.locator('#b-hero').dispatchEvent('pointerup', {
    pointerId: 19, pointerType: 'touch', isPrimary: true, clientX: 30, clientY: 590, button: 0,
  });
  await page.waitForTimeout(150);
  expect(Math.round(await page.evaluate(() => window.scrollY))).toBeLessThanOrEqual(2);
});

test('home desktop click-to-advance lands on the next authored cinematic peak', async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.emulateMedia({ reducedMotion: 'no-preference' });
  await expectSuccessfulNavigation(page, '/');
  await page.waitForFunction(() => window.__ready === true);

  await expect(page.locator('#motion-toggle')).toHaveCount(0);
  await expect(page.locator('#ops')).toHaveAttribute('data-click-peak', '0.72');

  const expectedStop = await page.locator('#film').evaluate((scene) => {
    const start = scene.getBoundingClientRect().top + window.scrollY;
    const span = Math.max(1, scene.offsetHeight - window.innerHeight);
    return Math.round(start + span * 0.24);
  });

  await page.mouse.click(18, 420);
  await expect.poll(async () => Math.round(await page.evaluate(() => window.scrollY)), {
    timeout: 6_000,
  }).toBeGreaterThan(expectedStop - 12);
  expect(Math.abs(Math.round(await page.evaluate(() => window.scrollY)) - expectedStop)).toBeLessThanOrEqual(18);
});

test('theme control is accessible and persists across public routes', async ({ page }) => {
  await page.emulateMedia({ colorScheme: 'dark' });
  await expectSuccessfulNavigation(page, '/solutions/');
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'light');

  const toggle = page.locator('[data-theme-toggle]').first();
  await expect(toggle).toHaveAttribute('aria-pressed', 'false');
  await expect(toggle).toHaveAttribute('aria-label', 'Use dark theme');
  await toggle.click();
  await expect(page.locator('html')).toHaveAttribute('data-theme', 'dark');
  await expect(toggle).toHaveAttribute('aria-pressed', 'true');

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
  await expect(page.locator('[data-theme-toggle]').first()).toHaveAttribute('aria-pressed', 'false');
});

test('default light theme keeps Careers and Workforce decision surfaces readable', async ({ page }) => {
  await page.goto('/');
  await page.evaluate(() => window.localStorage.setItem('taascor-color-theme', 'light'));

  await expectSuccessfulNavigation(page, '/jobs/');
  await expect(page.locator('.job-filters')).toHaveCSS('background-color', 'rgb(251, 253, 255)');
  await expect(page.locator('.job-filters label').first()).toHaveCSS('color', 'rgb(76, 92, 117)');
  await expect(page.locator('.career-safety')).toHaveCSS('color', 'rgb(5, 7, 12)');
  expect(await contrastRatio(page, '.job-filters label', '.job-filters')).toBeGreaterThanOrEqual(4.5);

  await expectSuccessfulNavigation(page, '/jobs/sample-warehouse-coordinator/');
  await expect(page.locator('.apply-rail')).toHaveCSS('background-color', 'rgb(251, 253, 255)');

  await expectSuccessfulNavigation(page, '/workforce/');
  await expect(page.locator('.brief-map')).toHaveCSS('background-color', 'rgb(251, 253, 255)');
  await expect(page.locator('.brief-form-panel')).toHaveCSS('background-color', 'rgb(251, 253, 255)');
  expect(await contrastRatio(page, '.brief-map dd', '.brief-map')).toBeGreaterThanOrEqual(4.5);
});

test('public marketing surfaces do not expose internal pre-release labels', async ({ page }) => {
  for (const route of ['/', '/about/', '/workforce/']) {
    await expectSuccessfulNavigation(page, route);
    await expect(page.locator('body')).not.toContainText(/pre-release experience|corporate identity pending owner verification/i);
  }
});
