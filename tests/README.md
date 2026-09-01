# TAASCOR local QA harness

This harness tests the local application without using production data. The default
Playwright server binds to `127.0.0.1:4177`, forces `APP_ENV=test`, uses a
disposable SQLite database under `tests/.artifacts`, and stores uploads there too.

## Commands

```powershell
npm ci
npx playwright install chromium
npm test
```

- `npm run test:static` runs PHP lint, local-reference/fragment checks, and the
  repository secret-signature scan. It also verifies that CLI mutation helpers
  refuse a missing `APP_ENV`, production collection requires explicit flags and
  approved notice versions, and sitemap publication remains opt-in.
- `npm run test:e2e` runs the browser, accessibility, responsive, route,
  security, and synthetic recruitment workflow tests. Governed-flow coverage
  includes the workforce brief receipt and SQLite persistence, job function and
  shift filters, privacy acknowledgements and account settings, session rotation
  and invalidation, forbidden application-status transitions, applicant
  withdrawal, Windows alias/dot/control-path protection, and the default empty
  sitemap/noindex policy. Lifecycle regressions bind submitted applications to
  reviewed job snapshots and prevent cross-application or terminal-state task
  mutations. Resume coverage verifies content hashing, owner-bound recording,
  the database uniqueness constraint, and unchanged blob, quota, and audit state
  after a duplicate upload is refused.
- `npm run test:e2e:visual` captures desktop and 390 px screenshots under
  `tests/.artifacts/screenshots`.

Set `QA_BASE_URL` only for read-only checks against another environment. Synthetic
registration/application tests refuse to mutate any non-loopback target. Never set
`QA_ALLOW_REMOTE_MUTATIONS`; the harness intentionally provides no such override.
The database assertions read only `tests/.artifacts/qa.sqlite`, which the managed
server deletes and recreates at the start of every run.
