# TAASCOR Phase 0 Readiness Pack

**Purpose:** Close the evidence, ownership, and source-control prerequisites before feature implementation begins.
**Current gate:** `G0 OPEN`
**Operating boundary:** Local preparation and read-only evidence recovery only. No commit, push, production access, applicant-data export, credential transfer, form submission, or deployment is authorized.

## Pack contents

- `PHASE_0_KICKOFF_2026-09-01.md` — workshop agenda, inputs, outputs, and gate checklist.
- `BASELINE_MANIFEST_2026-09-01.md` — protected source hashes, backup record, proposed tracked files, and verification commands.
- `OWNERSHIP_AND_DECISION_LOG_2026-09-01.md` — accountable-role roster and decisions that must be made.
- `RENDER_RECRUITMENT_SYSTEM_HANDOFF_CHECKLIST_2026-09-01.md` — safe evidence request for the PHP/Render recruitment system.
- `CONTENT_CLAIM_REGISTER_2026-09-01.md` — public-claim evidence and publication gates.
- `PRIVACY_DATA_INVENTORY_2026-09-01.md` — staged applicant-data decisions and privacy requirements.

## How to use this pack

1. Assign the named accountable roles in the ownership log.
2. Send the Render checklist to the actual source/system owner. Request environment-variable names, schema-only data, and aggregate inventories—not credentials or applicant records.
3. Have Corporate/Compliance complete the claim register and resolve the conflicting DOLE wording.
4. Have the DPO, Security, and Recruitment jointly approve the field-by-field privacy inventory.
5. Record decisions and gaps in the decision log.
6. Close the G0 checklist as `PASS`, `PASS WITH ACCEPTED GAPS`, or `NO-GO` and classify Render as `MIGRATABLE`, `PARTIALLY MIGRATABLE`, or `REFERENCE-ONLY`.
7. Seek separate approval to stage, commit, and push the reviewed baseline.

## Evidence-handling rule

This folder must not contain credentials, `.env` values, private keys, production database rows, applicant exports, government identifiers, medical information, résumés, uploaded files, session IDs, password-reset links, or unredacted production logs.
