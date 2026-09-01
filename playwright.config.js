import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.QA_BASE_URL || 'http://127.0.0.1:4177';
const useManagedServer = !process.env.QA_BASE_URL;

export default defineConfig({
  testDir: './tests/e2e',
  outputDir: './tests/.artifacts/results',
  fullyParallel: false,
  forbidOnly: Boolean(process.env.CI),
  retries: process.env.CI ? 1 : 0,
  // Recruitment-flow checks share one disposable SQLite database by design.
  workers: 1,
  timeout: 45_000,
  expect: {
    timeout: 8_000,
  },
  reporter: [
    ['list'],
    ['html', { outputFolder: './tests/.artifacts/report', open: 'never' }],
  ],
  use: {
    baseURL,
    actionTimeout: 10_000,
    navigationTimeout: 20_000,
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
    video: 'retain-on-failure',
  },
  projects: [
    {
      name: 'desktop-chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  webServer: useManagedServer
    ? {
        command: 'node tests/support/start-server.mjs',
        url: `${baseURL}/`,
        reuseExistingServer: false,
        timeout: 30_000,
        stdout: 'pipe',
        stderr: 'pipe',
      }
    : undefined,
});
