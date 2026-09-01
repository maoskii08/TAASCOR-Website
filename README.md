# TAASCOR Website

TAASCOR Management & General Services Corp. public website and planned workforce-experience platform.

## Current status

The current implementation is a cinematic static prototype contained in `index.html`. It is being preserved as the visual baseline while TAASCOR validates the source, content, compliance, privacy, and hosting requirements for the next-generation public website and recruitment experience.

**Release gate: NO-GO.** The current prototype is not approved for publication or production deployment. See the audit and integration plan before making release decisions.

## Local preview

Requirements: Python 3.

```powershell
python -m http.server 5177
```

Open `http://127.0.0.1:5177/`.

## Project map

- `index.html` — current cinematic public-site prototype.
- `favicon.svg` — current TAASCOR mark.
- `Audit/AUDIT_2026-09-01.md` — evidence-backed local audit.
- `Planning/TAASCOR_WEBSITE_INTEGRATION_PLAN_2026-09-01.md` — target experience and phased delivery plan.
- `Planning/Phase-0/` — source, ownership, compliance, privacy, and baseline readiness pack.

## Governance

- GitHub is the source of truth; Hostinger is deployment only.
- Local changes, commit/push, production deployment, and production-data movement are separate approval gates.
- Do not edit production directly or enable GitHub Actions auto-deployment unless explicitly approved.
- Do not publish legal, compliance, client, operational, or performance claims without current evidence and owner approval.
- Do not place credentials or applicant data in this repository.

## Environment configuration

The current static prototype requires no environment variables. An `.env.example` will be added only after the target application architecture and integration variable names are confirmed; secrets will remain blank and outside Git.
