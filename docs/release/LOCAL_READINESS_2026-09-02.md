# TAASCOR Website local readiness — 2026-09-02

## Gate decision

- **Local implementation gate: PASSED; separate feature commit/push gate: COMPLETE; isolated exact-commit source qualification: PASSED.** Fresh isolated verification and a separate clean-checkout rerun completed on `feature/integrated-experience` with no open P0/P1 local code or workflow defect. The qualified source commit is `5c6fdde3edb2caa63b8f20c921b98bfa9089f379`.
- **Production gate: NO-GO.** Local readiness, even when complete, will not close the privacy, security, infrastructure, content, business, release-authority or deployment gates in `KNOWN_GAPS_2026-09-02.md`.

This record is not a deployment authorization, production-parity report or security certification.

## Scope and safety boundary

| Item | Current evidence boundary |
| --- | --- |
| Known-good baseline | GitHub `main` commit `e7299f42908fd2e31b91067d79b433c14a713231`, the audited cinematic baseline. |
| Feature work | Local branch `feature/integrated-experience`; uncommitted and unpushed at the time this record was created. |
| Local profile | PHP modular monolith, synthetic SQLite data, loopback browser server and private temporary upload directory. |
| Workflows in scope | Public routes, Careers and job detail, staged applicant flow, applicant self-service, staff job/application workflows, resume quarantine and Workforce Planner. |
| Sensitive-data boundary | Synthetic records only. No real applicant, resume, staff credential, client or workforce-submission data is authorized for local verification. |
| Exclusions | Production infrastructure, MySQL qualification, mail/recovery/MFA, malware provider, HRIS/CRM integrations, live analytics, production content approval, Hostinger packaging/deployment and live smoke. |
| Mutation boundary | Local disposable state only. No remote form submissions, production database writes, push, package or deployment. |

## Implemented and locally verified surfaces

- Cinematic public homepage with progressive enhancement, reduced-motion behavior and audience routes.
- Shared public pages for solutions, industries, platform, proof, company/evidence-gated content, support/legal routes and portals.
- Database-backed published-job discovery, job detail and exact job-to-application context using synthetic fixtures locally.
- Applicant registration/login, staged draft submission, save/resume, status/tasks, withdrawal, settings and privacy-notice history.
- Separate staff entry, governed job publication and application review/status transitions.
- Private resume quarantine with type/signature/MIME/size/name/storage controls.
- Workforce Planner with explicit privacy gate, abuse controls and local-only receipt behavior.
- Dynamic robots/sitemap, custom error behavior, headers and source/private-path denial.

Presence in source is not proof that a surface works. The final verification table below is the local acceptance record for the pre-commit candidate tree now represented by feature commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379`.

## Final verification record

Evidence below was captured from the final pre-commit feature-branch run on 2026-09-02. The candidate was subsequently committed and pushed as `5c6fdde3edb2caa63b8f20c921b98bfa9089f379`, then separately rerun from an isolated clean checkout with the same static and browser results. This is source-level evidence for the synthetic local profile, not production qualification or packaging approval.

| Check | Final command / scope | Status | Final evidence |
| --- | --- | --- | --- |
| Git/source boundary | `git status --short --branch`; `git diff --check`; explicit changed-path review; `git rev-parse HEAD main origin/main` | **Pass — local tree** | `HEAD`, `main`, and `origin/main` all remain `e7299f42908fd2e31b91067d79b433c14a713231`. Candidate scope is 2 modified tracked paths plus 113 untracked paths (115 total). Whitespace check passed for tracked changes; the untracked candidate list was reviewed separately and no unrelated path was identified. |
| PHP syntax | Full candidate `*.php` lint through `npm run test:static` | **Pass** | PHP 8.5.7; all 72 PHP source files passed `php -l`; no PHP file was excluded. |
| Static governance/security | `npm run test:static` as the first stage of fresh `npm test` | **Pass** | 12/12 Node static tests passed. They include production-origin, mutation-helper, session-expiry, production-code-lock, sitemap/job fail-closed, migration-integrity, rewrite, PHP-lint, local-reference, fragment and textual-secret contracts. The run resolved 79 static references and scanned 120 textual files. |
| Full browser regression | Fresh managed-loopback `npm test` | **Pass** | 26/26 Playwright tests passed in 1.9 minutes with Node 24.16.0, npm 11.13.0 and Playwright 1.62.1. The QA harness recreated its disposable SQLite database/uploads and provisioned only synthetic jobs, applicants, applications, documents and staff. |
| Fresh schema lifecycle | Explicit `APP_ENV=test` and disposable `tests/.artifacts/final-migration-2.sqlite`; `scripts/migrate.php`; `scripts/seed.php`; direct post-check | **Pass** | Migration reported current SQLite schema; seed created 3 synthetic jobs; `PRAGMA integrity_check=ok`; `schema_migrations=1`; `jobs=3`. The file is ignored test state and is not release material; no repository or production database was used. |
| Responsive/visual | Public, careers/job, applicant, workforce, portal and staff surfaces at 1440 px, 768 px, 390 px and 360 px | **Pass — local visual review** | The final visual test generated 60 current review screenshots across desktop/tablet/mobile, dark/light preferences and reduced motion; 80 PNGs are present including 20 earlier supporting captures. Automated overflow assertions passed. Codex spot-reviewed the homepage and authenticated applicant/staff light-theme mobile captures with no clipping, overlap or unreadable-card defect observed. Artifacts: `tests/.artifacts/screenshots/` (ignored, non-release). |
| Accessibility | Automated keyboard, focus, landmarks, names, errors, responsive overflow and reduced-motion coverage | **Automated pass / named manual review pending** | Five accessibility/responsive browser tests passed, including mobile keyboard navigation, theme control and reduced-motion behavior. This is not a WCAG conformance claim: named screen-reader review, 200% zoom/reflow, formal contrast and target-size review remain external qualification work. |
| Security negative paths | Cross-app/object/task denial; draft/terminal-state denial; upload abuse; internal/path denial; CSRF; throttling; collection/indexing gates | **Pass — isolated profile** | All applicable governed-experience, hardened-lifecycle, recruitment-flow and security-routing cases passed. Expected 400/403/404/419 negative responses were observed, including cross-app ownership denial and invalid upload rejection; there were no failed assertions. Production data, edge and provider controls remain outside this evidence boundary. |
| Content and claims | Static route/link/fragment checks plus coverage/known-gap reconciliation | **Pass for source coherence / owner approval pending** | Local routes and references resolved with no broken static target or missing same-document fragment. Claims, jobs, identities, locations, clients, leaders and official channels remain explicitly evidence-gated under P1-04; no owner approval is inferred from route availability. |
| Secret/private artifact scan | Candidate textual-source scan, dependency audit and ignore-boundary check | **Pass — candidate source** | Textual-secret signature scan found no recognizable committed secret pattern across 120 textual files. `npm audit` reported 0 vulnerabilities. `Backups/` and `tests/.artifacts/` are ignored; their databases, uploads, screenshots and baseline archive are not candidate release material. |

## Acceptance rule applied after the final run

The local implementation gate changed to **READY FOR SEPARATE COMMIT/PUSH REVIEW** only after:

1. every required row above is completed with fresh evidence;
2. no P0/P1 code or local-workflow defect remains open;
3. synthetic data and private artifacts remain outside the release package;
4. documentation accurately distinguishes implemented local controls from unavailable production services; and
5. the exact changed-path review confirms no unrelated user work is included.

That local decision did not authorize commit/push. Separate approval was subsequently granted, feature commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` was pushed, and exact-commit clean-checkout verification passed. None of those gates authorizes packaging or production deployment.

## Safe local verification profile

The application reads process environment variables; it does not load a repository `.env` file. For an isolated loopback session using only synthetic data, set:

```powershell
$env:APP_ENV = 'local'
$env:APP_URL = 'http://127.0.0.1:8080'
$env:APP_KEY = 'replace-with-a-local-only-key-at-least-32-characters'
$env:PUBLIC_INDEXING_ENABLED = 'false'
$env:ALLOW_NON_PRODUCTION_WEB = 'true'
$env:APPLICANT_COLLECTION_ENABLED = 'true'
$env:WORKFORCE_COLLECTION_ENABLED = 'true'
$env:RESUME_UPLOAD_ENABLED = 'true'
$env:STAFF_WORKFLOWS_ENABLED = 'true'
$env:JOB_PUBLICATION_ENABLED = 'true'
```

These `true` settings are a local test override, not a production recommendation. Never copy this block to production. Each production feature gate requires the corresponding closure evidence in `KNOWN_GAPS_2026-09-02.md` and the approvals in `RELEASE_CHECKLIST.md`.

The local commands remain:

```powershell
npm ci
npx playwright install chromium
npm test

php -d extension=pdo_sqlite -d extension=sqlite3 scripts/migrate.php
php -d extension=pdo_sqlite -d extension=sqlite3 scripts/seed.php
php -d extension=pdo_sqlite -d extension=sqlite3 -S 127.0.0.1:8080 router.php
```

Never point mutation tests at a remote URL. Use the QA harness's disposable database and upload directory for automated flows.

## P0 Blocker

- Production privacy/legal/DPO/DSR/retention approval remains open.
- Applicant email ownership verification and recovery remain unavailable for production.
- Staff MFA, least privilege, provisioning and maker-checker controls remain unqualified.
- No approved production release candidate, package, deployment path or rollback point exists. Feature commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` is pushed and passed isolated exact-commit source qualification, but every production gate remains open.

## P1 Major

- Resume malware scan, retrieval, access-log, deletion and retention lifecycle remains unqualified.
- MySQL/TLS/strict-mode/least-privilege/migration/concurrency and backup/restore qualification remains open.
- Edge HTTPS, document-root, trusted-proxy and live denial/header behavior remains unverified.
- Jobs, company identity, locations, clients, leaders, claims and official channels remain subject to source and owner approval.
- Monitoring, encrypted backups, restore rehearsal, vulnerability management and penetration testing remain open.
- Fresh local and exact-commit clean-checkout verification are recorded; business UAT, named accessibility review and packaging qualification remain open.

## P2 Watch Item

- Canonical production domain, social metadata/channels and indexing activation await approval.
- Final font/media provenance, hosting strategy and fallbacks await review.
- Production-target Lighthouse/Core Web Vitals and representative-device budgets are not yet available.
- External HRIS, CRM/advisor, mail/SMS, malware and analytics integrations remain explicit handoffs.

## Expected Data Movement

After separate production approval, normal runtime activity may add or change approved jobs, applications, status/task history, documents, workforce briefs, sessions, throttles, audit events and retention/storage ledgers. Those records must be reconciled as operational data, not inferred to be deployment drift. A code deployment never authorizes a migration, bulk rewrite, deletion or real test-data submission.

## Commit/push status

- Baseline commit `e7299f42908fd2e31b91067d79b433c14a713231` is pushed and matches `main` and `origin/main`.
- The locally verified integrated implementation was committed and pushed under separate approval as `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` on `origin/feature/integrated-experience`; isolated clean-checkout source qualification passed on that exact commit.

## Exact-commit clean-checkout qualification

- Qualified source commit: `5c6fdde3edb2caa63b8f20c921b98bfa9089f379`; `origin/feature/integrated-experience` pointed to that commit throughout the qualification run.
- `npm ci`: passed in an isolated checkout.
- `npm test`: passed with 12/12 static checks and 26/26 Playwright tests.
- `npm audit --audit-level=moderate`: 0 vulnerabilities.
- At qualification-run completion: HEAD still matched the remote commit; staged, unstaged and untracked status was clean.
- Boundary: technical source qualification only. No package was built, no production path was used and no production data was touched.

## Deployment path

No production document root, Hostinger destination, package or deployment path has been approved or used. Nothing was deployed and no production data was touched.

## Next controlled action

The separately approved feature commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` is pushed and passed isolated exact-commit source qualification. Complete Recruitment/Workforce UAT, named accessibility review, and the external P0/P1 gates before requesting separate packaging approval. Hostinger upload, live smoke, and any production data movement remain later, separately approved gates.
