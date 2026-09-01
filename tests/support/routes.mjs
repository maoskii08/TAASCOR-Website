import { expect } from '@playwright/test';

export const industryContextRoutes = [
  '/industries/production-throughput/',
  '/industries/distribution-fulfilment/',
  '/industries/office-service-support/',
  '/industries/facilities-site-support/',
];

export const publicExperienceRoutes = [
  '/',
  '/solutions/',
  '/solutions/workforce-staffing/',
  '/solutions/recruitment-sourcing/',
  '/solutions/payroll-coordination/',
  '/solutions/hr-administration/',
  '/solutions/facility-support/',
  '/solutions/hris-enabled-operations/',
  '/industries/',
  ...industryContextRoutes,
  '/platform/',
  '/proof/',
  '/clients/',
  '/case-studies/',
  '/about/',
  '/leadership/',
  '/locations/',
  '/contact/',
  '/insights/',
  '/resources/',
  '/workforce/',
  '/portal/',
  '/legal/privacy/',
  '/legal/terms/',
  '/legal/accessibility/',
  '/legal/anti-fraud/',
];

export const accountRoutes = [
  '/account/register.php',
  '/account/login.php',
];

export const careersCandidates = [
  '/jobs/',
  '/careers/',
  '/careers.php',
  '/jobs.php',
];

export async function firstAvailableRoute(request, candidates, label) {
  const observations = [];

  for (const candidate of candidates) {
    const response = await request.get(candidate, { maxRedirects: 0 });
    observations.push(`${candidate} -> ${response.status()}`);
    if (response.status() >= 200 && response.status() < 400) {
      return candidate;
    }
  }

  expect(
    observations,
    `${label} route is missing. Checked: ${observations.join(', ')}`,
  ).toEqual([]);
  throw new Error(`${label} route is missing`);
}

export function sameOriginPath(href, baseURL) {
  try {
    const resolved = new URL(href, baseURL);
    const origin = new URL(baseURL).origin;
    if (resolved.origin !== origin) {
      return null;
    }
    return `${resolved.pathname}${resolved.search}${resolved.hash}`;
  } catch {
    return null;
  }
}

export async function expectSuccessfulNavigation(page, route) {
  const response = await page.goto(route, { waitUntil: 'domcontentloaded' });
  expect(response, `No navigation response for ${route}`).not.toBeNull();
  expect(response.status(), `${route} returned ${response.status()}`).toBeLessThan(400);
  return response;
}
