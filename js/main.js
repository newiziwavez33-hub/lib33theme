/**
 * City Library - Main JS
 *
 * Consolidates essential theme scripts for header, search, and mobile navigation.
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // 1. HEADER SCROLL BEHAVIOR
  // =================================
  const header = document.getElementById('site-header');
  if (header) {
    const headerShrink = () => {
      if (window.scrollY > 20) {
        header.classList.add('is-scrolled');
      } else {
        header.classList.remove('is-scrolled');
      }
    };
    window.addEventListener('scroll', headerShrink, { passive: true });
    headerShrink(); // Initial check
  }

  // 2. SEARCH MODAL
  // =================================
  const searchToggle = document.getElementById('search-toggle');
  const searchModal = document.getElementById('search-modal');

  if (searchToggle && searchModal) {
    const openSearch = () => {
      searchModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      requestAnimationFrame(() => {
        searchModal.classList.remove('opacity-0');
        const searchInput = searchModal.querySelector('input[type="search"]');
        if (searchInput) {
          setTimeout(() => searchInput.focus(), 100);
        }
      });
    };

    const closeSearch = () => {
      searchModal.classList.add('opacity-0');
      document.body.style.overflow = '';
      setTimeout(() => {
        searchModal.classList.add('hidden');
      }, 300); // Wait for transition
    };

    searchToggle.addEventListener('click', openSearch);

    // Close by clicking background
    searchModal.addEventListener('click', (e) => {
      if (e.target === searchModal) {
        closeSearch();
      }
    });

    // Close with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
        closeSearch();
      }
      // Also add CMD+K shortcut
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
      }
    });
  }

  // 3. MOBILE MENU PANEL & SUBMENUS
  // =================================
  const mobileMenuPanel = document.getElementById('mobile-menu-panel');
  const mobileMenuToggleBtns = document.querySelectorAll('.mobile-menu-toggle-btn');
  const mobileMenuCloseBtns = document.querySelectorAll('.mobile-menu-close-btn');

  if (mobileMenuPanel && mobileMenuToggleBtns.length > 0) {
    const panelInner = mobileMenuPanel.querySelector('.absolute');

    const openMenu = () => {
      mobileMenuPanel.classList.remove('pointer-events-none');
      mobileMenuPanel.classList.remove('opacity-0');
      if (panelInner) {
        panelInner.classList.remove('translate-x-full');
      }
      document.body.style.overflow = 'hidden';
    };

    const closeMenu = () => {
      mobileMenuPanel.classList.add('opacity-0');
      if (panelInner) {
        panelInner.classList.add('translate-x-full');
      }
      setTimeout(() => {
        mobileMenuPanel.classList.add('pointer-events-none');
        document.body.style.overflow = '';
      }, 300);
    };

    mobileMenuToggleBtns.forEach(btn => btn.addEventListener('click', openMenu));
    mobileMenuCloseBtns.forEach(btn => btn.addEventListener('click', closeMenu));

    // Close by clicking background
    mobileMenuPanel.addEventListener('click', (e) => {
      if (e.target === mobileMenuPanel) {
        closeMenu();
      }
    });

    // Close on link click
    mobileMenuPanel.querySelectorAll('a').forEach(link => {
        const parentLi = link.closest('li');
        // Don't close menu if the link is a submenu toggle trigger
        if (parentLi && parentLi.classList.contains('has-children') && link.getAttribute('href') === '#') {
            return;
        }
        link.addEventListener('click', closeMenu);
    });

    // Submenu toggle logic
    mobileMenuPanel.addEventListener('click', function(e) {
        const toggleButton = e.target.closest('.submenu-toggle');
        if (toggleButton) {
            e.preventDefault();
            const parentLi = toggleButton.closest('li.has-children');
            const submenu = parentLi.querySelector('.submenu');
            
            if (submenu) {
                submenu.classList.toggle('hidden');
                toggleButton.setAttribute('aria-expanded', !submenu.classList.contains('hidden'));
                toggleButton.querySelector('.material-symbols-outlined').classList.toggle('rotate-180');
            }
        }
    });
  }

  // 4. SCROLL ANIMATIONS
  // =================================
  const revealElements = document.querySelectorAll('.reveal');
  if (revealElements.length > 0) {
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // Add stagger delay for grid items
          const delay = entry.target.dataset.delay || 0;
          setTimeout(() => {
            entry.target.classList.add('is-visible');
          }, delay);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    revealElements.forEach((el, index) => {
      // If element is in a grid, apply a stagger delay
      if (el.parentElement.classList.contains('grid')) {
        el.dataset.delay = index * 100;
      }
      observer.observe(el);
    });
  }

  // 5. LIGHTBOX
  // =================================
  if (typeof GLightbox !== 'undefined') {
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: false,
        skin: 'clean', // or 'modern' or 'custom'
    });
  }

});

// 5. ACCESSIBILITY SCRIPT
// =================================
(function() {
    'use strict';

    var accessibilityButton = document.getElementById('accessibility-button');
    if (!accessibilityButton) return;

    // === 1. GOST-комплаентный CSS (встраивается один раз) ===
    var styleId = 'city-library-a11y-gost-styles';
    if (!document.getElementById(styleId)) {
        var style = document.createElement('style');
        style.id = styleId;
        style.textContent = [
            '/* === Базовый сброс для a11y-режима (исключая панель) === */',
            'body.a11y-active #a11y-panel,',
            'body.a11y-active #a11y-panel * {',
            '  all: revert !important;',
            '}',
            '#a11y-panel {',
            '  display: none !important;',
            '  position: fixed !important;',
            '  top: 0 !important;',
            '  left: 0 !important;',
            '  width: 100% !important;',
            '  z-index: 999999 !important;',
            '  background: #ffffff !important;',
            '  color: #000000 !important;',
            '  border-bottom: 3px solid #000000 !important;',
            '  padding: 0.75rem 0.5rem !important;',
            '  font-family: Arial, Helvetica, sans-serif !important;',
            '  font-size: 16px !important;',
            '  letter-spacing: normal !important;',
            '  line-height: 1.3 !important;',
            '  max-height: 100vh !important;',
            '  overflow-y: auto !important;',
            '  box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;',
            '}',
            '#a11y-panel.is-visible {',
            '  display: block !important;',
            '}',
            'body.a11y-active #a11y-panel * {',
            '  background: transparent !important;',
            '  color: #000000 !important;',
            '  border-color: #000000 !important;',
            '  font-size: inherit !important;',
            '  font-family: inherit !important;',
            '  letter-spacing: inherit !important;',
            '  line-height: inherit !important;',
            '  border-radius: 0 !important;',
            '  box-shadow: none !important;',
            '  text-shadow: none !important;',
            '  transition: none !important;',
            '  animation: none !important;',
            '  text-transform: none !important;',
            '  appearance: none !important;',
            '  -webkit-appearance: none !important;',
            '  margin: 0 !important;',
            '  text-align: left !important;',
            '}',
            'body.a11y-active #a11y-panel .a11y-panel-inner {',
            '  display: flex !important;',
            '  flex-wrap: wrap !important;',
            '  gap: 0.75rem !important;',
            '  align-items: center !important;',
            '  justify-content: center !important;',
            '  max-width: 1400px !important;',
            '  margin: 0 auto !important;',
            '}',
            'body.a11y-active #a11y-panel .a11y-group {',
            '  display: flex !important;',
            '  align-items: center !important;',
            '  gap: 0.35rem !important;',
            '  border: 2px solid #000000 !important;',
            '  padding: 0.35rem 0.5rem !important;',
            '}',
            'body.a11y-active #a11y-panel .a11y-label {',
            '  font-weight: bold !important;',
            '  font-size: 1rem !important;',
            '  margin-right: 0.5rem !important;',
            '  color: #000000 !important;',
            '  border: none !important;',
            '  text-transform: uppercase !important;',
            '  line-height: 1 !important;',
            '}',
            'body.a11y-active #a11y-panel button.a11y-btn {',
            '  background: #f8f9fa !important;',
            '  color: #000000 !important;',
            '  border: 2px solid #000000 !important;',
            '  padding: 0.4rem 0.7rem !important;',
            '  font-size: 1rem !important;',
            '  font-weight: bold !important;',
            '  cursor: pointer !important;',
            '  text-decoration: none !important;',
            '  min-width: 40px !important;',
            '  text-align: center !important;',
            '  line-height: 1.2 !important;',
            '}',
            'body.a11y-active #a11y-panel button.a11y-btn:hover,',
            'body.a11y-active #a11y-panel button.a11y-btn:focus {',
            '  background: #e2e8f0 !important;',
            '  outline: 3px solid #000000 !important;',
            '  outline-offset: 2px !important;',
            '}',
            'body.a11y-active #a11y-panel button.a11y-btn.active {',
            '  background: #000000 !important;',
            '  color: #ffffff !important;',
            '}',
            'body.a11y-active #a11y-panel button[data-action="theme"][data-value="bw"] { background: #ffffff !important; color: #000000 !important; }',
            'body.a11y-active #a11y-panel button[data-action="theme"][data-value="wb"] { background: #000000 !important; color: #ffffff !important; }',
            'body.a11y-active #a11y-panel button[data-action="theme"][data-value="bb"] { background: #9dd1ff !important; color: #063462 !important; border-color: #063462 !important; }',
            'body.a11y-active #a11y-panel button[data-action="theme"][data-value="br"] { background: #f7f3d6 !important; color: #4d3319 !important; border-color: #4d3319 !important; }',
            'body.a11y-active #a11y-panel button[data-action="theme"].active {',
            '  outline: 3px solid #ff6600 !important;',
            '  outline-offset: 2px !important;',
            '}',
            'body.a11y-active #a11y-panel .a11y-actions {',
            '  display: flex !important;',
            '  gap: 0.5rem !important;',
            '  margin-left: auto !important;',
            '}',
            'body.a11y-active #a11y-panel button.a11y-btn-turn-off {',
            '  background: #ffffff !important;',
            '  color: #000000 !important;',
            '  border-color: #000000 !important;',
            '  border-width: 3px !important;',
            '  padding: 0.4rem 1rem !important;',
            '  font-weight: bold !important;',
            '}',
            'body.a11y-active #a11y-panel button.a11y-btn-turn-off:hover,',
            'body.a11y-active #a11y-panel button.a11y-btn-turn-off:focus {',
            '  background: #000000 !important;',
            '  color: #ffffff !important;',
            '}',
            '@media (max-width: 768px) {',
            '  body.a11y-active #a11y-panel { padding: 0.5rem !important; font-size: 14px !important; }',
            '  body.a11y-active #a11y-panel .a11y-actions { margin-left: 0 !important; width: 100% !important; justify-content: space-between !important; margin-top: 0.5rem !important; }',
            '  body.a11y-active #a11y-panel .a11y-group { flex: 1 1 auto !important; justify-content: center !important; flex-wrap: wrap !important; }',
            '  body.a11y-active #a11y-panel .a11y-label { font-size: 0.85rem !important; }',
            '  body.a11y-active #a11y-panel button.a11y-btn { padding: 0.3rem 0.5rem !important; min-width: 34px !important; font-size: 0.9rem !important; }',
            '}',
            '',
            '/* === Цветовые темы для контента (панель исключена через body.a11y-active #a11y-panel) === */',
            'body.a11y-active {',
            '  --a11y-bg: #ffffff;',
            '  --a11y-text: #000000;',
            '  --a11y-link: #0000ee;',
            '  --a11y-border: #000000;',
            '}',
            'body.a11y-active[data-a11y-theme="wb"] {',
            '  --a11y-bg: #000000;',
            '  --a11y-text: #ffffff;',
            '  --a11y-link: #ffff00;',
            '  --a11y-border: #ffffff;',
            '}',
            'body.a11y-active[data-a11y-theme="bb"] {',
            '  --a11y-bg: #9dd1ff;',
            '  --a11y-text: #063462;',
            '  --a11y-link: #000000;',
            '  --a11y-border: #063462;',
            '}',
            'body.a11y-active[data-a11y-theme="br"] {',
            '  --a11y-bg: #f7f3d6;',
            '  --a11y-text: #4d3319;',
            '  --a11y-link: #000000;',
            '  --a11y-border: #4d3319;',
            '}',
            '',
            '/* === Сброс контента (панель и её дети исключены) === */',
            'html body.a11y-active,',
            'html body.a11y-active main,',
            'html body.a11y-active header,',
            'html body.a11y-active footer,',
            'html body.a11y-active section,',
            'html body.a11y-active article,',
            'html body.a11y-active nav,',
            'html body.a11y-active div,',
            'html body.a11y-active span,',
            'html body.a11y-active li,',
            'html body.a11y-active p,',
            'html body.a11y-active h1,',
            'html body.a11y-active h2,',
            'html body.a11y-active h3,',
            'html body.a11y-active h4 {',
            '  background-color: var(--a11y-bg) !important;',
            '  color: var(--a11y-text) !important;',
            '  border-color: var(--a11y-border) !important;',
            '}',
            'html body.a11y-active *, html body.a11y-active *:before, html body.a11y-active *:after {',
            '  transition: none !important;',
            '  animation: none !important;',
            '  box-shadow: none !important;',
            '  text-shadow: none !important;',
            '  border-radius: 0 !important;',
            '  background-image: none !important;',
            '}',
            'html body.a11y-active h1, html body.a11y-active h2, html body.a11y-active h3, html body.a11y-active h4, html body.a11y-active h5, html body.a11y-active h6,',
            'html body.a11y-active p, html body.a11y-active li, html body.a11y-active td, html body.a11y-active th, html body.a11y-active label {',
            '  color: var(--a11y-text) !important;',
            '}',
            'html body.a11y-active a {',
            '  color: var(--a11y-link) !important;',
            '  text-decoration: underline !important;',
            '  text-decoration-thickness: 2px !important;',
            '}',
            'html body.a11y-active a:hover, html body.a11y-active a:focus {',
            '  background-color: var(--a11y-link) !important;',
            '  color: var(--a11y-bg) !important;',
            '  outline: 4px solid var(--a11y-border) !important;',
            '  outline-offset: 4px !important;',
            '}',
            '',
            '/* === Шрифты === */',
            'body.a11y-active[data-a11y-font="sans"] *:not(.material-symbols-outlined) { font-family: Arial, Helvetica, sans-serif !important; }',
            'body.a11y-active[data-a11y-font="serif"] *:not(.material-symbols-outlined) { font-family: "Times New Roman", Times, serif !important; }',
            'body.a11y-active .material-symbols-outlined { font-family: "Material Symbols Outlined" !important; color: var(--a11y-text) !important; }',
            '',
            '/* === Размер шрифта === */',
            'body.a11y-active[data-a11y-size="normal"] { --a11y-scale: 1; }',
            'body.a11y-active[data-a11y-size="large"] { --a11y-scale: 1.5; }',
            'body.a11y-active[data-a11y-size="xlarge"] { --a11y-scale: 2; }',
            'body.a11y-active h1 { font-size: calc(2.5rem * var(--a11y-scale)) !important; line-height: 1.2 !important; font-weight: bold !important; }',
            'body.a11y-active h2 { font-size: calc(2rem * var(--a11y-scale)) !important; font-weight: bold !important; }',
            'body.a11y-active h3 { font-size: calc(1.75rem * var(--a11y-scale)) !important; font-weight: bold !important; }',
            'body.a11y-active p, body.a11y-active li, body.a11y-active div, body.a11y-active a {',
            '  font-size: calc(1.125rem * var(--a11y-scale)) !important;',
            '  line-height: 1.5 !important;',
            '}',
            '',
            '/* === Межбуквенный интервал === */',
            'body.a11y-active[data-a11y-spacing="normal"] * { letter-spacing: normal !important; }',
            'body.a11y-active[data-a11y-spacing="medium"] * { letter-spacing: 0.1em !important; }',
            'body.a11y-active[data-a11y-spacing="large"] * { letter-spacing: 0.2em !important; }',
            '',
            '/* === Изображения === */',
            'body.a11y-active[data-a11y-images="grayscale"] img { filter: grayscale(100%) contrast(150%) !important; }',
            'body.a11y-active[data-a11y-images="hidden"] img,',
            'body.a11y-active[data-a11y-images="hidden"] svg,',
            'body.a11y-active[data-a11y-images="hidden"] .swiper-slide > div.absolute.inset-0 { display: none !important; }',
            'body.a11y-active img { border: 2px solid var(--a11y-border) !important; max-width: 100% !important; max-height: 50vh !important; height: auto !important; object-fit: contain !important; margin: 0 auto !important; display: block !important; position: relative !important; }',
            '',
            '/* === UI-адаптация === */',
            'body.a11y-active .swiper-wrapper { display: flex !important; flex-direction: column !important; gap: 2rem !important; transform: none !important; }',
            'body.a11y-active .swiper-slide { width: 100% !important; height: auto !important; position: relative !important; }',
            'body.a11y-active .swiper-slide > div { position: relative !important; height: auto !important; }',
            'body.a11y-active article.group { display: flex !important; flex-direction: column !important; height: auto !important; width: 100% !important; border: 2px solid var(--a11y-border) !important; position: relative !important; overflow: visible !important; }',
            'body.a11y-active article.group > div.absolute.inset-0,',
            'body.a11y-active article.group > div:first-child { position: relative !important; height: auto !important; min-height: 200px !important; width: 100% !important; border-bottom: 2px solid var(--a11y-border) !important; overflow: visible !important; }',
            'body.a11y-active article.group > div.relative.z-10 { position: relative !important; padding: 1.5rem !important; pointer-events: auto !important; }',
            'body.a11y-active article.group a.absolute.inset-0.lg\\:hidden { display: none !important; }',
            'body.a11y-active .absolute.bg-primary\\/5, body.a11y-active .blur-3xl, body.a11y-active [class*="bg-gradient"]:not(.swiper-button-prev):not(.swiper-button-next) { display: none !important; }',
            'body.a11y-active article, body.a11y-active .widget, body.a11y-active .swiper-slide > div, body.a11y-active footer { border: 2px solid var(--a11y-border) !important; box-sizing: border-box !important; }',
            'body.a11y-active .hero-gradient, body.a11y-active .featured-cards-section { display: none !important; }',
            '',
            '/* === Хедер === */',
            'body.a11y-active #search-toggle,',
            'body.a11y-active #accessibility-button,',
            'body.a11y-active .mobile-menu-toggle-btn { border-radius: 1rem !important; }',
            'body.a11y-active #masthead { border-top: none !important; border-left: none !important; border-right: none !important; border-bottom: 4px solid var(--a11y-border) !important; height: auto !important; }',
            'body.a11y-active #masthead > div { flex-wrap: wrap !important; gap: 1rem !important; }',
            'body.a11y-active nav { width: 100% !important; overflow: visible !important; }',
            'body.a11y-active nav > ul { flex-wrap: wrap !important; display: flex !important; justify-content: flex-start !important; gap: 1rem !important; }',
            'body.a11y-active .submenu { position: static !important; display: block !important; opacity: 1 !important; visibility: visible !important; transform: none !important; box-shadow: none !important; padding-left: 1rem !important; border-left: 2px solid var(--a11y-border) !important; margin-top: 0.5rem !important; }',
            'body.a11y-active li.group > a .material-symbols-outlined { display: none !important; }'
        ].join('\n');
        document.head.appendChild(style);
    }

    // === 2. Настройки ===
    var defaultSettings = {
        active: false,
        size: 'normal',
        theme: 'bw',
        font: 'sans',
        spacing: 'normal',
        images: 'color'
    };
    var currentSettings = JSON.parse(JSON.stringify(defaultSettings));

    try {
        var saved = localStorage.getItem('city_library_a11y_settings');
        if (saved) {
            var parsed = JSON.parse(saved);
            for (var key in defaultSettings) {
                if (parsed[key] !== undefined) currentSettings[key] = parsed[key];
            }
        }
    } catch (e) {}

    // === 3. Панель (создаётся один раз, защищена от удаления) ===
    var panel;

    function createPanel() {
        if (document.getElementById('a11y-panel')) return document.getElementById('a11y-panel');
        var div = document.createElement('div');
        div.id = 'a11y-panel';
        div.setAttribute('aria-label', 'Панель настроек для слабовидящих');
        div.setAttribute('role', 'dialog');
        div.setAttribute('aria-modal', 'false');
        div.innerHTML = [
            '<div class="a11y-panel-inner">',
            '  <div class="a11y-group"><span class="a11y-label">Размер шрифта:</span>',
            '    <button class="a11y-btn" data-action="size" data-value="normal" title="Обычный размер шрифта">А</button>',
            '    <button class="a11y-btn" data-action="size" data-value="large" title="Увеличенный размер шрифта">А+</button>',
            '    <button class="a11y-btn" data-action="size" data-value="xlarge" title="Большой размер шрифта">А++</button>',
            '  </div>',
            '  <div class="a11y-group"><span class="a11y-label">Цветовая схема:</span>',
            '    <button class="a11y-btn" data-action="theme" data-value="bw" title="Чёрным по белому — стандартная">A</button>',
            '    <button class="a11y-btn" data-action="theme" data-value="wb" title="Белым по чёрному — инвертированная">A</button>',
            '    <button class="a11y-btn" data-action="theme" data-value="bb" title="Тёмно-синим по голубому">A</button>',
            '    <button class="a11y-btn" data-action="theme" data-value="br" title="Коричневым по бежевому">A</button>',
            '  </div>',
            '  <div class="a11y-group"><span class="a11y-label">Шрифт:</span>',
            '    <button class="a11y-btn" data-action="font" data-value="sans" title="Без засечек (Arial)">Без засечек</button>',
            '    <button class="a11y-btn" data-action="font" data-value="serif" title="С засечками (Times New Roman)">С засечками</button>',
            '  </div>',
            '  <div class="a11y-group"><span class="a11y-label">Межбуквенный интервал:</span>',
            '    <button class="a11y-btn" data-action="spacing" data-value="normal" title="Обычный интервал">Нормальный</button>',
            '    <button class="a11y-btn" data-action="spacing" data-value="medium" title="Средний интервал">Средний</button>',
            '    <button class="a11y-btn" data-action="spacing" data-value="large" title="Большой интервал">Большой</button>',
            '  </div>',
            '  <div class="a11y-group"><span class="a11y-label">Изображения:</span>',
            '    <button class="a11y-btn" data-action="images" data-value="color" title="Цветные">Цветные</button>',
            '    <button class="a11y-btn" data-action="images" data-value="grayscale" title="Чёрно-белые">Чёрно-белые</button>',
            '    <button class="a11y-btn" data-action="images" data-value="hidden" title="Скрыть">Скрыть</button>',
            '  </div>',
            '  <div class="a11y-actions">',
            '    <button class="a11y-btn" id="a11y-close-panel" title="Скрыть панель, но оставить режим">Свернуть панель</button>',
            '    <button class="a11y-btn a11y-btn-turn-off" id="a11y-turn-off" title="Выключить режим">Выключить режим</button>',
            '  </div>',
            '</div>'
        ].join('');
        document.body.insertBefore(div, document.body.firstChild);
        return div;
    }

    panel = createPanel();

    // MutationObserver — защита от удаления панели другими скриптами
    var panelGuard = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            m.removedNodes.forEach(function(node) {
                if (node.id === 'a11y-panel' || (node.querySelector && node.querySelector('#a11y-panel'))) {
                    panel = createPanel();
                    if (currentSettings.active) {
                        panel.classList.add('is-visible');
                        applySettings();
                    }
                }
            });
        });
    });
    panelGuard.observe(document.body, { childList: true, subtree: false });

    // === 4. ARIA-анонсер ===
    var announcer = document.createElement('div');
    announcer.setAttribute('aria-live', 'polite');
    announcer.setAttribute('aria-atomic', 'true');
    announcer.className = 'sr-only';
    document.body.appendChild(announcer);

    function announce(msg) {
        announcer.textContent = '';
        requestAnimationFrame(function() { announcer.textContent = msg; });
    }

    // === 5. applySettings ===
    function applySettings() {
        if (!currentSettings.active) {
            document.body.classList.remove('a11y-active');
            ['theme', 'font', 'size', 'spacing', 'images'].forEach(function(k) {
                document.body.removeAttribute('data-a11y-' + k);
            });
            document.body.style.paddingTop = '';
        } else {
            document.body.classList.add('a11y-active');
            document.body.setAttribute('data-a11y-theme', currentSettings.theme);
            document.body.setAttribute('data-a11y-font', currentSettings.font);
            document.body.setAttribute('data-a11y-size', currentSettings.size);
            document.body.setAttribute('data-a11y-spacing', currentSettings.spacing);
            document.body.setAttribute('data-a11y-images', currentSettings.images);
        }

        var btns = panel.querySelectorAll('button[data-action]');
        btns.forEach(function(btn) {
            var a = btn.getAttribute('data-action');
            var v = btn.getAttribute('data-value');
            btn.classList.toggle('active', currentSettings[a] === v);
        });

        try { localStorage.setItem('city_library_a11y_settings', JSON.stringify(currentSettings)); } catch (e) {}

        if (panel.classList.contains('is-visible')) {
            document.body.style.paddingTop = panel.offsetHeight + 'px';
        } else {
            document.body.style.paddingTop = '';
        }
    }

    // === 6. Обработчики ===
    // Кнопка-глазик
    accessibilityButton.addEventListener('click', function(e) {
        e.preventDefault();
        panel.classList.toggle('is-visible');
        if (panel.classList.contains('is-visible')) {
            if (!currentSettings.active) currentSettings.active = true;
            announce('Панель доступности открыта.');
        } else {
            announce('Панель доступности скрыта.');
        }
        applySettings();
    });

    // Клики внутри панели (кнопки настроек)
    panel.addEventListener('click', function(e) {
        var btn = e.target.closest('button[data-action]');
        if (btn) {
            var action = btn.getAttribute('data-action');
            var val = btn.getAttribute('data-value');
            currentSettings[action] = val;
            applySettings();
            announce('Настройки обновлены.');
            return;
        }
        var closeBtn = e.target.closest('#a11y-close-panel');
        if (closeBtn) {
            panel.classList.remove('is-visible');
            announce('Панель доступности скрыта.');
            applySettings();
            return;
        }
        var turnOffBtn = e.target.closest('#a11y-turn-off');
        if (turnOffBtn) {
            currentSettings = JSON.parse(JSON.stringify(defaultSettings));
            panel.classList.remove('is-visible');
            announce('Режим для слабовидящих выключен.');
            applySettings();
        }
    });

    // Resize
    window.addEventListener('resize', function() {
        if (panel.classList.contains('is-visible')) {
            document.body.style.paddingTop = panel.offsetHeight + 'px';
        }
    });

    // Клик вне панели → скрыть
    document.addEventListener('click', function(e) {
        if (!panel.classList.contains('is-visible')) return;
        if (panel.contains(e.target)) return;
        if (e.target === accessibilityButton || accessibilityButton.contains(e.target)) return;
        panel.classList.remove('is-visible');
        announce('Панель доступности скрыта.');
        applySettings();
    });

    // Escape + Ctrl+Shift+V
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && panel.classList.contains('is-visible')) {
            e.preventDefault();
            panel.classList.remove('is-visible');
            announce('Панель доступности скрыта.');
            applySettings();
            accessibilityButton.focus();
            return;
        }
        if (e.ctrlKey && e.shiftKey && (e.key === 'V' || e.key === 'v' || e.key === 'А' || e.key === 'а')) {
            e.preventDefault();
            panel.classList.toggle('is-visible');
            if (panel.classList.contains('is-visible')) {
                if (!currentSettings.active) currentSettings.active = true;
                announce('Панель доступности открыта.');
            } else {
                announce('Панель доступности скрыта.');
            }
            applySettings();
        }
    });

    // === 7. Восстановление состояния ===
    if (currentSettings.active) {
        applySettings();
        announce('Настройки доступности обновлены.');
    }
})();
