# TAASCOR Website production release — 2026-09-04

## Gate decision

**GO for the public responsive UI release.** Application commit `887a3f1789d7c9a0b95c1fd6e418831060f239a3` is live at `https://taascor.com/`. The oversized shared header/logo defect is closed on desktop and mobile, light mode remains the default, dark mode remains available, and the cinematic homepage supports mobile motion plus click/tap/swipe progression.

Governed applicant, workforce, staff, job-publication, and document workflows remain fail-closed until their separate privacy, security, retention, accessibility-owner, malware-scanning, recovery, and business-UAT gates are approved.

## Summary

The release deploys the verified responsive and asset-delivery remediation as one immutable package from the exact pushed application commit. The shared logo now has safe intrinsic dimensions, HTML emits content-versioned asset URLs, the mobile header and navigation fit without horizontal overflow, and the homepage motion experience is active on compact viewports unless the visitor explicitly requests reduced motion.

The previous production-parity audit's two P1 findings are closed by exact-package deployment, source/readback checks, and rendered desktop/mobile browser evidence. No production data was added, updated, deleted, or migrated.

## Deployment profile

| Field | Verified value |
| --- | --- |
| Project | TAASCOR Website |
| Repository | `https://github.com/maoskii08/TAASCOR-Website.git` |
| Branch | `feature/integrated-experience` |
| Application commit | `887a3f1789d7c9a0b95c1fd6e418831060f239a3` |
| Live URL | `https://taascor.com/` |
| Protocol | SSH/SFTP; verified `ssh-ed25519` host fingerprint |
| Public root | `/home/u716215139/domains/taascor.com/public_html` |
| Active release | `/home/u716215139/releases/taascor_website/887a3f17-20260904T105033Z/app` |
| Runtime | PHP 8.5.4; existing private runtime bootstrap preserved |
| Credential source | Project-root `.env.deploy.local`; ignored, untracked, and excluded from the package |

## Files changed in the application commit

- `app/config.php`
- `app/views/header.php`
- `clients/index.php`
- `index.html`
- `leadership/index.php`
- `site/bootstrap.php`
- `tests/e2e/accessibility-responsive.spec.mjs`
- `tests/e2e/governed-experience.spec.mjs`
- `Audit/AUDIT_2026-09-04.md`
- `Audit/AUDIT_2026-09-04-184152.md`

## Release and parity evidence

| Artifact | SHA-256 |
| --- | --- |
| Exact-commit release package | `80260c302d1a241682da4c1a8f7706fe0c03405061edb3142dc6b57312bdca01` |
| Live and local `assets/css/site.css` | `c6680f9f799a1426c4eb97ae27b968d31a361b272469310586fff18d004dba01` |
| Live and local `assets/js/site.js` | `0eef39fa41e754b58bdb551dc7fd468a8106ae6876967022c4b5af3adcd7aaa2` |
| Live and local `assets/js/theme.js` | `538801c7a2f69ab145493761277daf5ba1f9c0ec110a765d2237b7b8f9a320dc` |
| Active server `assets/brand/taascor-mark.png` | `650861b561a7d3e32aac60fe7ad28359ff8f628f7a397846cca9cbde0cefbbc7` |
| Public edge-served logo representation | `c33a949b7e2c73983d8cb015efcef9a26ef28dc5f56c80af822af3552514a576` |

The local package hash matched the remote upload before extraction. The active release pointer and `.taascor-commit` marker resolve to the application commit. CSS and JavaScript public bytes match the repository files exactly. The active server logo also matches Git; Hostinger serves a byte-different public image representation, but its rendered geometry and appearance passed browser review.

Three PHP package files use CRLF bytes while their Git blobs use LF. Their package hashes match the active server files exactly, and CRLF-to-LF normalization produces byte-identical content. This is recorded as packaging behavior rather than source drift.

## Backups and rollback

| Backup | Location | SHA-256 |
| --- | --- | --- |
| Pre-deploy document-root archive | `/home/u716215139/backups/taascor_website/predeploy-887a3f17-20260904T105033Z-files.tgz` | `5083a75127598606f896d4fdcbcc04907c3c8bbba263cc21f6c056ac0d2d7c5e` |
| Pre-deploy database dump | `/home/u716215139/backups/taascor_website/predeploy-887a3f17-20260904T105033Z-database.sql.gz` | `457404071395840cce755be3d5ac233d95d0baf5a30678342e74e60de65a08b8` |
| Immediate rollback symlink | `/home/u716215139/backups/taascor_website/public_html-887a3f17-20260904T105033Z` | Prior `f34fd28d-20260903T205821Z/app` release |

Both archives were downloaded to ignored local release storage at `Backups/taascor-website/remote-887a3f17-20260904T105033Z`; local and remote hashes match. Rollback is an atomic symlink switch and does not rewrite application data.

## Commit and push status

- Application commit `887a3f1789d7c9a0b95c1fd6e418831060f239a3` was pushed to `origin/feature/integrated-experience` before packaging.
- The production package was built from that exact commit and verified after upload.
- This release record is a documentation-only closure change and is not part of the deployed executable package.

## Deployment path

`GitHub application commit 887a3f1` → exact-commit archive → SSH/SFTP upload → remote SHA-256 readback → immutable release extraction → inactive PHP lint, database-connectivity check, and Solutions render → atomic `public_html` symlink switch → public HTTP and rendered browser smoke.

The remote package was removed after the release switch and verification. The previous immutable release and explicit rollback symlink remain available.

## Tests performed

- Local static controls: 12/12 passed, including lint of 145 PHP files.
- Local Playwright: 33/33 passed.
- Dependency audit: `npm audit --offline --audit-level=high` reported zero vulnerabilities.
- Git validation: cached diff check and tracked-source secret scan passed; branch and remote were aligned before packaging.
- Remote candidate: 72 PHP files passed PHP 8.5 lint.
- Runtime preflight: MySQL connection succeeded; no migration or data mutation was run.
- Pre-switch render: Solutions output contained the `41 x 36` logo attributes and versioned CSS, logo, theme, and site-script URLs.
- Public HTTP: `/`, `/solutions/`, `/about/`, `/leadership/`, `/clients/`, `/workforce/`, `/portal/`, `/robots.txt`, and `/sitemap.xml` returned HTTP 200.
- Protected-path checks: `/.user.ini`, `/database/schema.mysql.sql`, `/Audit/`, and `/Backups/` returned 404; `/app/.env.example` returned 403.
- Desktop browser at 1280 x 720: header 82.59px, logo 40.8 x 35.89px, light default, dark/light toggle passed, no horizontal overflow, and no console errors or warnings.
- Mobile browser at 390 x 844: header 74.59px, logo 31.19 x 27.44px, mobile navigation opened correctly, no horizontal overflow, and no console errors or warnings.
- Mobile homepage: motion-ready class present; the touch cue was visible; clicking empty space advanced the scroll position from 0 to 790px and revealed the following cinematic scene.

## Risks and gaps

- **P1 Major:** Governed applicant, workforce, staff, job-publication, and document workflows remain unavailable pending their previously identified governance and business-readiness approvals. The public informational experience is not dependent on these workflows.
- **P2 Watch Item:** Hostinger appears to transform the public PNG response. The active server file matches Git exactly and rendered geometry passed, but future image-parity checks should compare the origin file and rendered result separately from the edge representation.
- **P2 Watch Item:** The exact package carries CRLF for three PHP files whose Git blobs use LF. Normalized content is identical and runtime validation passed. A future release-hardening change can make PHP line-ending policy explicit in `.gitattributes`.
- **P2 Watch Item:** No Hostinger control-plane CDN purge was required or performed. Content-derived asset versions caused the new executable bytes to load in both fresh and already-open browser contexts; future releases should retain versioned asset URLs.
- **P2 Watch Item:** Physical iOS Safari and Android Chrome were not available in this release environment. Responsive emulation, interaction, overflow, theme, and console checks passed at 390px.
- **Expected Data Movement:** None. The database was backed up and connectivity-tested only.

## Next action

Keep this release under normal observation. The next production work should focus on closing the remaining governance gates before enabling any data-collecting workflow, while retaining the responsive browser matrix and exact-package provenance checks in every release.
