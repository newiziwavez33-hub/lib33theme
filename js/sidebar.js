document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar = document.getElementById('sidebar-column');
    const primary = document.getElementById('primary');

    if (!toggleBtn || !sidebar || !primary) return;

    toggleBtn.addEventListener('click', function() {
        const isHidden = sidebar.classList.contains('hidden') || !sidebar.classList.contains('lg:w-[30%]');
        // Toggle Sidebar visibility
        if (!isHidden) {
            // Hide Sidebar
            sidebar.classList.remove('lg:w-[30%]');
            sidebar.classList.add('hidden', 'lg:w-0', 'lg:overflow-hidden', 'lg:opacity-0', 'lg:p-0');

            // Expand Content
            primary.classList.remove('lg:w-[70%]');
            primary.classList.add('lg:w-full');

            // Update Icon
            const icon = toggleBtn.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = 'menu';
        } else {
            // Show Sidebar
            sidebar.classList.remove('hidden', 'lg:w-0', 'lg:overflow-hidden', 'lg:opacity-0', 'lg:p-0');
            sidebar.classList.add('lg:w-[30%]');

            // Shrink Content
            primary.classList.add('lg:w-[70%]');
            primary.classList.remove('lg:w-full');

            // Update Icon
            const icon = toggleBtn.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = 'menu_open';
        }
    });
});
