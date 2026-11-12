document.addEventListener('DOMContentLoaded', function() {
    const cookieKey = 'city_library_cookie_consent';
    if (localStorage.getItem(cookieKey)) {
        return;
    }

    // Create Banner
    const banner = document.createElement('div');
    banner.id = 'cookie-consent-banner';
    // Initial state: hidden (translate-y-full)
    banner.className = 'fixed bottom-0 left-0 w-full z-[90] bg-white border-t border-slate-200 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] transform translate-y-full transition-transform duration-500 ease-out flex flex-col md:flex-row items-center justify-between p-6 md:p-8 gap-6';

    // Height constraint roughly 20% of viewport if needed, but auto is better for responsiveness.
    // User asked "20% height". We can set min-height or max-height constraints.
    banner.style.minHeight = '15vh';

    banner.innerHTML = `
        <div class="flex items-start gap-4 max-w-4xl">
            <div class="shrink-0 p-3 bg-primary/10 rounded-full hidden sm:block">
                <span class="material-symbols-outlined text-3xl text-primary" aria-hidden="true">cookie</span>
            </div>
            <div>
                <h4 class="text-lg font-bold font-display text-slate-900 mb-2">Мы используем файлы cookie</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Этот сайт использует файлы cookie для хранения данных. Продолжая использовать сайт, вы даете свое согласие на работу с этими файлами.
                </p>
            </div>
        </div>
        <button id="cookie-accept-btn" class="shrink-0 px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full transition-all shadow-md hover:shadow-lg whitespace-nowrap">
            Хорошо, понятно
        </button>
    `;

    document.body.appendChild(banner);

    // Show after delay
    setTimeout(() => {
        banner.classList.remove('translate-y-full');
    }, 1000);

    // Handle Click
    const btn = document.getElementById('cookie-accept-btn');
    btn.addEventListener('click', () => {
        localStorage.setItem(cookieKey, 'true');
        banner.classList.add('translate-y-full');
        setTimeout(() => {
            banner.remove();
        }, 500);
    });
});
