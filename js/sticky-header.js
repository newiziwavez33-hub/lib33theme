document.addEventListener('DOMContentLoaded', function() {
    const masthead = document.getElementById('masthead');
    if (!masthead) return;

    function handleScroll() {
        const scrollY = window.scrollY;

        // Threshold: Hide logo after scrolling past 100px (approx Hero start/header height)
        // If we want it strictly "when not on hero block", we might need hero height.
        // But a fixed threshold is usually smoother UX for sticky headers.
        // Let's use 100px.

        if (scrollY > 100) {
            masthead.classList.add('header-scrolled');
        } else {
            masthead.classList.remove('header-scrolled');
        }
    }

    // Initial check
    handleScroll();

    // Throttled scroll listener
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                handleScroll();
                ticking = false;
            });
            ticking = true;
        }
    });
});
