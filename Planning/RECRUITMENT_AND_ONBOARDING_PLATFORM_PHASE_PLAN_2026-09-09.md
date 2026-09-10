# TAASCOR Recruitment and Onboarding Platform Phase Plan

**Date:** 2026-09-09
**Status:** Local R1-R5 implementation complete behind foundation source locks; implementation-level static checks pass; database qualification, business UAT, commit/push, production migration, deployment, and workflow activation remain separately gated
**Selected system boundary:** `taascor.com` owns every public and candidate-facing recruitment and onboarding URL. TAASCOR HRIS remains the authoritative staff/back-office system for requisitions, hiring decisions, onboarding administration, employee conversion, and employee records. The website may read only the approved public-job projection from HRIS; it must never expose the HRIS hostname as a candidate destination.

## 1. Goal

Deliver a secure, mobile-first recruitment and onboarding module inside TAASCOR HRIS that takes an approved requisition through publication, application, screening, interview, offer, onboarding readiness, and controlled employee activation without weakening the immersive public website or collecting unnecessary personal data.

The platform must give applicants a calm, transparent journey and give Recruitment, HR, hiring managers, and administrators an operational workspace with clear ownership, least-privilege access, human decision controls, and complete audit history.

## 2. Current foundation to retain

The project already contains:

- Public Careers discovery and job-detail routes.
- A two-stage, minimum-data application flow with server-validated job context.
- Applicant registration, login, save/resume, dashboard, status history, assigned tasks, withdrawal, settings, and privacy-notice history.
- Separate staff authentication and recruiter routes for jobs, applications, status transitions, and action requests.
- Database support for users, jobs, applications, job snapshots, status history, tasks, documents, privacy acknowledgements, authentication attempts, and audit events.
- Private-document quarantine boundaries, deterministic migrations, security checks, and browser tests.
- Production code locks that keep applicant collection, uploads, staff workflows, and job publication disabled until their governance gates close.

The website implementation is the canonical public and candidate presentation layer, but not the staff system of record. Its public Careers experience, applicant routes, and Guide Center remain same-origin on `taascor.com` and stay fail-closed until their governance gates close. HRIS supplies the authoritative approved job projection and owns staff operations and employee records. Candidate-facing records must use one governed integration boundary before activation; records and authentication must not be silently duplicated across both systems.

The canonical HRIS source is `C:\Users\mAOskii\Documents\TAASCOR HRIS\github_publish\TAASCOR`. It is a custom PHP/MySQL application with existing authenticated roles, employee identity, audit-oriented controls, and manual Hostinger release practices. Its current worktree contains unrelated payroll/DTR changes, so recruitment implementation must start from a verified clean base in an isolated branch or worktree.

## 3. Product boundary

### TAASCOR website owns

- Immersive public brand, company, solutions, proof, and portfolio content.
- Public Careers discovery using only approved, publishable HRIS job data.
- Search-friendly job-detail views containing no applicant or staff data.
- Same-origin applicant registration, sign-in, application, status, interview, offer, onboarding, document, message, settings, and help surfaces on `taascor.com`.
- A clear Apply transition into the `taascor.com` candidate surface using a validated job reference and safe return path.
- Public privacy, accessibility, and anti-fraud information.
- The public Recruitment and Onboarding Guide Center.

### TAASCOR HRIS owns

- Requisition intake, approval state, and accountable hiring owner.
- Job publication, closing, expiry, and channel provenance.
- Authoritative recruitment records and staff access to candidate/application records through the approved service boundary.
- Screening, assignments, interviews, scorecards, decisions, and candidate-visible status.
- Offer preparation, approval, controlled delivery, acceptance/decline, and version history.
- Pre-employment onboarding cases, requirements, checklists, due dates, exceptions, and readiness approval.
- Candidate communications and notification-delivery evidence.
- Internal recruitment decisions, protected documents, communications, and audit history.
- Onboarding readiness and the governed conversion from accepted candidate to employee.
- The authoritative employee master after approved conversion.
- Payroll, timekeeping, schedules, benefits, employee self-service, and ongoing employment records.
- Post-hire changes that are no longer part of candidate or pre-employment processing.

The public Careers catalogue receives only the minimum approved public-job projection through a read-only HRIS endpoint or release-safe export, with bounded cache and outage behavior. Candidate-facing workflows remain on `taascor.com`; their service integration may transmit only the fields required for the current authorized action. Employee and payroll datasets never move to the public website. No applicant-facing link may expose `taascor.visiotechsolutions.com`.

## 4. Governed lifecycle

1. **Requisition:** Draft → submitted for approval → approved/rejected → ready to publish.
2. **Opportunity:** Draft → published → paused/closed/expired, with job-version history.
3. **Application:** Draft → submitted → reviewing → shortlisted → interview/requirements → conditional decision → declined/withdrawn.
4. **Offer:** Draft → approval pending → approved → delivered → accepted/declined/expired/withdrawn.
5. **Pre-employment onboarding:** Not started → in progress → blocked → ready for employee conversion → converted/cancelled.
6. **Employee conversion:** Ready for review → independently reviewed → activated → reconciled, or blocked and queued for resolution.

Status changes that affect selection, offers, identity, sensitive requirements, or employee creation require an authorized human decision. Automation may route, remind, validate, and reconcile; it must not silently make hiring decisions.

## 5. Candidate experience

- Mobile-first application with progress, autosave, clear time expectations, and no repeated job selection.
- Email ownership verification, secure recovery, session controls, and anti-enumeration responses.
- Application timeline using candidate-safe language and dated events.
- Interview invitations with timezone, location/video details, confirmation, reschedule request, and accessibility needs route.
- Offer center with version, expiry, download/view, acceptance/decline, and confirmation receipt.
- Onboarding workspace showing only applicable tasks, why each item is needed, who can see it, due date, acceptable alternatives, and help/escalation path.
- Secure document requests that open only at the approved stage; manual-entry alternatives where practical.
- Notification preferences, privacy notice history, correction/withdrawal/request controls, and anti-fraud guidance.

## 6. Staff experience

- Requisition queue with owner, approver, workforce/client context, priority, target dates, and approval evidence.
- Kanban and accessible table views of the candidate pipeline, with saved filters and workload ownership.
- Candidate/application detail with role-scoped contact data, timeline, tasks, documents, notes, interview history, decisions, and audit history.
- Structured interview kits and scorecards based on approved job criteria; private panel input until submission.
- Offer generation and approval with immutable versions and separation between preparer and approver where required.
- Onboarding case templates by worker type, location, assignment, and client/site requirement.
- Exception queue for overdue, rejected, missing, expired, or inconsistent requirements.
- Operational dashboards for defined measures such as stage age, aging exceptions, completion, notification failure, and employee-conversion reconciliation. No illustrative homepage statistic becomes an operational KPI.

## 7. Data model extensions

Extend the canonical HRIS schema through additive, rerunnable migrations for:

- `requisitions`, `requisition_approvals`, and `job_publication_events`.
- `application_assignments`, `application_stage_events`, and structured staff notes.
- `interviews`, `interview_participants`, `interview_scorecards`, and scorecard criteria.
- `offers`, `offer_versions`, `offer_approvals`, and `offer_responses`.
- `onboarding_cases`, `onboarding_templates`, `onboarding_items`, and `onboarding_item_events`.
- `document_requests`, `document_reviews`, and approved document classifications.
- `notification_messages`, `notification_attempts`, and retry/dead-letter state.
- `candidate_employee_links`, `employee_conversion_reviews`, reconciliation results, and failure queues.

Every new table must define purpose, owner, classification, access roles, retention/deletion behavior, audit events, and recovery expectations before implementation.

## 8. Delivery phases

### R0 — Decisions, workflow evidence, and governance

- Record HRIS as the owner of authenticated recruitment, onboarding, and employee conversion; record the website as public discovery only.
- Approve the recruitment/onboarding state model, role matrix, decision rights, and maker-checker points.
- Approve the field-purpose-retention matrix and candidate language for every stage.
- Confirm official job source, email/SMS/calendar/e-sign providers, document scanner, and failure ownership.
- Define launch metrics and support/escalation channels.

**Exit gate:** Product, Recruitment, HR, Security, DPO/Legal, HRIS, and Release owners sign the exact scope and data map.

### R1 — Identity, authorization, and communication foundation

- Implement a candidate identity realm presented only on `taascor.com`, separated from employee and staff sessions and permissions, with an explicit governed service boundary to HRIS records.
- Complete applicant email verification, resend, recovery, secure reset, throttling, and session revocation.
- Implement least-privilege staff roles, MFA, provisioning/deprovisioning, assignment scope, PII-read audit, and negative authorization tests.
- Build notification outbox, provider adapter, retry/dead-letter handling, and delivery evidence.
- Qualify private document scanning, authorized release, retention, legal hold, deletion, and restore exclusions.

**Exit gate:** Security and privacy qualification passes in a non-production environment.

### R2 — Recruitment operations MVP

- Build requisition approval and governed job publication.
- Port the approved minimum-data application behavior and state controls from the website reference into HRIS, without copying synthetic or production records.
- Publish a minimal read-only job projection for the website and validate the same-origin website Apply transition while retaining the exact HRIS public job reference server-side.
- Add interviews, scheduling, structured scorecards, candidate communications, and exception handling.
- Add recruiter work queues, filters, ownership, audit views, and operational metrics.

**Exit gate:** Recruitment UAT completes the requisition-to-decision journey using synthetic records.

### R3 — Offers and pre-employment onboarding

- Build offer preparation, approval, delivery, response, expiry, and immutable version history.
- Create template-driven onboarding cases and conditional requirements.
- Add task dependencies, due dates, reminders, document requests, review outcomes, readiness checks, and escalation.
- Keep medical, government-ID, background-check, and other sensitive steps disabled until each has an approved purpose, minimum fields, lawful basis, access rule, retention rule, and secure provider/process.

**Exit gate:** HR, Recruitment, Security, and DPO/Legal approve the offer and onboarding workflows and their rendered candidate language.

### R4 — Candidate-to-employee conversion and reconciliation

- Prepare the minimum approved employee record only after offer acceptance and onboarding readiness approval.
- Add maker-checker review, idempotency, duplicate detection, reconciliation, and exception ownership inside HRIS.
- Prevent duplicate employee creation and retain evidence linking the application, offer, onboarding case, and resulting employee reference.
- Do not bulk migrate historic candidates or create production employees without a separate data action approval.

**Exit gate:** HRIS and data owners reconcile end-to-end synthetic cases, including failure and rollback scenarios.

### R5 — Release qualification and staged activation

- Run unit, database, migration, authorization, abuse, accessibility, responsive, performance, visual, backup/restore, deletion, notification-failure, and integration tests.
- Run business UAT on desktop and representative mobile devices with synthetic records only.
- Package from an exact approved Git commit; record hashes, backup, rollback, and deployment manifest.
- Activate capabilities in controlled slices: staff access, approved jobs, website job projection, applicant accounts, applications, documents, offers/onboarding, then employee conversion.

**Exit gate:** all P0/P1 findings are closed; P2 items have named owners and acceptance; the exact candidate receives separate commit/push and deployment approvals.

## 9. Initial route map

### Website public and candidate routes

- `/careers/`
- `/jobs/{approved-job-slug}/`
- `/apply/{approved-job-slug}/`
- `/account/register.php`, `/account/login.php`, and governed recovery/verification routes
- `/applicant/` and its candidate-owned application, interview, offer, onboarding, document, message, and settings views
- `/recruitment/guide/`

All routes above remain on `https://taascor.com`. HRIS candidate preview routes are legacy deployment artifacts and must redirect to the corresponding website destination; they are not canonical candidate URLs.

### HRIS staff routes

- `/staff/requisitions`
- `/staff/jobs`
- `/staff/pipeline`
- `/staff/candidates/{id}`
- `/staff/interviews`
- `/staff/offers`
- `/staff/onboarding`
- `/staff/exceptions`
- `/staff/reports`
- `/staff/admin/access`

The exact HRIS URL prefix should follow its existing route convention. These are target capabilities, not promises that each is currently available.

## 10. Risks and release boundaries

- **P0 Blocker:** No real applicant/onboarding collection until approved privacy notices, field purposes, retention/deletion, DSR process, and owner evidence are complete.
- **P0 Blocker:** No staff production access until MFA, role/assignment controls, lifecycle provisioning, PII-read audit, and decision controls pass.
- **P0 Blocker:** No candidate identity can be trusted until email verification and recovery are qualified.
- **P1 Major:** No production document collection until malware scanning, authorized retrieval, audit, quotas, retention, deletion, legal hold, and backup exclusions pass.
- **P1 Major:** No job may be published without an approved requisition, hiring entity, owner, dates, requirements, worksite, official contact path, and anti-fraud reconciliation.
- **P1 Major:** No candidate-to-employee conversion until minimum fields, duplicate detection, maker-checker approval, rollback/reversal behavior, and reconciliation are approved.
- **P1 Major:** The canonical HRIS worktree currently has unrelated payroll/DTR changes; recruitment work must not be layered into or stage those changes.
- **P2 Watch Item:** Email/SMS/calendar/e-sign providers remain replaceable adapters until contractual, privacy, security, and availability reviews are complete.
- **Expected Data Movement:** Approved jobs, applications, interview events, offers, onboarding tasks, documents, status events, notifications, and employee conversions will change through normal authorized operations; these are not deployment drift.

## 11. Definition of done

- A candidate can discover an approved role and complete the full mobile journey through accepted offer and onboarding readiness without duplicate entry or unexplained status.
- Each staff role sees only its authorized work and data; negative tests prove prohibited access fails.
- Every consequential decision has a named human actor, timestamp, reason/evidence boundary, and audit event.
- Sensitive requests appear only at the approved stage and follow the approved access and retention rules.
- Notifications are observable, retryable, and never treated as delivered without provider evidence.
- Candidate-to-employee conversion is minimal, maker-checker controlled, idempotent, and reconciled with no duplicate employee creation.
- Accessibility, responsive behavior, security, backup/restore, deletion, and failure paths pass against an exact commit.
- Production activation is separately approved, reversible, and smoke-tested without real test applicant or employee records.

## 12. Implementation handoff

The clean isolated HRIS worktree on `codex/recruitment-onboarding-20260909`, based on `c8da90d6a7a62b499185482faacdc7ae00c9a44a`, contains the complete local R1-R5 back-office implementation. It includes additive migrations 01 through 06, lifecycle policies and services, staff queues and action API, interviews and scorecards, offers, onboarding, secure documents, notifications, capability administration, employee conversion and reconciliation, and automated implementation checks. The website includes an opt-in HTTPS HRIS public-job projection, same-origin candidate handoffs, and a public Guide Center. HRIS candidate-preview URLs are non-canonical and redirect to `taascor.com`. All capabilities remain source-locked in `foundation` mode, so environment flags alone cannot activate public jobs, candidate identity, staff data access, documents, mutations, or employee conversion.

The next phase is qualification, not additional feature construction: apply all migrations twice to a disposable loopback database, execute synthetic end-to-end and negative-authorization cases, validate notification and scanner adapters, test deletion/restore and reconciliation failure paths, complete responsive/accessibility/business UAT, and close all P0/P1 findings. Neither repository is committed, pushed, migrated, or deployed until its bounded change set and exact test evidence receive separate approval.
