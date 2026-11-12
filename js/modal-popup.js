document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('city-library-modal');
    if (!modal) return;

    const closeBtn = modal.querySelector('.modal-close');
    const modalContent = modal.querySelector('.modal-content');
    // Logic: If delay is 0, show "on load" (via small timeout for DOM stability).
    // Otherwise use delay.
    let delay = parseInt(modal.dataset.delay);
    if (isNaN(delay)) delay = 3000;

    // Force faster load if user requested '0' or very short
    if (delay < 100) delay = 100;

    // Check sessionStorage to see if already shown in this session
    if (sessionStorage.getItem('city_library_modal_shown')) {
        return;
    }

    function showModal() {
        // Accessibility Check: Do not open if High Contrast Mode is active
        if (document.body.classList.contains('a11y-high-contrast')) {
            return;
        }

        modal.classList.remove('hidden');
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
        sessionStorage.setItem('city_library_modal_shown', 'true');
    }

    // Show after delay
    setTimeout(showModal, delay);

    // Close events
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // Close on click outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
