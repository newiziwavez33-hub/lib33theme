document.addEventListener('DOMContentLoaded', function() {
    const params = window.renewal_params || {};
    const settings = params.settings || {};

    const isMobile = window.innerWidth < 1024;
    const visibility = settings.btn_visibility || 'all';

    if (visibility === 'hidden') return;
    if (visibility === 'mobile-only' && !isMobile) return;
    if (visibility === 'desktop-only' && isMobile) return;

    // --- 1. Floating Button (Exact design from Screenshot 1) ---
    const renewBtn = document.createElement('button');
    renewBtn.id = 'book-renewal-btn';

    let btnClasses = 'fixed z-50 transition-all duration-300 flex items-center group translate-y-0';
    btnClasses += ' shadow-md hover:shadow-xl active:scale-95';

    const position = settings.btn_position || 'bottom-right';
    if (position === 'bottom-left') {
        btnClasses += ' bottom-28 left-4 lg:landscape:bottom-8 lg:landscape:left-8';
    } else {
        btnClasses += ' bottom-28 right-4 lg:landscape:bottom-8 lg:landscape:right-8';
    }

    renewBtn.className = btnClasses;
    renewBtn.style.borderRadius = '12px';
    renewBtn.style.background = settings.btn_bg && settings.btn_bg !== '#0b7930' ? settings.btn_bg : '#1c77d9';
    renewBtn.style.color = '#ffffff';
    renewBtn.style.boxShadow = '0 6px 20px rgba(28, 119, 217, 0.35)';

    renewBtn.setAttribute('aria-label', settings.btn_text || 'Продлить книгу');
    renewBtn.title = settings.btn_text || 'Продлить книгу онлайн';
    renewBtn.innerHTML = `
        <div class="flex items-center pl-4 pr-3 py-3">
            <svg class="w-6 h-6 text-white shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 6.5C10 4.5 6 4 3 5.5v13.5c3-1.5 7-1 9 1v-13.5z" />
                <path d="M12 6.5C14 4.5 18 4 21 5.5v13.5c-3-1.5-7-1-9 1v-13.5z" />
                <line x1="12" y1="6.5" x2="12" y2="20" />
            </svg>
        </div>
        <div class="h-6 w-[1.5px] bg-white/35 shrink-0"></div>
        <span class="pl-3.5 pr-5 py-3 text-white font-medium text-[15px] sm:text-base tracking-normal whitespace-nowrap select-none font-sans leading-none">
            ${settings.btn_text || 'Продлить книгу'}
        </span>
    `;
    document.body.appendChild(renewBtn);

    function checkVisibility() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const docHeight = document.documentElement.scrollHeight;
        const scrollBottom = currentScroll + windowHeight;
        if (scrollBottom >= docHeight - 50) {
            renewBtn.classList.remove('translate-y-0');
            renewBtn.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
        } else {
            renewBtn.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            renewBtn.classList.add('translate-y-0');
        }
    }

    checkVisibility();
    window.addEventListener('scroll', checkVisibility, { passive: true });

    // --- 2. Modal Overlay & Form (Exact design from Screenshot 2) ---
    const modalOverlay = document.createElement('div');
    modalOverlay.id = 'renewal-modal-overlay';
    modalOverlay.className = 'fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm hidden flex items-center justify-center p-4 transition-all duration-300 opacity-0';
    modalOverlay.setAttribute('role', 'dialog');
    modalOverlay.setAttribute('aria-modal', 'true');
    modalOverlay.setAttribute('aria-labelledby', 'renewal-modal-title');

    let branchOptions = '<option value="" disabled selected>Выберите филиал</option>';
    if (Array.isArray(params.branches)) {
        params.branches.forEach(function(branch) {
            branchOptions += `<option value="${branch.id}">${branch.name}</option>`;
        });
    } else if (params.branches && typeof params.branches === 'object') {
        for (const [id, name] of Object.entries(params.branches)) {
            branchOptions += `<option value="${id}">${name}</option>`;
        }
    }

    modalOverlay.innerHTML = `
        <div class="bg-white rounded-[24px] w-full max-w-[420px] shadow-2xl overflow-hidden transform scale-95 transition-all duration-300 relative max-h-[92vh] flex flex-col border border-slate-100" id="renewal-modal-content">
            <div class="relative bg-white px-6 pt-6 pb-2 shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#eaf2fd] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-[#1c77d9]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 6.5C10 4.5 6 4 3 5.5v13.5c3-1.5 7-1 9 1v-13.5z" />
                                <path d="M12 6.5C14 4.5 18 4 21 5.5v13.5c-3-1.5-7-1-9 1v-13.5z" />
                                <line x1="12" y1="6.5" x2="12" y2="20" />
                            </svg>
                        </div>
                        <span class="text-xs font-bold tracking-wider text-[#1c77d9] uppercase font-sans">СЕРВИС ЧИТАТЕЛЯ</span>
                    </div>
                    <button type="button" class="modal-close p-1.5 text-slate-400 hover:text-slate-600 rounded-lg transition-colors focus:outline-none" aria-label="Закрыть">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <h3 id="renewal-modal-title" class="text-[26px] font-bold text-slate-900 mt-4 tracking-tight leading-tight font-sans">
                    Продление книг
                </h3>
                <p class="text-sm text-slate-500 mt-1.5 leading-snug font-sans">
                    Заполните форму — мы продлим книги и ответим на вашу почту.
                </p>
            </div>

            <div class="px-6 pb-6 pt-2 overflow-y-auto custom-scrollbar bg-white">
                <form id="renewal-form" class="space-y-3.5">
                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5 font-sans">Ф. И. О. читателя *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" name="fio" required placeholder="Иванов Иван Иванович"
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-[#1c77d9] focus:ring-2 focus:ring-[#1c77d9]/15 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5 font-sans">Номер читательского билета *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                            </span>
                            <input type="text" name="card_number" required placeholder="Например, 12345"
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-[#1c77d9] focus:ring-2 focus:ring-[#1c77d9]/15 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5 font-sans">Филиал *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M4 18h16M3 7l9-4 9 4M5 7v11M9 7v11M15 7v11M19 7v11"/></svg>
                            </span>
                            <select name="branch" required
                                    class="w-full pl-10 pr-9 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 text-sm focus:outline-none focus:border-[#1c77d9] focus:ring-2 focus:ring-[#1c77d9]/15 transition-all appearance-none cursor-pointer"
                                    style="background-image: none !important; -webkit-appearance: none !important; -moz-appearance: none !important; appearance: none !important;">
                                ${branchOptions}
                            </select>
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5 font-sans">Электронная почта *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </span>
                            <input type="email" name="email" required placeholder="example@mail.ru"
                                   class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-[#1c77d9] focus:ring-2 focus:ring-[#1c77d9]/15 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-800 mb-1.5 font-sans">Список книг *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 pointer-events-none flex items-center justify-center">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/></svg>
                            </span>
                            <textarea name="books" rows="3" required placeholder="Автор и название каждой книги с новой строки"
                                      class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:border-[#1c77d9] focus:ring-2 focus:ring-[#1c77d9]/15 transition-all resize-y min-h-[78px]"></textarea>
                        </div>
                    </div>

                    <div id="renewal-message" class="hidden p-3.5 rounded-lg text-sm font-medium text-center border animate-fade-in-up"></div>

                    <button type="submit" id="renewal-submit-btn" class="w-full py-3 px-4 bg-[#1c77d9] hover:bg-[#1668c2] text-white font-medium text-sm rounded-lg transition-all duration-200 shadow-sm flex items-center justify-center gap-2 mt-4 group">
                        <span>Отправить заявку</span>
                        <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                    <p class="text-xs text-slate-400 text-center mt-3 font-normal leading-normal font-sans">
                        Нажимая кнопку, вы соглашаетесь с <a href="${params.privacy_link || '#'}" target="_blank" class="underline hover:text-slate-600 transition-colors">обработкой персональных данных</a>.
                    </p>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modalOverlay);

    const modalContent = document.getElementById('renewal-modal-content');
    const form = document.getElementById('renewal-form');
    const msgBox = document.getElementById('renewal-message');
    const submitBtn = document.getElementById('renewal-submit-btn');

    function openModal() {
        modalOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        requestAnimationFrame(function() {
            modalOverlay.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        });
    }

    function closeModal() {
        modalOverlay.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(function() {
            modalOverlay.classList.add('hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }, 300);
    }

    renewBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        openModal();
    });

    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) closeModal();
    });

    const closeBtns = modalOverlay.querySelectorAll('.modal-close');
    closeBtns.forEach(function(btn) { btn.addEventListener('click', closeModal); });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modalOverlay.classList.contains('hidden')) closeModal();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-80', 'cursor-wait');
        const originalBtnContent = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span>Отправка заявки...</span>
        `;
        msgBox.classList.add('hidden');
        msgBox.className = 'hidden p-3.5 rounded-lg text-sm font-medium text-center border animate-fade-in-up';

        const formData = new FormData(form);
        formData.append('action', 'city_library_send_book_renewal');
        if (params.nonce) formData.append('nonce', params.nonce);

        const minDelay = new Promise(function(resolve) { setTimeout(resolve, 500); });
        const fetchRequest = fetch(params.ajax_url, { method: 'POST', body: formData });

        Promise.all([fetchRequest, minDelay])
            .then(function(result) { return result[0].json(); })
            .then(function(data) {
                msgBox.classList.remove('hidden');
                if (data.success) {
                    msgBox.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
                    msgBox.innerHTML = '<div class="flex items-center justify-center gap-2"><span>' + data.data.message + '</span></div>';
                    form.reset();
                    setTimeout(closeModal, 2500);
                } else {
                    throw new Error(data.data.message || 'Ошибка сервера.');
                }
            })
            .catch(function(err) {
                console.error(err);
                msgBox.classList.remove('hidden');
                msgBox.classList.add('bg-red-50', 'text-red-700', 'border-red-200');
                msgBox.innerHTML = '<span>' + (err.message || 'Ошибка соединения.') + '</span>';
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-80', 'cursor-wait');
                submitBtn.innerHTML = originalBtnContent;
            });
    });
});
