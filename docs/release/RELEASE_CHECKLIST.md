# TAASCOR Website governed release checklist

No checkbox below authorizes another checkbox. Record the exact owner, evidence link/path, date, and release commit for every approval.

## G0 — Source and scope

- [x] Audited original cinematic source preserved on GitHub `main` at `e7299f42908fd2e31b91067d79b433c14a713231`.
- [x] Local feature work isolated on `feature/integrated-experience`.
- [ ] Product owner approves the release-one scope and every explicit deferral.
- [ ] Render backend/database/storage/mail/authenticated functions are supplied or formally classified reference-only.
- [ ] Feature-branch commit and push are explicitly approved.

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

- [ ] Exact Hostinger account, document root, PHP version/extensions, MySQL version/database, TLS behavior, and deployment method are confirmed.
- [ ] Production environment variables are provisioned outside Git; no secret appears in source or package.
- [ ] MySQL schema/migration dry run passes on a production-like non-production environment.
- [ ] Backup and restore rehearsal passes for database, private uploads, and current deployed artifact.
- [ ] Any legacy migration has separate approval, read-only extract, mapping, preview, reconciliation, rollback, and data-owner sign-off.
- [ ] No real applicant data is used for development or release qualification.

## G4 — Product qualification

- [ ] Static/lint/link/secret checks pass from a clean checkout.
- [ ] Public, applicant, staff, workforce, negative authorization, upload-abuse, and failure-path browser suites pass.
- [ ] Keyboard, screen-reader, zoom/reflow, reduced-motion, target-size, contrast, and error-state review meets WCAG 2.2 AA acceptance.
- [ ] Desktop, tablet, 390 px, and 360 px visual baselines are approved.
- [ ] Core Web Vitals and asset/CPU/network budgets pass on the release target.
- [ ] Titles, canonical URLs, robots, sitemap, structured data, active/closed jobs, and search indexing policy are validated.
- [ ] Recruitment, employer-conversion, content, and staff UAT pass with isolated synthetic records.
- [ ] All P0/P1 issues are closed; accepted P2 items have named owners and dates.

## G5 — Commit-specific release

- [ ] Explicit paths are reviewed and staged without unrelated changes.
- [ ] No archive, backup, editor-swap, private database, upload, or release-package artifact is placed in the public document root.
- [ ] The exact feature commit is reviewed and pushed to GitHub.
- [ ] Clean-checkout verification passes at that commit.
- [ ] The deployment package is built only from that commit.
- [ ] Package manifest and SHA-256 hashes are recorded and match the reviewed source.
- [ ] Release, Product, Recruitment, Security, DPO/Legal, and data owners approve the exact candidate.

## G6 — Manual deployment and live smoke

- [ ] Separate production-deployment approval is recorded.
- [ ] Named production files/database are backed up and the rollback point is verified.
- [ ] Package is manually uploaded to the confirmed Hostinger path; production is not edited directly.
- [ ] Upload/readback hashes reconcile to the approved package and Git commit.
- [ ] Public routes, headers, forms, job-to-apply context, error paths, and portals pass live smoke.
- [ ] Proportionate authenticated smoke uses approved non-production/test accounts and data only.
- [ ] Logs/queues/notifications are checked and rollback readiness remains intact.
- [ ] Product owner accepts live behavior; data owner separately accepts any approved data movement.

## Current decision

**NO-GO for production.** G0 baseline preservation is complete. The remaining unchecked items require evidence, named ownership, infrastructure, credentials, legal/business decisions, or explicit release authority.
