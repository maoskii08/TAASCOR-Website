# TAASCOR Website production release — 2026-09-02

## Gate decision

**GO for the fail-closed public website. NO-GO for governed data workflows.** Commit `8d5e929822d85d75f7c2fd1aae28e5d0ac3820d3` is live at `https://taascor.com/`. Applicant/workforce collection, resume uploads, staff workflows, job publication, and public indexing remain disabled in production.

## Deployment profile

| Field | Verified value |
| --- | --- |
| Project | TAASCOR Website |
| Repository | `https://github.com/maoskii08/TAASCOR-Website.git` |
| Branch | `feature/integrated-experience` |
| Commit | `8d5e929822d85d75f7c2fd1aae28e5d0ac3820d3` |
| Live URL | `https://taascor.com/` |
| Protocol | SSH/SFTP; host key `ssh-ed25519 SHA256:Rj5TJlVihe6G2m0iYexrAYuXqGmIHTZg06ROv7RwN7U` |
| Public root | `/home/u716215139/domains/taascor.com/public_html` |
| Active release | `/home/u716215139/releases/taascor_website/8d5e9298-20260902T092712Z/app` |
| Runtime | PHP 8.5.4; `pdo_mysql` and `mbstring` loaded |
| Credential source | Project-root `.env.deploy.local`; ignored and untracked; values not displayed or packaged |

The active document root is a symlink to the immutable release. Hostinger ignored `.user.ini` for the web handler, so a non-secret `php_value auto_prepend_file` deployment overlay was added to the release `.htaccess`. The referenced mode-`600` runtime bootstrap and private upload directory remain outside the web root. The source `.htaccess` is retained beside the release for hash comparison and rollback.

## Release and parity evidence

| Artifact | SHA-256 |
| --- | --- |
| Uploaded Git archive | `20949177278c971e60f7a501f232704f57c1d88327a129703f06b20b5c2d1426` |
| Live `assets/css/site.css` | `fa44f6366cfc5684cc7085973dea7c60ecbe42c7ceec3228d721db8f9e23fca0` |
| Live `assets/js/theme.js` | `ef3a09cc691d95bddcb193832a3a0becb3c05f2987e74330b3cd9c090fab32ca` |
| Live `favicon.svg` | `967fa0ba93fc413eae7e590784b1b91bc5c2d04d9f7f6c6abf8d17a4906f56b9` |
| Source `.htaccess` | `57b74f958feb88cd6b869543a47ada609c9c1f6842342b814d2196b7f22a5f45` |
| Deployed `.htaccess` with non-secret runtime overlay | `8c7957a7162be9c7b3bd30c79e1c3cd9a7e7f617000943bf0c454f3dbc912cbd` |

The uploaded archive hash matched its remote readback before extraction. The three public executable assets matched the local exact-commit package and their remote release files.

## Backups and rollback

| Backup | Location | SHA-256 |
| --- | --- | --- |
| Pre-deploy document root archive | `/home/u716215139/backups/taascor_website/predeploy-8d5e9298-20260902T092712Z-files.tgz` | `2bb1482422b6e4f29be6329e1031067c0094b7735f4be30d80f06ac16264df36` |
| Pre-migration database dump | `/home/u716215139/backups/taascor_website/predeploy-8d5e9298-20260902T092712Z-database.sql.gz` | `a04c786fa298cd5377cf99bf0b4954435353993c8455e4cda272c18ba10238cb` |
| Previous live directory | `/home/u716215139/backups/taascor_website/public_html-8d5e9298-20260902T092712Z` | Retained directory |

The two archive backups were also downloaded to ignored local release storage. Rollback is the recoverable document-root switch back to the retained prior directory; no production data rewrite is authorized by that switch.

## Tests performed

- Local exact-worktree checks: 12/12 static tests and 26/26 Playwright tests passed before commit.
- GitHub: remote feature branch resolves to the exact deployed commit.
- SSH target: password authentication, host identity, target root, write access, PHP runtime, extensions, MySQL client, dump tool, and tar tool passed.
- Database: pre-migration dump completed; production migration reported the MySQL schema current; 14 application tables and one schema-migration record were verified.
- Remote source: all PHP files passed PHP 8.5 lint; pre-switch production homepage rendering passed.
- External HTTP: `/`, `/about/`, `/careers/`, `/workforce/`, `/portal/`, `/robots.txt`, and `/sitemap.xml` returned `200`.
- Denials: `/.user.ini`, `/database/schema.mysql.sql`, and `/Backups/` returned `404`; `/app/.env.example` returned `403`.
- Security transport: HTTPS returned HSTS, no-sniff, frame denial, referrer policy, permissions policy, and `X-Robots-Tag: noindex,nofollow`.
- Browser: desktop light, desktop dark, persistent theme preference, 390 px mobile menu/no-overflow, public navigation, and zero page errors passed.

## Risks and gaps

- **P1 Major:** Hostinger replaces the application-generated CSP with `Content-Security-Policy: upgrade-insecure-requests`. Other tested security headers remain active, but the stricter application CSP requires a hosting-level correction or an independently reviewed markup policy.
- **P1 Major:** Privacy, security, retention, accessibility-owner, content/claim, malware-scanning, recovery, staff-access, and business-UAT gates remain open. Their corresponding production capabilities remain disabled.
- **P2 Watch Item:** Search indexing remains deliberately disabled until canonical-domain, content, claims, social metadata, and owner approvals close.
- **Expected Data Movement:** The approved migration created/confirmed the application schema only. No real applicant, workforce, staff, job, document, or synthetic test record was inserted during live smoke.

## Post-release observation

The live site returned `200` after the runtime bootstrap was activated. Public-route, protected-path, asset-parity, desktop/mobile, and theme checks passed. No authenticated or mutating production workflow was exercised because those capabilities remain intentionally disabled.
