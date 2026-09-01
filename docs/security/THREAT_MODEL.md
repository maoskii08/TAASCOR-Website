# TAASCOR Website threat model

**Scope:** Local integrated experience on `feature/integrated-experience` as of 2026-09-01. This is an engineering threat model, not a penetration-test result or production security certification.

## Assets requiring protection

- Applicant identity, contact details, application answers, tasks, status, and notice history.
- Resume documents held in private quarantine.
- Staff credentials, sessions, role permissions, and job/application decisions.
- Workforce-planning contact and demand details.
- Audit history and source-of-truth job records.
- Application secrets, database credentials, private storage paths, and production configuration.
- TAASCOR brand trust, official job authenticity, public claims, and anti-fraud channels.

## Trust boundaries

```text
Public browser
  |-- public pages / jobs (read-only published data)
  |-- applicant session (own application data)
  |-- workforce brief (minimal business submission)
  `-- staff session (separate entry and protected workflow)
             |
        PHP application
          |       |
       database  private upload quarantine

External and not trusted by default:
HRIS portal, mail/SMS providers, malware scanner, CRM, analytics,
Render legacy system, production host, and any future client portal.
```

## Primary threats and current controls

| Threat | Local control | Remaining production requirement |
| --- | --- | --- |
| Credential stuffing / brute force | Email and IP-scoped login throttles; generic registration response; modern password minimum | Central monitoring, alerting, staff MFA, approved verification/recovery channel |
| Session theft / fixation | HttpOnly, SameSite=Lax, secure-cookie production requirement, rotation, idle and absolute expiry, session-version revocation | TLS/HSTS validation on final host, session-store design and incident procedure |
| Cross-site request forgery | Per-session CSRF token on state-changing forms | Automated negative coverage for every added state route and deployment header validation |
| Broken object authorization | Applicant records are queried by the signed-in user; staff routes require staff role | Full role matrix, negative authorization suite, authenticated security test |
| Injection | Prepared PDO statements and allowlisted domain states | MySQL-target testing, dependency/runtime patch policy, security review |
| Stored/reflected XSS | Contextual HTML escaping, local scripts, restrictive CSP on task pages | Review all owner-entered rich content before adding any HTML-capable CMS fields |
| Malicious upload | Extension/signature/MIME/size checks, randomized private name, storage outside web root, quarantine only | Approved malware scanner, release workflow, retrieval authorization, retention/deletion jobs |
| Sensitive-data overcollection | Two-stage minimum profile and application summary; no statutory IDs, religion, medical, or family data in stage one | Field-by-field DPO purpose/retention approval before later-stage requirements |
| Applicant enumeration | Generic registration outcome and generic login failure | Recovery/email provider implementation must keep generic responses |
| Status tampering | Explicit transition graph, CSRF, staff authorization, status and audit history | Recruitment owner approval of states/language; immutable/central audit retention decision |
| Spam / workforce-form abuse | Honeypot and hashed-IP rate limit | CAPTCHA/edge controls only if evidence justifies them; monitored owner queue |
| Source/config disclosure | Router and Apache denials for dotfiles, manifests, internal directories and sensitive extensions; directory indexes disabled | Confirm equivalent controls at final document root and test common encoding/bypass variants |
| Clickjacking / MIME sniffing / referrer leakage | `frame-ancestors 'none'`, `X-Frame-Options: DENY`, nosniff, strict-origin referrer policy | Live header/readback smoke and hosting proxy review |
| Supply-chain outage/compromise | Task surfaces use local assets; homepage third-party scripts have pinned versions and SRI; no-JS fallback | Approve/self-host font and motion assets, dependency monitoring, release SBOM policy |
| Privacy notice drift | Versioned acknowledgements and draft noindex gate | DPO-approved notice publishing, change process, retention and data-subject fulfillment procedure |
| Fraudulent job claims | Demo roles are explicitly synthetic; public anti-fraud route; governed publication status | Approved official-channel register and Recruitment ownership of published jobs |

## Abuse cases to keep in regression coverage

- Access another applicant's application by changing an identifier.
- Post a staff status change as an applicant or anonymous visitor.
- Skip or replay a disallowed application status transition.
- Submit forms without or with a stale CSRF token.
- Upload renamed executable/polyglot, unsupported MIME, or oversized content.
- Traverse to private uploads, schemas, scripts, dotfiles, manifests, backups, or QA artifacts.
- Enumerate registered email addresses through login, registration, or future recovery.
- Reuse a session after password change or after idle/absolute expiry.
- Apply to a closed, expired, missing, or context-switched job.
- Index a draft privacy collection route, applicant/staff route, or synthetic job detail.
- Turn illustrative homepage telemetry into a production performance claim.

## Explicitly unavailable controls

The local build does not claim production malware scanning, MFA, email verification, password recovery, notification delivery, central logs/SIEM, WAF, encrypted backup, restore readiness, key rotation, vulnerability scanning, penetration-test completion, or incident response. Those omissions are release gates, not silent assumptions.
