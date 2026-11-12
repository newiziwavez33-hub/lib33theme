document.addEventListener('DOMContentLoaded', function() {

    // 1. Trigger Button Logic
    const triggerBtn = document.createElement('button');
    triggerBtn.id = 'lumos-btn';
    triggerBtn.innerText = 'Lumos!';
    triggerBtn.style.position = 'fixed';
    triggerBtn.style.top = '20px';
    triggerBtn.style.right = '20px';
    triggerBtn.style.zIndex = '9999';
    triggerBtn.style.padding = '10px 20px';
    triggerBtn.style.background = '#2c3e50';
    triggerBtn.style.color = '#fff';
    triggerBtn.style.border = 'none';
    triggerBtn.style.borderRadius = '5px';
    triggerBtn.style.fontFamily = 'serif';
    triggerBtn.style.cursor = 'pointer';
    triggerBtn.style.transition = 'all 0.3s ease';

    // Check local storage for persistence
    if (localStorage.getItem('magicMode') === 'active') {
        activateMagicMode();
    }

    document.body.appendChild(triggerBtn);

    triggerBtn.addEventListener('click', function() {
        if (document.body.classList.contains('hp-magic-active')) {
            deactivateMagicMode();
        } else {
            activateMagicMode();
        }
    });

    function activateMagicMode() {
        document.body.classList.add('hp-magic-active');
        triggerBtn.innerText = 'Nox!';
        triggerBtn.style.background = '#8a0303';
        localStorage.setItem('magicMode', 'active');

        injectMagicHeader();
        injectMagicFooter();
        injectSpecialModules();
        startFootprints();
    }

    function deactivateMagicMode() {
        document.body.classList.remove('hp-magic-active');
        triggerBtn.innerText = 'Lumos!';
        triggerBtn.style.background = '#2c3e50';
        localStorage.setItem('magicMode', 'inactive');

        removeMagicElements();
        stopFootprints();
    }

    // 2. DOM Injection
    function injectMagicHeader() {
        if (document.getElementById('hp-header-overlay')) return;

        const headerOverlay = document.createElement('div');
        headerOverlay.id = 'hp-header-overlay';
        headerOverlay.className = 'hp-magic-header';
        headerOverlay.innerHTML = `
            <h1 class="hp-masthead-title">DAILY PROPHET: MAGICAL ARCHIVE REVEALED!</h1>
            <div class="hp-ticker">
                <div class="hp-ticker-content">
                    BLINKING HEADLINES • MOVING AND ARCHIVE REVEALED! • BLINKING MAGIC HEADLINES! • DARK ARTS DEFENSE CLASS CANCELLED • QUIDDITCH WORLD CUP UPDATES •
                </div>
            </div>
            <nav class="hp-nav-parchment">
                <a href="#" class="hp-nav-item">GREAT HALL</a>
                <a href="#" class="hp-nav-item">RESTRICTED SECTION</a>
                <a href="#" class="hp-nav-item">OWL ELECTION</a>
                <a href="#" class="hp-nav-item">OWL POST</a>
                <a href="#" class="hp-nav-item">MORE</a>
            </nav>
        `;

        // Insert before main content
        const main = document.querySelector('main') || document.querySelector('#content') || document.body;
        main.insertBefore(headerOverlay, main.firstChild);
    }

    function injectSpecialModules() {
        if (document.getElementById('hp-modules-overlay')) return;

        const container = document.createElement('div');
        container.id = 'hp-modules-overlay';

        container.innerHTML = `
            <div class="hp-ministry-decree">
                <h2>MINISTRY DECREE!</h2>
                <div style="font-size: 5rem; line-height: 1; font-weight: bold; margin: 1rem 0;">M</div>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <div class="hp-wax-seal-btn">READ</div>
            </div>

            <div class="hp-wanted-section">
                <div class="hp-wanted-poster">
                    <h3>WANTED!</h3>
                    <img src="https://via.placeholder.com/200x250/333/fff?text=WITCH" alt="Wanted Witch">
                    <p>Have you seen this wizard?</p>
                </div>
                <div class="hp-wanted-poster">
                    <h3>WANTED!</h3>
                    <img src="https://via.placeholder.com/200x250/333/fff?text=WIZARD" alt="Wanted Wizard">
                    <p>Approach with extreme caution!</p>
                </div>
            </div>
        `;

        // Append to main content area
        const postsContainer = document.querySelector('#posts-container') || document.querySelector('#primary');
        if (postsContainer) {
            postsContainer.appendChild(container);
        }
    }

    function injectMagicFooter() {
        const footer = document.querySelector('footer');
        if (footer && !footer.querySelector('.hp-footer-content')) {
            const footerContent = document.createElement('div');
            footerContent.className = 'hp-footer-content';
            footerContent.innerHTML = `
                <h3>SEND OWL FOR SUBSCRIPTION</h3>
                <div class="hp-wax-seal-btn" style="margin: 0 auto;">SEND</div>
            `;
            footer.appendChild(footerContent);
        }
    }

    function removeMagicElements() {
        const header = document.getElementById('hp-header-overlay');
        if (header) header.remove();

        const modules = document.getElementById('hp-modules-overlay');
        if (modules) modules.remove();

        const footerContent = document.querySelector('.hp-footer-content');
        if (footerContent) footerContent.remove();
    }

    // 3. Footprint Animation
    let footprintInterval;

    function startFootprints() {
        if (footprintInterval) return;

        footprintInterval = setInterval(() => {
            createFootprint();
        }, 800);
    }

    function stopFootprints() {
        clearInterval(footprintInterval);
        footprintInterval = null;
        document.querySelectorAll('.hp-footprint').forEach(el => el.remove());
    }

    function createFootprint() {
        const footprint = document.createElement('div');
        footprint.className = 'hp-footprint';

        // Random position path logic (simplified: random walk)
        const x = Math.random() * window.innerWidth;
        const y = Math.random() * window.innerHeight;
        const rotation = Math.random() * 360;

        footprint.style.left = `${x}px`;
        footprint.style.top = `${y}px`;
        footprint.style.transform = `rotate(${rotation}deg)`;

        document.body.appendChild(footprint);

        // Animate
        requestAnimationFrame(() => {
            footprint.classList.add('visible');
            setTimeout(() => {
                footprint.classList.remove('visible');
                setTimeout(() => footprint.remove(), 1000);
            }, 3000);
        });
    }

});
