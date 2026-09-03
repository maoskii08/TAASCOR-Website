(() => {
  'use strict';

  const storageKey = 'taascor-color-theme';
  const root = document.documentElement;
  root.classList.add('theme-ready');
  const defaultTheme = 'light';

  const storedTheme = () => {
    try {
      const value = window.localStorage.getItem(storageKey);
      return value === 'light' || value === 'dark' ? value : null;
    } catch {
      return null;
    }
  };

  const resolvedTheme = () => storedTheme() || defaultTheme;

  const renderControls = (theme) => {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      const nextTheme = theme === 'light' ? 'dark' : 'light';
      button.setAttribute('aria-label', `Use ${nextTheme} theme`);
      button.setAttribute('aria-pressed', String(theme === 'dark'));
      const label = button.querySelector('[data-theme-label]');
      if (label) label.textContent = theme === 'light' ? 'Light' : 'Dark';
    });
  };

  const applyTheme = (theme) => {
    const previousTheme = root.dataset.theme;
    root.dataset.theme = theme;
    root.style.colorScheme = theme;
    const themeColor = document.querySelector('meta[name="theme-color"]');
    if (themeColor) themeColor.setAttribute('content', theme === 'light' ? '#f4f7fb' : '#080b13');
    renderControls(theme);
    if (previousTheme !== theme) {
      root.dispatchEvent(new CustomEvent('taascor:themechange', { detail: { theme } }));
    }
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

})();
