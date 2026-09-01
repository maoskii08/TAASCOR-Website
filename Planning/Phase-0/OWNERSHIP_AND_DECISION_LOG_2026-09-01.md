# Phase 0 Ownership and Decision Log

**Status:** Open working register. Replace role placeholders with names only after the accountable person accepts ownership.

## Ownership roster

| Area | Accountable role | Named owner | Alternate | Evidence/location | Acceptance status |
| --- | --- | --- | --- | --- | --- |
| Executive scope and funding | Executive sponsor |  |  |  | OPEN |
| Three audience journeys and release scope | Product owner |  |  |  | OPEN |
| Jobs, candidates, statuses, SLAs | Recruitment owner |  |  |  | OPEN |
| Corporate/DOLE/legal claims | Compliance/Legal owner |  |  |  | OPEN |
| Applicant privacy and retention | DPO/privacy owner |  |  |  | OPEN |
| Authentication and application security | Security owner |  |  |  | OPEN |
| HRIS capabilities and portal boundary | HRIS product/system owner |  |  |  | OPEN |
| Source, architecture, tests, migration | Engineering owner |  |  |  | OPEN |
| Database and production data | Data owner |  |  |  | OPEN |
| Render and Hostinger topology/release | Infrastructure/release owner |  |  |  | OPEN |
| Public content, SEO, media, client approvals | Marketing/content owner |  |  |  | OPEN |
| Applicant support and notification operations | Operations owner |  |  |  | OPEN |

## Decision log

Status values: `OPEN`, `READY FOR DECISION`, `DECIDED`, `SUPERSEDED`, `BLOCKED`.

| ID | Decision | Recommended position | Accountable owner | Required evidence | Status | Decision / date |
| --- | --- | --- | --- | --- | --- | --- |
| D-001 | Initial GitHub baseline scope | Track the reviewed local source, audit, plan, Phase 0 pack, README, and Git controls only; no feature remediation in the first commit | Engineering + Product | Baseline manifest and dry-run staging list | READY FOR DECISION |  |
| D-002 | Permission to commit/push baseline | Commit and push are separate explicit actions after manifest review | Product/repository owner | Exact staged names, secret scan, diff check | OPEN |  |
| D-003 | Render system disposition | Classify as migratable, partially migratable, or reference-only | Product + Engineering | Render handoff checklist | OPEN |  |
| D-004 | Primary experience model | Adopt Build a Workforce, Find Work, and Access TAASCOR | Product + Executive sponsor | Journey prototype/user validation | OPEN |  |
| D-005 | Portal boundary | Applicant/Staff in recruitment system; Employee/HRIS and Client remain separate unless owners approve contracts | Product + HRIS + Engineering | Authenticated workflow/system map | OPEN |  |
| D-006 | Target application architecture | Leading option: supported Laravel/Blade/Vite modular application, subject to Hostinger/runtime evidence | Engineering + Infrastructure | Architecture decision record | OPEN |  |
| D-007 | Canonical domain and deployment target | One canonical domain; GitHub source of truth; named Hostinger path for approved manual release | Product + Infrastructure | DNS/hosting inventory and rollback | OPEN |  |
| D-008 | DOLE/SEC public wording | Hold old/current-status wording until documentary and owner verification | Compliance/Legal | Current entity/certificate/registry evidence | OPEN |  |
| D-009 | Applicant collection stages | Adopt minimum staged collection; exclude legacy religion/family/government-ID first-touch fields | DPO + Recruitment | Field-by-field privacy register | OPEN |  |
| D-010 | Client and media publication | Import all 27 client candidates privately as HOLD; publish only approved items | Account owner + Legal/Marketing | Permission and relationship register | OPEN |  |
| D-011 | Release-one authenticated scope | Build only confirmed jobs/apply/account/status workflows; defer unverified orientation/medical/HR/admin features | Product + Recruitment | Source-backed workflow inventory | OPEN |  |
| D-012 | Live-data migration | Separate approval, backup, preview, rehearsal, reconciliation, and DPO/data-owner sign-off | Data owner + DPO + Release | Migration plan and dry-run evidence | OPEN |  |

## Decision record template

For every `DECIDED` item, record:

- **Decision ID and title:**
- **Date and approver:**
- **Context/evidence reviewed:**
- **Decision:**
- **Alternatives rejected and why:**
- **Scope affected:**
- **Risks/conditions:**
- **Verification/closure evidence:**
- **Review or expiry date:**

No blank owner or missing evidence should be converted into an assumed business rule.
