(() => {
  'use strict';

  const storageKey = 'taascor-color-theme';
  const root = document.documentElement;
  root.classList.add('theme-ready');
  const media = window.matchMedia('(prefers-color-scheme: light)');

  const storedTheme = () => {
    try {
      const value = window.localStorage.getItem(storageKey);
      return value === 'light' || value === 'dark' ? value : null;
    } catch {
      return null;
    }
  };

  const resolvedTheme = () => storedTheme() || (media.matches ? 'light' : 'dark');

  const renderControls = (theme) => {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      const nextTheme = theme === 'light' ? 'dark' : 'light';
      button.setAttribute('aria-label', `Use ${nextTheme} theme`);
      button.setAttribute('aria-pressed', String(theme === 'light'));
      const label = button.querySelector('[data-theme-label]');
      if (label) label.textContent = theme === 'light' ? 'Light' : 'Dark';
    });
  };

  const applyTheme = (theme) => {
    root.dataset.theme = theme;
    root.style.colorScheme = theme;
    const themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) themeColor.setAttribute('content', theme === 'light' ? '#f4efe5' : '#080b13');
    renderControls(theme);
  };

  applyTheme(resolvedTheme());

  document.addEventListener('DOMContentLoaded', () => {
    renderControls(resolvedTheme());
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const nextTheme = root.dataset.theme === 'light' ? 'dark' : 'light';
        try {
          window.localStorage.setItem(storageKey, nextTheme);
        } catch {
          // The preference still applies for this page when storage is unavailable.
        }
        applyTheme(nextTheme);
      });
    });
  });

  media.addEventListener('change', () => {
    if (!storedTheme()) applyTheme(resolvedTheme());
  });
})();
