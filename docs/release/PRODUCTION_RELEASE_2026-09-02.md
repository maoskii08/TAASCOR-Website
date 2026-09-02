# TAASCOR Website production release — 2026-09-02

## Gate decision

**GO for the fail-closed public website and the approved mission, vision, and core-values publication. NO-GO for governed data workflows.** Application commit `72a6a5d742d08e876917c54c4da4f297283c559f` is live at `https://taascor.com/`. Applicant/workforce collection, resume uploads, staff workflows, job publication, and public indexing remain disabled in production.

## Summary

The About page now presents TAASCOR's mission, vision, and five company core values in a responsive editorial sequence for light and dark modes. The content preserves the supplied company meaning while improving grammar and digital readability. The publication is registered as user-supplied company content, with the legacy TAASCOR About page retained as corroborating context.

The release also adds Hostinger PHP opcode-cache timestamp validation to the non-secret deployment overlay. This ensures immutable document-root switches revalidate changed PHP source under the stable `public_html` path.

## Deployment profile

| Field | Verified value |
| --- | --- |
| Project | TAASCOR Website |
| Repository | `https://github.com/maoskii08/TAASCOR-Website.git` |
| Branch | `feature/integrated-experience` |
| Application commit | `72a6a5d742d08e876917c54c4da4f297283c559f` |
| Live URL | `https://taascor.com/` |
| Protocol | SSH/SFTP; host key `ssh-ed25519 SHA256:Rj5TJlVihe6G2m0iYexrAYuXqGmIHTZg06ROv7RwN7U` |
| Public root | `/home/u716215139/domains/taascor.com/public_html` |
| Active release | `/home/u716215139/releases/taascor_website/72a6a5d7-20260902T101655Z/app` |
| Runtime | PHP 8.5.4; existing mode-`600` runtime bootstrap preserved |
| Credential source | Project-root `.env.deploy.local`; ignored and untracked; values not displayed or packaged |

The active document root is a symlink to the immutable release. The Hostinger overlay sets the external runtime bootstrap, immediate opcode timestamp revalidation, and the verified CSP fallback. The runtime bootstrap and private upload directory remain outside the web root. Hostinger CDN Development mode was used temporarily during cache diagnosis, restored to off, and followed by a final cache flush.

## Files changed

- `about/index.php`: published the mission, vision, and five core values and updated the evidence-gated company-profile copy.
- `assets/css/site.css`: added responsive, theme-aware purpose and values layouts.
- `docs/content/MEDIA_AND_CLAIM_REGISTER.md`: recorded the supplied company content and corroborating legacy source.
- `tests/e2e/governed-experience.spec.mjs`: added exact About-content coverage.
- `tests/e2e/visual.spec.mjs`: added full-page About coverage to the responsive light/dark matrix.

## Release and parity evidence

| Artifact | SHA-256 |
| --- | --- |
| Uploaded exact-commit Git archive | `16221e37d624dd39ec2a31df19d834ee1609b8e68b2807b3a6f7b8624b7be574` |
| Live `about/index.php` | `8d28e05b93cc024346a013696493351ac0e0ea8db5191561485f009d938d9539` |
| Live `assets/css/site.css` | `d201cf98bb10d895dca06b1b5222277b4a80dd5fa0c366c775b3b889d8ddc9fd` |
| Live `assets/js/theme.js` | `ef3a09cc691d95bddcb193832a3a0becb3c05f2987e74330b3cd9c090fab32ca` |
| Live `favicon.svg` | `967fa0ba93fc413eae7e590784b1b91bc5c2d04d9f7f6c6abf8d17a4906f56b9` |
| Deployed `.htaccess` with runtime, opcode, and CSP overlays | `a35b6ce452ca22f9ba3cfe2e4d17acd6159cb873d8824eb39ab86d7752cbe050` |

The local package hash matched the remote upload readback before extraction. The active release commit marker, executable file hashes, and public CSS response match the application commit.

## Backups and rollback

| Backup | Location | SHA-256 |
| --- | --- | --- |
| Pre-deploy document-root archive | `/home/u716215139/backups/taascor_website/predeploy-72a6a5d7-20260902T101655Z-files.tgz` | `39ef94fea9892cb37690c6696db4582ecfdc45a0383615050bb168cac434600e` |
| Pre-migration database dump | `/home/u716215139/backups/taascor_website/predeploy-72a6a5d7-20260902T101655Z-database.sql.gz` | `7f56d0d4fa54ebe4a6ae5dc0476612864c60af5a644e6cbc96b92c1d1ff77881` |
| Immediate rollback symlink | `/home/u716215139/backups/taascor_website/public_html-72a6a5d7-20260902T101655Z` | Prior verified `72a6a5d7-20260902T100244Z/app` release |
| Earlier rollback symlink | `/home/u716215139/backups/taascor_website/public_html-72a6a5d7-20260902T100244Z` | Prior `8d5e9298-20260902T092712Z/app` release |

The two final archive backups were downloaded to ignored local release storage at `Backups/taascor-website/remote-72a6a5d7-20260902T101655Z`, and their local hashes match the remote records. Rollback is an atomic document-root symlink switch; it does not rewrite production data.

## Commit and push status

- Application commit `72a6a5d742d08e876917c54c4da4f297283c559f` was pushed to `origin/feature/integrated-experience` before packaging.
- The immutable production package was built only from that commit.
- This release record is a documentation-only closure change and is not part of the deployed executable package.

## Deployment path

`GitHub application commit 72a6a5d` → exact tracked-file archive → SSH/SFTP upload → remote hash verification → immutable release extraction → PHP lint and production migration check → pre-switch About render → atomic `public_html` symlink switch → Hostinger CDN flush → live browser smoke.

The first 10:02 UTC activation automatically rolled back when the standard public response still presented the previous CDN representation. No data mutation occurred. The final 10:16 UTC release retained the same exact application commit, added the Hostinger opcode validation overlay before activation, and passed all switch and public checks.

## Tests performed

- Local static suite: 12/12 passed.
- Local Playwright suite: 27/27 passed, including the new mission/vision/value assertions.
- Local visual review: full-page About rendering inspected at 1440 px dark, 768 px light, 390 px dark, and 360 px light.
- Git: working branch matched `origin/feature/integrated-experience` at the application commit before packaging.
- SSH target: password authentication, host identity, current root, rollback baseline, PHP 8.5.4, runtime permissions, and release roots verified.
- Backups: file archive and MySQL dump created, verified non-empty, hashed remotely, downloaded, and locally re-hashed.
- Database: production migration reported the MySQL schema current; 14 application tables and one schema-migration record remained present.
- Remote source: all PHP files passed PHP 8.5 lint; pre-switch production About rendering contained mission, vision, and core-value markers.
- External HTTP: `/`, `/about/`, `/careers/`, `/workforce/`, `/portal/`, `/robots.txt`, and `/sitemap.xml` returned `200`.
- Denials: `/.user.ini`, `/database/schema.mysql.sql`, and `/Backups/` returned `404`; `/app/.env.example` returned `403`.
- Security transport: HTTPS returned CSP, HSTS, no-sniff, frame denial, referrer policy, permissions policy, and `X-Robots-Tag: noindex,nofollow`.
- Live browser: standard `/about/` content, desktop light, desktop dark, 390 px mobile light, zero horizontal overflow, and zero page errors passed after the final CDN flush.

## Risks and gaps

- **P1 Major:** Privacy, security, retention, accessibility-owner, malware-scanning, recovery, staff-access, and business-UAT gates remain open. Their corresponding production capabilities remain disabled.
- **P2 Watch Item:** Hostinger overrides PHP-generated nonce/hash CSP headers. The deployed fallback restricts sources, frames, objects, forms, and connections, but permits existing inline presentation scripts. Moving all inline scripts to versioned assets would allow removal of `'unsafe-inline'` in a future qualified release.
- **P2 Watch Item:** Immutable PHP releases behind Hostinger's stable document-root symlink require opcode timestamp revalidation and a CDN flush. Both controls are now part of this release evidence and should remain in the deployment runbook.
- **P2 Watch Item:** Search indexing remains deliberately disabled until canonical-domain, remaining corporate facts, social metadata, and owner approvals close.
- **Expected Data Movement:** The migration verified the existing schema only. No real applicant, workforce, staff, job, document, or synthetic test record was inserted, updated, or deleted during live smoke.

## Post-release observation

The live pointer and commit marker resolve to the final immutable release. Standard and cache-busted About responses present the approved content, executable hashes match, production routes and protected-path denials behave as expected, the schema remains current, and desktop/mobile theme rendering passed. Hostinger CDN is active with Development mode off after the final flush. No authenticated or mutating production workflow was exercised because those capabilities remain intentionally disabled.

## Next action

Keep the release in observation and continue the broader TAASCOR program against the open P1 governance gates. Publish registration, leadership, location, detailed service, and proof claims only after their named owners provide source and public-use approval.
