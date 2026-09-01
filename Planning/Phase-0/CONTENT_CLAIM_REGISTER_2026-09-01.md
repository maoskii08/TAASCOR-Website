# Phase 0 Content and Claim Register

**Purpose:** Prevent stale, unsupported, expired, or conflicting public assertions from entering the redesigned TAASCOR experience.
**Current state:** Working register; no row is publication approval.
**Default:** If owner or evidence is missing, the claim remains a visible gap in internal planning and is not published as fact.

## Status and classification

- `PENDING` — evidence/approval incomplete.
- `VERIFIED` — current evidence and allowed wording/surfaces approved.
- `QUALIFIED` — publish only with the approved limitation or context.
- `REMOVE` — do not carry into the new experience.
- `EXPIRED` — once valid but outside its effective/permission period.
- `DYNAMIC` — operational content requiring an authoritative source, owner, timestamp, and automatic review/closure behavior.

Each approved register row must eventually record: exact current copy, source/surface, claim class, current and conflicting values, evidence file/link, evidence date, business owner, evidence custodian, Compliance/Legal approver, effective/review/expiry dates, allowed wording, allowed surfaces, status, and publication notes.

## Initial claim inventory

| ID | Current assertion or conflict | Classification | Accountable owner | Minimum evidence before publication | Initial status / action |
| --- | --- | --- | --- | --- | --- |
| CL-01 | TAASCOR legal name, SEC No. `CS201212925`, and incorporation in 2012 | Corporate identity | Corporate Secretary / Legal | Current SEC certificate/GIS or official verification matching the exact legal entity and number | `PENDING` |
| CL-02 | Local page cites D.O. 18-A certificate `NCR-PFO-7491-101712-098`; Render cites D.O. 174 certificate `RO1VA-LPO DO174-1220-083-R` | Regulatory status | Compliance / Legal | Current certificate, entity match, issuing region, issue/expiry, official verification, and approved wording | `REMOVE` old D.O. 18-A presentation; `PENDING` new value |
| CL-03 | “DOLE-registered manpower outsourcing/contractor” plus recruiting/placement language | Regulatory/service authority | Compliance / Legal | Current D.O. 174 evidence and review of whether recruiting/placement wording creates any separate authority or qualification requirement | `PENDING — HOLD` |
| CL-04 | Six service lines and the precise capabilities within each | Service capability | COO / service-line owners | Current service catalogue, process/SOW support, geographic availability, exclusions, and approved wording | `PENDING` |
| CL-05 | Every candidate receives credential, reference, NBI, medical, and statutory checks | Recruitment control | Recruitment + Compliance | Approved current SOP, stage/role exceptions, owner, completion evidence, and privacy basis | `QUALIFY`; remove universal “every” unless proven |
| CL-06 | SSS, PhilHealth, Pag-IBIG, and BIR are computed, remitted, and filed on cycle with retained audit evidence | Payroll/statutory control | Payroll/Finance + Compliance | Control owner, approved calendar, reconciled anonymized evidence, exception handling, effective period | `PENDING — HIGH RISK` |
| CL-07 | TAASCOR owns/operates an HRIS supporting DTR, payroll basis, payslips, validations, and data-quality controls | Product/operational capability | HRIS product owner + Engineering | Module inventory, production status, role matrix, approved demo evidence, service boundary, security wording | `PENDING` |
| CL-08 | Cainta base, NCR/Rizal/Laguna coverage, seven offices/branches, and published hours | Location/coverage | Admin/Facilities + Operations | Current addresses, operating confirmation, coverage boundary, contacts/hours, effective date | `PENDING` |
| CL-09 | Four jobs and any “active/open” vacancy, company, worksite, requirements, or closing state | Dynamic recruitment data | Recruitment / Hiring manager | Requisition ID, job owner, openings, site, posted/closing dates, publication state, last-reviewed time | `DYNAMIC`; auto-close/expire |
| CL-10 | 27 organizations are trusted clients/partners and/or have job offers | Client/relationship proof | Account director + Marketing/Legal | Exact name, relationship/currentness, logo/publicity permission, approved copy, job link, client approval | `PENDING — HOLD ALL` |
| CL-11 | Seven leadership/management roles, names, and organization chart | Leadership/corporate | Executive Office / HR | Current appointment/org chart, approved bios/images, public-display approval | `PENDING` |
| CL-12 | Applicant accounts track status, orientation, and medical requirements; HR/admin access exists | Authenticated product capability | Recruitment systems owner + Engineering | Source-backed feature inventory, role authorization, state map, notifications, non-production demonstration | `REMOVE FROM CLAIMS` until verified |
| CL-13 | Applicant data is “securely stored/confidential” and not shared except with consent/law | Privacy/security | DPO + Security | Data/recipient map, access roles, controls, retention/deletion, incident process, approved collection-specific notice | `REMOVE` absolute wording; replace with evidenced facts |
| CL-14 | “Accepting manpower requisitions,” contact address/email, phone, and Mon–Sat hours | Sales/operations availability | Sales/Operations + Content | Monitored channels, routing owner/SLA, current hours/address, quarterly verification | `PENDING` |
| CL-15 | “Decades of combined experience,” “trusted,” “one accountable partner,” and any KPI/counter | Marketing/performance | Executive/Marketing + metric owner | Defined method, dated source, denominator/population where quantitative, approval, and illustration label where applicable | `QUALIFY OR REMOVE` |

DOLE states that D.O. 174 superseded D.O. 18-A. The two certificate values must not be blended or silently carried forward. Verification requires current TAASCOR documentation and owner approval; this register does not make a legal determination. Sources: [DOLE announcement](https://dole.gov.ph/news/bello-signs-d-o-on-contracting-and-subcontracting/), [registration guidance](https://ble.dole.gov.ph/registration-of-job-contractor/), and [official registry portal](https://csrs.dole.gov.ph/).

## Client/logo validation register

All 27 Render client cards enter the internal register as `HOLD`. A former relationship must not be represented as current; a logo permission does not automatically approve a testimonial, case study, job association, or outcome claim.

Required fields:

| Field group | Required values |
| --- | --- |
| Identity | Proof ID, exact legal name, approved display name, source card/page |
| Relationship | Client/former client/partner/vendor/prospect, current status, scope, capability, start/end dates, account owner |
| Permission | Client approver/contact, written permission/license basis, allowed channels/surfaces, territory, start/expiry, brand guidelines |
| Assets/copy | Logo source/version, approved description, case-study/testimonial approval, contract/SOW evidence reference |
| Jobs | Linked active job IDs, site/location, owner, last verified, automatic unlink/closure rule |
| Governance | Confidentiality restrictions, Legal/Marketing approver, next review, status, publication notes |

Candidate list for reconciliation: First Sumiden, Ohgitani, Multimix, Lazada, Shopee, Cainiao, Leslie, Aboitiz Land, Bi-Chain, Fujifilm, SIIX Coxon, MTC-Transport, Prime Worldwide, Swhistler Steel, Centro, Cavite Light Industrial Park, Globalmaxx, Auto 88, Sealed Air, WCL Cold Storage, CYA, Delta Milling, Pasture to Plate, Euro-Med, Yuanshan, Maxistar, and Shinsei.

## Decision questions

### Compliance / Legal

1. Which current SEC and D.O. 174 documents match the exact publishing entity, and what are their issue/expiry/verification details?
2. Which certificate number is current, and who owns correction of the older public D.O. 18-A footprint?
3. Does public recruiting, placement, contractual-staffing, or applicant-routing language require a separate authority or qualification?
4. Which screening, onboarding, remittance, and compliance claims are universal versus client-, service-, role-, or period-dependent?
5. Which offices, coverage, leadership, service, and corporate facts are current and approved?

### Recruitment / Operations

1. What is the canonical source and owner for jobs, openings, clients/worksites, dates, and closures?
2. Which of the four public jobs and 42 company choices are active and authorized?
3. What exact statuses, reasons, SLAs, candidate messages, orientation steps, and medical steps exist today?
4. What response-time promise, official channels, fee/anti-fraud statement, and escalation route can Operations sustain?
5. What applicant information may a client/principal see, at which stage, for which purpose, and under whose approval?

### Content / Marketing

1. Which vision, mission, and values version is canonical?
2. Which service descriptions and office/location facts have operational owners and review dates?
3. Which client names, logos, descriptions, case studies, leadership biographies, photographs, and HRIS captures have current public-use permission?
4. Which quantitative statements have exact definitions, sources, denominators, periods, and owner approval?

## Publication rule

Only a `VERIFIED` or explicitly `QUALIFIED` row with owner, evidence, wording, allowed surfaces, and review/expiry date may be released. Dynamic jobs must be driven from their authoritative source and never inferred from client-card tags. Missing evidence remains an internal action—not a public zero, badge, or claim.
