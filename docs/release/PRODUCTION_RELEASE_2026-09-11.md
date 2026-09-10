# Production release — 2026-09-11

## Gate decision

**GO — deployed and smoke-tested.** Public and candidate-facing recruitment journeys are served from `https://taascor.com`. The Visiotech subdomain remains limited to authenticated HRIS staff/back-office use.

## Public website release

- Git commit: `058076e9ab47b04fa21e43258a1987668d09fdf7`
- Git branch: `feature/integrated-experience`
- Package: `taascor-website-058076e.tar.gz`
- Package SHA-256: `94dd5c06545ed4b6693325c4257586f94b4f344aa63a977eaa053ddeeb950135`
- Active release: `/home/u716215139/releases/taascor_website/058076e9-20260910T180735Z/app`
- Previous active release: `/home/u716215139/releases/taascor_website/1e0ac3fe-20260907T090634Z/app`
- Rollback snapshot: `/home/u716215139/backups/taascor_website/public_html-058076e9-20260910T180735Z`
- File backup: `/home/u716215139/backups/taascor_website/predeploy-058076e9-20260910T180735Z-files.tgz`
- File backup SHA-256: `fba623a7d03dabdc2280d90ba9583ad9c34356e65eccc5626cb826c338f77c07`
- Database backup: `/home/u716215139/backups/taascor_website/predeploy-058076e9-20260910T180735Z-database.sql.gz`
- Database backup SHA-256: `613bbcf62f830907e305d0a38a04b72a73d438adc46ab444f8a6bd5e18d5c8dc`
- Runtime configuration SHA-256 before and after: `164a3790096ced38f0e0b3c08356de38a60f70f3398c75f59b93d7497c083884`
- Database state remained unchanged at 14 tables and 1 recorded migration.

### Website verification

- Exact Git archive, upload, remote readback, production overlay, and PHP lint checks passed.
- Static suite passed: 15/15 suites, 148 PHP files linted, 191 static references, 29 fragments, and 263 files scanned for secrets.
- Full local browser suite passed: 33 tests; the subsequently added Guide test passed independently.
- Live Guide browser test passed.
- Live mobile smoke at 390 × 844 passed with light mode, no horizontal overflow, expected content, and zero console errors.
- Live HTTP `200`: `/`, `/jobs/`, `/recruitment/guide/`, `/account/login.php`, `/account/register.php`, `/apply/privacy.php`, `/applicant/`, and `/portal/`.

## HRIS domain-boundary release

- Git commit: `9477527810b00cadad6f8b85aa1e2744f93c1424`
- Git branch: `codex/recruitment-onboarding-20260909`
- Release: `/home/u716215139/taascor-hris-releases/9477527-domain-boundary-20260910T181157Z`
- File backup: `/home/u716215139/taascor-hris-backups/9477527-domain-boundary-20260910T181157Z`
- Production root: `/home/u716215139/domains/taascor.visiotechsolutions.com/public_html/hris`
- Scope: six PHP route shells plus one new redirect helper; no database files, migrations, applicant records, employee records, or payroll records were changed.
- Server identity verified with pinned ED25519 fingerprint `SHA256:Rj5TJlVihe6G2m0iYexrAYuXqGmIHTZg06ROv7RwN7U`.
- All pre-existing target files matched commit `af37d0d41daaee75cb0130b669778753a28d14c2` before deployment.
- Exact Git blob upload/readback and remote PHP lint passed for every deployed file.

### HRIS verification

- HRIS Guide redirects to `https://taascor.com/recruitment/guide/`.
- Candidate login, registration, privacy, dashboard, offers, documents, and application routes redirect to the corresponding `taascor.com` journey.
- Safe job slugs redirect to `https://taascor.com/apply/<slug>/`; invalid slugs redirect to `https://taascor.com/jobs/`.
- All tested public redirect destinations return HTTP `200`.
- The HRIS recruitment workspace still redirects unauthenticated staff to `/hris/login/`.
- A staff queue remains protected and the internal redirect helper returns HTTP `403` when requested directly.
- Recruitment remains foundation-locked; this release does not activate the HRIS candidate runtime or change HRIS business data.

## Risks and accepted gaps

- **P2 Watch Item:** database-backed HRIS recruitment tests could not be executed in the isolated local worktree because its ignored `config/mysql-config.php` is intentionally absent. The deployed change is routing-only, remote PHP lint passed, and no database code or migration was included.
- **Expected production boundary:** local browser fixtures include a synthetic sample job that is intentionally not published in production; its live `404` is not release drift.

## Rollback

- Public website: repoint the document root to the recorded previous active release or restore the recorded website snapshot.
- HRIS: restore the six prior route files from the recorded HRIS backup and remove `recruitment/includes/public_candidate_origin.php`, which did not exist before this release.
