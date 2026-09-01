(() => {
  'use strict';

  const header = document.querySelector('[data-site-header]');
  const toggle = header?.querySelector('.nav-toggle');
  const nav = header?.querySelector('#site-navigation');

  if (header && toggle && nav) {
    header.classList.add('nav-enhanced');

    const closeNav = () => {
      toggle.setAttribute('aria-expanded', 'false');
      toggle.querySelector('.sr-only').textContent = 'Open navigation';
      header.classList.remove('nav-open');
    };

    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(open));
      toggle.querySelector('.sr-only').textContent = open ? 'Close navigation' : 'Open navigation';
      header.classList.toggle('nav-open', open);
    });

    nav.addEventListener('click', (event) => {
      if (event.target.closest('a')) closeNav();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && header.classList.contains('nav-open')) {
        closeNav();
        toggle.focus();
      }
    });

    window.matchMedia('(min-width: 64rem)').addEventListener('change', (event) => {
      if (event.matches) closeNav();
    });
  }

  document.querySelectorAll('[data-current-year]').forEach((node) => {
    node.textContent = String(new Date().getFullYear());
  });

  document.querySelectorAll('[data-disclosure]').forEach((group) => {
    group.addEventListener('toggle', () => {
      if (!group.open) return;
      document.querySelectorAll('[data-disclosure]').forEach((other) => {
        if (other !== group) other.open = false;
      });
    });
  });
})();
