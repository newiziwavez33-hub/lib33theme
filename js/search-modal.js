/**
 * City Library - Search Modal & Live Search Controller
 * 
 * Features:
 * - Smooth modal open/close with backdrop blur and keyboard accessibility (ESC, Focus trap)
 * - Yandex/Google-style Ghost Autocomplete inside input (Tab / ArrowRight to accept)
 * - Live search dropdown suggestions with snippet previews, highlight (<mark>), and badges
 * - Full keyboard navigation (ArrowDown, ArrowUp, Enter, Tab, Escape)
 * - WordPress AJAX & REST API dual-mode fetch with debounce & AbortController
 *
 * @package CityLibrary
 */

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const searchModal = document.getElementById('search-modal');
    if (!searchModal) return;

    const modalContent = document.getElementById('search-modal-content');
    const searchClose = document.getElementById('search-modal-close');
    const searchForm = document.getElementById('search-modal-form');
    const searchInput = document.getElementById('search-modal-input');
    const ghostOverlay = document.getElementById('search-ghost-overlay');
    const ghostTyped = document.getElementById('search-ghost-typed');
    const ghostRest = document.getElementById('search-ghost-rest');
    const clearBtn = document.getElementById('search-clear-btn');
    const dropdown = document.getElementById('search-suggestions-dropdown');
    const suggestionsList = document.getElementById('search-suggestions-list');
    const loadingState = document.getElementById('search-suggestions-loading');
    const emptyState = document.getElementById('search-suggestions-empty');
    const viewAllQuery = document.getElementById('search-view-all-query');
    const viewAllLink = document.getElementById('search-view-all-link');

    // Config & Parameters passed via wp_localize_script
    const ajaxUrl = window.city_library_search_params?.ajax_url || window.ajaxurl || '/wp-admin/admin-ajax.php';
    const nonce = window.city_library_search_params?.nonce || '';
    const homeUrl = window.city_library_search_params?.home_url || '/';

    let lastFocusedElement = null;
    let debounceTimer = null;
    let abortController = null;
    let activeIndex = -1;
    let currentGhostMatch = '';
    let currentResults = [];

    // Helper: Escape Regex special characters
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Helper: Highlight Matched Substring with <mark>
    function highlightQuery(text, query) {
        if (!query || !text) return text || '';

        // Highlight full query or individual words (>=3 chars)
        const words = query.trim().split(/\s+/).filter(w => w.length >= 2);
        if (!words.length) return text;

        const pattern = words.map(w => escapeRegExp(w)).join('|');
        const regex = new RegExp(`(${pattern})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // Helper: Category/Type Icon SVG
    function getTypeIcon(type) {
        switch (type) {
            case 'book':
                return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>';
            case 'event':
                return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
            case 'news':
                return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>';
            case 'branch':
                return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v4M12 14v4M16 14v4"></path></svg>';
            case 'page':
                return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>';
            case 'article':
            default:
                return '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>';
        }
    }

    // Helper: Map badge CSS class
    function getBadgeClass(type) {
        switch (type) {
            case 'book': return 'badge-book';
            case 'event': return 'badge-event';
            case 'news': return 'badge-news';
            case 'branch': return 'badge-branch';
            case 'page': return 'badge-page';
            default: return 'badge-article';
        }
    }

    // Modal: Open
    function openSearch() {
        lastFocusedElement = document.activeElement;
        searchModal.classList.remove('hidden');
        requestAnimationFrame(() => {
            searchModal.classList.remove('opacity-0');
            searchModal.classList.add('is-active');
            if (modalContent) {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }
        });
        searchModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';

        if (searchInput) {
            setTimeout(() => {
                searchInput.focus();
                if (searchInput.value.trim().length >= 2) {
                    performSearch(searchInput.value.trim());
                }
            }, 60);
        }
    }

    // Modal: Close
    function closeSearch() {
        searchModal.classList.add('opacity-0');
        searchModal.classList.remove('is-active');
        if (modalContent) {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
        }
        searchModal.setAttribute('aria-hidden', 'true');

        if (dropdown) dropdown.classList.remove('is-open');
        clearGhost();

        setTimeout(() => {
            searchModal.classList.add('hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        }, 280);
    }

    // Ghost Autocomplete: Clear
    function clearGhost() {
        if (ghostTyped) ghostTyped.textContent = '';
        if (ghostRest) ghostRest.textContent = '';
        currentGhostMatch = '';
    }

    // Ghost Autocomplete: Update
    function updateGhost(query, bestMatch) {
        if (!ghostTyped || !ghostRest || !searchInput) {
            clearGhost();
            return;
        }

        const currentVal = searchInput.value;
        const trimmedVal = currentVal.trim();

        if (!trimmedVal || !bestMatch || trimmedVal.length < 2) {
            clearGhost();
            return;
        }

        const lowerVal = trimmedVal.toLowerCase();
        let candidate = String(bestMatch).trim();
        let lowerCandidate = candidate.toLowerCase();

        // 1. Direct prefix match (case-insensitive)
        if (lowerCandidate.startsWith(lowerVal) && lowerCandidate !== lowerVal) {
            currentGhostMatch = currentVal + candidate.slice(currentVal.length);
            ghostTyped.textContent = currentVal;
            ghostRest.textContent = candidate.slice(currentVal.length);
            return;
        }

        // 2. Word located inside candidate (e.g. "А.С. Пушкин в воспоминаниях", user typed "пушкин")
        const wordIdx = lowerCandidate.indexOf(lowerVal);
        if (wordIdx >= 0) {
            const sliced = candidate.slice(wordIdx);
            const lowerSliced = sliced.toLowerCase();
            if (lowerSliced.startsWith(lowerVal) && lowerSliced !== lowerVal) {
                currentGhostMatch = currentVal + sliced.slice(currentVal.length);
                ghostTyped.textContent = currentVal;
                ghostRest.textContent = sliced.slice(currentVal.length);
                return;
            }
        }

        clearGhost();
    }

    // Ghost Autocomplete: Accept (Tab or ArrowRight)
    function acceptGhost() {
        if (currentGhostMatch && ghostRest && ghostRest.textContent) {
            searchInput.value = currentGhostMatch;
            const fullQuery = currentGhostMatch;
            clearGhost();
            if (clearBtn) clearBtn.classList.add('is-visible');
            performSearch(fullQuery.trim());
            return true;
        }
        return false;
    }

    // Dropdown: Render results
    function renderSuggestions(query, results) {
        if (!dropdown || !suggestionsList) return;

        currentResults = results;
        activeIndex = -1;

        if (loadingState) loadingState.style.display = 'none';

        if (!results || results.length === 0) {
            suggestionsList.innerHTML = '';
            if (emptyState) emptyState.style.display = 'flex';
            if (viewAllQuery) viewAllQuery.textContent = query;
            if (viewAllLink) viewAllLink.href = `${homeUrl}?s=${encodeURIComponent(query)}`;
            dropdown.classList.add('is-open');
            searchInput.setAttribute('aria-expanded', 'true');
            return;
        }

        if (emptyState) emptyState.style.display = 'none';

        suggestionsList.innerHTML = results.map((item, idx) => `
            <li class="suggestion-item" data-index="${idx}" role="option" id="suggestion-item-${idx}">
                <a href="${item.url}" class="suggestion-link">
                    <div class="suggestion-icon-box">
                        ${getTypeIcon(item.type)}
                    </div>
                    <div class="suggestion-body">
                        <div class="suggestion-title">${highlightQuery(item.title, query)}</div>
                        ${item.snippet ? `<div class="suggestion-snippet">${highlightQuery(item.snippet, query)}</div>` : ''}
                        ${item.meta ? `<div class="suggestion-meta">${item.meta}</div>` : ''}
                    </div>
                    <span class="suggestion-badge ${getBadgeClass(item.type)}">${item.type_label || item.label || 'Запись'}</span>
                </a>
            </li>
        `).join('');

        if (viewAllQuery) viewAllQuery.textContent = query;
        if (viewAllLink) viewAllLink.href = `${homeUrl}?s=${encodeURIComponent(query)}`;

        dropdown.classList.add('is-open');
        searchInput.setAttribute('aria-expanded', 'true');
    }

    // Search Engine: Fetch from WordPress AJAX with REST API fallback
    async function performSearch(query) {
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        if (dropdown) dropdown.classList.add('is-open');
        if (loadingState) loadingState.style.display = 'flex';
        if (emptyState) emptyState.style.display = 'none';
        if (suggestionsList) suggestionsList.innerHTML = '';

        try {
            // Priority 1: WordPress AJAX Endpoint
            const formData = new FormData();
            formData.append('action', 'city_library_live_search');
            formData.append('query', query);
            if (nonce) formData.append('nonce', nonce);

            const ajaxResponse = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                signal: abortController.signal
            });

            if (ajaxResponse.ok) {
                const data = await ajaxResponse.json();
                if (data && data.success && Array.isArray(data.data?.results)) {
                    const topMatch = data.data.suggestion_text || data.data.best_match || (data.data.results[0] ? data.data.results[0].title : '');
                    updateGhost(query, topMatch);
                    renderSuggestions(query, data.data.results);
                    return;
                }
            }

            // Priority 2: Fallback to WordPress REST API
            const restUrl = `${homeUrl.replace(/\/$/, '')}/wp-json/wp/v2/search?search=${encodeURIComponent(query)}&per_page=8`;
            const restResponse = await fetch(restUrl, { signal: abortController.signal });

            if (restResponse.ok) {
                const items = await restResponse.json();
                if (Array.isArray(items)) {
                    const formatted = items.map(p => {
                        const cleanTitle = (p.title || '').replace(/<[^>]*>/g, '');
                        let itemType = p.subtype || p.type || 'article';
                        let itemLabel = 'Статья';

                        if (itemType === 'page') {
                            itemLabel = 'Страница';
                        } else if (itemType === 'library_branch') {
                            itemType = 'branch';
                            itemLabel = 'Филиал';
                        } else if (itemType === 'post') {
                            itemLabel = 'Статья';
                        }

                        return {
                            id: p.id,
                            title: cleanTitle,
                            url: p.url || `${homeUrl}?p=${p.id}`,
                            type: itemType,
                            type_label: itemLabel,
                            label: itemLabel,
                            snippet: '',
                            meta: ''
                        };
                    });

                    const firstMatch = formatted.find(i => i.title.toLowerCase().startsWith(query.toLowerCase()));
                    updateGhost(query, firstMatch ? firstMatch.title : (formatted[0] ? formatted[0].title : ''));
                    renderSuggestions(query, formatted);
                    return;
                }
            }

            renderSuggestions(query, []);
        } catch (err) {
            if (err.name !== 'AbortError') {
                console.warn('Live search request warning:', err);
                renderSuggestions(query, []);
            }
        }
    }

    // Input Events
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const currentVal = e.target.value;
            const trimmed = currentVal.trim();

            if (clearBtn) {
                clearBtn.classList.toggle('is-visible', trimmed.length > 0);
            }

            if (trimmed.length < 2) {
                clearGhost();
                if (dropdown) dropdown.classList.remove('is-open');
                searchInput.setAttribute('aria-expanded', 'false');
                if (abortController) abortController.abort();
                return;
            }

            // Instant ghost update if an existing suggestion matches what is being typed
            if (currentGhostMatch) {
                updateGhost(currentVal, currentGhostMatch);
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                performSearch(trimmed);
            }, 200);
        });

        // Sync horizontal scroll between input and ghost overlay
        searchInput.addEventListener('scroll', () => {
            if (ghostOverlay) {
                ghostOverlay.scrollLeft = searchInput.scrollLeft;
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            // Tab key -> Accept ghost autocomplete
            if (e.key === 'Tab' && !e.shiftKey) {
                if (acceptGhost()) {
                    e.preventDefault();
                    return;
                }
            }

            // ArrowRight at end of input -> Accept ghost autocomplete
            if (e.key === 'ArrowRight' && searchInput.selectionStart === searchInput.value.length) {
                if (acceptGhost()) {
                    e.preventDefault();
                    return;
                }
            }

            const items = suggestionsList ? suggestionsList.querySelectorAll('.suggestion-item') : [];
            const isDropdownOpen = dropdown && dropdown.classList.contains('is-open');

            // Keyboard navigation through suggestions dropdown
            if (e.key === 'ArrowDown' && isDropdownOpen && items.length > 0) {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % items.length;
                updateActiveSuggestion(items);
            } else if (e.key === 'ArrowUp' && isDropdownOpen && items.length > 0) {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                updateActiveSuggestion(items);
            } else if (e.key === 'Enter') {
                if (isDropdownOpen && activeIndex >= 0 && items[activeIndex]) {
                    e.preventDefault();
                    const link = items[activeIndex].querySelector('a');
                    if (link && link.href) {
                        window.location.href = link.href;
                    }
                }
            } else if (e.key === 'Escape') {
                if (isDropdownOpen) {
                    e.preventDefault();
                    dropdown.classList.remove('is-open');
                    clearGhost();
                } else {
                    closeSearch();
                }
            }
        });
    }

    function updateActiveSuggestion(items) {
        items.forEach((item, idx) => {
            if (idx === activeIndex) {
                item.classList.add('is-selected');
                item.scrollIntoView({ block: 'nearest' });
                searchInput.setAttribute('aria-activedescendant', item.id);
                clearGhost();
            } else {
                item.classList.remove('is-selected');
            }
        });
    }

    // Clear Button (cross icon)
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            clearGhost();
            if (dropdown) dropdown.classList.remove('is-open');
            clearBtn.classList.remove('is-visible');
        });
    }

    // Trigger Listeners: Event delegation for all toggles
    document.addEventListener('click', (e) => {
        const toggle = e.target.closest('#search-toggle, #search-toggle-mobile, .search-toggle-btn');
        if (toggle) {
            e.preventDefault();
            openSearch();
            return;
        }

        // Close dropdown when clicking outside modal card
        if (dropdown && dropdown.classList.contains('is-open')) {
            if (!e.target.closest('#search-suggestions-dropdown') && !e.target.closest('.search-input-group')) {
                dropdown.classList.remove('is-open');
            }
        }
    });

    if (searchClose) {
        searchClose.addEventListener('click', (e) => {
            e.preventDefault();
            closeSearch();
        });
    }

    // Global Keyboard Shortcuts (Escape to close, Ctrl+K / Cmd+K to open)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
            closeSearch();
            return;
        }

        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            if (searchModal.classList.contains('hidden') || searchModal.classList.contains('opacity-0')) {
                openSearch();
            } else {
                closeSearch();
            }
        }
    });

    // Backdrop Click to close
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal || e.target.classList.contains('backdrop-blur-md')) {
            closeSearch();
        }
    });
});
