import { execFileSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import { readFile, readdir } from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';
import { expect, test } from '@playwright/test';
import { queryQaDatabase } from '../support/database.mjs';
import { phpRuntimeArgs } from '../support/php.mjs';
import { projectRoot } from '../support/project.mjs';

const staff = {
  email: 'qa.staff@example.test',
  password: 'QA-only-password-43!',
};

const snapshotApplicant = {
  name: 'Synthetic Snapshot Applicant',
  email: 'qa.snapshot.applicant@example.test',
  phone: '+639170000541',
  password: 'Synthetic-snapshot-password-55!',
};

const taskApplicant = {
  name: 'Synthetic Task Boundary Applicant',
  email: 'qa.task-boundary@example.test',
  phone: '+639170000551',
  password: 'Synthetic-task-password-56!',
};

const resumeApplicant = {
  name: 'Synthetic Resume Invariant Applicant',
  email: 'qa.resume-invariant@example.test',
  phone: '+639170000561',
  password: 'Synthetic-resume-password-57!',
};

const resumePath = path.join(projectRoot, 'tests', 'fixtures', 'synthetic-resume.pdf');
const qaUploadDirectory = path.join(projectRoot, 'tests', '.artifacts', 'uploads');

const originalJob = {
  slug: 'synthetic-snapshot-contract-role',
  title: 'Synthetic Snapshot Contract Role',
  company: 'Synthetic QA Employer',
  location: 'Original QA Location',
  employment_type: 'Full-time',
  function_area: 'Contract testing',
  shift_pattern: 'Original synthetic shift',
  summary: 'Synthetic role used only to verify immutable application terms.',
  description: 'Validate a fictional workflow with isolated, non-production information.',
  requirements: 'Review the original synthetic role terms before submitting an application.',
  openings: '1',
  closing_date: '',
  status: 'published',
};

const revisedJob = {
  ...originalJob,
  title: 'Synthetic Snapshot Contract Role — Revised',
  location: 'Revised QA Location',
  shift_pattern: 'Revised synthetic evening shift',
  requirements: 'Review and explicitly confirm these revised synthetic role terms before submitting.',
};

const postSubmissionJob = {
  ...revisedJob,
  title: 'Synthetic Snapshot Contract Role — Later Revision',
  location: 'Later Live QA Location',
  shift_pattern: 'Later live synthetic shift',
  requirements: 'These later synthetic terms must not rewrite a submitted application snapshot.',
};

function requireLoopback(baseURL) {
  expect(
    ['127.0.0.1', 'localhost', '::1'],
    'Synthetic lifecycle mutations are allowed only on the managed loopback server.',
  ).toContain(new URL(baseURL).hostname);
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

async function loginStaff(page) {
  await page.goto('/staff/login.php');
  await page.locator('[name="email"]').fill(staff.email);
  await page.locator('[name="password"]').fill(staff.password);
  await submitAndWait(page, page.getByRole('button', { name: 'Enter staff workspace' }));
  await expect(page).toHaveURL(/\/staff\/(?:index\.php)?(?:[?#]|$)/);
}

function jobForm(page) {
  return page.locator('form.portal-form').filter({
    has: page.locator('input[name="action"][value="save_job"]'),
  });
}

async function fillJobForm(page, job) {
  const form = jobForm(page);
  for (const name of [
    'title', 'slug', 'company', 'location', 'employment_type', 'function_area',
    'shift_pattern', 'summary', 'description', 'requirements', 'openings', 'closing_date',
  ]) {
    await form.locator(`[name="${name}"]`).fill(String(job[name] ?? ''));
  }
  await form.locator('[name="status"]').selectOption(job.status);
  await submitAndWait(page, form.getByRole('button', { name: 'Save job' }));
  await expect(page.getByRole('status')).toContainText('Job saved.');
}

async function createJob(page, job) {
  await page.goto('/staff/jobs.php');
  await fillJobForm(page, job);
  const rows = queryQaDatabase(
    'SELECT id FROM jobs WHERE slug = :slug LIMIT 1',
    { slug: job.slug },
  );
  expect(rows).toHaveLength(1);
  return Number(rows[0].id);
}

async function editJob(page, jobId, job) {
  await page.goto(`/staff/jobs.php?edit=${jobId}`);
  await fillJobForm(page, job);
}

async function createApplicationDraft(page, profile, jobSlug) {
  await page.goto(`/apply/?job=${jobSlug}`);
  await page.locator('[name="full_name"]').fill(profile.name);
  await page.locator('[name="phone"]').fill(profile.phone);
  await page.locator('[name="current_city"]').fill('Synthetic QA City');
  await page.locator('[name="eligibility_confirmed"]').check();
  await page.locator('[name="privacy_accepted"]').check();
  await submitAndWait(page, page.getByRole('button', { name: 'Save and continue' }));
  const applicationId = new URL(page.url()).searchParams.get('id');
  expect(applicationId).toMatch(/^\d+$/);
  return Number(applicationId);
}

async function createSubmittedApplication(page, profile, jobSlug) {
  const applicationId = await createApplicationDraft(page, profile, jobSlug);
  await page.locator('[name="experience_summary"]').fill(
    'Synthetic experience used only for the isolated task-boundary regression.',
  );
  await page.locator('[name="certified"]').check();
  await submitAndWait(page, page.locator('button[name="action"][value="submit"]'));
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)/);
  return applicationId;
}

function applicationDocumentDomainProbe(applicationId, userId, label) {
  const php = String.raw`
    require $argv[1];
    try {
        $documentId = record_application_document((int) $argv[2], (int) $argv[3], [
            'original_name' => 'synthetic-domain-probe.pdf',
            'storage_name' => $argv[4] . '.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 123,
            'content_sha256' => str_repeat('a', 64),
        ]);
        echo json_encode(['accepted' => true, 'document_id' => $documentId], JSON_THROW_ON_ERROR);
    } catch (Throwable $exception) {
        echo json_encode([
            'accepted' => false,
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
        ], JSON_THROW_ON_ERROR);
    }
  `;
  const output = execFileSync(
    'php',
    [
      ...phpRuntimeArgs(), '-r', php, path.join(projectRoot, 'app', 'bootstrap.php'),
      String(applicationId), String(userId), label,
    ],
    {
      cwd: projectRoot,
      env: {
        ...process.env,
        APP_ENV: 'test',
        APP_URL: 'http://127.0.0.1:4177',
        APP_KEY: 'qa-only-local-key-do-not-use-in-production',
        DB_DSN: `sqlite:${path.join(projectRoot, 'tests', '.artifacts', 'qa.sqlite')}`,
        DB_USER: '',
        DB_PASSWORD: '',
        UPLOAD_DIR: qaUploadDirectory,
        PRIVACY_NOTICE_VERSION: '2026-09-01-qa',
      },
      encoding: 'utf8',
      windowsHide: true,
    },
  );
  return JSON.parse(output);
}

test('changed job terms require re-confirmation and submitted application terms remain immutable', async ({ browser, page, baseURL }) => {
  requireLoopback(baseURL);

  const staffContext = await browser.newContext({ baseURL });
  const staffPage = await staffContext.newPage();
  await loginStaff(staffPage);
  const jobId = await createJob(staffPage, originalJob);

  await registerApplicant(page, snapshotApplicant);
  const applicationId = await createApplicationDraft(page, snapshotApplicant, originalJob.slug);
  const initialRows = queryQaDatabase(
    'SELECT current_status, job_snapshot_json FROM applications WHERE id = :id',
    { id: applicationId },
  );
  expect(initialRows).toHaveLength(1);
  expect(initialRows[0].current_status).toBe('draft');
  const initialSnapshot = JSON.parse(initialRows[0].job_snapshot_json);
  expect(initialSnapshot).toMatchObject({
    title: originalJob.title,
    location: originalJob.location,
    shift_pattern: originalJob.shift_pattern,
    requirements: originalJob.requirements,
  });
  expect(initialSnapshot.content_sha256).toMatch(/^[a-f0-9]{64}$/);
  const initialSnapshotHistory = queryQaDatabase(
    `SELECT event_type, previous_content_sha256, accepted_content_sha256,
            applicant_reviewed_changes, acknowledged_at
       FROM application_job_snapshot_history
      WHERE application_id = :id ORDER BY id`,
    { id: applicationId },
  );
  expect(initialSnapshotHistory).toEqual([{
    event_type: 'draft_terms_captured',
    previous_content_sha256: null,
    accepted_content_sha256: initialSnapshot.content_sha256,
    applicant_reviewed_changes: 0,
    acknowledged_at: null,
  }]);

  await editJob(staffPage, jobId, revisedJob);
  await page.goto(`/apply/step2.php?id=${applicationId}`);
  await expect(page.locator('aside.portal-card')).toContainText(originalJob.title);
  await expect(page.locator('aside.portal-card')).toContainText(originalJob.location);
  const changedTermsNotice = page.getByRole('status');
  await expect(changedTermsNotice).toContainText('The role changed after you started.');
  await expect(changedTermsNotice).toContainText(revisedJob.title);
  await expect(changedTermsNotice).toContainText(revisedJob.location);
  await expect(changedTermsNotice).toContainText(revisedJob.shift_pattern);
  await expect(changedTermsNotice).toContainText(revisedJob.requirements);

  await page.locator('[name="experience_summary"]').fill(
    'Synthetic snapshot experience that contains no real applicant information.',
  );
  await page.locator('[name="certified"]').check();
  await submitAndWait(page, page.locator('button[name="action"][value="submit"]'));
  await expect(page.getByRole('alert')).toContainText(
    'Review the current role details shown on this page, then confirm them before submitting.',
  );

  const refusedRows = queryQaDatabase(
    'SELECT current_status, job_snapshot_json FROM applications WHERE id = :id',
    { id: applicationId },
  );
  expect(refusedRows).toEqual([{
    current_status: 'draft',
    job_snapshot_json: initialRows[0].job_snapshot_json,
  }]);
  expect(queryQaDatabase(
    "SELECT id FROM audit_events WHERE event_type = 'application.submitted' AND entity_id = :id",
    { id: applicationId },
  )).toEqual([]);
  expect(queryQaDatabase(
    `SELECT event_type, previous_content_sha256, accepted_content_sha256,
            applicant_reviewed_changes, acknowledged_at
       FROM application_job_snapshot_history
      WHERE application_id = :id ORDER BY id`,
    { id: applicationId },
  )).toEqual(initialSnapshotHistory);

  await page.locator('[name="certified"]').check();
  await page.locator('[name="job_change_reviewed"]').check();
  await submitAndWait(page, page.locator('button[name="action"][value="submit"]'));
  await expect(page).toHaveURL(/\/applicant\/(?:index\.php)?(?:[?#]|$)/);

  const submittedRows = queryQaDatabase(
    'SELECT current_status, job_snapshot_json FROM applications WHERE id = :id',
    { id: applicationId },
  );
  expect(submittedRows).toHaveLength(1);
  expect(submittedRows[0].current_status).toBe('submitted');
  const submittedSnapshot = JSON.parse(submittedRows[0].job_snapshot_json);
  expect(submittedSnapshot).toMatchObject({
    title: revisedJob.title,
    location: revisedJob.location,
    shift_pattern: revisedJob.shift_pattern,
    requirements: revisedJob.requirements,
  });
  expect(submittedSnapshot.content_sha256).toMatch(/^[a-f0-9]{64}$/);
  expect(submittedSnapshot.content_sha256).not.toBe(initialSnapshot.content_sha256);
  const submittedSnapshotHistory = queryQaDatabase(
    `SELECT event_type, previous_content_sha256, accepted_content_sha256,
            applicant_reviewed_changes, acknowledged_at
       FROM application_job_snapshot_history
      WHERE application_id = :id ORDER BY id`,
    { id: applicationId },
  );
  expect(submittedSnapshotHistory).toHaveLength(2);
  expect(submittedSnapshotHistory[1]).toMatchObject({
    event_type: 'submission_terms_accepted',
    previous_content_sha256: initialSnapshot.content_sha256,
    accepted_content_sha256: submittedSnapshot.content_sha256,
    applicant_reviewed_changes: 1,
  });
  expect(submittedSnapshotHistory[1].acknowledged_at).toMatch(/^\d{4}-\d{2}-\d{2} /);

  await editJob(staffPage, jobId, postSubmissionJob);
  const persistedAfterLaterEdit = queryQaDatabase(
    'SELECT job_snapshot_json FROM applications WHERE id = :id',
    { id: applicationId },
  );
  expect(persistedAfterLaterEdit).toEqual([{
    job_snapshot_json: submittedRows[0].job_snapshot_json,
  }]);

  await page.goto('/applicant/');
  const applicantRecord = page.locator('article.application-panel').filter({
    hasText: `Application #${applicationId}`,
  });
  await expect(applicantRecord).toContainText(revisedJob.title);
  await expect(applicantRecord).toContainText(revisedJob.location);
  await expect(applicantRecord).not.toContainText(postSubmissionJob.title);

  await staffPage.goto(`/staff/application.php?id=${applicationId}`);
  await expect(staffPage.locator('section.application-record')).toContainText(revisedJob.title);
  await expect(staffPage.locator('section.application-record')).toContainText(revisedJob.location);
  await expect(staffPage.locator('section.application-record')).not.toContainText(postSubmissionJob.title);
  await staffContext.close();
});

test('task mutations are application-bound and become read-only in terminal states', async ({ browser, page, baseURL }) => {
  requireLoopback(baseURL);
  await registerApplicant(page, taskApplicant);
  const firstApplicationId = await createSubmittedApplication(
    page,
    taskApplicant,
    'sample-operations-associate',
  );
  const secondApplicationId = await createSubmittedApplication(
    page,
    taskApplicant,
    'sample-office-support-specialist',
  );

  const staffContext = await browser.newContext({ baseURL });
  const staffPage = await staffContext.newPage();
  await loginStaff(staffPage);
  await staffPage.goto(`/staff/application.php?id=${firstApplicationId}`);
  const addTaskForm = staffPage.locator('form').filter({
    has: staffPage.locator('input[name="action"][value="add_task"]'),
  });
  const staffCsrf = await addTaskForm.locator('[name="_csrf"]').inputValue();
  await addTaskForm.locator('[name="title"]').fill('Synthetic application-bound task');
  await addTaskForm.locator('[name="applicant_visible"]').check();
  await submitAndWait(staffPage, addTaskForm.getByRole('button', { name: 'Add task' }));

  const taskRows = queryQaDatabase(
    'SELECT id, application_id, status FROM application_tasks WHERE title = :title',
    { title: 'Synthetic application-bound task' },
  );
  expect(taskRows).toHaveLength(1);
  const taskId = Number(taskRows[0].id);
  expect(Number(taskRows[0].application_id)).toBe(firstApplicationId);
  expect(taskRows[0].status).toBe('pending');

  await page.goto('/applicant/');
  const firstPanel = page.locator('article.application-panel').filter({
    hasText: `Application #${firstApplicationId}`,
  });
  const applicantTaskForm = firstPanel.locator('form[action="/applicant/task.php"]');
  await expect(applicantTaskForm).toBeVisible();
  const applicantCsrf = await applicantTaskForm.locator('[name="_csrf"]').inputValue();

  const crossApplicationResponse = await staffContext.request.post('/staff/application.php', {
    form: {
      _csrf: staffCsrf,
      id: String(secondApplicationId),
      action: 'set_task_status',
      task_id: String(taskId),
      status: 'completed',
    },
    maxRedirects: 0,
  });
  expect(crossApplicationResponse.status()).toBe(303);
  expect(crossApplicationResponse.headers().location)
    .toBe(`/staff/application.php?id=${secondApplicationId}`);
  await staffPage.goto(`/staff/application.php?id=${secondApplicationId}`);
  await expect(staffPage.getByRole('status')).toContainText('Task not found for this application.');
  expect(queryQaDatabase(
    'SELECT status FROM application_tasks WHERE id = :id',
    { id: taskId },
  )).toEqual([{ status: 'pending' }]);

  await staffPage.goto(`/staff/application.php?id=${firstApplicationId}`);
  const statusForm = staffPage.locator('form').filter({
    has: staffPage.locator('input[name="action"][value="update_status"]'),
  });
  await statusForm.locator('[name="status"]').selectOption('declined');
  await statusForm.locator('[name="public_note"]').fill(
    'Synthetic terminal decision used only to verify immutable task history.',
  );
  await submitAndWait(staffPage, statusForm.getByRole('button', { name: 'Record status decision' }));
  await expect(staffPage.locator('.status-chip.status-declined')).toBeVisible();
  await expect(staffPage.locator('input[name="action"][value="add_task"]')).toHaveCount(0);
  await expect(staffPage.locator('input[name="action"][value="set_task_status"]')).toHaveCount(0);

  const rejectedAdd = await staffContext.request.post('/staff/application.php', {
    form: {
      _csrf: staffCsrf,
      id: String(firstApplicationId),
      action: 'add_task',
      title: 'Synthetic forbidden terminal task',
      due_date: '',
      applicant_visible: '1',
    },
    maxRedirects: 0,
  });
  expect(rejectedAdd.status()).toBe(303);
  await staffPage.goto(`/staff/application.php?id=${firstApplicationId}`);
  await expect(staffPage.getByRole('status')).toContainText(
    'Tasks can be added only while a submitted application remains active.',
  );

  const rejectedStaffUpdate = await staffContext.request.post('/staff/application.php', {
    form: {
      _csrf: staffCsrf,
      id: String(firstApplicationId),
      action: 'set_task_status',
      task_id: String(taskId),
      status: 'completed',
    },
    maxRedirects: 0,
  });
  expect(rejectedStaffUpdate.status()).toBe(303);
  await staffPage.goto(`/staff/application.php?id=${firstApplicationId}`);
  await expect(staffPage.getByRole('status')).toContainText(
    'Tasks cannot change after the application reaches a terminal state.',
  );

  const rejectedApplicantUpdate = await page.request.post('/applicant/task.php', {
    form: {
      _csrf: applicantCsrf,
      task_id: String(taskId),
      status: 'completed',
    },
    maxRedirects: 0,
  });
  expect(rejectedApplicantUpdate.status()).toBe(303);
  await page.goto('/applicant/');
  await expect(page.getByRole('status')).toContainText(
    'Tasks cannot change after the application reaches a terminal state.',
  );
  const terminalPanel = page.locator('article.application-panel').filter({
    hasText: `Application #${firstApplicationId}`,
  });
  await expect(terminalPanel.locator('form[action="/applicant/task.php"]')).toHaveCount(0);

  expect(queryQaDatabase(
    'SELECT application_id, status FROM application_tasks WHERE id = :id',
    { id: taskId },
  )).toEqual([{ application_id: firstApplicationId, status: 'pending' }]);
  expect(queryQaDatabase(
    'SELECT id FROM application_tasks WHERE application_id = :id ORDER BY id',
    { id: firstApplicationId },
  )).toHaveLength(1);
  expect(queryQaDatabase(
    `SELECT event_type FROM audit_events
      WHERE entity_type = 'application_task' AND entity_id = :id
        AND event_type IN ('application.task_updated_by_staff', 'application.task_updated_by_applicant')`,
    { id: taskId },
  )).toEqual([]);
  await staffContext.close();
});

test('one resume per application is enforced across UI, domain, storage, quota, and audit state', async ({ page, baseURL }) => {
  requireLoopback(baseURL);
  await registerApplicant(page, resumeApplicant);
  const applicationId = await createApplicationDraft(
    page,
    resumeApplicant,
    'sample-warehouse-coordinator',
  );

  const fixture = await readFile(resumePath);
  const fixtureHash = createHash('sha256').update(fixture).digest('hex');
  await page.locator('[name="resume"]').setInputFiles(resumePath);
  await submitAndWait(page, page.locator('button[name="action"][value="save"]'));
  await expect(page).toHaveURL(`/apply/step2.php?id=${applicationId}`);
  await expect(page.locator('.document-summary')).toContainText('synthetic-resume.pdf');
  await expect(page.locator('[name="resume"]')).toHaveCount(0);

  const initialDocuments = queryQaDatabase(
    `SELECT id, application_id, original_name, storage_name, mime_type, size_bytes,
            content_sha256, scan_status, scanned_at, scan_result, retention_expires_at
       FROM application_documents WHERE application_id = :application_id`,
    { application_id: applicationId },
  );
  expect(initialDocuments).toHaveLength(1);
  expect(initialDocuments[0]).toMatchObject({
    application_id: applicationId,
    original_name: 'synthetic-resume.pdf',
    mime_type: 'application/pdf',
    size_bytes: fixture.length,
    content_sha256: fixtureHash,
    scan_status: 'quarantine',
    scanned_at: null,
    scan_result: null,
    retention_expires_at: null,
  });
  expect(initialDocuments[0].storage_name).toMatch(/^[a-f0-9]{48}\.pdf$/);

  const uniqueIndexes = queryQaDatabase("PRAGMA index_list('application_documents')")
    .filter((index) => Number(index.unique) === 1);
  const hasApplicationUniqueIndex = uniqueIndexes.some((index) => {
    if (!/^[A-Za-z0-9_]+$/.test(String(index.name))) {
      return false;
    }
    const columns = queryQaDatabase(`PRAGMA index_info("${index.name}")`)
      .map((column) => column.name);
    return columns.length === 1 && columns[0] === 'application_id';
  });
  expect(hasApplicationUniqueIndex, 'SQLite must enforce one document row per application').toBeTruthy();

  const ownerRows = queryQaDatabase(
    'SELECT applicant_user_id FROM applications WHERE id = :id',
    { id: applicationId },
  );
  const staffRows = queryQaDatabase(
    "SELECT id FROM users WHERE role = 'staff' ORDER BY id LIMIT 1",
  );
  expect(ownerRows).toHaveLength(1);
  expect(staffRows).toHaveLength(1);
  expect(applicationDocumentDomainProbe(
    applicationId,
    Number(ownerRows[0].applicant_user_id),
    `qa-duplicate-owner-${applicationId}`,
  )).toMatchObject({
    accepted: false,
    class: 'DomainException',
    message: 'A resume is already attached to this application.',
  });
  expect(applicationDocumentDomainProbe(
    applicationId,
    Number(staffRows[0].id),
    `qa-cross-owner-${applicationId}`,
  )).toMatchObject({
    accepted: false,
    class: 'DomainException',
    message: 'Application not found.',
  });

  const initialFiles = (await readdir(qaUploadDirectory)).sort();
  const initialUsage = queryQaDatabase(
    "SELECT used_bytes FROM private_storage_usage WHERE scope = 'application_documents'",
  );
  const initialAudit = queryQaDatabase(
    `SELECT id, metadata_json FROM audit_events
      WHERE event_type = 'application.document_quarantined'
        AND entity_type = 'application_document'
        AND entity_id = :document_id`,
    { document_id: initialDocuments[0].id },
  );
  expect(initialAudit).toHaveLength(1);
  expect(JSON.parse(initialAudit[0].metadata_json)).toMatchObject({
    application_id: applicationId,
    size_bytes: fixture.length,
    content_sha256: fixtureHash,
  });
  expect(initialUsage).toEqual([{ used_bytes: fixture.length }]);

  const csrf = await page.locator('[name="_csrf"]').first().inputValue();
  const duplicateResponse = await page.request.post(`/apply/step2.php?id=${applicationId}`, {
    multipart: {
      _csrf: csrf,
      id: String(applicationId),
      action: 'save',
      experience_summary: 'Synthetic duplicate upload refusal probe.',
      resume: {
        name: 'duplicate-synthetic-resume.pdf',
        mimeType: 'application/pdf',
        buffer: fixture,
      },
    },
    maxRedirects: 0,
  });
  expect(duplicateResponse.status()).toBe(200);
  expect(await duplicateResponse.text()).toContain('A resume is already attached to this application.');

  expect(queryQaDatabase(
    `SELECT id, application_id, original_name, storage_name, mime_type, size_bytes,
            content_sha256, scan_status, scanned_at, scan_result, retention_expires_at
       FROM application_documents WHERE application_id = :application_id`,
    { application_id: applicationId },
  )).toEqual(initialDocuments);
  expect((await readdir(qaUploadDirectory)).sort()).toEqual(initialFiles);
  expect(queryQaDatabase(
    "SELECT used_bytes FROM private_storage_usage WHERE scope = 'application_documents'",
  )).toEqual(initialUsage);
  expect(queryQaDatabase(
    `SELECT id, metadata_json FROM audit_events
      WHERE event_type = 'application.document_quarantined'
        AND entity_type = 'application_document'
        AND entity_id = :document_id`,
    { document_id: initialDocuments[0].id },
  )).toEqual(initialAudit);
});
