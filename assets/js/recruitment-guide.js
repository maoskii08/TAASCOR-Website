(() => {
  'use strict';

  const search = document.querySelector('[data-guide-search]');
  const result = document.querySelector('[data-guide-search-result]');
  const items = [...document.querySelectorAll('[data-guide-item]')];
  const sections = [...document.querySelectorAll('[data-guide-section]')];

  if (!search || !result || items.length === 0) return;

  const normalize = (value) => value.toLocaleLowerCase().trim();

  const applyFilter = () => {
    const terms = normalize(search.value).split(/\s+/).filter(Boolean);
    let visible = 0;

    for (const item of items) {
      const index = normalize(`${item.dataset.guideKeywords || ''} ${item.textContent || ''}`);
      const matches = terms.every((term) => index.includes(term));
      item.hidden = !matches;
      if (matches) visible += 1;
    }

    for (const section of sections) {
      const sectionItems = [...section.querySelectorAll('[data-guide-item]')];
      const ownIndex = normalize(`${section.dataset.guideKeywords || ''} ${section.textContent || ''}`);
      section.hidden = terms.length > 0
        && !terms.every((term) => ownIndex.includes(term))
        && !sectionItems.some((item) => !item.hidden);
    }

    result.textContent = terms.length === 0
      ? `${items.length} guide entries available.`
      : visible === 0
        ? 'No guide entry matched. Try a stage, action, or outcome.'
        : `${visible} ${visible === 1 ? 'entry' : 'entries'} matched.`;
  };

  search.addEventListener('input', applyFilter);
  applyFilter();
})();
