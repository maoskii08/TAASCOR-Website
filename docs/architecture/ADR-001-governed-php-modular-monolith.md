# ADR-001: Governed PHP modular monolith

- **Status:** Accepted for the local release candidate; production acceptance remains gated.
- **Date:** 2026-09-01
- **Decision owners:** Product, Engineering, Recruitment, Security, DPO/Legal, and Release owners to be assigned.

## Context

The repository began as a single cinematic HTML document. The reference Render website publicly demonstrated jobs, an application form, client/company content, and applicant authentication, but its backend, authenticated workflows, database, storage, and operating controls were unavailable. Copying that implementation would import unverified behavior and an over-collecting mobile form into a stronger visual baseline.

The target needs a premium public narrative and dependable task flows without requiring a large framework or merging HRIS/payroll data into a public application.

## Decision

Use a dependency-light PHP modular monolith with server-rendered routes and two presentation modes:

1. The homepage keeps the cinematic `index.html` experience, served through `index.php` so security headers and governed job data can be applied without rewriting the visual source.
2. Public task pages use the shared `site/` shell and local assets.
3. Recruitment, applicant, staff, and employer-brief routes use the shared `app/` security, data, audit, and domain modules.
4. Applicant and Staff authentication remain separate entry points even though they share the users table.
5. Employee/HRIS remains an explicit external handoff. No payroll or employee record is copied into this public repository.
6. SQLite supports isolated local QA. The production schema is also expressed for MySQL, but the exact supported Hostinger runtime and database are an approval gate.
7. Dynamic truth—jobs, applications, tasks, statuses, notice acknowledgements, audit events, and workforce briefs—comes from the database rather than duplicated page copy.
8. Static claims, locations, leadership, clients, logos, and case studies remain evidence-gated until an owner approves a maintained source.

## Component boundaries

| Component | Responsibility | Data boundary |
| --- | --- | --- |
| `index.html`, `index.php` | Cinematic home, audience routing, governed job preview | Reads published jobs; does not start an authenticated session |
| `site/`, `assets/` | Shared public presentation, navigation, metadata, and error surface | No private records |
| `careers/` | Public search, filters, job detail, closing state | Published jobs only |
| `apply/`, `account/`, `applicant/` | Staged application and applicant self-service | Current applicant's own records only |
| `staff/` | Job administration and application workflow | Authorized staff only; audited state changes |
| `workforce/` | Employer staffing brief | Minimal business contact and planning details |
| `app/` | Configuration, session/auth, CSRF, authorization, database, audit, upload quarantine | Shared private application core |
| `database/`, `scripts/` | Schemas and explicit CLI lifecycle | No automatic production migrations or seeds |
| `tests/` | Disposable synthetic verification environment | Loopback and `tests/.artifacts` only |

## Security and privacy consequences

- Production configuration fails closed if secrets, database, private upload storage, or secure cookies are not explicit.
- State changes require CSRF validation; sessions rotate at authentication and have idle and absolute expiry.
- Uploaded resumes remain outside the web root in quarantine. There is deliberately no download/release route until malware scanning, authorization, retention, and operational ownership are approved.
- Notice versions are stored with applicant/workforce acknowledgements. Draft collection notices keep the affected routes out of search indexing.
- Staff status changes follow an explicit transition map and create audit/status history.
- The application does not implement production email/SMS, account recovery, MFA, CRM, HRIS, or live-data migration without named providers and owners.

## Alternatives considered

- **Keep one static file:** rejected because secure applications, identity, one-source jobs, audit history, and privacy acknowledgement require server-side state.
- **Copy the Render PHP surface:** rejected because the source and authenticated behavior are unavailable and its public form/security/mobile posture is not an acceptable baseline.
- **Adopt a full framework immediately:** deferred. A framework may be justified after hosting/runtime, team ownership, integrations, queues, and authenticated scope are confirmed; adding it now would increase migration surface without resolving governance gates.
- **Combine public, applicant, staff, HRIS, and client login:** rejected because the data and authorization boundaries differ materially.

## Validation required before production acceptance

- MySQL migration dry run and rollback on the confirmed target runtime.
- DPO/Legal approval of notices, purposes, retention, deletion, and data-subject channels.
- Role matrix, staff provisioning owner, email verification/recovery, and MFA decision.
- Malware scanning/release workflow and authorized document retrieval.
- Security review and proportionate penetration test.
- Backup/restore rehearsal, observability, failure queues, incident and rollback owners.
- Content/claim/media approval and business UAT against the exact release commit.
