# TAASCOR Website

TAASCOR Management & General Services Corp. public website, workforce-planning entry point, and governed recruitment experience.

## Current status

The audited cinematic baseline remains preserved on `main` and `origin/main` at commit `e7299f42908fd2e31b91067d79b433c14a713231`. The integrated experience is maintained on `origin/feature/integrated-experience`. Its light-first responsive release, commit `8d5e929822d85d75f7c2fd1aae28e5d0ac3820d3`, was packaged from Git, deployed to `https://taascor.com/` through the verified Hostinger SSH target, migrated, hash-checked, and externally smoke-tested on 2026-09-02.

**Public-experience release gate: PASSED. Governed data-workflow gate: NO-GO.** The public site is live with light mode as the default, an optional persistent dark mode, and responsive desktop/tablet/mobile behavior. Applicant collection, workforce collection, resume upload, staff workflows, job publication, and indexing remain disabled in production until their separate privacy, security, content, accessibility, retention, and business-ownership gates close.

The 2026-09-02 local release run passed 12/12 static checks and 26/26 Playwright tests. Production qualification additionally passed SSH host verification, PHP 8.5/MySQL preflight, pre-migration file and database backups, MySQL migration, package/readback hashing, public-route and denial-route checks, static-asset parity, and live desktop/mobile light/dark browser smoke. See `docs/release/PRODUCTION_RELEASE_2026-09-02.md` for the release evidence and `docs/release/LOCAL_READINESS_2026-09-02.md` for the broader evidence boundary.

## What is implemented locally

- Preserved cinematic homepage with progressive-enhancement and reduced-motion fallbacks.
- Three audience journeys: Build a Workforce, Find Work, and Access TAASCOR.
- Modular public pages for solutions, platform, proof, company information, legal/support content, and portals.
- A governed job register powering Careers, job details, homepage previews, and exact job-to-application context.
- Searchable/filterable jobs with active/closing state and structured job metadata for approved non-demo roles.
- Two-stage applicant flow, account registration/login, save/resume, status/tasks, withdrawal, settings, and notice history.
- Separate staff login, job publishing, application review, controlled status transitions, and audit history.
- Private quarantine uploads with extension, signature, MIME, size, and randomized-name controls.
- A privacy-gated Workforce Planner with rate limiting, a honeypot, and local receipt state.
- Dynamic `robots.txt`, `sitemap.xml`, custom 404 handling, security headers, and internal-path denial.
- Isolated SQLite-based browser QA with synthetic jobs, users, applications, and documents only.

## Architecture

This is a dependency-light PHP modular monolith. The public shell remains visually expressive; task routes use calm, accessible server-rendered interfaces. Applicant, staff, workforce-brief, and job data share a governed application core while Employee/HRIS remains an explicit external handoff.

```text
index.html + index.php          Cinematic source and secure server wrapper
site/ + assets/                 Shared public shell, styles, scripts, and 404
careers/ + apply/               Job discovery and staged application
account/ + applicant/           Applicant identity and self-service
staff/                           Separately protected recruiter workspace
workforce/                       Employer staffing brief
app/                             Config, security, auth, data, audit, upload logic
database/                        SQLite and MySQL schemas
scripts/                         Controlled migration, seed, and local staff CLI
tests/                           Static, browser, responsive, visual, and flow QA
Planning/ + Audit/              Evidence, decisions, risks, and delivery plan
docs/                            Architecture, security, content, and release records
```

The architecture decision and security boundaries are recorded in `docs/architecture/ADR-001-governed-php-modular-monolith.md` and `docs/security/THREAT_MODEL.md`.

## Requirements

- PHP 8.1 or newer.
- PDO SQLite for isolated local development, or a separately configured MySQL database.
- Node.js 20 or newer for the verification harness.
- Chromium installed through Playwright for browser checks.

## Configuration

The authoritative blank template is:

`app/.env.example`

The application reads process/host environment variables directly; it does not load a repository `.env` file. Keep secrets outside Git. `APP_KEY`, database credentials, upload storage, privacy versions, and staff seed credentials are intentionally blank in the template.

For production, the application fails closed unless:

- `APP_ENV=production`;
- `APP_URL` is a credential-free HTTPS origin with no path, query, or fragment;
- `APP_KEY` is a secret of at least 32 characters;
- `DB_DSN` is an explicit MySQL DSN using `utf8mb4`, with separate credentials;
- `UPLOAD_DIR` is an absolute private path outside the document root; and
- `SESSION_COOKIE_SECURE=true`.

The draft applicant and workforce privacy versions deliberately keep collection routes out of search indexing. Do not replace their draft identifiers until the approved notices, owners, purposes, retention rules, and data-subject channels are published.

## Fastest safe local verification

The QA harness provisions a disposable database and private upload directory under `tests/.artifacts`, seeds only synthetic records, starts a loopback PHP server, and tears it down after the suite.

```powershell
npm ci
npx playwright install chromium
npm test
```

Useful focused commands:

```powershell
npm run test:static
npm run test:e2e
npm run test:e2e:visual
```

Never point mutation tests at a remote host. The harness refuses non-loopback registration/application flows by design.

## Manual local server

Set local-only process variables, migrate, seed synthetic jobs, and start the PHP router:

```powershell
$env:APP_ENV = 'local'
$env:APP_URL = 'http://127.0.0.1:8080'
$env:APP_KEY = 'replace-with-a-local-only-key-at-least-32-characters'
$env:ALLOW_NON_PRODUCTION_WEB = 'true'
$env:APPLICANT_COLLECTION_ENABLED = 'true'
$env:WORKFORCE_COLLECTION_ENABLED = 'true'
$env:RESUME_UPLOAD_ENABLED = 'true'
$env:STAFF_WORKFLOWS_ENABLED = 'true'
$env:JOB_PUBLICATION_ENABLED = 'true'

php -d extension=pdo_sqlite -d extension=sqlite3 scripts/migrate.php
php -d extension=pdo_sqlite -d extension=sqlite3 scripts/seed.php
php -d extension=pdo_sqlite -d extension=sqlite3 -S 127.0.0.1:8080 router.php
```

Open `http://127.0.0.1:8080/`. The local defaults place the SQLite file and uploads under the operating-system temporary directory, outside this public project root.

Those six `true` feature flags are for an isolated, synthetic local session only. They intentionally override fail-closed collection, upload, staff-workflow, and job-publication gates so the local flows can be tested. Do not copy them into production: each production flag requires the matching privacy, security, ownership, infrastructure, and release evidence in the governed release checklist. Keep `PUBLIC_INDEXING_ENABLED=false` during local verification.

Applicant/workforce collection, resume upload, and staff workflows are additionally code-locked off in production in this release. Environment flags cannot activate them. A future reviewed commit must both implement the missing external controls and update the corresponding qualification gate; job publication and indexing remain separate explicit production gates.

To create a synthetic local staff account, set `STAFF_SEED_NAME`, `STAFF_SEED_EMAIL`, and `STAFF_SEED_PASSWORD` in the current process, then run:

```powershell
php -d extension=pdo_sqlite -d extension=sqlite3 scripts/create_staff.php --confirm-local-staff
```

The command is disabled in production and never prints the supplied password.

## Governance

- GitHub is the source of truth; Hostinger is deployment only.
- Local changes, feature commit/push, packaging, production deployment, and production-data movement are distinct approval gates.
- Do not edit Hostinger directly or enable GitHub Actions auto-deployment unless explicitly approved.
- Do not publish legal, compliance, client, leadership, location, operational, security, or performance claims without current evidence and owner approval.
- Never place credentials, real applicant records, production documents, or private database files in this repository.
- Synthetic fixtures stay isolated from real workspaces.
- A code release never authorizes migration or bulk rewriting of live jobs, applicants, statuses, documents, or workforce briefs.

See `docs/release/IMPLEMENTATION_COVERAGE_2026-09-02.md`, `docs/release/RELEASE_CHECKLIST.md`, `docs/release/KNOWN_GAPS_2026-09-02.md`, and `docs/release/LOCAL_READINESS_2026-09-02.md` before any release decision.
