document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Logic ---
    const mobileMenu = document.getElementById('mobile-menu');
    // Updated selector to support multiple triggers (Header + Bottom Nav) via class
    const mobileMenuToggles = document.querySelectorAll('.mobile-menu-toggle-btn');
    const globalCloseBtn = document.getElementById('mobile-menu-close');

    if (mobileMenu) {
        const mobileMenuInner = mobileMenu.querySelector('.mobile-menu-inner');

        function isMobileMenuOpen() {
            return !mobileMenu.classList.contains('opacity-0') && !mobileMenu.classList.contains('pointer-events-none');
        }

        // Function to open menu
        function openMobileMenu() {
            // Show Overlay
            mobileMenu.classList.remove('pointer-events-none', 'opacity-0');
            mobileMenu.setAttribute('aria-hidden', 'false');
            // Slide in Drawer
            if (mobileMenuInner) {
                mobileMenuInner.classList.remove('translate-x-full');
                mobileMenuInner.classList.add('translate-x-0');
            }
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        }

        // Function to close menu
        function closeMobileMenu() {
            // Slide out Drawer
            if (mobileMenuInner) {
                mobileMenuInner.classList.remove('translate-x-0');
                mobileMenuInner.classList.add('translate-x-full');
            }

            // Hide Overlay
            mobileMenu.classList.add('pointer-events-none', 'opacity-0');
            mobileMenu.setAttribute('aria-hidden', 'true');

            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }

        function toggleMobileMenu() {
            if (isMobileMenuOpen()) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        }

        // Expose globally just in case
        window.openMobileMenu = openMobileMenu;
        window.closeMobileMenu = closeMobileMenu;

        // Toggle Listeners (for all toggle buttons)
        if (mobileMenuToggles.length > 0) {
            mobileMenuToggles.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleMobileMenu();
                });
            });
        }

        // Backward compatibility for legacy ID (if still present)
        const legacyToggle = document.getElementById('mobile-menu-toggle');
        if (legacyToggle && !legacyToggle.classList.contains('mobile-menu-toggle-btn')) {
            legacyToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
        }

        // Close Listener (Global Button)
        if (globalCloseBtn) {
            globalCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileMenu();
            });
        }

        // Ensure the ID based internal close button and explicit .menu-item-close links work
        const internalIdBtn = mobileMenu.querySelector('#mobile-menu-close');
        if (internalIdBtn && internalIdBtn !== globalCloseBtn) {
            internalIdBtn.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileMenu();
            });
        }

        const explicitCloseLinks = mobileMenu.querySelectorAll('.menu-item-close a');
        explicitCloseLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileMenu();
            });
        });

        // Close on Outside Click (Overlay backdrop outside .mobile-menu-inner)
        mobileMenu.addEventListener('click', function(e) {
            if (e.target === mobileMenu || (mobileMenuInner && !mobileMenuInner.contains(e.target))) {
                closeMobileMenu();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMobileMenuOpen()) {
                closeMobileMenu();
            }
        });

        // Helper to find direct child submenu of a list item
        function getDirectSubmenu(parentLi) {
            if (!parentLi) return null;
            return Array.from(parentLi.children).find(
                el => el.tagName === 'UL' && el.classList.contains('submenu')
            ) || parentLi.querySelector(':scope > ul.submenu');
        }

        // Toggle direct submenu of a list item (supports 1st, 2nd, 3rd levels)
        function toggleSubmenu(parentLi) {
            const submenu = getDirectSubmenu(parentLi);
            if (!submenu) return false;

            submenu.classList.toggle('hidden');
            const isExpanded = !submenu.classList.contains('hidden');

            const toggleBtn = parentLi.querySelector(':scope > div > .submenu-toggle, :scope > .submenu-toggle');
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('.material-symbols-outlined');
                if (icon) {
                    icon.classList.toggle('rotate-180', isExpanded);
                }
                toggleBtn.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            }

            return true;
        }

        // --- Submenu Toggle Logic (Mobile & Kiosk 1080x1920) ---
        const toggles = mobileMenu.querySelectorAll('.submenu-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const parentLi = this.closest('li');
                if (!parentLi) return;

                toggleSubmenu(parentLi);
            });
        });

        // --- Menu Link Click Logic ---
        const links = mobileMenu.querySelectorAll('a');
        links.forEach(link => {
            if (link.closest('.menu-item-close')) {
                return;
            }

            link.addEventListener('click', function(e) {
                const parentLi = this.closest('li');
                const href = (this.getAttribute('href') || '').trim();
                const isRealLink = Boolean(href && href !== '#' && !href.toLowerCase().startsWith('javascript'));

                if (parentLi && parentLi.classList.contains('has-children')) {
                    const submenu = getDirectSubmenu(parentLi);

                    // Non-navigable parent anchor ('#', empty, javascript:) always toggles submenu
                    if (!isRealLink) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleSubmenu(parentLi);
                        return;
                    }

                    // Real link on parent item: first tap opens submenu if closed; second tap navigates
                    if (submenu && submenu.classList.contains('hidden')) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleSubmenu(parentLi);
                        return;
                    }
                }

                closeMobileMenu();
            });
        });
    }

    // --- Smart Scroll Visibility (Bottom Nav & Renewal Button) ---
    // DISABLED: User feedback indicated this was "broken". Keeping nav always visible for now.
    /*
    const bottomNav = document.querySelector('nav.safe-area-bottom');
    const renewalBtn = document.getElementById('book-renewal-btn');

    if (bottomNav) {
        function checkNavVisibility() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const docHeight = document.documentElement.scrollHeight;
            const scrollBottom = currentScroll + windowHeight;

            // Logic: Hide ONLY if at the very bottom (Footer)
            // We use a threshold of approx 50-100px from the bottom
            const isAtBottom = scrollBottom >= docHeight - 50;

            if (isAtBottom) {
                // Hide Nav
                bottomNav.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');

                // Hide Renewal Button
                if (renewalBtn) {
                    renewalBtn.classList.add('translate-x-full', 'opacity-0', 'pointer-events-none');
                }
            } else {
                // Show Nav
                bottomNav.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');

                // Show Renewal Button
                if (renewalBtn) {
                    renewalBtn.classList.remove('translate-x-full', 'opacity-0', 'pointer-events-none');
                }
            }
        }

        // Init
        checkNavVisibility();
        window.addEventListener('scroll', checkNavVisibility, { passive: true });
    }
    */
});
