# Phase 0 Applicant Privacy and Data Inventory

**Purpose:** Decide what applicant information TAASCOR needs, at which recruitment stage, for what declared purpose, under whose authority, with which access and retention rules.
**Current status:** Proposed minimum-collection model awaiting Recruitment, DPO/Legal, and Security approval.
**Boundary:** This document contains field categories only—no applicant records or example personal data.

## Stage model

- `S0` — anonymous job browsing.
- `S1` — initial intent/direct application.
- `S2` — role-fit screening.
- `S3` — shortlisted verification.
- `S4` — conditional offer/pre-employment.
- `S5` — hired employee onboarding.
- `X` — exclude unless DPO/Legal documents a specific necessity, lawful basis, access, retention, and approved exception.

The current Render form collects many S2–S5 categories in one first-touch submission. That pattern must not be carried over literally.

## Proposed applicant-data register

| Data group observed or implied | Risk / concern | Earliest recommended stage | Default decision |
| --- | --- | ---: | --- |
| Selected job/company, first/last name, email/mobile, preferred contact, broad location/work eligibility | Identity/contact data | `S1` | Collect only what routes and contacts the applicant; persist a server-validated job ID so the role is not reselected |
| Essential role-specific screening answers | Can create discrimination/proxy risk if poorly designed | `S1–S2` | Use only objective job-related questions approved by Recruitment/Compliance |
| Resume/CV and accessible manual-entry equivalent | Mixed high-risk container that may include photo, DOB, address, or other excess data | `S1` optional or `S2` | Make optional at first touch where feasible; warn against unnecessary sensitive data; private scanned storage |
| Education and certifications | Detailed personal/qualification history | `S2` | Collect only role-relevant qualification/level; verify later |
| Employment history | Detailed professional history | `S2` | Limit to screening-relevant roles/dates; do not require a lifetime record |
| References and referee contacts | Third-party personal data | `S3` | Collect only after shortlist; applicant should inform referees; define use/retention/contact rules |
| Full present/residential address | High identity/location exposure | `S4` | Use city/municipality or worksite feasibility at S1; full address only when genuinely required later |
| Birth date/place and age | Sensitive/discrimination risk | `S4` only if documented, otherwise `X` | Never use for screening; collect for a named legal/onboarding purpose only |
| Civil/marital status | Sensitive/discrimination risk | `S5` if a benefits/legal purpose requires it, otherwise `X` | Remove from application and screening |
| Religion | Sensitive personal information; no evidenced recruiting need | `X` | Remove by default; any exception requires written necessity, lawful basis, and DPO/Legal approval |
| Spouse, parents, children/dependents/family details | Reveals family/marital context and includes third-party data | `S5` only for specific benefits/statutory purpose | Remove from application/pre-screening and collect only required fields later |
| Emergency contact | Third-party personal data | `S5`, or `S4` for a documented need | Not an application requirement; provide instruction/notice for third-party data |
| SSS, TIN, Pag-IBIG, PhilHealth numbers/documents | Government identifiers; sensitive and fraud-prone | `S4` after conditional offer or `S5` | Never first-touch; masked display, strict role access, excluded from analytics/logs, defined retention |
| NBI/background-check authorization and result | Offence/proceeding-related sensitive data | `S3–S4`, role/law dependent | Separate notice/authorization; restricted result visibility, criteria, and retention |
| Medical/health information, clearance, schedule, or result | Health data; sensitive personal information | `S4` only when legitimately required | Separate conditional workflow; specify provider, recipients, result granularity, retention |
| Application status, recruiter notes, decisions, reason codes | Can materially affect applicants and contain inferred/profiling data | From `S1`, controlled | Map internal statuses to candidate labels; structured notes; prohibit unnecessary sensitive free text; audit changes/access |
| Password and recovery/verification data | Security-critical | Account creation after `S1` or save/resume | Hash passwords; never expose to staff/logs; protect recovery and rate-limit |
| Session/device/security telemetry | Identifiers and behavioral metadata | When form/account starts | Security-only minimum, short retention, no undisclosed marketing reuse |
| Accuracy certification | Attestation, not privacy consent | Review/submission | Keep separate from privacy notice and optional marketing consent |
| Recruitment email/SMS notifications | Contact plus message metadata | `S1` onward | Separate transactional recruiting messages from optional marketing; minimal logs and appropriate opt-out |
| Talent-community/marketing messages | Optional purpose beyond a specific application | Separate opt-in | Unbundled, unselected by default, easy withdrawal, separate retention/purpose |

## Required field-by-field columns before build

For every actual field, record:

`field ID | label | stage | purpose | lawful-basis decision owner | mandatory/optional | applicant explanation | recipient classes | authorized roles | system/vendor | validation | display masking | log prohibition | retention trigger/period | deletion method | notice version | evidence owner | DPO decision | status`.

No field becomes required merely because it exists in the Render database or printable form.

## Collection-specific notice requirements

Before processing, the applicant experience must clearly provide the approved information for that activity, including:

- Categories of data collected.
- Specific purposes and, where applicable, the processing basis.
- Scope and method of processing.
- Recipients or recipient classes, including principals/clients and vendors where applicable.
- TAASCOR/PIC identity and contact information, DPO/data-subject request route.
- Retention period or decision criteria.
- Applicant rights and how to exercise them.
- Any automated decision-making/profiling and its consequence, if used.

The notice is not the same as consent. Accuracy certification, application processing acknowledgment, optional talent-pool consent, and optional marketing consent must not be bundled into one ambiguous checkbox.

The [Philippine Data Privacy Act](https://privacy.gov.ph/data-privacy-act/) requires transparency, legitimate purpose, proportionality, necessary/non-excessive collection, and appropriate retention. The NPC’s [right-to-be-informed guidance](https://privacy.gov.ph/the-right-to-be-informed/) enumerates notice information, and the [implementing rules](https://privacy.gov.ph/implementing-rules-regulations-data-privacy-act-2012/) establish accountability and processing obligations. Final legal/lawful-basis decisions belong to TAASCOR’s DPO/Legal owner.

## Access and security decisions

- Applicant can access only their own account, applications, notices, acknowledgments, status, and documents.
- Recruiter, HR, Admin, Client, and support permissions are separately enumerated and deny by default.
- Client/principal access requires a stage, purpose, approved data subset, contract/notice basis, and audit event.
- Government IDs and medical/background records use separate access groups, masked presentation, and restricted export.
- Uploaded files are privately stored, renamed, type/signature validated, size bounded, scanned/quarantined, and delivered through authorized short-lived access.
- Logs, analytics, error reports, URLs, notifications, and support tickets must not expose passwords, tokens, government IDs, medical information, résumé contents, or unnecessary applicant data.
- Access, export, status, role, document, correction, and deletion actions generate auditable events with defined retention.

## Retention decisions required

Define exact periods/triggers and deletion/anonymization behavior for:

- Abandoned/incomplete applications and temporary uploads.
- Submitted applications under review.
- Rejected, withdrawn, duplicated, or closed applications.
- Optional talent-pool records.
- Shortlist verification and referee data.
- NBI/background and medical information.
- Statutory/government identifiers and onboarding records.
- Accounts and authentication/security logs.
- Notifications and delivery metadata.
- Recruiter notes, audit logs, legal holds, backups, and restored copies.
- Hired records transferred to HRIS/employee systems.

Code deployment, content deployment, and retention/deletion execution are separate gates. No production applicant record should move or be deleted as part of ordinary website release work.

## DPO / Security decisions

1. Who is the Personal Information Controller and published DPO/data-subject contact for recruitment?
2. What exact purpose and lawful-basis decision applies to each field, especially age/birth, education, references, government IDs, NBI, and medical information?
3. Which legacy fields are removed entirely, and what trigger authorizes each deferred S3–S5 field?
4. Which clients/principals, clinics, background providers, mail/SMS vendors, hosting/storage vendors, or affiliates receive data, and what exact subset?
5. What are the retention/deletion rules for every category above?
6. Which notice versions, acknowledgments, and genuinely optional consents are required?
7. What access roles, masking, encryption, upload scanning, logging, incident escalation, and data-subject request process must exist?
8. Is a privacy impact assessment required before government-ID, NBI, medical, or client-sharing workflows launch?

## Recruitment decisions

1. Which questions/documents are essential at S1, S2, S3, conditional offer, and onboarding for each job family?
2. Can a candidate apply without a résumé through an accessible manual-entry path?
3. How are duplicate applicants/applications reconciled while preserving exact job context and history?
4. When, why, and by whom are references, NBI, medical, and statutory identifiers requested and verified?
5. What applicant information may a principal/client see, and at which approved stage?
6. Which candidate-facing statuses, next actions, expected response windows, and official support channels can Operations sustain?

## Privacy gate record

- **Field inventory:** `OPEN / APPROVED / APPROVED WITH GAPS / NO-GO`
- **Collection notice:** `OPEN / APPROVED / NO-GO`
- **Retention schedule:** `OPEN / APPROVED / NO-GO`
- **Recipient/vendor map:** `OPEN / APPROVED / NO-GO`
- **Access matrix:** `OPEN / APPROVED / NO-GO`
- **PIA decision:**
- **Recruitment approver:**
- **DPO/Legal approver:**
- **Security approver:**
- **Accepted gaps/owners:**
- **Next review date:**
