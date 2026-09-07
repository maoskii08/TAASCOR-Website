# TAASCOR Website production release — 2026-09-07

## Gate decision

**GO for the streamlined public portfolio experience.** Application commit `1e0ac3feb935c790ec0ce332895c403149b38a37` is live at `https://taascor.com/` through an immutable Hostinger release. The public journey now promotes only current, useful destinations while preserving future route code for governed activation.

**NO-GO remains in force for governed applicant, workforce, staff, job-publication, upload, and indexing capabilities.** This release changes their public entry boundaries; it does not authorize data collection or activate those workflows.

## Summary

The release reduces the indexable SEO portfolio to 19 canonical routes, removes unfinished and draft destinations from global discovery, redirects promoted calls to available outcomes, and makes unavailable applicant and staff workflows explicit at the portal boundary. Light mode remains the default; dark mode, desktop/mobile motion, tap/click progression, and reduced-motion behavior remain supported.

All 43 route templates remain in GitHub. No source page, database record, applicant record, job, document, or runtime secret was removed or rewritten.

## Deployment profile

| Field | Verified value |
| --- | --- |
| Project | TAASCOR Website |
| Repository | `https://github.com/maoskii08/TAASCOR-Website.git` |
| Branch | `feature/integrated-experience` |
| Application commit | `1e0ac3feb935c790ec0ce332895c403149b38a37` |
| Live URL | `https://taascor.com/` |
| Protocol | SSH/SFTP; retained `ssh-ed25519` host identity verified |
| Public root | `/home/u716215139/domains/taascor.com/public_html` |
| Active release | `/home/u716215139/releases/taascor_website/1e0ac3fe-20260907T090634Z/app` |
| Runtime | PHP 8.5.4; mode-`600` private runtime bootstrap preserved |
| Credential source | Project-root `.env.deploy.local`; ignored, untracked, and excluded from the package |

## Files changed in the application commit

- Public information architecture: `index.html`, `site/bootstrap.php`, `sitemap.php`.
- Current journeys: Solutions, Industries, Clients, Locations, and Contact route templates.
- Availability boundaries: `portal/index.php`, `account/login.php`, `staff/login.php`, and the applicant footer.
- Regression coverage: public-foundation and static hardening tests.
- Audit evidence: `Audit/AUDIT_2026-09-04-225529.md`.

## Release and parity evidence

| Artifact | SHA-256 / result |
| --- | --- |
| Exact-commit Git archive | `a6566b45cd57785a285db147c808d8b5f390ca9af1dec000524edfbf35883201` |
| Uploaded package readback | Exact match |
| Changed production source files | 14/14 package-to-candidate and package-to-active hashes matched |
| Live `assets/css/site.css` | `c6680f9f799a1426c4eb97ae27b968d31a361b272469310586fff18d004dba01` |
| Live `assets/js/site.js` | `0eef39fa41e754b58bdb551dc7fd468a8106ae6876967022c4b5af3adcd7aaa2` |
| Live `assets/js/theme.js` | `538801c7a2f69ab145493761277daf5ba1f9c0ec110a765d2237b7b8f9a320dc` |

The active document-root symlink and `.taascor-commit` marker both resolve to the application commit. The retained Hostinger runtime, opcode, and CSP overlay was copied byte-for-byte from the previously verified active release because the tracked `.htaccess` did not change in this commit.

## Backups and rollback

| Artifact | Location | SHA-256 |
| --- | --- | --- |
| Pre-deploy files | `/home/u716215139/backups/taascor_website/predeploy-1e0ac3fe-20260907T090634Z-files.tgz` | `842e3fe6cc1abda0aff5ee795f8ecc34291f359ef5e4f61c8774d887391be0a4` |
| Pre-deploy database | `/home/u716215139/backups/taascor_website/predeploy-1e0ac3fe-20260907T090634Z-database.sql.gz` | `7ffddab7b061365952c005b7b251cf55a25152228b16a504a4a956c1d1627c47` |
| Immediate rollback | `/home/u716215139/backups/taascor_website/public_html-1e0ac3fe-20260907T090634Z` | Prior verified `887a3f17-20260904T105033Z/app` release |

Both backup archives were downloaded to ignored local release storage at `Backups/taascor-website/remote-1e0ac3fe-20260907T090634Z`; local SHA-256 values matched their remote files. Rollback is an atomic symlink replacement and does not rewrite application data.

An initial candidate attempt stopped before activation because its portal-copy assertion used stale wording. It produced an additional verified backup pair but never changed `public_html`. The inactive candidate directory was removed after the successful release; its exact package remains recoverable from Git and ignored local release storage.

## Commit and push status

- Application commit `1e0ac3feb935c790ec0ce332895c403149b38a37` was pushed to `origin/feature/integrated-experience` before packaging.
- The production archive was created from that exact Git commit.
- This production-release record is a documentation-only closure change and is not part of the deployed executable package.

## Deployment path

`GitHub application commit 1e0ac3f` → exact-commit Git archive → SSH host and active-pointer verification → file/database backups with local readback → SFTP upload and package hash readback → immutable candidate extraction → PHP lint, read-only schema verification, content render, and changed-file parity → atomic document-root switch → public HTTP/security and rendered desktop/mobile smoke.

## Tests performed

- Local static controls: 14/14 passed; 145 PHP files linted, 185 local references resolved, 27 fragments validated, and committed-secret signatures absent.
- Local Playwright: 33/33 passed in 1.9 minutes.
- Dependency audit: zero vulnerabilities at the high threshold using the local audit cache.
- Remote candidate: 145 PHP files linted with PHP 8.5; read-only MySQL schema verification passed.
- Pre-switch candidate: current Solutions links, unavailable Portal states, and hidden future-route assertions passed.
- Public HTTP: Home, Solutions, Industries, Clients, About, Leadership, Locations, Contact, Careers, Portal, Accessibility, Recruitment Safety, robots, and sitemap returned HTTP 200.
- Protected paths: `.user.ini`, database schema, Audit, and Backups returned 404; the environment template remained denied.
- Public asset hashes: CSS, site JavaScript, and theme JavaScript matched the verified release values.
- Desktop browser at 1280 x 720: light default, exact streamlined navigation, dark-mode persistence, zero overflow, zero hidden-future links, and zero console errors.
- Mobile browser at 390 x 844: light default, working menu, mobile-layout and mobile-motion states, tap-to-advance to the authored stop, zero overflow, no motion-pause control, and zero console errors.

## Risks and gaps

- **P1 Major:** Corporate facts, client marks, leadership details, location details, legal notices, and governed workflows still require their named owner approvals. Data-collecting capabilities remain fail-closed.
- **P2 Watch Item:** Physical iOS Safari and Android Chrome were not available. Chromium desktop/mobile rendering and interaction passed.
- **P2 Watch Item:** Public indexing remains disabled pending the separate corporate, legal, metadata, and owner-approval gates. The 19-route sitemap set is ready when that gate is explicitly enabled.
- **Expected Data Movement:** None. The database was backed up and verified read-only; no migration or production workflow mutation was run.

## Post-release observation

The active pointer and commit marker remain on `1e0ac3f` after normal and cache-busted HTTP checks. The streamlined navigation, role-aware Portal states, default light theme, dark persistence, mobile motion, and tap-to-advance behavior are live without console or overflow findings.

## Next action

Keep the release under normal observation. Complete content-owner and Legal/DPO approvals before publishing the hidden future routes, final legal notices, or any data-collecting workflow.
