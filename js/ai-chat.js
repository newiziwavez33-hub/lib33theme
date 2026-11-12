document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('ai-chat-toggle');
    const closeBtn = document.getElementById('close-ai-chat');
    const chatWindow = document.getElementById('ai-chat-window');
    const inputField = document.getElementById('ai-chat-input');
    const sendBtn = document.getElementById('ai-chat-send');
    const messagesContainer = document.getElementById('ai-chat-messages');
    const fullscreenBtn = document.getElementById('fullscreen-ai-chat');
    const attachmentBtn = document.getElementById('ai-chat-attachment');
    const fileInput = document.getElementById('ai-chat-file-input');

    if (!toggleBtn || !chatWindow) return;

    // Ensure file input allows PDFs
    if (fileInput) {
        fileInput.setAttribute('accept', '.txt,.docx,.pdf,.jpg,.jpeg,.png,.webp');
        // Double check after a small delay to handle any race conditions or dynamic DOM swaps
        setTimeout(() => fileInput.setAttribute('accept', '.txt,.docx,.pdf,.jpg,.jpeg,.png,.webp'), 100);
    }

    let attachedFileText = "";
    let attachedFileName = "";
    let attachedFileData = "";
    let attachedPdfImages = []; // To store base64 images of PDF pages
    let persistentInstructionContext = ""; // For brandbooks and instructions


    // Clean and encode Cyrillic URLs for generated images
    function safeImageUrl(href) {
        if (!href) return '';
        let safeHref = href;
        if (/[а-яА-ЯёЁ]/.test(safeHref)) {
            safeHref = encodeURI(safeHref);
        }
        return safeHref;
    }

    // Initialize marked.js custom renderer safely for images and code
    if (typeof marked !== 'undefined') {
        const renderer = new marked.Renderer();
        // Fallback for different marked.js versions (v8+ uses token, older uses arguments)
        renderer.image = function(href_or_token, title, text) {
            let href = typeof href_or_token === 'object' ? href_or_token.href : href_or_token;
            let imgText = typeof href_or_token === 'object' ? href_or_token.text : text;

            const processedHref = safeImageUrl(href);
            const cleanHref = processedHref.replace(/\s/g, '%20');

            return `
                <div class="library-image-wrapper mt-3 mb-3 relative group overflow-hidden rounded-xl border border-slate-200/60 shadow-sm bg-slate-100 pointer-events-auto">
                    <a href="${cleanHref}" class="glightbox block" data-gallery="ai-chat-gallery" data-type="image" data-title="${imgText || 'Сгенерированное изображение'}" data-glightbox="type: image; description: ${imgText || 'Изображение от ИИ'};">
                        <img src="${cleanHref}" alt="${imgText || 'Сгенерированное изображение'}" style="max-width:100%; height:auto; display:block;" class="transition-transform duration-500 group-hover:scale-105 bg-slate-50 min-h-[200px] w-full max-h-[450px] object-contain mx-auto" onerror="this.outerHTML='<div class=\'p-4 text-center text-slate-500 bg-slate-100 rounded-lg border border-dashed border-slate-300 w-full\'>⚠️ Ошибка загрузки.</div>'">
                        <div class="image-controls absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[1px]">
                             <span class="material-symbols-outlined text-white text-4xl drop-shadow-md" aria-hidden="true">zoom_in</span>
                        </div>
                    </a>
                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10 pointer-events-auto">
                        <a href="${cleanHref}" target="_blank" download="Library_Poster.png" class="btn-download flex items-center gap-1.5 px-3 py-1.5 bg-white/90 text-slate-800 font-bold text-xs rounded-lg hover:bg-white hover:shadow-lg transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">download</span> Скачать
                        </a>
                    </div>
                </div>
            `;
        };

        renderer.code = function(code_or_token, infostring, escaped) {
            let code = typeof code_or_token === 'object' ? code_or_token.text : code_or_token;
            let lang = typeof code_or_token === 'object' ? code_or_token.lang : infostring;

            const escapedCode = code.replace(/[&<>"']/g, m => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            })[m]);

            return `
                <div class="relative group code-block-wrapper my-4">
                    <div class="absolute right-2 top-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="copy-code-btn flex items-center gap-1 px-2 py-1 bg-slate-700/80 hover:bg-slate-600 text-white text-[10px] rounded border border-slate-500/50 transition-all active:scale-95" aria-label="Копировать код">
                            <span class="material-symbols-outlined text-[14px]">content_copy</span> Копировать
                        </button>
                    </div>
                    ${lang ? `<div class="absolute left-4 top-0 -translate-y-1/2 px-2 py-0.5 bg-slate-800 text-slate-400 text-[9px] font-mono rounded uppercase tracking-wider border border-slate-700">${lang}</div>` : ''}
                    <pre><code class="language-${lang || 'none'}">${escapedCode}</code></pre>
                </div>
            `;
        };

        marked.use({ renderer });
    }

    // Chat History Management (30 days)
    const STORAGE_KEY = 'city_library_ai_chat_history';
    const VERIFIED_STATE_KEY = 'city_library_ai_verified_messages';
    const EXPIRY_DAYS = 30;
    let chatHistory = [];
    let verifiedMessages = JSON.parse(localStorage.getItem(VERIFIED_STATE_KEY) || '{}');
    let isSelectionMode = false;
    let selectedMessages = new Set();

    // Cache for Google Books covers to avoid redundant API calls
    const bookCoverCache = new Map();

    async function fetchBookCover(query) {
        if (!query) return null;
        if (bookCoverCache.has(query)) return bookCoverCache.get(query);

        try {
            const response = await fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=1`);
            const data = await response.json();
            if (data.items && data.items[0]?.volumeInfo?.imageLinks?.thumbnail) {
                const cover = data.items[0].volumeInfo.imageLinks.thumbnail.replace('http:', 'https:');
                bookCoverCache.set(query, cover);
                return cover;
            }
        } catch (e) {
            console.error('Error fetching book cover:', e);
        }
        return null;
    }

    function extractBooksFromText(text) {
        // Regex to find "Author - Title" patterns or quoted titles
        // Matches patterns like: "А.С. Пушкин - Евгений Онегин" or "Лев Толстой «Война и мир»"
        const books = [];
        const patterns = [
            /(?:^|\n|\d\.\s+)([А-ЯA-Z][а-яa-z\.]+\s+[А-ЯA-Z][а-яa-z\.]*(?:\s+[А-ЯA-Z][а-яa-z\.]*)?)\s+[-—]\s+([«"']?[А-ЯA-Z][^«"'\n\r]+[»"']?)/g,
            /([А-ЯA-Z][а-яa-z\.]+(?:\s+[А-ЯA-Z][а-яa-z\.]*)*)\s+([«"'][^»"']+([»"']))/g
        ];

        patterns.forEach(regex => {
            let match;
            while ((match = regex.exec(text)) !== null) {
                const author = match[1].trim();
                const title = match[2].trim().replace(/[«»"']/g, '');
                books.push(`${author} ${title}`);
            }
        });

        return [...new Set(books)]; // Unique items
    }

    async function injectBookCovers(container, text) {
        const books = extractBooksFromText(text);
        if (books.length === 0) return;

        const shelf = document.createElement('div');
        shelf.className = 'ai-book-shelf mt-4 flex gap-3 overflow-x-auto pb-2 scrollbar-hide py-1';

        let hasCovers = false;
        for (const book of books) {
            const coverUrl = await fetchBookCover(book);
            if (coverUrl) {
                hasCovers = true;
                const bookEl = document.createElement('div');
                bookEl.className = 'flex-shrink-0 group relative cursor-pointer';
                bookEl.innerHTML = `
                    <div class="w-[80px] h-[120px] rounded-lg overflow-hidden shadow-md border border-slate-200 transition-all duration-300 group-hover:scale-105 group-hover:shadow-lg">
                        <a href="${coverUrl}" class="glightbox-book block w-full h-full" data-title="${escapeHtml(book)}">
                            <img src="${coverUrl}" alt="${escapeHtml(book)}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <span class="material-symbols-outlined text-white text-xl">zoom_in</span>
                            </div>
                        </a>
                    </div>
                `;
                shelf.appendChild(bookEl);
            }
        }

        if (hasCovers) {
            container.appendChild(shelf);
            if (typeof GLightbox !== 'undefined') {
                GLightbox({ selector: '.glightbox-book' });
            }
        }
    }

    function loadHistory() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const parsed = JSON.parse(saved);
                const now = new Date().getTime();
                if (now - parsed.timestamp > EXPIRY_DAYS * 24 * 60 * 60 * 1000) {
                    localStorage.removeItem(STORAGE_KEY);
                    return;
                }

                chatHistory = parsed.messages || [];
                if (chatHistory.length > 0) {
                    if (messagesContainer.querySelector('.prose')) {
                        messagesContainer.innerHTML = '';
                    }
                    chatHistory.forEach((msg, idx) => {
                        addMessageToUI(msg.role, msg.content, null, false, idx);
                    });
                }
            }
        } catch (e) {
            console.error('Failed to load chat history', e);
        }
    }

    function saveHistory() {
        try {
            // Keep only the last 50 messages to stay within storage limits
            const historyToSave = chatHistory.slice(-50);
            const data = {
                timestamp: new Date().getTime(),
                messages: historyToSave
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            localStorage.setItem(VERIFIED_STATE_KEY, JSON.stringify(verifiedMessages));
        } catch (e) {
            if (e.name === 'QuotaExceededError') {
                // If we hit the limit, try saving only the last 10 messages (emergency cleanup)
                try {
                    const criticalHistory = chatHistory.slice(-10);
                    localStorage.setItem(STORAGE_KEY, JSON.stringify({
                        timestamp: new Date().getTime(),
                        messages: criticalHistory
                    }));
                } catch (innerE) {
                    localStorage.removeItem(STORAGE_KEY);
                }
            }
            console.error('Failed to save chat history', e);
        }
    }

    loadHistory();

    // Toggle Window
    function toggleChat() {
        if (chatWindow.classList.contains('hidden')) {
            chatWindow.classList.remove('hidden');
            chatWindow.classList.add('flex');
            // Remove notification dot if exists
            const dot = toggleBtn.querySelector('.animate-pulse');
            if(dot) dot.remove();
            // Focus input
            setTimeout(() => inputField.focus(), 100);
        } else {
            chatWindow.classList.add('hidden');
            chatWindow.classList.remove('flex');
            // Reset fullscreen state if closed
            if (chatWindow.classList.contains('fixed')) {
                toggleFullscreen();
            }
        }
    }

    function toggleFullscreen() {
        chatWindow.classList.toggle('fixed');
        chatWindow.classList.toggle('inset-0');
        chatWindow.classList.toggle('z-[99999]');
        chatWindow.classList.toggle('!h-[100dvh]');
        chatWindow.classList.toggle('!w-[100vw]');
        chatWindow.classList.toggle('!max-w-none');
        chatWindow.classList.toggle('!rounded-none');
        chatWindow.classList.toggle('!m-0');

        // Disable default dimensional classes from PHP
        chatWindow.classList.toggle('w-[96vw]');
        chatWindow.classList.toggle('sm:w-[90vw]');
        chatWindow.classList.toggle('md:w-[680px]');
        chatWindow.classList.toggle('h-[70vh]');
        chatWindow.classList.toggle('max-h-[750px]');
        chatWindow.classList.toggle('sm:h-[650px]');
        chatWindow.classList.toggle('mb-6');
        chatWindow.classList.toggle('rounded-[2.5rem]');

        if (fullscreenBtn) {
            const icon = fullscreenBtn.querySelector('span');
            if (chatWindow.classList.contains('fixed')) {
                icon.textContent = 'fullscreen_exit';
            } else {
                icon.textContent = 'fullscreen';
            }
        }
    }

    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);
    if (fullscreenBtn) fullscreenBtn.addEventListener('click', toggleFullscreen);

    // Quick Actions
    const quickActionBtns = document.querySelectorAll('.ai-quick-action-btn');
    if (quickActionBtns) {
        quickActionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const command = this.getAttribute('data-command');
                const userSubject = inputField.value.trim();

                if (command) {
                    // Combine command and current input text if present
                    if (userSubject) {
                        inputField.value = `${command} ${userSubject}`;
                    } else {
                        inputField.value = command;
                    }
                    sendMessage();
                }
            });
        });
    }

    // Send Message
    function sendMessage() {
        const message = inputField.value.trim();
        if (!message && !attachedFileData) return;

        // 1. Add User Message to UI
        if (message) {
            addMessageToUI('user', message);
        } else if (attachedFileData) {
            addMessageToUI('user', `[Отправлено изображение: ${attachedFileName}]`);
        }
        inputField.value = '';

        // 2. Show typing or drawing indicator
        const typingId = 'typing-' + Date.now();

        let isDrawCommand = false;
        const msgLower = message.toLowerCase();
        if (msgLower.startsWith('/aimg') || msgLower.match(/^(нарисуй|сгенерируй|создай картинку|нарисуй мне|сделай картинку)\s+/)) {
            isDrawCommand = true;
        }

        if (isDrawCommand) {
            addMessageToUI('bot', `<div class="flex items-center gap-3 text-slate-500 font-medium">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0 shadow-sm border border-slate-300 overflow-hidden">
                    <img src="${cl_ai_ajax.avatar_url}" alt="AI" class="w-full h-full object-cover opacity-50">
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined animate-spin text-primary" aria-hidden="true" style="font-size: 18px;">sync</span>
                    <span class="text-xs">Библиотекарь создаёт изображение...</span>
                </div>
            </div>`, typingId, false);
        } else if (attachedPdfImages.length > 0) {
            addMessageToUI('bot', `<div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0 shadow-sm border border-slate-300 overflow-hidden relative">
                    <img src="${cl_ai_ajax.avatar_url}" alt="AI" class="w-full h-full object-cover opacity-50">
                </div>
                <div class="bg-white border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex flex-col gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full typing-dot"></span>
                        <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full typing-dot" style="animation-delay: 0.2s"></span>
                        <span class="w-1.5 h-1.5 bg-indigo-800 rounded-full typing-dot" style="animation-delay: 0.4s"></span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest animate-pulse">Анализирую PDF визуально...</span>
                </div>
            </div>`, typingId, false);
        } else {
            addMessageToUI('bot', `<div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0 shadow-sm border border-slate-300 overflow-hidden relative">
                    <img src="${cl_ai_ajax.avatar_url}" alt="AI" class="w-full h-full object-cover opacity-50">
                </div>
                <div class="bg-white border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-sm shadow-sm flex flex-col gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-primary/40 rounded-full typing-dot"></span>
                        <span class="w-1.5 h-1.5 bg-primary/60 rounded-full typing-dot" style="animation-delay: 0.2s"></span>
                        <span class="w-1.5 h-1.5 bg-primary/80 rounded-full typing-dot" style="animation-delay: 0.4s"></span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest animate-pulse">Думаю...</span>
                </div>
            </div>`, typingId, false);
        }

        const contextHistory = chatHistory.slice(-40).map(m => ({ // Support 20 requests (40 messages)
            role: m.role === 'bot' ? 'assistant' : 'user',
            content: m.content
        }));

        // 3. Send AJAX Request
        const requestData = {
            action: 'city_library_ai_chat',
            nonce: cl_ai_ajax.nonce,
            message: message,
            history: JSON.stringify(contextHistory),
            user_name: cl_ai_ajax.user_name,
            is_logged_in: cl_ai_ajax.is_logged_in
        };

        // If an image is attached
        if (attachedFileData) {
            requestData.image_data = attachedFileData;
            requestData.image_name = attachedFileName;
            // If it's just an image upload without text, set a default prompt
            if (!message) {
                requestData.message = "Проанализируй это изображение и опиши его подробно.";
            }
        }

        // If PDF images are available
        if (attachedPdfImages.length > 0) {
            requestData.pdf_images = JSON.stringify(attachedPdfImages);
        }

        // If a file is attached, inject its content into the prompt and persistent context
        if (attachedFileText && !attachedFileData) {
            persistentInstructionContext += `\n\n[ДАННЫЕ ИЗ ФАЙЛА "${attachedFileName}"]: \n${attachedFileText}\n`;
            requestData.message = `[НОВЫЙ ФАЙЛ "${attachedFileName}"]: \n\n ${attachedFileText} \n\n --- \n\n ВОПРОС ПОЛЬЗОВАТЕЛЯ: ${message}`;
        }

        // Always inject persistent context if exists
        if (persistentInstructionContext && !requestData.message.includes(persistentInstructionContext)) {
             requestData.persistent_context = persistentInstructionContext;
        }

        // Reset attachment after sending
        if (attachedFileName) {
            attachedFileText = "";
            attachedFileName = "";
            attachedFileData = "";
            attachedPdfImages = [];
            attachmentBtn.innerHTML = '<span class="material-symbols-outlined text-[20px]">attach_file</span>';
            attachmentBtn.title = "Прикрепить файл (до 50МБ)";
        }

        jQuery.ajax({
            url: cl_ai_ajax.url,
            type: 'POST',
            data: requestData,
            success: function(response) {
                // Remove typing indicator
                const typingEl = document.getElementById(typingId);
                if (typingEl) typingEl.remove();

                if (response.success) {
                    addMessageToUI('bot', response.data.reply);
                } else {
                    addMessageToUI('bot', '<span class="text-red-500">' + response.data.reply + '</span>');
                }
            },
            error: function() {
                const typingEl = document.getElementById(typingId);
                if (typingEl) typingEl.remove();
                addMessageToUI('bot', '<span class="text-red-500">Произошла ошибка сети.</span>');
            }
        });
    }

    // Scroll to bottom button
    const scrollToBottomBtn = document.createElement('button');
    scrollToBottomBtn.id = 'ai-scroll-bottom';
    scrollToBottomBtn.className = 'fixed bottom-28 right-8 w-10 h-10 bg-white shadow-lg border border-slate-200 rounded-full flex items-center justify-center text-primary opacity-0 pointer-events-none transition-all duration-300 hover:bg-slate-50 z-20 active:scale-90';
    scrollToBottomBtn.innerHTML = '<span class="material-symbols-outlined">expand_more</span>';
    scrollToBottomBtn.setAttribute('aria-label', 'Прокрутить вниз');
    chatWindow.querySelector('.flex-grow.flex.flex-col').appendChild(scrollToBottomBtn);

    messagesContainer.addEventListener('scroll', () => {
        const diff = messagesContainer.scrollHeight - messagesContainer.scrollTop - messagesContainer.clientHeight;
        if (diff > 200) {
            scrollToBottomBtn.classList.remove('opacity-0', 'pointer-events-none');
            scrollToBottomBtn.classList.add('opacity-100');
        } else {
            scrollToBottomBtn.classList.add('opacity-0', 'pointer-events-none');
            scrollToBottomBtn.classList.remove('opacity-100');
        }
    });

    scrollToBottomBtn.addEventListener('click', () => {
        messagesContainer.scrollTo({ top: messagesContainer.scrollHeight, behavior: 'smooth' });
    });

    // Add message helper
    async function addMessageToUI(sender, text, id = null, save = true, forceIndex = null) {
        let msgIndex = forceIndex;
        if (save && !text.includes('animate-bounce') && !text.includes('Создаю изображение')) {
            chatHistory.push({ role: sender, content: text });
            saveHistory();
            msgIndex = chatHistory.length - 1;
        }

        const wrapper = document.createElement('div');
        wrapper.className = sender === 'user' ? 'flex justify-end' : 'flex gap-2';
        if (id) wrapper.id = id;

        let content = '';

        // Implement /clear command
        if (sender === 'user' && text.trim().toLowerCase() === '/clear') {
            chatHistory = [];
            persistentInstructionContext = ""; // Reset instructions
            localStorage.removeItem(STORAGE_KEY);
            if (messagesContainer.querySelector('.prose')) {
                messagesContainer.innerHTML = '';
            }
            if (!save) return;
            // Add a confirmation message that doesn't save to history
            addMessageToUI('bot', 'История чата успешно очищена. Инструкции из файлов также сброшены.', null, false);
            return;
        }

        let parsedText = text;
        if (sender === 'bot' && !text.includes('animate-bounce') && !text.includes('Создаю изображение') && typeof marked !== 'undefined') {
            parsedText = marked.parse(text);
        }

        if (sender === 'user') {
            content = `
                <div class="bg-primary text-white p-3 rounded-2xl rounded-tr-sm shadow-sm max-w-[85%] whitespace-pre-wrap break-words overflow-hidden">
                    ${escapeHtml(text)}
                </div>
            `;
        } else {
            let actionButtons = '';
            let verifiedBadge = '';
            let inoagentBadge = '';

            // Logic for badges
            if (sender === 'bot' && !text.includes('animate-bounce') && !text.includes('Создаю изображение')) {
                // 1. Verified Badge
                const isVerified = (verifiedMessages[msgIndex] || text.length > 100) && !text.includes('Данные уточняются');
                if (isVerified) {
                    verifiedMessages[msgIndex] = true;
                    verifiedBadge = `
                        <div class="absolute -top-2 -right-2 bg-green-500 text-white px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter shadow-sm border border-white flex items-center gap-0.5 z-10 animate-in fade-in zoom-in duration-500">
                            <span class="material-symbols-outlined text-[11px]">verified</span> Данные проверены
                        </div>
                    `;
                }

                // 2. Foreign Agent Badge (Requirement from Memory)
                const textLower = text.toLowerCase();
                if (textLower.includes('иностранный агент') || textLower.includes('иноагент') || text.includes('⚠️')) {
                    inoagentBadge = `
                        <div class="absolute -top-2 left-4 bg-orange-600 text-white px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter shadow-sm border border-white flex items-center gap-0.5 z-10 animate-in fade-in slide-in-from-top-1 duration-500">
                            <span class="material-symbols-outlined text-[11px]">warning</span> Упоминание иноагента
                        </div>
                    `;
                }
            }

            // Only add download buttons to substantial bot replies (not loaders or short confirmations)
            if ((save || forceIndex !== null) && text.length > 50 && !text.includes('animate-bounce') && !text.includes('Создаю изображение')) {
                // Generate a base64 encoded text string for the data URI
                const encodedText = encodeURIComponent(text);
                actionButtons = `
                    <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100/50 justify-end flex-wrap">
                        <button class="text-xs text-slate-400 hover:text-primary transition-all flex items-center gap-1 font-medium ai-copy-btn hover:scale-105" data-text="${escapeHtml(text)}" aria-label="Копировать текст">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">content_copy</span> Копировать
                        </button>
                        <a href="data:text/plain;charset=utf-8,${encodedText}" download="Ответ_Виртуального_Библиотекаря.txt" class="text-xs text-slate-400 hover:text-primary transition-all flex items-center gap-1 font-medium hover:scale-105" aria-label="Скачать TXT">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">download</span> Скачать (TXT)
                        </a>
                        <button class="text-xs text-slate-400 hover:text-primary transition-all flex items-center gap-1 font-medium ai-pdf-btn hover:scale-105" data-text="${escapeHtml(text)}" aria-label="Сохранить как PDF">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">picture_as_pdf</span> PDF
                        </button>
                        <button class="text-xs text-slate-400 hover:text-primary transition-all flex items-center gap-1 font-medium ai-docx-btn hover:scale-105" data-text="${escapeHtml(text)}" aria-label="Скачать как DOCX">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">description</span> DOCX
                        </button>
                        <button class="text-xs text-slate-400 hover:text-primary transition-all flex items-center gap-1 font-medium ai-email-btn hover:scale-105" data-text="${escapeHtml(text)}" aria-label="Отправить на почту">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">mail</span> На почту
                        </button>
                        ${text.toLowerCase().includes('источники') && text.length > 500 ? `
                        <button class="text-xs text-slate-500 hover:text-primary transition-all flex items-center gap-1 font-bold ai-save-draft-btn bg-slate-100 hover:bg-white px-2.5 py-1.5 rounded-lg border border-slate-200 ml-auto shadow-sm hover:shadow-md active:scale-95" data-text="${escapeHtml(text)}" aria-label="Создать черновик в WordPress">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">note_add</span> Сохранить черновик в WP
                        </button>
                        ` : ''}
                    </div>
                `;
            }

            content = `
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0 mt-1 shadow-sm border border-slate-300 overflow-hidden relative">
                    <img src="${cl_ai_ajax.avatar_url}" alt="AI Avatar" class="w-full h-full object-cover">
                </div>
                <div class="bg-white border border-slate-200 p-4 rounded-[1.25rem] rounded-tl-sm shadow-sm hover:shadow-lg transition-all text-slate-800 max-w-[85%] text-[14px] leading-relaxed break-words overflow-visible relative prose prose-sm prose-slate !max-w-full">
                    ${verifiedBadge}
                    ${inoagentBadge}
                    ${parsedText}
                    ${actionButtons}
                </div>
            `;
        }

        wrapper.innerHTML = content;

        if (sender === 'bot' && !text.includes('animate-bounce') && !text.includes('Создаю изображение')) {
            wrapper.classList.add('ai-selectable-message', 'cursor-pointer', 'transition-all', 'duration-300', 'rounded-2xl', 'p-1', 'hover:bg-indigo-50/50');
            wrapper.setAttribute('data-index', msgIndex !== null ? msgIndex : (chatHistory.length - 1));

            wrapper.addEventListener('click', (e) => {
                if (!isSelectionMode) return;

                // Prevent trigger if clicking on action buttons
                if (e.target.closest('button') || e.target.closest('a')) return;

                toggleMessageSelection(wrapper);
            });
        }

        messagesContainer.appendChild(wrapper);

        // Inject Book Covers if it's a bot message
        if (sender === 'bot' && !text.includes('animate-bounce') && !text.includes('Создаю изображение')) {
            const proseContainer = wrapper.querySelector('.prose');
            if (proseContainer) {
                injectBookCovers(proseContainer, text);
            }
        }

        // Scroll to bottom with optimized behavior
        messagesContainer.scrollTo({
            top: messagesContainer.scrollHeight,
            behavior: save ? 'smooth' : 'auto'
        });

    // Ensure file input allows PDFs
    if (fileInput && !fileInput.getAttribute('accept').includes('.pdf')) {
        fileInput.setAttribute('accept', '.txt,.docx,.pdf,.jpg,.jpeg,.png,.webp');
    }

        // Re-initialize GLightbox if a new message was added with images
        if (typeof GLightbox !== 'undefined') {
            const lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true,
                zoomable: true
            });

            // For dynamic AI images, we need to explicitly refresh or handle the click
            wrapper.querySelectorAll('.glightbox').forEach(el => {
                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    // If it's a dynamic image, sometimes it helps to open directly via the instance
                    // to ensure the newly added DOM element is recognized
                    const instance = GLightbox({
                        elements: [
                            {
                                'href': el.getAttribute('href'),
                                'type': 'image',
                                'title': el.getAttribute('data-title') || ''
                            }
                        ]
                    });
                    instance.open();
                });
            });
        }

    // Scroll to bottom after adding message
    messagesContainer.scrollTo({
        top: messagesContainer.scrollHeight,
        behavior: 'auto'
    });
    }

    // Escape user input to prevent XSS (Hoisted for reuse)
    function escapeHtml(unsafe) {
        return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // --- Event Delegation for Chat Actions ---
    messagesContainer.addEventListener('click', function(e) {
        // 1. Copy Button
        const copyBtn = e.target.closest('.ai-copy-btn');
        if (copyBtn) {
            const rawText = copyBtn.getAttribute('data-text');
            navigator.clipboard.writeText(rawText).then(() => {
                const originalHTML = copyBtn.innerHTML;
                copyBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]" aria-hidden="true">check</span> Скопировано';
                copyBtn.classList.add('text-green-600');
                setTimeout(() => {
                    copyBtn.innerHTML = originalHTML;
                    copyBtn.classList.remove('text-green-600');
                }, 2000);
            });
            return;
        }

        // 2. PDF Button
        const pdfBtn = e.target.closest('.ai-pdf-btn');
        if (pdfBtn) {
            const rawText = pdfBtn.getAttribute('data-text');
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            let htmlContent = rawText;
            if (typeof marked !== 'undefined') {
                htmlContent = marked.parse(rawText);
            }
            printWindow.document.write(`
                <html><head><title>Печать / Сохранить как PDF</title>
                <style>
                    body { font-family: sans-serif; padding: 40px; line-height: 1.6; color: #333; }
                    img { max-width: 100%; height: auto; }
                </style>
                </head><body>
                ${htmlContent}
                <script>window.onload = function() { window.print(); window.close(); }</script>
                </body></html>
            `);
            printWindow.document.close();
            return;
        }

        // 3. DOCX Button
        const docxBtn = e.target.closest('.ai-docx-btn');
        if (docxBtn) {
            const rawText = docxBtn.getAttribute('data-text');
            const originalHTML = docxBtn.innerHTML;
            docxBtn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin" aria-hidden="true">sync</span> ...';
            docxBtn.disabled = true;

            jQuery.ajax({
                url: cl_ai_ajax.url,
                type: 'POST',
                data: {
                    action: 'city_library_ai_docx',
                    nonce: cl_ai_ajax.nonce,
                    content: rawText
                },
                success: (response) => {
                    if (response.success) {
                        const byteCharacters = atob(response.data.html);
                        const byteNumbers = new Array(byteCharacters.length);
                        for (let i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/msword;charset=utf-8' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'Ответ_Библиотекаря.doc';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        alert("Ошибка генерации DOCX: " + response.data);
                    }
                },
                error: () => {
                    alert("Произошла ошибка при генерации документа.");
                },
                complete: () => {
                    docxBtn.innerHTML = originalHTML;
                    docxBtn.disabled = false;
                }
            });
            return;
        }

        // 4. Email Button
        const emailBtn = e.target.closest('.ai-email-btn');
        if (emailBtn) {
            const rawText = emailBtn.getAttribute('data-text');
            const userEmail = prompt("Введите ваш email адрес для отправки ответа:");
            if (userEmail && userEmail.trim() !== '') {
                const originalHTML = emailBtn.innerHTML;
                emailBtn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin" aria-hidden="true">sync</span> ...';
                emailBtn.disabled = true;

                jQuery.ajax({
                    url: cl_ai_ajax.url,
                    type: 'POST',
                    data: {
                        action: 'city_library_ai_email',
                        nonce: cl_ai_ajax.nonce,
                        email: userEmail,
                        content: rawText
                    },
                    success: (response) => {
                        if (response.success) {
                            emailBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]" aria-hidden="true">check</span> Отправлено';
                            emailBtn.classList.add('text-green-600');
                            setTimeout(() => {
                                emailBtn.innerHTML = originalHTML;
                                emailBtn.classList.remove('text-green-600');
                            }, 3000);
                        } else {
                            alert("Ошибка: " + response.data);
                            emailBtn.innerHTML = originalHTML;
                        }
                    },
                    error: () => {
                        alert("Произошла ошибка при отправке.");
                        emailBtn.innerHTML = originalHTML;
                    },
                    complete: () => {
                        emailBtn.disabled = false;
                    }
                });
            }
            return;
        }

        // 5. Save WP Draft Button
        const saveDraftBtn = e.target.closest('.ai-save-draft-btn');
        if (saveDraftBtn) {
            const rawText = saveDraftBtn.getAttribute('data-text');
            const postTitle = 'Черновик: ' + (rawText.split('\n')[0].replace(/#/g, '').trim() || 'Статья');
            const originalHTML = saveDraftBtn.innerHTML;
            saveDraftBtn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin" aria-hidden="true">sync</span> Сохраняем...';
            saveDraftBtn.disabled = true;

            jQuery.ajax({
                url: cl_ai_ajax.url,
                type: 'POST',
                data: {
                    action: 'city_library_ai_draft',
                    nonce: cl_ai_ajax.nonce,
                    title: postTitle,
                    content: rawText
                },
                success: (response) => {
                    if (response.success) {
                        saveDraftBtn.innerHTML = `<a href="${response.data.edit_link}" target="_blank" class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]" aria-hidden="true">open_in_new</span> Редактировать</a>`;
                        saveDraftBtn.classList.add('text-green-600', 'hover:text-green-700');
                        saveDraftBtn.classList.remove('text-slate-500', 'hover:text-primary', 'bg-slate-100');
                    } else {
                        alert("Ошибка: " + response.data);
                        saveDraftBtn.innerHTML = originalHTML;
                    }
                },
                error: () => {
                    alert("Произошла ошибка при создании черновика.");
                    saveDraftBtn.innerHTML = originalHTML;
                },
                complete: () => {
                    saveDraftBtn.disabled = false;
                }
            });
            return;
        }

        // 6. Interactive Author Draft Button (from AI response)
        const authorBtn = e.target.closest('.ai-draft-btn');
        if (authorBtn) {
            const authorName = authorBtn.getAttribute('data-author');
            if (authorName) {
                inputField.value = '/author ' + authorName;
                sendMessage();
            }
            return;
        }

        // 7. Copy Code Button
        const copyCodeBtn = e.target.closest('.copy-code-btn');
        if (copyCodeBtn) {
            const wrapper = copyCodeBtn.closest('.code-block-wrapper');
            const codeEl = wrapper.querySelector('code');
            if (codeEl) {
                const codeText = codeEl.innerText;
                navigator.clipboard.writeText(codeText).then(() => {
                    const originalHTML = copyCodeBtn.innerHTML;
                    copyCodeBtn.innerHTML = '<span class="material-symbols-outlined text-[14px]">check</span> Готово';
                    copyCodeBtn.classList.add('bg-green-600/80');
                    setTimeout(() => {
                        copyCodeBtn.innerHTML = originalHTML;
                        copyCodeBtn.classList.remove('bg-green-600/80');
                    }, 2000);
                });
            }
            return;
        }
    });

    // --- Selection Mode Logic ---
    const collectDraftBtn = document.getElementById('ai-collect-draft-btn');
    const collectDraftBtnMobile = document.getElementById('ai-collect-draft-btn-mobile');
    const selectionToolbar = document.getElementById('ai-selection-toolbar');
    const selectedCountEl = document.getElementById('ai-selected-count');
    const cancelSelectionBtn = document.getElementById('ai-cancel-selection');
    const compilePdfBtn = document.getElementById('ai-compile-pdf');
    const compileDocxBtn = document.getElementById('ai-compile-docx');

    function toggleSelectionMode(active) {
        isSelectionMode = active;
        if (active) {
            selectionToolbar.classList.remove('hidden');
            chatWindow.classList.add('selection-active');
            // Highlight existing messages
            document.querySelectorAll('.ai-selectable-message').forEach(el => {
                el.classList.add('ring-2', 'ring-indigo-100');
            });
        } else {
            selectionToolbar.classList.add('hidden');
            chatWindow.classList.remove('selection-active');
            selectedMessages.clear();
            updateSelectionUI();
            document.querySelectorAll('.ai-selectable-message').forEach(el => {
                el.classList.remove('ring-2', 'ring-indigo-100', 'ring-indigo-500', 'bg-indigo-50');
            });
        }
    }

    function toggleMessageSelection(element) {
        const index = element.getAttribute('data-index');
        if (selectedMessages.has(index)) {
            selectedMessages.delete(index);
            element.classList.remove('ring-indigo-500', 'bg-indigo-50');
            element.classList.add('ring-indigo-100');
        } else {
            selectedMessages.add(index);
            element.classList.add('ring-indigo-500', 'bg-indigo-50');
            element.classList.remove('ring-indigo-100');
        }
        updateSelectionUI();
    }

    function updateSelectionUI() {
        if (!selectedCountEl) return;
        selectedCountEl.textContent = selectedMessages.size;
        if (selectedMessages.size > 0) {
            compilePdfBtn.classList.remove('opacity-50', 'pointer-events-none');
            compileDocxBtn.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            compilePdfBtn.classList.add('opacity-50', 'pointer-events-none');
            compileDocxBtn.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    if (collectDraftBtn) {
        collectDraftBtn.addEventListener('click', () => toggleSelectionMode(true));
    }
    if (collectDraftBtnMobile) {
        collectDraftBtnMobile.addEventListener('click', () => toggleSelectionMode(true));
    }

    if (cancelSelectionBtn) {
        cancelSelectionBtn.addEventListener('click', () => toggleSelectionMode(false));
    }

    function compileSelected(format) {
        if (selectedMessages.size === 0) return;

        const contents = [];
        // Sort selected indices to maintain conversation order
        const sortedIndices = Array.from(selectedMessages).sort((a, b) => a - b);

        sortedIndices.forEach(idx => {
            const msg = chatHistory[idx];
            if (msg && msg.role === 'bot') {
                // Better send HTML from UI for better visual fidelity
                const el = document.querySelector(`.ai-selectable-message[data-index="${idx}"] .prose`);
                if (el) {
                    // Clone to remove UI noise
                    const clone = el.cloneNode(true);

                    // Remove action buttons (Copy, TXT, PDF etc)
                    clone.querySelectorAll('.flex.gap-2.mt-3, .ai-copy-btn, .btn-download').forEach(btn => btn.remove());

                    // Cleanup image wrappers for better PDF alignment
                    clone.querySelectorAll('.library-image-wrapper').forEach(wrapper => {
                        const img = wrapper.querySelector('img');
                        if (img) {
                             // Force absolute URL and clean styling for the PDF compiler
                             img.style.width = '100%';
                             img.style.maxWidth = '600px';
                             img.style.height = 'auto';
                             img.style.borderRadius = '12px';
                             wrapper.innerHTML = '';
                             wrapper.appendChild(img);
                        }
                    });

                    contents.push(clone.innerHTML);
                }
            }
        });

        const btn = format === 'pdf' ? compilePdfBtn : compileDocxBtn;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined text-[14px] animate-spin">sync</span> ...';
        btn.disabled = true;

        jQuery.ajax({
            url: cl_ai_ajax.url,
            type: 'POST',
            data: {
                action: 'city_library_ai_compile_draft',
                nonce: cl_ai_ajax.nonce,
                content: contents,
                format: format
            },
            success: (response) => {
                if (response.success) {
                    if (format === 'docx') {
                        const byteCharacters = atob(response.data.base64);
                        const byteNumbers = new Array(byteCharacters.length);
                        for (let i = 0; i < byteCharacters.length; i++) {
                            byteNumbers[i] = byteCharacters.charCodeAt(i);
                        }
                        const byteArray = new Uint8Array(byteNumbers);
                        const blob = new Blob([byteArray], { type: 'application/msword;charset=utf-8' });
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = response.data.filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    } else {
                        // PDF Print
                        const printWindow = window.open('', '_blank', 'width=1000,height=800');
                        printWindow.document.write(`
                            <html><head><title>Черновик ИИ</title>
                            <style>
                                body { font-family: sans-serif; background: #f1f5f9; padding: 40px; }
                                img { max-width: 100%; height: auto; border-radius: 8px; }
                                .ai-compiled-report { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                            </style>
                            </head><body>
                            <div class="ai-compiled-report">${response.data.html}</div>
                            <script>window.onload = function() { window.print(); }</script>
                            </body></html>
                        `);
                        printWindow.document.close();
                    }
                } else {
                    alert("Ошибка: " + response.data);
                }
            },
            error: () => alert("Ошибка соединения с сервером."),
            complete: () => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
                toggleSelectionMode(false);
            }
        });
    }

    if (compilePdfBtn) compilePdfBtn.addEventListener('click', () => compileSelected('pdf'));
    if (compileDocxBtn) compileDocxBtn.addEventListener('click', () => compileSelected('docx'));

    // Auto-resize textarea
    inputField.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Listeners
    sendBtn.addEventListener('click', () => {
        sendMessage();
        inputField.style.height = 'auto';
    });

    inputField.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.ctrlKey && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
            this.style.height = 'auto';
        } else if (e.key === 'Enter' && (e.ctrlKey || e.shiftKey)) {
            // Standard behavior for textarea (new line) is preserved,
            // but we can explicitly handle it if needed.
        }
    });

    // File Attachment Logic
    if (attachmentBtn && fileInput) {
        // Set worker src for pdf.js
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
        }

        attachmentBtn.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                const isPdf = file.type === 'application/pdf';
                const maxSize = 50 * 1024 * 1024; // 50MB

                if (file.size > maxSize) {
                    alert('Файл слишком большой. Максимум 50МБ.');
                    this.value = '';
                    return;
                }

                const isImage = file.type.startsWith('image/');
                const originalIcon = attachmentBtn.innerHTML;
                attachmentBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">sync</span>';
                attachmentBtn.disabled = true;

                // Handle PDF client-side if possible
                if (isPdf && typeof pdfjsLib !== 'undefined') {
                    try {
                        const reader = new FileReader();
                        reader.onload = async function() {
                            try {
                                const typedarray = new Uint8Array(this.result);
                                const pdf = await pdfjsLib.getDocument(typedarray).promise;
                                let fullText = "";
                                const images = [];

                                // Render first 5 pages to images for vision analysis
                                const pagesToRender = Math.min(pdf.numPages, 5);

                                for (let i = 1; i <= Math.min(pdf.numPages, 50); i++) {
                                    const page = await pdf.getPage(i);

                                    // Extract Text
                                    const textContent = await page.getTextContent();
                                    fullText += textContent.items.map(item => item.str).join(' ') + "\n";

                                    // Visual extraction for first few pages
                                    if (i <= pagesToRender) {
                                        const viewport = page.getViewport({ scale: 1.5 });
                                        const canvas = document.createElement('canvas');
                                        const context = canvas.getContext('2d');
                                        canvas.height = viewport.height;
                                        canvas.width = viewport.width;

                                        await page.render({ canvasContext: context, viewport: viewport }).promise;
                                        images.push(canvas.toDataURL('image/jpeg', 0.8));
                                    }
                                }

                                attachedFileText = fullText;
                                attachedFileName = file.name;
                                attachedPdfImages = images;

                                attachmentBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] text-green-500">task</span>';
                                attachmentBtn.title = `PDF прочитан: ${attachedFileName}`;
                                addMessageToUI('bot', `<span class="text-slate-500 text-xs italic"><span class="material-symbols-outlined text-[14px] align-middle mr-1">picture_as_pdf</span> PDF «${attachedFileName}» (${pdf.numPages} стр.) прочитан. Я проанализировала текст и визуальный стиль первых страниц. Что мне сделать?</span>`, null, false);
                                attachmentBtn.disabled = false;
                            } catch (pdfErr) {
                                console.error("PDF internal parsing error:", pdfErr);
                            }
                        };
                        reader.readAsArrayBuffer(file);
                        this.value = '';
                        return;
                    } catch (err) {
                        console.error("PDF extraction failed:", err);
                    }
                }

                const formData = new FormData();
                formData.append('action', 'city_library_ai_upload');
                formData.append('nonce', cl_ai_ajax.nonce);
                formData.append('file', file);

                jQuery.ajax({
                    url: cl_ai_ajax.url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.success) {
                            attachedFileText = response.data.text || "";
                            attachedFileName = response.data.filename;
                            attachedFileData = response.data.data_url || "";

                            attachmentBtn.innerHTML = '<span class="material-symbols-outlined text-[20px] text-green-500">task</span>';
                            attachmentBtn.title = `Файл прикреплен: ${attachedFileName}`;

                            if (isImage) {
                                addMessageToUI('bot', `<span class="text-slate-500 text-xs italic"><span class="material-symbols-outlined text-[14px] align-middle mr-1">image</span> Изображение «${attachedFileName}» успешно загружено. Я проанализировала его и готова обсудить детали или сгенерировать что-то похожее.</span>`, null, false);
                            } else if (isPdf) {
                                addMessageToUI('bot', `<span class="text-slate-500 text-xs italic"><span class="material-symbols-outlined text-[14px] align-middle mr-1">picture_as_pdf</span> PDF «${attachedFileName}» успешно загружен и обработан. Я готова использовать его данные.</span>`, null, false);
                            } else {
                                addMessageToUI('bot', `<span class="text-slate-500 text-xs italic"><span class="material-symbols-outlined text-[14px] align-middle mr-1">attach_file</span> Файл «${attachedFileName}» успешно прочитан. Теперь я могу проанализировать его содержимое. Задайте свой вопрос по файлу.</span>`, null, false);
                            }
                        } else {
                            alert("Ошибка загрузки: " + response.data);
                            attachmentBtn.innerHTML = originalIcon;
                        }
                    },
                    error: () => {
                        alert("Произошла ошибка при загрузке файла.");
                        attachmentBtn.innerHTML = originalIcon;
                    },
                    complete: () => {
                        attachmentBtn.disabled = false;
                        this.value = '';
                    }
                });
            }
        });
    }
});