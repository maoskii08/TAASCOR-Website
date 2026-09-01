# Phase 0 Kickoff — Evidence, Ownership, and Baseline

**Gate:** `G0 OPEN`
**Recommended workshop:** 90 minutes
**Decision objective:** Establish what can be migrated, what must be rebuilt, who owns each source of truth, and whether the reviewed local site may become the first GitHub baseline.

## Work completed locally

- Initialized a local Git repository on branch `main`.
- Added remote `origin` as `https://github.com/maoskii08/TAASCOR-Website.git`.
- Confirmed the remote still exposes no heads or tags.
- Created a structured backup of the original public source files under the ignored `Backups/` folder.
- Added repository hygiene files and a current-state `README.md`.
- Prepared this Phase 0 evidence, ownership, claim, privacy, and Render-handoff pack.
- Did not stage, commit, push, access authenticated production, submit a form, transfer applicant data, or deploy.

## Workshop outcome

At the end of the session, the team should be able to state:

1. Who owns Product, Recruitment, Compliance/Legal, Privacy/DPO, HRIS, Engineering, Content, Security, Infrastructure/Release, and production data.
2. Whether the Render system is **migratable**, **partially migratable**, or **reference-only**.
3. Whether the current local cinematic source and governance documents are approved as the initial GitHub baseline.
4. Which public claims are verified, qualified, removed, expired, or pending.
5. Which applicant fields are collected at each approved stage and which are removed.
6. Which client logos, descriptions, jobs, offices, leadership details, and media are approved for public use.
7. What Hostinger runtime/path, database, storage, mail, queue/cron, backup, and rollback capabilities are available.
8. The exact release-one scope and exclusions.

## Required attendees

| Role | Why required | Named attendee |
| --- | --- | --- |
| Executive sponsor | Resolves scope and ownership conflicts |  |
| Product owner | Owns the three audience journeys and release scope |  |
| Recruitment owner | Owns jobs, applications, statuses, orientation, and candidate communications |  |
| Compliance/Legal owner | Owns corporate, DOLE, recruiting/placement, and public claim approval |  |
| DPO/privacy owner | Owns collection purposes, lawful bases, notices, retention, rights, and vendors |  |
| HRIS/system owner | Defines HRIS capability and portal boundaries |  |
| Engineering owner | Owns source, architecture, migration, security implementation, and tests |  |
| Infrastructure/release owner | Owns Render/Hostinger topology, backup, rollback, and release evidence |  |
| Marketing/content owner | Owns copy, media, client permissions, SEO, and review dates |  |
| Security owner | Owns authentication, authorization, uploads, logging, and test-access controls |  |

## Inputs to bring

- Current SEC/entity documents and authorized corporate profile.
- Current DOLE certificate and official verification evidence, including issue/expiry details.
- Render source/repository owner and the safe G0 handoff package described in the Render checklist.
- Current job/requisition owner list and active/closed job inventory.
- Applicant workflow/status definitions, notification templates, and role-permission matrix.
- Privacy Notice, retention policy, vendor/subprocessor list, DPO contact, and current application-form purpose decisions.
- Client/logo permission records, approved relationship descriptions, current leadership/org chart, and office list.
- Hostinger plan/runtime information, named deployment path, database/storage/queue/mail capabilities, backup, and rollback evidence.

Do not bring live secrets or applicant records into the workshop pack.

## Agenda

| Time | Topic | Required decision/output |
| --- | --- | --- |
| 0–10 min | Confirm boundary and desired outcome | No production/data/credential action; approve G0 scope |
| 10–25 min | Source and system ownership | Render owner, source availability, migratability classification path |
| 25–40 min | Jobs and applicant workflow | Canonical job source, statuses, roles, release-one workflow |
| 40–55 min | Compliance and public proof | DOLE/SEC resolution, claims, clients, leadership, offices |
| 55–70 min | Privacy and sensitive data | Field staging, notice, retention, recipients, DPO decisions |
| 70–80 min | Hosting and architecture constraints | Hostinger/runtime/backup/rollback facts and technical owner |
| 80–90 min | Decisions, owners, and next gate | G0 result, accepted gaps, evidence due, G1 start condition |

## G0 gate checklist

- [ ] All accountable roles have named owners and alternates.
- [ ] The exact local baseline scope is accepted or revised.
- [ ] The Render source/live relationship is evidenced or formally unavailable.
- [ ] Render is classified as migratable, partially migratable, or reference-only.
- [ ] Runtime, dependency, environment-variable-name, schema, role, workflow, storage, integration, backup, and test-access inventories have dispositions.
- [ ] Current DOLE/SEC evidence and approved wording are resolved—or public claims remain held.
- [ ] Client/logo/media register has an owner; all unverified items remain private/on hold.
- [ ] Applicant data groups have stage, purpose, lawful basis owner, access, retention, and notice decisions.
- [ ] Canonical production domain and Hostinger runtime/path ownership are known.
- [ ] Release-one inclusions and exclusions are signed off.
- [ ] Every open gap has an owner, target decision date, and accepted effect on scope.
- [ ] Product and Engineering record the G0 result.

## Gate record

- **G0 result:** `OPEN / PASS / PASS WITH ACCEPTED GAPS / NO-GO`
- **Render classification:** `MIGRATABLE / PARTIALLY MIGRATABLE / REFERENCE-ONLY`
- **Baseline approval:** `PENDING / APPROVED / REVISE`
- **Decision date:**
- **Product approver:**
- **Engineering approver:**
- **Compliance/Legal acknowledgment:**
- **DPO/Security acknowledgment:**
- **Accepted gaps and owners:**
- **Next action:**
