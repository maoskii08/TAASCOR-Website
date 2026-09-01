import { stat } from 'node:fs/promises';
import path from 'node:path';
import { expect, test } from '@playwright/test';
import { queryQaDatabase } from '../support/database.mjs';
import { projectRoot } from '../support/project.mjs';

const applicant = {
  name: 'Synthetic QA Applicant',
  email: 'qa.applicant@example.test',
  phone: '+639170000143',
  password: 'Synthetic-only-password-42!',
};

const secondApplicant = {
  name: 'Synthetic QA Applicant Two',
  email: 'qa.applicant.two@example.test',
  phone: '+639170000144',
  password: 'Synthetic-only-password-44!',
};

const staff = {
  email: 'qa.staff@example.test',
  password: 'QA-only-password-43!',
};

const selectedJobSlug = 'sample-operations-associate';
const selectedJobTitle = 'Sample Operations Associate';
const resumePath = path.join(projectRoot, 'tests', 'fixtures', 'synthetic-resume.pdf');

function requireLoopback(baseURL) {
  const host = new URL(baseURL).hostname;
  expect(
    ['127.0.0.1', 'localhost', '::1'],
    'Synthetic account and application mutations are allowed only on the managed loopback server.',
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
  const submit = page.locator('button[type="submit"], input[type="submit"]').first();
  await submitAndWait(page, submit);
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)|\/apply\//);
}

async function loginApplicant(page, profile) {
  await page.goto('/account/login.php');
  await page.locator('[name="email"]').fill(profile.email);
  await page.locator('[name="password"]').fill(profile.password);
  await submitAndWait(page, page.locator('button[type="submit"], input[type="submit"]').first());
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)|\/apply\//);
}

async function expectNoHorizontalOverflow(page, label) {
  const dimensions = await page.evaluate(() => ({
    clientWidth: document.documentElement.clientWidth,
    scrollWidth: document.documentElement.scrollWidth,
  }));
  expect(
    dimensions.scrollWidth,
    `${label} horizontally overflows ${dimensions.clientWidth}px`,
  ).toBeLessThanOrEqual(dimensions.clientWidth + 1);
}

test('synthetic applicant, application, role isolation, and staff review flow', async ({ browser, page, baseURL }) => {
  requireLoopback(baseURL);
  await page.setViewportSize({ width: 390, height: 844 });

  await test.step('register an isolated synthetic applicant', async () => {
    await registerApplicant(page, applicant);
    await expect(page.locator('body')).toContainText(applicant.name);
  });

  await test.step('prove the applicant role cannot enter the staff workspace', async () => {
    const response = await page.goto('/staff/index.php');
    expect(response).not.toBeNull();
    expect([403], 'An authenticated applicant must receive an authorization failure').toContain(response.status());
  });

  await test.step('exercise applicant login with the persisted synthetic account', async () => {
    await page.context().clearCookies();
    await loginApplicant(page, applicant);
  });

  let applicationId;
  await test.step('create a job-bound application draft', async () => {
    await page.goto(`/apply/?job=${selectedJobSlug}`);
    await expect(page.locator('body')).toContainText(selectedJobTitle);
    await expect(page.locator('[name="job"]')).toHaveValue(selectedJobSlug);
    await page.locator('[name="full_name"]').fill(applicant.name);
    await page.locator('[name="phone"]').fill(applicant.phone);
    await page.locator('[name="current_city"]').fill('Synthetic QA City');
    await page.locator('[name="eligibility_confirmed"]').check();
    await page.locator('[name="privacy_accepted"]').check();
    await expectNoHorizontalOverflow(page, 'Application stage one');
    const applicationForm = page.locator('form.portal-form').filter({
      has: page.locator('input[name="job"]'),
    });
    await submitAndWait(page, applicationForm.locator('button[type="submit"]'));
    await expect(page).toHaveURL(/\/apply\/step2\.php\?id=\d+/);
    await expectNoHorizontalOverflow(page, 'Application stage two');
    applicationId = new URL(page.url()).searchParams.get('id');
    expect(applicationId).toMatch(/^\d+$/);
  });

  await test.step('save the draft, sign out, and resume it after a fresh sign-in', async () => {
    const savedSummary = 'Synthetic draft retained only to verify secure save and resume behavior.';
    await page.locator('[name="experience_summary"]').fill(savedSummary);
    await submitAndWait(page, page.locator('button[name="action"][value="save"]'));
    await expect(page.getByRole('status')).toContainText('Draft saved.');

    await submitAndWait(page, page.getByRole('button', { name: 'Sign out' }));
    await expect(page).toHaveURL(/\/$/);
    const protectedResponse = await page.goto('/applicant/');
    expect(protectedResponse).not.toBeNull();
    await expect(page).toHaveURL(/\/account\/login\.php\?next=/);

    await loginApplicant(page, applicant);
    const draftPanel = page.locator('article.application-panel').filter({ hasText: selectedJobTitle });
    await expect(draftPanel).toContainText('Draft');
    await draftPanel.getByRole('link', { name: 'Continue application' }).click();
    await expect(page).toHaveURL(new RegExp(`/apply/step2\\.php\\?id=${applicationId}$`));
    await expect(page.locator('[name="experience_summary"]')).toHaveValue(savedSummary);
  });

  await test.step('upload a synthetic resume and submit the application', async () => {
    await page.locator('[name="experience_summary"]').fill(
      'Synthetic operations experience created only for the isolated local QA workflow.',
    );
    await page.locator('[name="resume"]').setInputFiles({
      name: 'forged-resume.pdf',
      mimeType: 'application/pdf',
      buffer: Buffer.from('This is not a PDF document.'),
    });
    await submitAndWait(page, page.locator('button[name="action"][value="save"]'));
    await expect(page.getByRole('alert')).toContainText('not a valid PDF');
    await expect(page.locator('.document-summary')).toHaveCount(0);

    await page.locator('[name="resume"]').setInputFiles({
      name: 'unsupported-resume.txt',
      mimeType: 'text/plain',
      buffer: Buffer.from('Synthetic unsupported upload.'),
    });
    await submitAndWait(page, page.locator('button[name="action"][value="save"]'));
    await expect(page.getByRole('alert')).toContainText('Use PDF, DOC, or DOCX format.');
    await expect(page.locator('.document-summary')).toHaveCount(0);

    await page.locator('[name="resume"]').setInputFiles(resumePath);
    await page.locator('[name="certified"]').check();
    const submit = page.locator(
      'button[name="action"][value="submit"], input[name="action"][value="submit"]',
    );
    await submitAndWait(page, submit);
    await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)/);
    await expect(page.locator('body')).toContainText(selectedJobTitle);
    await expect(page.locator('body')).toContainText(/submitted/i);
    await expect(page.locator('.submission-confirmation')).toContainText(
      `TAASCOR-APP-${String(applicationId).padStart(6, '0')}`,
    );
    await expect(page.locator('.submission-confirmation')).toContainText(
      'No response-time commitment is published',
    );

    const uploadDirectory = path.join(projectRoot, 'tests', '.artifacts', 'uploads');
    const documents = queryQaDatabase(
      `SELECT storage_name, size_bytes
         FROM application_documents WHERE application_id = :application_id`,
      { application_id: Number(applicationId) },
    );
    expect(documents).toHaveLength(1);
    const uploadedFile = await stat(path.join(uploadDirectory, documents[0].storage_name));
    expect(uploadedFile.size).toBe(documents[0].size_bytes);
    expect(uploadedFile.size).toBeGreaterThan(20);
  });

  await test.step('deny a second applicant access to the first applicant draft route', async () => {
    const secondContext = await browser.newContext({ baseURL });
    const secondPage = await secondContext.newPage();
    await registerApplicant(secondPage, secondApplicant);
    const response = await secondPage.goto(`/apply/step2.php?id=${applicationId}`);
    expect(response).not.toBeNull();
    const deniedByStatus = [403, 404].includes(response.status());
    const deniedByRedirect = !new URL(secondPage.url()).pathname.endsWith('/apply/step2.php');
    expect(
      deniedByStatus || deniedByRedirect,
      'A different applicant must not open another applicant’s application step.',
    ).toBeTruthy();
    await secondContext.close();
  });

  await test.step('staff can review the submitted application but cannot enter applicant-only space', async () => {
    const staffContext = await browser.newContext({ baseURL });
    const staffPage = await staffContext.newPage();
    await staffPage.goto('/staff/login.php');
    await staffPage.locator('[name="email"]').fill(staff.email);
    await staffPage.locator('[name="password"]').fill(staff.password);
    await submitAndWait(staffPage, staffPage.locator('button[type="submit"], input[type="submit"]').first());
    await expect(staffPage).toHaveURL(/\/staff\/(?:index\.php)?(?:[?#]|$)/);

    await staffPage.goto('/staff/applications.php');
    await expect(staffPage.locator('body')).toContainText(selectedJobTitle);
    await expect(staffPage.locator('body')).toContainText(applicant.name);
    const applicationLink = staffPage.locator(`a[href*="application.php?id=${applicationId}"]`).first();
    await expect(applicationLink).toBeVisible();
    await applicationLink.click();
    await expect(staffPage).toHaveURL(new RegExp(`/staff/application\\.php\\?id=${applicationId}`));
    await expect(staffPage.locator('body')).toContainText(/submitted/i);

    const statusForm = staffPage.locator('form').filter({
      has: staffPage.locator('input[name="action"][value="update_status"]'),
    });
    await statusForm.locator('[name="status"]').selectOption('reviewing');
    await statusForm.locator('[name="public_note"]').fill(
      'Synthetic QA review is in progress.',
    );
    await submitAndWait(staffPage, statusForm.locator('button[type="submit"]'));
    await expect(staffPage.locator('body')).toContainText(/reviewing/i);
    await expect(staffPage.locator('body')).toContainText('Synthetic QA review is in progress.');

    const taskForm = staffPage.locator('form').filter({
      has: staffPage.locator('input[name="action"][value="add_task"]'),
    });
    await taskForm.locator('[name="title"]').fill('Synthetic orientation readiness check');
    await taskForm.locator('[name="applicant_visible"]').check();
    await submitAndWait(staffPage, taskForm.locator('button[type="submit"]'));
    await expect(staffPage.locator('body')).toContainText('Synthetic orientation readiness check');

    const applicantAreaResponse = await staffPage.goto('/applicant/index.php');
    expect(applicantAreaResponse).not.toBeNull();
    expect(applicantAreaResponse.status()).toBe(403);
    await staffContext.close();
  });

  await test.step('applicant receives the human-reviewed status and completes the visible task', async () => {
    await page.goto('/applicant/index.php');
    await expect(page.locator('body')).toContainText(/reviewing/i);
    await expect(page.locator('body')).toContainText('Synthetic QA review is in progress.');
    const taskForm = page.locator('form[action="/applicant/task.php"]').filter({
      hasText: 'Mark complete',
    });
    await expect(taskForm).toContainText('Mark complete');
    await submitAndWait(page, taskForm.locator('button[type="submit"]'));
    await expect(page.locator('body')).toContainText('Task updated.');
    await expect(page.locator('body')).toContainText('Reopen');
  });

  const databaseFile = await stat(path.join(projectRoot, 'tests', '.artifacts', 'qa.sqlite'));
  expect(databaseFile.size).toBeGreaterThan(0);
});
