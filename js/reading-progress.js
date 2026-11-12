document.addEventListener('DOMContentLoaded', () => {
    // Check if we are on a single post page
    // Using 'single' class which is standard for single post templates
    if (!document.body.classList.contains('single')) return;

    // Create progress bar container
    const progressBar = document.createElement('div');
    progressBar.id = 'reading-progress-bar';
    progressBar.style.position = 'fixed';
    progressBar.style.top = '0';
    progressBar.style.left = '0';
    progressBar.style.height = '4px';
    progressBar.style.backgroundColor = 'var(--btn-bg, #0b7930)'; // Fallback to green
    progressBar.style.zIndex = '9999';
    progressBar.style.width = '0%';
    progressBar.style.transition = 'width 0.1s ease-out';

    document.body.appendChild(progressBar);

    const updateProgress = () => {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;

        if (docHeight <= 0) return;

        const scrollPercent = (scrollTop / docHeight) * 100;
        progressBar.style.width = Math.min(scrollPercent, 100) + '%';
    };

    window.addEventListener('scroll', updateProgress);
    // Initial call
    updateProgress();
});
