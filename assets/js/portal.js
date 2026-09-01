(() => {
  'use strict';

  document.querySelectorAll('[data-character-count]').forEach((field) => {
    const output = document.getElementById(field.dataset.characterCount);
    if (!output) return;
    const maximum = Number(field.getAttribute('maxlength')) || 0;
    const update = () => {
      output.textContent = maximum
        ? `${field.value.length.toLocaleString()} of ${maximum.toLocaleString()} characters.`
        : `${field.value.length.toLocaleString()} characters.`;
    };
    field.addEventListener('input', update);
    update();
  });

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
  });
})();
