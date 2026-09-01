# Render Recruitment System Evidence and Handoff Checklist

**System in scope:** `https://taascor.onrender.com/`
**Purpose:** Determine whether the existing PHP recruitment application can be migrated, selectively reused, or must remain a functional reference before TAASCOR begins implementation.
**Phase:** Phase 0 — evidence recovery and ownership
**Operating boundary:** Evidence gathering only. Do not deploy, change production, submit public forms, create real applicants, export applicant data, rotate credentials, or test authenticated workflows without separate approval.

## Gate decision this checklist supports

G0 can close only when the Product and Engineering owners can make one of these evidence-backed decisions:

1. **Migratable** — the complete source, schema/migrations, runtime contract, dependencies, workflows, service integrations, and recovery path are available and owned.
2. **Partially migratable** — specifically named modules or data structures are recoverable, while the remaining capabilities will be rebuilt from approved requirements.
3. **Reference-only** — the public Render application may inform requirements, but its code, authenticated behavior, and data are not treated as reusable or authoritative.

An unavailable item is not an automatic failure if the accountable owner formally classifies it as unavailable and accepts the resulting rebuild, migration, or continuity risk.

## Safe evidence-handling rules

- Provide **environment-variable names only**, never secret values, in the handoff package.
- Transfer credentials, if later approved, through the organization's approved password manager or secret-sharing channel. Never place credentials in Git, email, chat, screenshots, or this checklist.
- Provide a **schema-only** database export for G0. No production rows, applicant profiles, government identifiers, medical information, résumés, or uploaded documents are required.
- Where examples are essential, use synthetic or irreversibly redacted fixtures that cannot be linked to a real person.
- Provide file-storage inventory as counts, aggregate size, types, retention classes, and paths/prefixes—not the files themselves.
- Sanitize logs before sharing: remove credentials, session IDs, reset links, email addresses, phone numbers, government IDs, addresses, résumé contents, and request bodies.
- Record a SHA-256 hash, capture date/time zone, source owner, and provenance for every delivered archive or export.
- Keep the original evidence read-only. Any normalization or redaction should create a derivative file with its own hash.

## G0 essential evidence

The items below are the minimum evidence needed to choose an architecture and determine what “include all features” can safely mean.

### 1. System ownership and source provenance

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Application owner | Name/role responsible for the Render system and authority to hand it over | Signed handoff cover sheet or owner email | Product / Engineering | ☐ |
| Source location | Canonical repository URL or original maintained source location | Read-only repository access or source archive | Engineering | ☐ |
| Exact source snapshot | Branch/tag/commit for the live version; if no Git, a dated archive | Commit SHA, or SHA-256 of ZIP/TAR plus capture timestamp | Engineering | ☐ |
| Completeness declaration | Whether source includes public pages, authentication, applicant, recruiter/HR/admin, database, uploads, jobs, and notifications | Module checklist signed by source owner | Engineering | ☐ |
| Third-party ownership | Identify code/assets owned by a contractor or other party and any handoff/licensing restriction | License/ownership summary; do not send unrelated contracts | Product / Legal | ☐ |
| Live-release relationship | How the supplied source maps to the running Render deployment | Render deploy ID/date mapped to commit or archive hash | Engineering / Release | ☐ |

**G0 pass condition:** The live application's source lineage is reproducible, or an owner formally declares the site reference-only.

### 2. Runtime and dependency contract

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Runtime | PHP version, server/runtime type, framework/CMS if any, required extensions | `README`, Render runtime settings export, or owner-attested inventory | Engineering | ☐ |
| PHP dependencies | Exact dependency definitions and resolved versions | `composer.json` and `composer.lock`, if used | Engineering | ☐ |
| Frontend dependencies | Build tooling and exact packages, if any | `package.json` plus lockfile; identify CDN-only production dependencies | Engineering | ☐ |
| Build/start contract | Root directory, install/build/start commands, public document root | Sanitized Render service settings or runbook | Engineering / Release | ☐ |
| Scheduled/background work | Cron jobs, queue workers, webhooks, and required schedules | Job inventory with command, cadence, purpose, owner, retry behavior | Engineering / Operations | ☐ |
| Local recovery | Minimum supported local or staging setup steps | Read-only setup/runbook; secrets represented as placeholders | Engineering | ☐ |

**G0 pass condition:** A fresh non-production environment could in principle be constructed without guessing runtime, dependencies, commands, or background services.

### 3. Configuration and secret-name inventory

Provide **names and metadata only**. Do not provide secret values at G0.

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Environment variables | Every variable name read by the application | Sanitized `.env.example` or variable inventory | Engineering | ☐ |
| Variable classification | Purpose, required/optional, secret/non-secret, environment scope, consuming module | Configuration matrix | Engineering | ☐ |
| Secret ownership | Who can issue, rotate, and revoke each secret class | Owner and recovery-channel matrix; no values | Infrastructure / Security | ☐ |
| External service identity | Provider/account/project identifiers that are safe to disclose internally | Service name and non-secret identifier | Infrastructure | ☐ |
| Environment separation | Production, preview/staging, and developer configuration boundaries | Environment map without credentials | Engineering / Security | ☐ |

Suggested columns: `variable name | purpose | required | secret class | environments | consuming component | issuing owner | rotation owner | replacement needed`.

**G0 pass condition:** The team knows which configuration and secret classes exist, who controls them, and what must be replaced—without receiving live secrets.

### 4. Database, schema, and data lineage

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Database platform | Engine, major version, provider, region, encoding/collation | Sanitized service inventory | Engineering / Infrastructure | ☐ |
| Schema | Tables, columns, types, keys, constraints, indexes, views, procedures/triggers | Schema-only SQL dump or migration-generated schema | Engineering / Data owner | ☐ |
| Migration history | Ordered schema changes and current migration level | Migration files/table with no credentials or row data | Engineering | ☐ |
| Entity map | Jobs, companies/clients, users, applicants, applications, statuses, education, employment, references, documents, notifications, audit data | ERD or data dictionary | Engineering / Recruitment | ☐ |
| Row-count inventory | Approximate or exact record counts per table and latest-update anchor | Aggregate counts only; no row extracts | Data owner | ☐ |
| System of record | Which database/table or external service is authoritative for each entity | Data-lineage matrix | Product / Recruitment / Engineering | ☐ |
| Sensitive-data map | Which fields contain identity, contact, family, government ID, health/medical, résumé, authentication, or free text | Field-classification inventory; no values | DPO / Data owner | ☐ |
| Retention/deletion | Current retention, archival, deletion, correction, and account-closure behavior | Policy-to-table mapping, including gaps | DPO / Engineering | ☐ |

Suggested lineage record: `business entity -> authoritative source -> table/field -> create/update owner -> consumers -> retention -> migration disposition`.

**G0 pass condition:** The team can reason about the real data model, sensitive-data scope, and migration complexity without accessing production personal data.

### 5. Jobs and publishing lifecycle

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Job source of truth | Where jobs are created, edited, opened, closed, and archived | Workflow and source mapping | Recruitment | ☐ |
| Job identifier | Stable key passed from listing/detail to application and later workflow | Schema/route mapping with synthetic example | Engineering / Recruitment | ☐ |
| Publication states | Draft, scheduled, active, paused, closed, expired, filled, archived, or actual equivalents | State list and transition owner | Recruitment | ☐ |
| Job taxonomy | Company/client, role/title, location, employment type, shift, requirements, dates, vacancies | Data dictionary and approved controlled values | Recruitment / Content | ☐ |
| Listing/detail/apply mapping | Exact route/query/body relationship and behavior for missing/closed jobs | Route contract; no production submission | Engineering | ☐ |
| Current-job inventory | Which public listings are currently authorized and their last verification date | Owner-approved list; no applicant data | Recruitment / Content | ☐ |

**G0 pass condition:** A job can be traced from its source through publication and into an application without inventing identifiers, states, or ownership.

### 6. Users, roles, and authenticated workflow inventory

No login is required during evidence collection. At G0, obtain the contracts and ownership for the workflows that the public site advertises.

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Account types | Applicant, recruiter, HR, administrator, or actual roles | Role inventory with intended users | Product / HR / Engineering | ☐ |
| Role permissions | View/create/edit/delete/export/upload/status/admin permissions per resource | Authorization matrix; code policy references if available | Security / Engineering | ☐ |
| Account lifecycle | Registration/invitation, email verification, login, lockout, recovery, disablement, deletion | Workflow map and owner | Engineering / Security | ☐ |
| Applicant self-service | Profile, applications, status, action requests, orientation, medical, document upload, account/privacy functions | Screen/route inventory and business owner attestation | Recruitment / Product | ☐ |
| Staff workflow | Queues, assignment, review, notes, status updates, document access, exports, administration | Workflow map and role matrix | Recruitment / HR | ☐ |
| Client/employee/HRIS boundary | Whether these identities use this system or link to a separate portal | System-boundary statement | Product / HRIS owner | ☐ |
| Auditability | Events recorded for sign-in, role changes, record access, status changes, exports, and deletions | Audit-event inventory, including known gaps | Security / Engineering | ☐ |

**G0 pass condition:** Every claimed authenticated capability is marked **confirmed**, **not implemented**, **retired**, or **unverified**, with an owner. Public copy alone is not evidence of a working workflow.

### 7. Application status state machine and operating handoffs

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Status definitions | Every internal and applicant-visible status with plain-language meaning | Status dictionary | Recruitment | ☐ |
| Allowed transitions | From/to states, prerequisites, actor/role, side effects, reversals | State diagram/table | Recruitment / Engineering | ☐ |
| Applicant visibility | Which internal states map to which candidate-facing labels | Internal-to-public mapping | Recruitment / Applicant support | ☐ |
| Ownership and SLA | Queue owner, aging trigger, escalation, and resolution rule per state | Operating matrix | Recruitment / HR | ☐ |
| Action requests | Documents, interview, orientation, medical, onboarding, or actual actions | Trigger/owner/data/notice inventory | Recruitment / DPO | ☐ |
| Notifications | Email/SMS/in-app trigger, recipient, template, retry, and failure owner | Event-to-notification matrix | Recruitment / Operations | ☐ |
| Corrections/cancellations | Duplicate, withdrawn, rejected, reopened, erroneous status, and record correction handling | Exception map | Recruitment / DPO | ☐ |

**G0 pass condition:** The team can distinguish real workflow rules from public marketing copy and can identify every state that would need redesign or migration.

### 8. File upload and storage contract

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Storage provider | Render disk, object storage, database, or another provider; region and persistence model | Service inventory; no access keys | Engineering / Infrastructure | ☐ |
| Storage layout | Logical prefixes/paths and record-to-file linkage | Path/data-model diagram using synthetic identifiers | Engineering | ☐ |
| Accepted uploads | Current file types, size limits, count limits, validation, and rejection behavior | Source/config references and control inventory | Engineering / Security | ☐ |
| Access control | Who can upload, view, download, replace, or delete each file class | Resource/role matrix | Security / Recruitment | ☐ |
| Security controls | Server-side type/signature checks, randomized names, private access, scanning/quarantine, encryption, signed links | Control statement plus source/config evidence | Security / Engineering | ☐ |
| Retention/deletion | Retention class, deletion trigger, orphan cleanup, legal hold, applicant request behavior | File-lifecycle map | DPO / Engineering | ☐ |
| Inventory | Count, aggregate size, oldest/newest dates, types, orphan/failed count | Aggregate report only—no applicant files | Data owner | ☐ |
| Backup/restore | Whether stored files are backed up and how restore is verified | Backup/runbook summary and last test date | Infrastructure | ☐ |

**G0 pass condition:** The team knows whether any candidate document can be safely migrated and operated. Actual résumé/document transfer is a later, separately approved data-migration action.

### 9. Email, notifications, and external integrations

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Mail provider | Provider, sending domain, environment, account owner | Provider inventory; no API keys/passwords | Operations / Engineering | ☐ |
| Sender identities | From/reply-to names and mailbox owners | Address-role inventory; redact personal addresses if unnecessary | Recruitment / Operations | ☐ |
| Template inventory | Registration, verification, recovery, application receipt, status, orientation, medical, staff alert, or actual templates | Template names and redacted/synthetic previews | Recruitment / Content | ☐ |
| Trigger map | Event, recipient, template, channel, delay, retry, and idempotency rule | Notification matrix | Engineering / Recruitment | ☐ |
| Delivery handling | Queues, retries, failures, bounce/complaint handling, suppression, monitoring | Operational runbook | Operations / Engineering | ☐ |
| Other integrations | SMS, calendar, analytics, anti-bot, malware scanning, HRIS, CRM, webhooks | Integration inventory with data exchanged and owner | Product / Engineering / DPO | ☐ |

**G0 pass condition:** Every external dependency and data transfer is named, owned, and replaceable; no credential values need to be transferred yet.

### 10. Render hosting and deployment topology

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Render services | Web services, databases, disks, workers, cron jobs, static services | Sanitized service topology and safe service identifiers | Infrastructure | ☐ |
| Region/network | Service regions, internal connections, allowed origins/hosts, proxies/CDN | Architecture diagram without credentials | Infrastructure / Security | ☐ |
| Domain/TLS | Custom domains, DNS owner, certificate management, redirect/canonical rules | Domain inventory and owner | Infrastructure | ☐ |
| Deployment mode | Manual/automatic deploy, source branch, build hooks, release command | Deployment runbook; redact hooks/tokens | Engineering / Release | ☐ |
| Health/readiness | Health-check routes, startup expectations, dependency checks | Route/monitor contract; do not expose internal-only URLs publicly | Engineering / Operations | ☐ |
| Persistence | What survives deploy/restart and where sessions/files/jobs live | Persistence map | Infrastructure / Engineering | ☐ |
| Rollback | Prior deploy selection, code rollback, database compatibility, owner approval | Rollback runbook and last rehearsal date | Release / Engineering | ☐ |
| Cost/plan constraints | Service plan limits affecting sleep, storage, workers, backups, bandwidth | Constraint summary | Infrastructure / Finance owner | ☐ |

**G0 pass condition:** The current production topology and continuity dependencies are understood well enough to compare them with the intended GitHub/Hostinger architecture.

### 11. Logs, monitoring, incidents, and support

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Log sources | Web/runtime, application, authentication, queue/mail, database, storage, and audit logs | Log-source inventory and access owner | Operations / Engineering | ☐ |
| Redaction | Fields excluded or masked and known sensitive-data exposure | Logging policy/config references | Security / DPO | ☐ |
| Retention/access | Retention period and roles that can view/export logs | Access/retention matrix | Security / Operations | ☐ |
| Monitoring | Uptime, error tracking, queue/mail/storage/database alerts | Monitor inventory and escalation owner | Operations | ☐ |
| Current health | Open P0/P1 incidents, recurring failures, and unresolved data loss/delivery issues | Sanitized issue list or incident summary | Product / Operations | ☐ |
| Deployment evidence | Recent successful and failed deployment dates/IDs and associated source | Sanitized release history | Release / Engineering | ☐ |

For G0, log samples are not required unless needed to explain an open blocker. If supplied, they must be redacted and narrowly scoped.

**G0 pass condition:** The team understands observability coverage, active operational risks, and who can provide safe evidence later.

### 12. Backups, recovery, and migration readiness

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Backup coverage | Source, database, file storage, and configuration metadata included/excluded | Backup inventory | Infrastructure / Data owner | ☐ |
| Backup schedule | Frequency, retention, encryption, location, and access owner | Policy/runbook; no recovery keys | Infrastructure / Security | ☐ |
| Latest known backup | Timestamp, scope, result, and integrity evidence | Backup job record/hash metadata; no backup payload | Infrastructure | ☐ |
| Restore evidence | Last restore test date, environment, scope, result, and gaps | Sanitized rehearsal record | Infrastructure / Engineering | ☐ |
| Recovery objectives | Approved or current RPO/RTO and business impact | Continuity statement | Product / Operations | ☐ |
| Migration candidate | Per entity: migrate, rebuild, archive, retain read-only, or retire | Initial disposition matrix | Product / Data owner / DPO | ☐ |
| Referential reconciliation | Counts and relationships that must reconcile after any future migration | Proposed reconciliation matrix | Engineering / Data owner | ☐ |

**G0 pass condition:** The team knows whether recoverable backups exist and can state which data/assets might later move. G0 does not authorize restoration or migration.

### 13. Authenticated test-access readiness

Do not request or use production passwords during this checklist. Establish how later verification can be performed safely.

| Check | Required evidence | Acceptable safe form | Accountable owner | Status |
| --- | --- | --- | --- | --- |
| Non-production environment | Staging/preview availability and data-isolation guarantee | Environment statement and safe URL when approved | Engineering / Security | ☐ |
| Synthetic accounts | One test account per real role using synthetic data | Account-issuance plan; credentials shared later through approved channel | Security / Engineering | ☐ |
| Test role matrix | Expected accessible/denied routes and actions for each role | Authorization test sheet | Security / Product | ☐ |
| Safe test records | Synthetic jobs/applications/documents separated from production | Fixture plan and cleanup owner | Recruitment / Engineering | ☐ |
| Audit/cleanup | How tests are logged, identified, removed, and reconciled | Test runbook | Engineering / Data owner | ☐ |
| Approval owner | Person authorized to permit later authenticated read-only inspection | Named role and approval channel | Product / Security | ☐ |

**G0 pass condition:** There is an approved, non-production path for later authenticated verification, or the authenticated system is explicitly classified as unverified/reference-only.

## Later evidence — not required to close G0

These items become necessary for detailed requirements, migration, security qualification, or release, but should not delay the initial migratable/partial/reference-only decision if the G0 contracts above are complete.

| Later evidence | Earliest gate | Why it can wait | Boundary |
| --- | --- | --- | --- |
| Authenticated screen recordings or guided walkthrough by role | G1 | Helpful for detailed journey mapping; role/workflow inventory is sufficient at G0 | Use staging/synthetic data only |
| Non-production test-account credentials | G1/G2 | G0 needs an issuance plan, not access | Share through approved secret channel |
| Sanitized representative records/fixtures | G1/G2 | Needed for validation and prototypes after schema approval | No copied production identities |
| Full historical job archive | G1/G4 | Public launch can start from owner-approved active jobs | Retention/archive decision required |
| Mail template bodies and brand review | G1/G5 | Template names/triggers establish architecture at G0 | Remove tokens and applicant data |
| Detailed security test and dependency scan results | G2/G7 | Essential before release, not before ownership classification | Use approved non-production copy |
| Narrow redacted log samples | G2/G7 | Only needed for observability design or defect evidence | No session IDs, reset links, or PII |
| Analytics history and event exports | G1/G4 | Useful for content/product decisions, not core recovery | Data owner and retention review |
| Production database export | Separate migration gate | Not needed for source/schema recovery and carries material privacy risk | Requires explicit approval, encrypted transfer, backup, reconciliation |
| Applicant documents/résumés | Separate migration gate | Highest-risk data; never needed for G0 | Requires DPO/data-owner approval and tested private storage |
| Live secrets and provider credentials | Release/integration gate | Should be replaced or rotated rather than copied whenever possible | Never store in Git or planning artifacts |
| Full backup payload or restore exercise | G5/G7 | Metadata and prior restore evidence are sufficient for G0 | Restore only to approved isolated environment |
| Production authenticated smoke | Post-deployment gate | Validates the released system, not the Phase 0 handoff | Requires explicit approval and proportionate test data |

## Requested handoff-package structure

The source owner may use this structure or provide equivalent evidence with a manifest:

```text
render-handoff-YYYY-MM-DD/
  README-HANDOFF.md
  EVIDENCE-MANIFEST.csv
  OWNERSHIP-AND-GAPS.md
  source/
    repository-pointer.txt
    source-archive.zip                 # only if no maintained repository exists
    source-archive.sha256              # when an archive is used
    dependency-files/
  runtime/
    runtime-and-build.md
    background-jobs.md
    env-variable-names.csv             # names and metadata only; no values
  database/
    schema-only.sql
    migrations/
    data-dictionary.csv
    entity-lineage.csv
    table-counts.csv                    # aggregate counts only
  product/
    route-and-module-inventory.csv
    jobs-lifecycle.md
    role-permission-matrix.csv
    application-status-state-machine.md
    notification-trigger-matrix.csv
  storage/
    file-storage-contract.md
    aggregate-file-inventory.csv
  operations/
    render-topology.md
    deploy-and-rollback.md
    backup-and-restore-evidence.md
    monitoring-and-log-inventory.md
    open-material-incidents.md
  test-readiness/
    staging-and-synthetic-access-plan.md
```

Every manifest entry should record:

`artifact path | purpose | source system | capture timestamp and time zone | owner | sensitivity class | redaction performed | SHA-256 | known gaps`.

## Phase 0 ownership roster

| Area | Required accountable role | Named owner | Alternate | Evidence due | Sign-off |
| --- | --- | --- | --- | --- | --- |
| Product scope and migratability | Product owner |  |  |  | ☐ |
| PHP source and runtime | Engineering owner |  |  |  | ☐ |
| Render hosting/release | Infrastructure or release owner |  |  |  | ☐ |
| Recruitment jobs/workflows | Recruitment owner |  |  |  | ☐ |
| Applicant/staff roles | HR/recruitment operations owner |  |  |  | ☐ |
| Database and migration | Data owner |  |  |  | ☐ |
| Privacy/retention | DPO/privacy owner |  |  |  | ☐ |
| Authentication/security | Security owner |  |  |  | ☐ |
| Mail/notifications/support | Operations owner |  |  |  | ☐ |
| HRIS/client/employee boundaries | HRIS or systems owner |  |  |  | ☐ |

## G0 acceptance record

### Required decisions

- [ ] The current local cinematic site is approved or rejected as the initial GitHub baseline under a separate source-control action.
- [ ] The Render application is classified as **migratable**, **partially migratable**, or **reference-only**.
- [ ] The delivered source snapshot and its relationship to the live deployment are verified—or explicitly unavailable.
- [ ] Runtime, dependency, build/start, scheduled/background, and configuration-name inventories are complete enough to reproduce the system without guessing.
- [ ] Schema/migration/entity evidence establishes the real database model without transferring personal data.
- [ ] Jobs, roles, authenticated capabilities, application statuses, files, notifications, and external integrations are confirmed or explicitly unverified.
- [ ] Hosting topology, persistence, backups, restore evidence, rollback, and active material incidents are documented.
- [ ] Secret owners and later test-access owners are named; no secret values were placed in the package.
- [ ] Every missing item has a named owner, disposition, risk, and target decision date.
- [ ] Product and Engineering accept the resulting scope and handoff classification.

### Gate result

- **G0 result:** `OPEN / PASS / PASS WITH ACCEPTED GAPS / NO-GO`
- **Render classification:** `MIGRATABLE / PARTIALLY MIGRATABLE / REFERENCE-ONLY`
- **Decision date:**
- **Product approver:**
- **Engineering approver:**
- **Security/DPO acknowledgment, when sensitive-data migration is contemplated:**
- **Accepted gaps and owners:**
- **Immediate next action:**

## Stop conditions

Pause the handoff and escalate if any of these occur:

- The requested archive contains `.env` values, private keys, credentials, database connection strings, session data, or password-reset links.
- Production applicant rows, government identifiers, medical data, résumés, or uploaded documents are included without a separately approved migration/data-handling process.
- The source owner cannot establish whether the supplied code matches the live deployment.
- A backup or restore claim cannot identify its scope, date, or accountable owner.
- Authenticated testing would require using a real applicant, employee, recruiter, or administrator account.
- The only available route to recovery would mutate production or expose sensitive data.

In those cases, preserve what is safely available, record the gap, and classify the affected module as unverified or reference-only until an approved path exists.

## Immediate use

Send the source owner only the G0 tables and package structure first. A 60–90 minute evidence workshop can then resolve ownership and classify each category as `available`, `partially available`, `unavailable`, or `requires separate approval`. Detailed authenticated discovery begins only after the test-access and data-isolation plan is approved.
