# TAASCOR Website governed release checklist

No checkbox below authorizes another checkbox. Record the exact owner, evidence link/path, date, and release commit for every approval.

## G0 — Source and scope

- [x] Audited original cinematic source preserved on GitHub `main` at `e7299f42908fd2e31b91067d79b433c14a713231`.
- [x] Local feature work isolated on `feature/integrated-experience`.
- [ ] Product owner approves the release-one scope and every explicit deferral.
- [ ] Render backend/database/storage/mail/authenticated functions are supplied or formally classified reference-only.
- [x] The requesting user explicitly approved feature-branch commit/push in the current Codex task on 2026-09-02; commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` is pushed to `origin/feature/integrated-experience`.

## G1 — Content, claims, and media

- [ ] Corporate identity and current registration evidence reconcile to the named legal entity.
- [ ] Current D.O. 174 evidence, region, issue/expiry, owner, and approved wording are recorded.
- [ ] Services, responsibility split, locations, contact channels, hours, leadership, and organization structure are owner-approved.
- [ ] Client names, logos, relationships, copy, job relationships, and case-study permissions are approved.
- [ ] Photo, video, illustration, logo, icon, font, and social-image rights are recorded.
- [ ] Illustrative operating telemetry is either approved as clearly illustrative or replaced with source-backed evidence.

## G2 — Privacy, security, and data

- [ ] DPO/Legal approves applicant and workforce notices, purposes, fields, legal basis, recipients, cross-border processing, retention, and data-subject channels.
- [ ] Final notice versions replace draft identifiers and acknowledgement behavior is reviewed.
- [ ] Applicant/status/task field dictionary and Recruitment ownership are approved.
- [ ] Role matrix, staff provisioning/deprovisioning, and least-privilege review are approved.
- [ ] Email verification, recovery, MFA, notifications, and anti-enumeration designs are approved and tested if in release scope.
- [ ] Upload malware scanning, release/retrieval authorization, retention, and deletion are implemented and tested if uploads are enabled.
- [ ] Threat model review, dependency/runtime scan, code review, and proportionate penetration test are complete.
- [ ] Log redaction, monitoring, alerting, incident response, and credential/key rotation owners are confirmed.

## G3 — Runtime and data readiness

- [x] Hostinger account, `/home/u716215139/domains/taascor.com/public_html`, PHP 8.5.4 with required extensions, the named MySQL database/user, HTTPS behavior, and SSH/SFTP deployment method were confirmed for the public-experience release.
- [x] Production runtime values are provisioned in a mode-`600` bootstrap outside the web root; no secret appears in source, release manifests, logs, or packages.
- [ ] MySQL schema/migration dry run passes on a production-like non-production environment.
- [ ] Backup and restore rehearsal passes for database, private uploads, and current deployed artifact.
- [ ] Any legacy migration has separate approval, read-only extract, mapping, preview, reconciliation, rollback, and data-owner sign-off.
- [ ] No real applicant data is used for development or release qualification.

## G4 — Product qualification

- [x] Static/lint/link/secret checks passed from a clean checkout of `5c6fdde3edb2caa63b8f20c921b98bfa9089f379`.
- [x] Public, applicant, staff, workforce, negative-authorization, upload-abuse and failure-path browser suites passed on the exact feature commit.
- [ ] Keyboard, screen-reader, zoom/reflow, reduced-motion, target-size, contrast, and error-state review meets WCAG 2.2 AA acceptance.
- [ ] Desktop, tablet, 390 px, and 360 px visual baselines are approved.
- [ ] Core Web Vitals and asset/CPU/network budgets pass on the release target.
- [ ] Titles, canonical URLs, robots, sitemap, structured data, active/closed jobs, and search indexing policy are validated.
- [ ] Recruitment, employer-conversion, content, and staff UAT pass with isolated synthetic records.
- [ ] All P0/P1 issues are closed; accepted P2 items have named owners and dates.

## G5 — Commit-specific release

- [x] Explicit paths for commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` were reviewed and staged without unrelated changes.
- [x] Commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` contains no backup/archive/editor-swap, runtime database/upload, dependency-tree, or release-package artifact.
- [x] The exact feature commit `5c6fdde3edb2caa63b8f20c921b98bfa9089f379` was reviewed and pushed to GitHub.
- [x] Clean-checkout verification passed at `5c6fdde3edb2caa63b8f20c921b98bfa9089f379`: 12/12 static checks, 26/26 Playwright tests and 0 dependency-audit vulnerabilities.
- [x] The public-experience deployment package was built only from commit `8d5e929822d85d75f7c2fd1aae28e5d0ac3820d3`.
- [x] Package, upload/readback, executable asset, runtime-overlay and backup SHA-256 hashes are recorded in `PRODUCTION_RELEASE_2026-09-02.md`.
- [ ] Release, Product, Recruitment, Security, DPO/Legal, and data owners approve the exact candidate.

## G6 — Manual deployment and live smoke

- [x] The requesting user explicitly approved commit, push, and production deployment in the current Codex task on 2026-09-02 and supplied the exact Hostinger target evidence.
- [x] The previous document root and pre-migration database were backed up remotely and downloaded locally; the rollback directory is verified.
- [x] The Git-built package was uploaded over SSH/SFTP to an immutable release directory and activated by a recoverable document-root switch.
- [x] Upload/readback and executable asset hashes reconcile to Git commit `8d5e929822d85d75f7c2fd1aae28e5d0ac3820d3`.
- [x] Public routes, HTTPS/security headers, protected-path denials, portals, default light mode, dark-mode persistence, and 390 px mobile behavior passed live smoke. Data-collection forms remain intentionally disabled.
- [ ] Proportionate authenticated smoke uses approved non-production/test accounts and data only.
- [ ] Logs/queues/notifications are checked and rollback readiness remains intact.
- [ ] Product owner accepts live behavior; data owner separately accepts any approved data movement.

## Current decision

**GO for the deployed fail-closed public experience at commit `8d5e929822d85d75f7c2fd1aae28e5d0ac3820d3`. NO-GO for enabling governed data workflows.** The public release, package, backups, MySQL schema, deployment target, parity checks, and live responsive/theme smoke are recorded in `PRODUCTION_RELEASE_2026-09-02.md`. Applicant/workforce collection, uploads, staff workflows, job publication, and indexing remain disabled until their unchecked legal, privacy, security, content, accessibility, recovery, and business gates close.
