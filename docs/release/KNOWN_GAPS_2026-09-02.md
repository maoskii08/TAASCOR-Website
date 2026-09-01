# TAASCOR Website known gaps — 2026-09-02

## Gate decision

**NO-GO for production.** This register describes what remains before a production candidate can be approved. It does not authorize a feature commit, push, package, migration, deployment, indexing change, collection of real personal data, or production test-data creation.

## Evidence boundary

- The audited cinematic baseline is preserved and pushed to GitHub `main` at commit `e7299f42908fd2e31b91067d79b433c14a713231`.
- The integrated implementation on local branch `feature/integrated-experience` has passed fresh isolated source verification and remains uncommitted and unpushed.
- No production package has been approved or built from the feature branch, no Hostinger path has been approved, and no deployment or live smoke test is in scope.
- Fresh local source and test results are recorded in `LOCAL_READINESS_2026-09-02.md`. They support separate feature commit/push review, but are not evidence of an exact-commit clean checkout, production parity or production readiness.
- The production feature gates remain fail-closed. Their existence is a safety boundary, not proof that the gated workflows are production-ready.
- Applicant/workforce collection, resume upload, and staff workflows are code-locked off in production in this release. Environment flags alone cannot activate them; a future reviewed source change must accompany the missing control implementation and closure evidence.

## P0 Blocker

| ID | Gap | Release impact | Required owner / decision | Closure evidence |
| --- | --- | --- | --- | --- |
| P0-01 | Applicant and workforce collection do not yet have approved production privacy/legal governance: final notices and immutable approval evidence, purposes, lawful basis, minimum fields, recipients, cross-border treatment, retention/deletion schedule, DPO identity, data-subject request channel and fulfillment procedure. | Real applicant or workforce data must not be collected. The current source code-locks both collection capabilities off in production. | DPO, Legal, Recruitment, Workforce and Product owners. | Approved notice registry and field-purpose-retention matrix are published; DPO/Legal sign-off names and fingerprints the production versions; DSR/retention/deletion behavior passes; a reviewed source change opens only the exact qualified configuration. |
| P0-02 | Applicant email ownership verification, resend, recovery and secure reset are not qualified for production. | An applicant account cannot be treated as belonging to the email owner; recovery could create account takeover or support dead ends. | Security, Recruitment/Product and approved mail-provider owner. | End-to-end tests on a production-like environment show generic anti-enumeration responses, expiring single-use verification/reset tokens, resend throttling, session revocation and auditable recovery for the full applicant scope. |
| P0-03 | Privileged staff workflows lack an approved least-privilege role matrix, MFA, provisioning/deprovisioning, assignment controls, PII-read audit and maker-checker rules for identity- or outcome-impacting actions. | Staff publication and application-decision workflows must remain disabled in production. The current source code-locks staff workflows off in production. | Security, Recruitment, HR/Legal and Product owners. | Approved access/decision matrix is implemented; negative authorization and MFA tests pass for every role; provisioning, deprovisioning, assignment, PII-read audit and maker-checker evidence covers job publication and application status decisions; a reviewed source change opens the qualified capability. |
| P0-04 | No exact production release candidate, approval set, deployment path or rollback point exists. The feature branch is local, uncommitted and unpushed. | There is no deployable or reviewable feature artifact. | Product/Release owner, Security, DPO/Legal and repository owner. | A separately approved feature commit is pushed; clean-checkout qualification passes on that exact commit; an exact package manifest and hashes are approved; the Hostinger path, backup and rollback record are confirmed under a separate deployment approval. |

## P1 Major

| ID | Gap | Release impact | Required owner / decision | Closure evidence |
| --- | --- | --- | --- | --- |
| P1-01 | Resume handling is quarantine-only; the production malware scanner, scan-result enforcement, authorized release/retrieval, access logging, quota monitoring, retention/legal-hold execution and deletion verification are not approved or qualified. | Resume upload must remain disabled in production. The current source code-locks upload off in production. | Security, Recruitment, DPO and infrastructure owners. | Benign and malicious-file tests prove fail-closed scanning; unauthorized retrieval is denied; authorized retrieval and read access are audited; retention/legal-hold/deletion jobs and restore exclusions reconcile to the approved policy; a reviewed source change opens the qualified capability. |
| P1-02 | The final MySQL/runtime profile is unverified: server/PHP versions, extensions, TLS or verified local socket, `utf8mb4`, strict SQL behavior, least-privilege database user, migration preflight/post-verification, concurrency behavior and backup/restore rehearsal. | Data integrity, migration recovery and concurrent workflow behavior are not production-qualified. | Hosting/database, Security and Release owners. | A production-like rehearsal records versions and configuration; least-privilege tests pass; migration preflight/post-checks and concurrency suites pass; database/private-upload/artifact backup and restore reconcile without data loss. |
| P1-03 | Final edge and hosting behavior is unverified: exact document root, HTTPS redirect, certificates, HSTS scope, trusted-proxy topology, forwarded-protocol stripping, security headers, path-denial rules, PHP routing and private-storage separation. | Transport or routing mistakes could expose private/internal content or weaken session security. | Hostinger/infrastructure, Security and Release owners. | Read-only live smoke at the approved target confirms HTTPS and headers, exact-proxy behavior, document-root routing, denial of dotfiles/internal paths/backups/private uploads, secure cookies and no mixed content. |
| P1-04 | Published content has not been reconciled to a current owner-approved corporate source register: legal company identity, jobs and hiring entities, locations, clients, leaders, service/compliance claims, case studies, official email/phone/social channels and anti-fraud channel. | Unverified claims or channels could mislead applicants, clients or regulators and could enable recruitment fraud. | Corporate/Brand, Legal, Recruitment, Marketing and Product owners. | The media-and-claim register links every published item to current evidence, owner, approval date and expiry/review date; rendered-route review contains no unapproved placeholders or claims; job UAT reconciles each production posting to an approved source record. |
| P1-05 | Operational security and recovery controls are not qualified: centralized/redacted logs, alerting, incident response, credential/key rotation, dependency/runtime monitoring, vulnerability scanning, encrypted backups, restore drills and a proportionate penetration test. | A production incident may not be detected, contained or recovered within an approved service objective. | Security, infrastructure, DPO and business-continuity owners. | Monitoring and alert tests produce owned events; log review shows required audit detail without sensitive payload leakage; rotation and restore drills pass; material scan/penetration findings are closed and independently retested. |
| P1-06 | Local source verification is complete, but release-candidate qualification is not: Recruitment and employer-flow UAT, named accessibility review, exact-commit clean-checkout rerun and packaging remain unrecorded. | The local tree may proceed to separate feature commit/push review but cannot be promoted as a production release candidate. | Engineering/QA, Product, Recruitment, Workforce and Accessibility owners. | `LOCAL_READINESS_2026-09-02.md` records the fresh local results; all external P0/P1 gates are closed or formally accepted by authorized owners; business and accessibility sign-offs identify the exact feature commit; clean-checkout verification passes and the package is built only from that commit. |

## P2 Watch Item

| ID | Gap | Impact / treatment | Closure evidence |
| --- | --- | --- | --- |
| P2-01 | The canonical production domain, route canonicals, social accounts, social-share image and final search/indexing activation are not approved. | Keep indexing disabled and avoid publishing social-channel claims until Brand/Product and domain ownership are confirmed. | Canonical/structured-data/robots/sitemap and social-preview tests pass on the approved domain; owner-approved channel links are recorded. |
| P2-02 | The preferred production font/media delivery approach is not finalized. Third-party homepage dependencies may still be replaced or self-hosted after licensing, privacy and performance review. | Track provenance, permissions, availability fallback and cache behavior; do not add unlicensed media. | Rights/provenance register is complete and network-offline/fallback checks preserve legibility and core journeys. |
| P2-03 | Production-target Lighthouse/Core Web Vitals, CPU/network budgets and representative-device performance evidence are unavailable before an approved live target exists. | Local scores can guide optimization but cannot predict production edge, cache, database or device performance. | Agreed Lighthouse and field-monitoring budgets pass on the approved release target and regressions have an owner. |
| P2-04 | The external HRIS/employee portal, CRM/advisor queue, mail/SMS delivery, malware service and analytics remain integration boundaries rather than locally proven services. | Keep handoffs explicit and do not imply synchronization, delivery or service-level guarantees until each provider is approved and tested. | Contract/owner, data map, failure handling, security review and production-like integration tests exist for every enabled provider. |

## Expected Data Movement

These changes are normal only after the relevant production gates and business approvals have passed. They must be reconciled separately from deployment parity:

- Approved job publication, closing, expiry and withdrawal changes made by authorized Recruitment staff.
- New applicant profiles, applications, acknowledgements, documents, tasks, withdrawals and status-history events created through approved workflows.
- Workforce-planning submissions and their owner-queue state once an approved production destination exists.
- Session, throttle, audit, storage-ledger and retention timestamps produced by legitimate runtime activity.
- Owner-approved content changes to leaders, locations, clients, services, claims and official channels after their source evidence is updated.

Expected data movement does not authorize bulk migration, source rewriting, test-data creation, status changes or deletion. Release parity must be checked with source/package hashes and configuration; live data must be reconciled at its own grain with the named data owner.

## Release sequence required for closure

1. **Complete:** fresh isolated local verification is recorded in `LOCAL_READINESS_2026-09-02.md` without modifying production.
2. Obtain separate approval to commit and push the feature branch.
3. Verify a clean checkout of the exact feature commit; complete Recruitment/Workforce UAT and named accessibility review against that commit.
4. Close or formally defer every remaining external P0/P1 item with authorized owner, date and the closure evidence above.
5. Obtain separate approval to package the verified commit and record the manifest and hashes.
6. Confirm the exact Hostinger path plus infrastructure, backup/restore and rollback evidence.
7. Obtain separate production-deployment approval, manually upload to the confirmed Hostinger path and verify readback hashes.
8. Run non-mutating live smoke; use only separately approved accounts/data for any authenticated check.
9. Obtain separate owner approval for any migration or production data movement.
