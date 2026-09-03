import { expect, test } from '@playwright/test';
import { queryQaDatabase } from '../support/database.mjs';

const workforceBrief = {
  organization: 'Synthetic QA Workforce Studio',
  contact_name: 'Synthetic Brief Owner',
  contact_email: 'qa.workforce@example.test',
  contact_phone: '+639170000511',
  sites: 'Synthetic Laguna site and synthetic Cavite site',
  roles_needed: 'Synthetic operations associates and warehouse coordinators',
  estimated_headcount: '36',
  shift_pattern: 'Synthetic two-shift planning pattern',
  target_start_date: '2027-02-15',
  service_needs: 'Evaluate sourcing, onboarding coordination, and documented workforce reporting.',
  notes: 'Loopback-only planning record. No real employee, applicant, or client data.',
};

const privacyApplicant = {
  name: 'Synthetic Privacy Applicant',
  updatedName: 'Synthetic Privacy Applicant Updated',
  email: 'qa.privacy.applicant@example.test',
  phone: '+639170000521',
  updatedPhone: '+639170000522',
  password: 'Synthetic-privacy-password-52!',
  newPassword: 'Synthetic-privacy-password-53!',
};

const withdrawalApplicant = {
  name: 'Synthetic Withdrawal Applicant',
  email: 'qa.withdrawal.applicant@example.test',
  phone: '+639170000531',
  password: 'Synthetic-withdraw-password-54!',
};

const staff = {
  email: 'qa.staff@example.test',
  password: 'QA-only-password-43!',
};

const selectedJobSlug = 'sample-operations-associate';

test('approved TAASCOR identity assets are consistent across public and portal surfaces', async ({ page }) => {
  const faviconHrefs = [
    '/assets/brand/favicon-32.png',
    '/assets/brand/icon-192.png',
    '/assets/brand/apple-touch-icon.png',
  ];

  for (const route of ['/', '/about/', '/portal/']) {
    await page.goto(route);
    for (const href of faviconHrefs) {
      await expect(page.locator(`link[href="${href}"]`), `${route} should publish ${href}`).toHaveCount(1);
    }
  }

  await page.goto('/');
  await expect(page.locator('#hdr .lockup-mark')).toHaveAttribute('src', '/assets/brand/taascor-mark.png');
  await expect(page.locator('#hdr .lockup-name')).toHaveText('TAASCOR');
  await expect(page.locator('#hdr .lockup-legal')).toHaveText('Management & General Services Corp.');
  await expect(page.locator('#hdr .lockup svg')).toHaveCount(0);
  await expect(page.locator('#hdr .lockup-wordmark')).toHaveCount(0);
  expect(await page.locator('#hdr .lockup').evaluate((element) => ({
    background: getComputedStyle(element).backgroundColor,
    shadow: getComputedStyle(element).boxShadow,
  }))).toEqual({ background: 'rgba(0, 0, 0, 0)', shadow: 'none' });

  await page.goto('/about/');
  await expect(page.locator('.site-header .brand-mark')).toHaveAttribute('src', '/assets/brand/taascor-mark.png');
  await expect(page.locator('.site-header .brand-name')).toHaveText('TAASCOR');
  await expect(page.locator('.site-header .brand-legal')).toHaveText('Management & General Services Corp.');
  await expect(page.locator('.site-header .brand svg')).toHaveCount(0);
  await expect(page.locator('.site-header .brand-wordmark')).toHaveCount(0);
  expect(await page.locator('.site-header .brand').evaluate((element) => ({
    background: getComputedStyle(element).backgroundColor,
    shadow: getComputedStyle(element).boxShadow,
  }))).toEqual({ background: 'rgba(0, 0, 0, 0)', shadow: 'none' });

  await page.goto('/account/login.php');
  await expect(page.locator('.portal-brand-mark')).toHaveAttribute('src', '/assets/brand/taascor-mark.png');
  await expect(page.locator('.portal-brand-name')).toHaveText('TAASCOR');
  await expect(page.locator('.portal-brand-legal')).toHaveText('Management & General Services Corp.');
  await expect(page.locator('.portal-brand-wordmark')).toHaveCount(0);
  expect(await page.locator('.portal-brand').evaluate((element) => ({
    background: getComputedStyle(element).backgroundColor,
    shadow: getComputedStyle(element).boxShadow,
  }))).toEqual({ background: 'rgba(0, 0, 0, 0)', shadow: 'none' });

  expect(await page.locator('.portal-brand img').evaluateAll(
    (images) => images.every((image) => image.complete && image.naturalWidth > 0),
  )).toBe(true);
});

test('about page presents the supplied mission, vision, and five core values', async ({ page }) => {
  const response = await page.goto('/about/');
  expect(response?.status()).toBe(200);
  await expect(page.getByRole('heading', { name: 'A clear purpose for every person we place and every client we support.' })).toBeVisible();
  await expect(page.getByText('To be a leading job outsourcing provider in the industry by giving excellent and varied services to our clients.')).toBeVisible();
  await expect(page.getByText('To continuously support our clients in their outsourcing needs by providing well-trained, skilled, and motivated people.')).toBeVisible();
  await expect(page.locator('.values-list > li')).toHaveCount(5);
  for (const value of ['Quality', 'Service', 'Results Oriented', 'Responsibility', 'Passion']) {
    await expect(page.getByRole('heading', { name: value, exact: true })).toBeVisible();
  }
});

test('legacy company portfolio content is integrated across the immersive public experience', async ({ page }) => {
  await page.goto('/');
  await expect(page.locator('#company')).toBeVisible();
  await expect(page.locator('#company .company-values span')).toHaveCount(5);
  await expect(page.getByRole('link', { name: /Leadership & org chart/i })).toHaveAttribute('href', '/leadership/');

  await page.goto('/leadership/');
  await expect(page.locator('.leader-card')).toHaveCount(7);
  await expect(page.getByRole('heading', { name: 'Ernesto P. Villanueva' })).toBeVisible();
  await expect(page.locator('.org-chart-media img')).toHaveAttribute('src', '/assets/img/organizational-chart.webp');
  await expect(page.locator('.org-chart-media img')).toHaveJSProperty('complete', true);
  await expect(page.locator('.org-directory')).toContainText('Branch operations');

  await page.goto('/locations/');
  await expect(page.locator('.office-card')).toHaveCount(7);
  await expect(page.getByRole('heading', { name: 'Main Office — San Pedro' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Bulacan Branch' })).toBeVisible();

  await page.goto('/clients/');
  await expect(page.locator('.client-portfolio-card')).toHaveCount(27);
  await expect(page.locator('.client-portfolio-card img')).toHaveCount(27);
  await expect(page.getByRole('heading', { name: 'First Sumiden Circuits Inc.' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Shinsei Printing' })).toBeVisible();
  await page.locator('.client-portfolio-card').last().scrollIntoViewIfNeeded();
  await expect.poll(async () => page.locator('.client-portfolio-card img').evaluateAll(
    (images) => images.filter((image) => !image.complete || image.naturalWidth === 0).length,
  )).toBe(0);

  await page.goto('/solutions/');
  await expect(page.locator('.service-spectrum article')).toHaveCount(6);
  await expect(page.getByRole('heading', { name: 'Warehousing & 3PL logistics' })).toBeVisible();
  await expect(page.getByRole('heading', { name: 'Electronics manufacturing' })).toBeVisible();
});

function requireLoopback(baseURL) {
  const host = new URL(baseURL).hostname;
  expect(
    ['127.0.0.1', 'localhost', '::1'],
    'Synthetic mutations are allowed only on the managed loopback server.',
  ).toContain(host);
}

async function submitAndWait(page, submitter) {
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    submitter.click(),
  ]);
}

async function registerApplicant(page, profile) {
  await page.goto('/account/register.php');
  await page.locator('[name="full_name"]').fill(profile.name);
  await page.locator('[name="email"]').fill(profile.email);
  await page.locator('[name="phone"]').fill(profile.phone);
  await page.locator('[name="password"]').fill(profile.password);
  await page.locator('[name="password_confirmation"]').fill(profile.password);
  await page.locator('[name="privacy_acknowledged"]').check();
  await submitAndWait(page, page.getByRole('button', { name: 'Create secure account' }));
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)/);
}

async function loginApplicant(page, profile, password = profile.password) {
  await page.goto('/account/login.php');
  await page.locator('[name="email"]').fill(profile.email);
  await page.locator('[name="password"]').fill(password);
  await submitAndWait(page, page.getByRole('button', { name: 'Sign in securely' }));
}

async function loginStaff(page) {
  await page.goto('/staff/login.php');
  await page.locator('[name="email"]').fill(staff.email);
  await page.locator('[name="password"]').fill(staff.password);
  await submitAndWait(page, page.getByRole('button', { name: 'Enter staff workspace' }));
  await expect(page).toHaveURL(/\/staff\/(?:index\.php)?(?:[?#]|$)/);
}

async function createSubmittedApplication(page, profile) {
  await page.goto(`/apply/?job=${selectedJobSlug}`);
  await page.locator('[name="full_name"]').fill(profile.name);
  await page.locator('[name="phone"]').fill(profile.phone);
  await page.locator('[name="current_city"]').fill('Synthetic QA City');
  await page.locator('[name="eligibility_confirmed"]').check();
  await page.locator('[name="privacy_accepted"]').check();
  await submitAndWait(page, page.getByRole('button', { name: 'Save and continue' }));

  const applicationId = new URL(page.url()).searchParams.get('id');
  expect(applicationId).toMatch(/^\d+$/);
  await page.locator('[name="experience_summary"]').fill(
    'Synthetic experience used only to verify governed application-state transitions.',
  );
  await page.locator('[name="certified"]').check();
  await submitAndWait(
    page,
    page.locator('button[name="action"][value="submit"]'),
  );
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)/);
  return Number(applicationId);
}

test('workforce brief renders, rejects missing CSRF, and persists an isolated receipt', async ({ page, baseURL }) => {
  requireLoopback(baseURL);

  await page.goto('/workforce/');
  await expect(page.getByRole('heading', { level: 1 })).toContainText('operating truth');
  await expect(page.locator('form.workforce-form')).toBeVisible();
  await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', 'noindex,nofollow');
  await expect(page.locator('body')).toContainText('Collection safeguard');

  const missingCsrf = await page.request.post('/workforce/', {
    form: {
      ...workforceBrief,
      privacy_accepted: '1',
    },
    maxRedirects: 0,
  });
  expect(missingCsrf.status(), 'Workforce submission without CSRF must be rejected').toBe(419);

  await page.goto('/workforce/');
  for (const [name, value] of Object.entries(workforceBrief)) {
    await page.locator(`[name="${name}"]`).fill(value);
  }
  await page.locator('[name="privacy_accepted"]').check();
  await page.locator('[name="website"]').fill('synthetic-bot-trap.example', { force: true });
  const [spamResponse] = await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.getByRole('button', { name: 'Record workforce brief' }).click(),
  ]);
  expect(spamResponse.status(), 'The honeypot path must reject the request').toBe(400);
  await expect(page.locator('body')).toContainText('could not be accepted');
  expect(queryQaDatabase(
    `SELECT COUNT(*) AS total FROM workforce_briefs WHERE contact_email = :email`,
    { email: workforceBrief.contact_email },
  )[0].total).toBe(0);
  expect(queryQaDatabase(
    `SELECT COUNT(*) AS total FROM audit_events WHERE event_type = 'workforce_brief.spam_rejected'`,
  )[0].total).toBe(1);

  await page.goto('/workforce/');
  for (const [name, value] of Object.entries(workforceBrief)) {
    await page.locator(`[name="${name}"]`).fill(value);
  }
  await page.locator('[name="privacy_accepted"]').check();
  await submitAndWait(page, page.getByRole('button', { name: 'Record workforce brief' }));

  await expect(page).toHaveURL(/\/workforce\/\?submitted=1$/);
  await expect(page.getByRole('heading', { name: 'Your workforce brief is recorded.' })).toBeVisible();
  const receiptCode = (await page.locator('.receipt-code').innerText()).trim();
  expect(receiptCode).toMatch(/^BRIEF \/ WB-\d{6}$/);

  const rows = queryQaDatabase(
    `SELECT id, organization, contact_name, contact_email, contact_phone, sites,
            roles_needed, estimated_headcount, shift_pattern, target_start_date,
            service_needs, notes, privacy_notice_version, status,
            privacy_accepted_at, submitted_at
       FROM workforce_briefs
      WHERE contact_email = :email
      ORDER BY id DESC LIMIT 1`,
    { email: workforceBrief.contact_email },
  );
  expect(rows).toHaveLength(1);
  expect(rows[0]).toMatchObject({
    organization: workforceBrief.organization,
    contact_name: workforceBrief.contact_name,
    contact_email: workforceBrief.contact_email,
    contact_phone: workforceBrief.contact_phone,
    sites: workforceBrief.sites,
    roles_needed: workforceBrief.roles_needed,
    estimated_headcount: 36,
    shift_pattern: workforceBrief.shift_pattern,
    target_start_date: workforceBrief.target_start_date,
    service_needs: workforceBrief.service_needs,
    notes: workforceBrief.notes,
    privacy_notice_version: 'draft-workforce-2026-09-01-qa',
    status: 'submitted',
  });
  expect(rows[0].privacy_accepted_at).toMatch(/^\d{4}-\d{2}-\d{2} /);
  expect(rows[0].submitted_at).toMatch(/^\d{4}-\d{2}-\d{2} /);
  expect(receiptCode.endsWith(String(rows[0].id).padStart(6, '0'))).toBeTruthy();

  const auditRows = queryQaDatabase(
    `SELECT event_type, entity_type, entity_id
       FROM audit_events
      WHERE event_type = 'workforce_brief.submitted' AND entity_id = :id`,
    { id: rows[0].id },
  );
  expect(auditRows).toEqual([
    {
      event_type: 'workforce_brief.submitted',
      entity_type: 'workforce_brief',
      entity_id: rows[0].id,
    },
  ]);
});

test('job catalogue filters, empty state, and clean-route detail metadata remain coherent', async ({ page }) => {
  await page.goto('/jobs/?function=Logistics&shift=Sample%20rotating%20schedule');

  await expect(page.locator('[name="function"]')).toHaveValue('Logistics');
  await expect(page.locator('[name="shift"]')).toHaveValue('Sample rotating schedule');
  await expect(page.locator('.jobs-heading')).toContainText('1 role shown');
  await expect(page.locator('article.job-card')).toHaveCount(1);
  await expect(page.locator('article.job-card')).toContainText('Sample Warehouse Coordinator');
  await expect(page.locator('article.job-card')).toContainText('Logistics');
  await expect(page.locator('article.job-card')).toContainText('Sample rotating schedule');
  await expect(page.locator('article.job-card')).not.toContainText('Sample Operations Associate');

  await page.goto('/jobs/?q=warehouse&location=Laguna&type=Full-time&sort=relevant');
  await expect(page.locator('[name="q"]')).toHaveValue('warehouse');
  await expect(page.locator('[name="location"]')).toHaveValue('Laguna');
  await expect(page.locator('[name="type"]')).toHaveValue('Full-time');
  await expect(page.locator('[name="sort"]')).toHaveValue('relevant');
  await expect(page.locator('article.job-card')).toHaveCount(1);
  await expect(page.locator('article.job-card')).toContainText('Sample Warehouse Coordinator');
  await expect(page.getByRole('link', { name: 'Clear filters' })).toHaveAttribute('href', '/jobs/');

  await page.goto('/jobs/?q=role-that-does-not-exist');
  await expect(page.locator('.jobs-heading')).toContainText('0 roles shown');
  await expect(page.getByRole('heading', { name: 'No published role matches this search.' })).toBeVisible();
  await expect(page.getByRole('link', { name: 'Show all published roles' })).toHaveAttribute('href', '/jobs/');

  await page.goto('/jobs/sample-warehouse-coordinator/');
  await expect(page).toHaveTitle(/Sample Warehouse Coordinator.*TAASCOR/);
  await expect(page.locator('meta[name="description"]')).toHaveAttribute(
    'content',
    'Synthetic warehouse coordination role for local validation.',
  );
  await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', 'noindex,nofollow');
  await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
    'href',
    /\/jobs\/sample-warehouse-coordinator\/$/,
  );
  const facts = page.locator('dl.job-facts');
  await expect(facts).toContainText('TAASCOR Demo Workforce Partner');
  await expect(facts).toContainText('Laguna');
  await expect(facts).toContainText('Full-time');
  await expect(facts).toContainText('Logistics');
  await expect(facts).toContainText('Sample rotating schedule');
  await expect(facts).toContainText('2');
  await expect(facts).toContainText('Published');
  await expect(page.locator('script[type="application/ld+json"]')).toHaveCount(0);
  await expect(page.locator('.fraud-notice')).toContainText('Recruitment safety');
  await expect(page.locator('.related-job-card')).toHaveCount(2);
  await expect(page.getByRole('link', { name: 'Start application' })).toHaveAttribute(
    'href',
    '/apply/sample-warehouse-coordinator/',
  );

  await page.goto('/jobs/sample-warehouse-coordinator/?job=sample-operations-associate');
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Sample Warehouse Coordinator');
});

test('privacy acknowledgement, settings, and session-version controls remain governed', async ({ browser, page, baseURL }) => {
  requireLoopback(baseURL);

  await page.goto('/account/register.php');
  const initialCookie = (await page.context().cookies()).find(
    (cookie) => cookie.name === 'taascor_qa_session',
  );
  expect(initialCookie).toBeTruthy();

  await page.locator('[name="full_name"]').fill(privacyApplicant.name);
  await page.locator('[name="email"]').fill(privacyApplicant.email);
  await page.locator('[name="phone"]').fill(privacyApplicant.phone);
  await page.locator('[name="password"]').fill(privacyApplicant.password);
  await page.locator('[name="password_confirmation"]').fill(privacyApplicant.password);
  await page.locator('[name="privacy_acknowledged"]').check();
  await submitAndWait(page, page.getByRole('button', { name: 'Create secure account' }));

  const registeredCookie = (await page.context().cookies()).find(
    (cookie) => cookie.name === 'taascor_qa_session',
  );
  expect(registeredCookie).toBeTruthy();
  expect(registeredCookie.value, 'Registration must rotate the pre-authentication session ID')
    .not.toBe(initialCookie.value);
  expect(registeredCookie.httpOnly).toBeTruthy();
  expect(registeredCookie.sameSite).toBe('Lax');

  await page.goto('/account/settings.php');
  await expect(page.getByRole('heading', { level: 1 })).toHaveText('Account and privacy controls');
  await expect(page.locator('table tbody')).toContainText('Account');
  await expect(page.locator('table tbody')).toContainText('2026-09-01-qa');

  const acknowledgements = queryQaDatabase(
    `SELECT pa.notice_scope, pa.notice_version, pa.acknowledged_at
       FROM privacy_acknowledgements pa
       JOIN users u ON u.id = pa.user_id
      WHERE u.email = :email`,
    { email: privacyApplicant.email },
  );
  expect(acknowledgements).toHaveLength(1);
  expect(acknowledgements[0]).toMatchObject({
    notice_scope: 'account',
    notice_version: '2026-09-01-qa',
  });
  expect(acknowledgements[0].acknowledged_at).toMatch(/^\d{4}-\d{2}-\d{2} /);

  const profileForm = page.locator('form').filter({
    has: page.locator('input[name="action"][value="profile"]'),
  });
  await profileForm.locator('[name="full_name"]').fill(privacyApplicant.updatedName);
  await profileForm.locator('[name="phone"]').fill(privacyApplicant.updatedPhone);
  await submitAndWait(page, profileForm.getByRole('button', { name: 'Update profile' }));
  await expect(page.getByRole('status')).toContainText('Account contact details updated.');
  await expect(page.locator('[name="full_name"]')).toHaveValue(privacyApplicant.updatedName);
  await expect(page.locator('[name="phone"]')).toHaveValue(privacyApplicant.updatedPhone);

  const parallelContext = await browser.newContext({ baseURL });
  const parallelPage = await parallelContext.newPage();
  await loginApplicant(parallelPage, privacyApplicant);
  await expect(parallelPage).toHaveURL(/\/applicant\//);

  await page.goto('/account/settings.php');
  const beforePasswordCookie = (await page.context().cookies()).find(
    (cookie) => cookie.name === 'taascor_qa_session',
  );
  const passwordForm = page.locator('form').filter({
    has: page.locator('input[name="action"][value="password"]'),
  });
  await passwordForm.locator('[name="current_password"]').fill(privacyApplicant.password);
  await passwordForm.locator('[name="new_password"]').fill(privacyApplicant.newPassword);
  await passwordForm.locator('[name="new_password_confirmation"]').fill(privacyApplicant.newPassword);
  await submitAndWait(page, passwordForm.getByRole('button', { name: 'Change password' }));
  await expect(page.getByRole('status')).toContainText('Password changed');

  const afterPasswordCookie = (await page.context().cookies()).find(
    (cookie) => cookie.name === 'taascor_qa_session',
  );
  expect(afterPasswordCookie.value, 'Password change must rotate the active session ID')
    .not.toBe(beforePasswordCookie.value);

  await parallelPage.goto('/account/settings.php');
  await expect(parallelPage).toHaveURL(/\/account\/login\.php\?next=/);
  await parallelContext.close();

  const userRows = queryQaDatabase(
    'SELECT full_name, phone, session_version FROM users WHERE email = :email',
    { email: privacyApplicant.email },
  );
  expect(userRows).toEqual([{
    full_name: privacyApplicant.updatedName,
    phone: privacyApplicant.updatedPhone,
    session_version: 2,
  }]);

  const freshContext = await browser.newContext({ baseURL });
  const freshPage = await freshContext.newPage();
  await loginApplicant(freshPage, privacyApplicant, privacyApplicant.password);
  await expect(freshPage).toHaveURL(/\/account\/login\.php/);
  await expect(freshPage.getByRole('alert')).toContainText('Email or password is incorrect.');

  await freshPage.locator('[name="password"]').fill(privacyApplicant.newPassword);
  await submitAndWait(freshPage, freshPage.getByRole('button', { name: 'Sign in securely' }));
  await expect(freshPage).toHaveURL(/\/applicant\//);
  await freshContext.close();
});

test('forbidden staff transition is rejected and applicant withdrawal is persisted', async ({ browser, page, baseURL }) => {
  requireLoopback(baseURL);
  await registerApplicant(page, withdrawalApplicant);
  const applicationId = await createSubmittedApplication(page, withdrawalApplicant);

  const staffContext = await browser.newContext({ baseURL });
  const staffPage = await staffContext.newPage();
  await loginStaff(staffPage);
  await staffPage.goto(`/staff/application.php?id=${applicationId}`);
  const statusForm = staffPage.locator('form').filter({
    has: staffPage.locator('input[name="action"][value="update_status"]'),
  });
  await expect(statusForm.locator('[name="status"]')).not.toContainText('Hired');
  const csrf = await statusForm.locator('[name="_csrf"]').inputValue();
  const forbidden = await staffContext.request.post('/staff/application.php', {
    form: {
      _csrf: csrf,
      id: String(applicationId),
      action: 'update_status',
      status: 'hired',
      public_note: 'Synthetic forbidden direct-transition probe.',
    },
    maxRedirects: 0,
  });
  expect(forbidden.status()).toBe(303);
  expect(forbidden.headers().location).toBe(`/staff/application.php?id=${applicationId}`);

  await staffPage.goto(`/staff/application.php?id=${applicationId}`);
  await expect(staffPage.getByRole('status')).toContainText('status transition is not allowed');
  expect(queryQaDatabase(
    'SELECT current_status FROM applications WHERE id = :id',
    { id: applicationId },
  )).toEqual([{ current_status: 'submitted' }]);
  await staffContext.close();

  await page.goto('/applicant/');
  const applicationPanel = page.locator('article.application-panel').filter({
    hasText: `Application #${applicationId}`,
  });
  await applicationPanel.locator('summary').click();
  await applicationPanel.locator('[name="reason"]').fill(
    'Synthetic applicant chose to withdraw during loopback QA.',
  );
  page.once('dialog', (dialog) => dialog.accept());
  await submitAndWait(
    page,
    applicationPanel.getByRole('button', { name: 'Confirm withdrawal' }),
  );

  await expect(page.getByRole('status')).toContainText('application was withdrawn');
  await expect(applicationPanel.locator('.status-chip')).toHaveText('Withdrawn');
  await expect(applicationPanel).toContainText('Application withdrawn by applicant');
  await expect(applicationPanel.locator('details.withdraw-panel')).toHaveCount(0);

  expect(queryQaDatabase(
    'SELECT current_status FROM applications WHERE id = :id',
    { id: applicationId },
  )).toEqual([{ current_status: 'withdrawn' }]);
  const history = queryQaDatabase(
    `SELECT status, note_public
       FROM application_status_history
      WHERE application_id = :id
      ORDER BY id ASC`,
    { id: applicationId },
  );
  expect(history.map((event) => event.status)).toEqual(['draft', 'submitted', 'withdrawn']);
  expect(history.at(-1).note_public).toContain(
    'Synthetic applicant chose to withdraw during loopback QA.',
  );
});

test('router protects internal files and default sitemap remains empty while every public route is noindex', async ({ request }) => {
  const restrictedPaths = [
    '/.env',
    '/%2eenv',
    '/.git/config',
    '/%2egit/config',
    '/.claude/launch.json',
    '/app/config.php',
    '/app/.env.example',
    '/database/schema.sqlite.sql',
    '/storage/private/',
    '/Backups/',
    '/Planning/TAASCOR_WEBSITE_INTEGRATION_PLAN_2026-09-01.md',
    '/Audit/AUDIT_2026-09-01.md',
    '/tests/fixtures/synthetic-resume.pdf',
    '/scripts/migrate.php',
    '/node_modules/',
    '/composer.json',
    '/package.json',
    '/package-lock.json',
    '/release.zip',
    '/release.tar.gz',
    '/index.php.bak',
    '/index.html.old',
    '/config.php~',
    '/playwright.config.js',
    '/assets/.private.css',
    '/assets/%2eprivate.css',
    '/assets%2f..%2fapp%2fconfig.php',
    '/assets%5c..%5capp%5cconfig.php',
    '/GIT~1/config',
    '/CLAUDE~1/launch.json',
    '/HTACCE~1',
    '/GITATT~1',
    '/GITIGN~1',
    '/APP~1/config.php',
    '/DATABA~1/schema.sqlite.sql',
    '/NODE_M~1/.package-lock.json',
    '/COMPOS~1.JSON',
    '/PACKAG~1.JSON',
    '/PACKAG~1.JSO',
    '/PACKAG~2.JSO',
    '/PLAYWR~1.JS',
  ];

  for (const route of restrictedPaths) {
    const response = await request.get(route, { maxRedirects: 0 });
    expect(response.status(), `${route} must not be served from the public document root`).toBe(404);
    expect(response.headers()['content-type'] || '', `${route} should use the branded 404 response`)
      .toMatch(/text\/html/i);
    expect(await response.text()).toMatch(/Page Not Found|Route unavailable/i);
  }

  for (const route of ['/qa%00probe', '/qa%01probe', '/qa%1fprobe', '/qa%7fprobe']) {
    const response = await request.get(route, { maxRedirects: 0 });
    expect(
      response.status(),
      `${route} must be rejected before filesystem resolution as a malformed control-character path`,
    ).toBe(400);
    expect(await response.text()).toMatch(/Bad Request|Request rejected/i);
  }

  const sitemapResponse = await request.get('/sitemap.xml');
  expect(sitemapResponse.status()).toBe(200);
  expect(sitemapResponse.headers()['content-type'] || '').toMatch(/application\/xml/i);
  const sitemap = await sitemapResponse.text();
  const locations = [...sitemap.matchAll(/<loc>([^<]+)<\/loc>/g)].map((match) => match[1]);
  expect(locations, 'The default-disabled indexing policy must emit no sitemap locations').toEqual([]);

  const publicRoutes = [
    '/',
    '/solutions/',
    '/solutions/workforce-staffing/',
    '/solutions/recruitment-sourcing/',
    '/solutions/payroll-coordination/',
    '/solutions/hr-administration/',
    '/solutions/facility-support/',
    '/solutions/hris-enabled-operations/',
    '/jobs/',
    '/careers/',
    '/platform/',
    '/proof/',
    '/about/',
    '/industries/',
    '/industries/production-throughput/',
    '/industries/distribution-fulfilment/',
    '/industries/office-service-support/',
    '/industries/facilities-site-support/',
    '/locations/',
    '/clients/',
    '/case-studies/',
    '/leadership/',
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
  for (const route of publicRoutes) {
    const response = await request.get(route, { maxRedirects: 0 });
    expect(response.status(), `Noindex route ${route} must resolve`).toBe(200);
    expect(response.headers()['x-robots-tag'] || '', `${route} must send noindex`)
      .toMatch(/noindex/i);
    expect(await response.text(), `${route} must explicitly declare noindex`)
      .toMatch(/<meta\s+[^>]*name=["']robots["'][^>]*content=["'][^"']*noindex/i);
  }

  const lastModified = [...sitemap.matchAll(/<lastmod>([^<]+)<\/lastmod>/g)].map((match) => match[1]);
  expect(lastModified).toEqual([]);
});
