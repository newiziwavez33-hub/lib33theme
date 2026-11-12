document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('dark-mode-toggle');

    // Check for saved user preference, or system preference
    if (localStorage.getItem('color-theme') === 'dark' ||
       (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }

    toggleButton.addEventListener('click', () => {
        // Toggle class on root element
        document.documentElement.classList.toggle('dark');

        // Update localStorage
        const theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('color-theme', theme);
    });
});
