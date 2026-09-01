# Phase 0 Baseline Manifest

**Captured:** 1 September 2026, Asia/Manila
**Purpose:** Preserve and identify the exact pre-integration public source while the first GitHub baseline awaits review and explicit commit/push approval.

## Protected original source anchors

| Path | Bytes | SHA-256 | Role |
| --- | ---: | --- | --- |
| `index.html` | 67,049 | `4978AA7B96E716FA4DA588776B141ECCBB918477B7337D8C7316C9FA3EA50DFA` | Complete cinematic public prototype |
| `favicon.svg` | 649 | `967FA0BA93FC413EAE7E590784B1B91BC5C2D04D9F7F6C6ABF8D17A4906F56B9` | Current TAASCOR mark |
| `.claude/launch.json` | 202 | `74A2D21298E8C133CD18FFD4A40539B62C7517501DDEE1A2D45668CCF768E9D1` | Local Python preview configuration |

No source change was made to these three files during Phase 0 bootstrap.

## Recovery copy

| Archive | Bytes | SHA-256 | Contents | Tracking |
| --- | ---: | --- | --- | --- |
| `Backups/TAASCOR_Website_Local_Baseline_2026-09-01_structured.zip` | 21,458 | `8DBA66D3C1D86B24F99EB27002857884BEBC2F482FF79B47C6E8F7A64903C3FB` | `.claude/launch.json`, `index.html`, `favicon.svg` | Ignored; local recovery only |

The archive entries were inspected and preserve the `.claude/launch.json` path. A second ignored backup without the `_structured` suffix exists but flattens `launch.json`; it is not the primary restore artifact and remains untouched pending a cleanup decision.

## Current repository state

- Local Git repository initialized on `main`.
- Remote `origin`: `https://github.com/maoskii08/TAASCOR-Website.git`.
- Local commits: none.
- Staged files: none.
- Remote heads/tags: none observed during Phase 0 verification.
- GitHub remains empty until a separately approved push.

## Proposed initial tracked manifest

Only these explicit paths should enter the first baseline commit:

```text
.gitattributes
.gitignore
README.md
.claude/launch.json
index.html
favicon.svg
Audit/AUDIT_2026-09-01.md
Planning/TAASCOR_WEBSITE_INTEGRATION_PLAN_2026-09-01.md
Planning/Phase-0/**
```

Do not include feature remediation, a generated application framework, production configuration, deployment scripts, or applicant/system exports in this baseline commit.

## Explicitly excluded

```text
.git/
Backups/
*.zip
.env and secret-bearing configuration
private keys/certificates
auth.json and credential-bearing package configuration
local databases
dependencies/build output
runtime cache/log/session files
test output
applicant uploads, exports, database rows, or unredacted logs
```

## Pre-commit verification

Run from `C:\Users\mAOskii\Documents\TAASCOR Website`:

```powershell
git status --short --branch
git remote -v
git ls-remote --heads --tags origin

rg -n --hidden --glob '!.git/**' --glob '!Backups/**' `
  -e '-----BEGIN (RSA|EC|OPENSSH|DSA) PRIVATE KEY-----|AKIA[0-9A-Z]{16}|ASIA[0-9A-Z]{16}|ghp_[A-Za-z0-9]{36}|github_pat_[A-Za-z0-9_]{20,}' .

git check-ignore -v `
  'Backups\TAASCOR_Website_Local_Baseline_2026-09-01.zip' `
  'Backups\TAASCOR_Website_Local_Baseline_2026-09-01_structured.zip'

git add --dry-run -- `
  .gitattributes `
  .gitignore `
  README.md `
  .claude/launch.json `
  index.html `
  favicon.svg `
  Audit/AUDIT_2026-09-01.md `
  Planning/TAASCOR_WEBSITE_INTEGRATION_PLAN_2026-09-01.md `
  Planning/Phase-0
```

The dry run must list only the approved manifest. Before an approved commit, also run `git diff --cached --check`, review `git diff --cached --name-status`, and confirm the structured backup hashes still match the three source anchors.

## Baseline closure checklist

- [ ] Product/Engineering accept the exact tracked manifest.
- [ ] Phase 0 files are reviewed and contain no credentials or applicant data.
- [ ] Secret signature scan returns zero matches.
- [ ] Backup files are ignored and absent from the dry-run staging list.
- [ ] Source-anchor hashes still match.
- [ ] No source remediation is mixed into the baseline commit.
- [ ] Commit approval is explicit.
- [ ] Push approval is explicit and separate.
- [ ] Local and remote commit hashes reconcile after push.
- [ ] No deployment follows automatically.
