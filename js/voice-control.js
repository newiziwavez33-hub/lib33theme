/**
 * Voice Control for City Library
 * Activated by double clicking the accessibility toggle button.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Check if voice control is enabled via localized script variables
    if (typeof cl_voice_control === 'undefined' || !cl_voice_control.enabled) {
        return;
    }

    const mobileVoiceBtn = document.getElementById('mobile-voice-assistant-btn');
    if (!mobileVoiceBtn) {
        return;
    }

    // Match the CSS lg:landscape:hidden logic (shows on mobile < 1024 OR any portrait screen like 1080x1920 Kiosk)
    const isMobileOrKiosk = !window.matchMedia('(min-width: 1024px) and (orientation: landscape)').matches;

    // Abort completely if not on Mobile/Kiosk as per user request
    if (!isMobileOrKiosk) {
        mobileVoiceBtn.style.display = 'none';
        return;
    }

    // --- 24-HOUR TEST LOGIC ---
    let hasAccess = cl_voice_control.is_logged_in || !cl_voice_control.test_mode;

    // 1. Check if user just activated the test via hash
    if (window.location.hash === '#voicetest' && isMobileOrKiosk) {
        const date = new Date();
        const expirationTimestamp = date.getTime() + (24 * 60 * 60 * 1000); // 24 hours from now

        // We set the browser cookie expiration to 30 days so it isn't automatically deleted,
        // allowing us to detect it later and show the feedback modal.
        const cookieExpireDate = new Date();
        cookieExpireDate.setTime(cookieExpireDate.getTime() + (30 * 24 * 60 * 60 * 1000));

        document.cookie = "cl_voice_test_active=" + expirationTimestamp + "; expires=" + cookieExpireDate.toUTCString() + "; path=/";
        hasAccess = true;

        // Remove hash cleanly
        history.replaceState(null, null, ' ');

        // Show Welcome Modal
        const welcomeModal = document.getElementById('voice-test-welcome-modal');
        if (welcomeModal) {
            welcomeModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                welcomeModal.classList.remove('opacity-0');
                welcomeModal.querySelector('.test-modal-content').classList.remove('scale-90');
                welcomeModal.querySelector('.test-modal-content').classList.add('scale-100');
            });

            const startBtn = document.getElementById('voice-test-start-btn');
            if (startBtn) {
                startBtn.addEventListener('click', () => {
                    welcomeModal.classList.add('opacity-0');
                    welcomeModal.querySelector('.test-modal-content').classList.remove('scale-100');
                    welcomeModal.querySelector('.test-modal-content').classList.add('scale-90');
                    setTimeout(() => welcomeModal.classList.add('hidden'), 500);
                });
            }
        }
    }

    // 2. Check existing cookie
    const getCookie = (name) => {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    };

    const cookieVal = getCookie('cl_voice_test_active');
    if (cookieVal) {
        const expiresAt = parseInt(cookieVal, 10);
        if (new Date().getTime() < expiresAt) {
            hasAccess = true;
        } else {
            // Cookie expired! Show feedback modal if we haven't already.
            // We use a secondary flag in localStorage so it only shows once after expiration.
            if (!localStorage.getItem('cl_voice_test_feedback_done') && isMobileOrKiosk) {
                const feedbackModal = document.getElementById('voice-test-feedback-modal');
                if (feedbackModal) {
                    feedbackModal.classList.remove('hidden');
                    requestAnimationFrame(() => {
                        feedbackModal.classList.remove('opacity-0');
                        feedbackModal.querySelector('.test-modal-content').classList.remove('scale-90');
                        feedbackModal.querySelector('.test-modal-content').classList.add('scale-100');
                    });

                    // Star Rating Logic
                    const stars = document.querySelectorAll('#voice-feedback-stars span');
                    const ratingInput = document.getElementById('voice-feedback-rating-input');
                    let currentRating = 0;

                    stars.forEach(star => {
                        star.addEventListener('click', function() {
                            currentRating = parseInt(this.getAttribute('data-value'));
                            ratingInput.value = currentRating;
                            stars.forEach(s => {
                                if (parseInt(s.getAttribute('data-value')) <= currentRating) {
                                    s.classList.add('text-yellow-400');
                                    s.classList.remove('text-slate-300');
                                    s.classList.add('material-symbols-rounded');
                                    s.classList.remove('material-symbols-outlined');
                                } else {
                                    s.classList.remove('text-yellow-400');
                                    s.classList.add('text-slate-300');
                                    s.classList.remove('material-symbols-rounded');
                                    s.classList.add('material-symbols-outlined');
                                }
                            });
                        });
                    });

                    // Form Submit Logic
                    const form = document.getElementById('voice-test-feedback-form');
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        if (currentRating === 0) {
                            alert('Пожалуйста, поставьте оценку от 1 до 5 звезд.');
                            return;
                        }

                        const btn = document.getElementById('voice-feedback-submit-btn');
                        btn.disabled = true;
                        btn.textContent = 'Отправка...';

                        const formData = new FormData(form);
                        formData.append('action', 'city_library_voice_feedback');
                        formData.append('nonce', cl_voice_control.ai_nonce);

                        fetch(cl_voice_control.ajax_url, { method: 'POST', body: formData })
                            .then(res => res.json())
                            .then(data => {
                                localStorage.setItem('cl_voice_test_feedback_done', 'true');
                                // Force reload to completely remove assistant
                                window.location.reload();
                            })
                            .catch(err => {
                                btn.disabled = false;
                                btn.textContent = 'Отправить отчет';
                                alert('Произошла ошибка при отправке.');
                            });
                    });
                }
            }
        }
    }

    // Handle dynamic button swapping in the mobile bottom nav
    const mobileAfishaBtn = document.getElementById('mobile-afisha-btn');
    if (hasAccess) {
        if (mobileVoiceBtn) {
            mobileVoiceBtn.classList.remove('hidden');
            mobileVoiceBtn.style.display = ''; // Reset any inline styles
        }
        if (mobileAfishaBtn) {
            mobileAfishaBtn.classList.add('hidden');
        }
    } else {
        if (mobileVoiceBtn) {
            mobileVoiceBtn.classList.add('hidden');
        }
        if (mobileAfishaBtn) {
            mobileAfishaBtn.classList.remove('hidden');
        }
        return; // Stop initialization since they don't have access and the button is hidden
    }

    // --- END TEST LOGIC ---

    let recognition = null;
    let isListening = false;
    const synth = window.speechSynthesis;

    // TTS State Management (Default OFF on mobile to prevent unwanted reading)
    let isTTSActive = localStorage.getItem('cl_voice_tts_active') === 'true';

    // Allow user to toggle via button if we ever add one, but mostly via voice commands
    function toggleTTS(state) {
        isTTSActive = state;
        localStorage.setItem('cl_voice_tts_active', isTTSActive);
        if (!isTTSActive && synth.speaking) {
            synth.cancel();
        }
    }

    // Web Speech API Initialization
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.continuous = false; // Stop after one command
        recognition.lang = 'ru-RU'; // Russian
        recognition.interimResults = false;
    } else {
        console.warn('Speech Recognition API not supported in this browser.');
        // Optionally, hide the button if API is not supported
        mobileVoiceBtn.style.display = 'none';
        return;
    }

    // UI Indicator (Stylish Mobile Overlay)
    const indicator = document.createElement('div');
    indicator.className = 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black/80 backdrop-blur-xl text-white px-10 py-8 rounded-3xl shadow-2xl z-[9999] font-bold text-xl flex flex-col items-center gap-4 transition-all duration-300 opacity-0 scale-90 pointer-events-none text-center border border-white/10';
    indicator.innerHTML = `
        <div class="relative w-16 h-16 flex items-center justify-center bg-primary rounded-full mb-2">
            <span class="material-symbols-outlined text-4xl animate-pulse" aria-hidden="true">mic</span>
            <div class="absolute inset-0 rounded-full border-[3px] border-primary animate-ping opacity-75"></div>
        </div>
        <span class="tracking-wide text-glow">Слушаю Вас!</span>
    `;
    document.body.appendChild(indicator);

    function showIndicator() {
        indicator.style.opacity = '1';
        indicator.style.transform = 'translate(-50%, -50%) scale(1)';
        // Change the button state to show it's active
        mobileVoiceBtn.classList.add('bg-primary', 'text-white');
        mobileVoiceBtn.classList.remove('bg-white', 'text-primary');
    }

    function hideIndicator() {
        indicator.style.opacity = '0';
        indicator.style.transform = 'translate(-50%, -50%) scale(0.9)';
        // Revert button state
        mobileVoiceBtn.classList.remove('bg-primary', 'text-white');
        mobileVoiceBtn.classList.add('bg-white', 'text-primary');
    }

    function speak(text, showInModal = false) {
        // Handle TTS Enable/Disable Commands internally before attempting to speak
        const lowerText = text.toLowerCase().trim();
        if (lowerText.includes('включи озвуч') || lowerText.includes('включи голос') || lowerText.includes('озвучивай ответы')) {
            toggleTTS(true);
            text = 'Голосовое сопровождение включено.';
            // Proceed to speak this confirmation
        } else if (lowerText.includes('выключи озвуч') || lowerText.includes('выключи голос') || lowerText.includes('перестань говорить')) {
            text = 'Голосовое сопровождение выключено.';
            // We speak this confirmation once, then turn it off
            setTimeout(() => toggleTTS(false), 2000);
        }

        // As per request: "Вместо этого (Слушаю) для мобильной версии надо сделать всплывающее уведомление"
        // We will skip voice synthesis for the "Слушаю" phrase, but still use it for other responses.
        if (text === 'Слушаю' && isMobileOrKiosk) {
            return;
        }

        function renderAIModal(modalText) {
            const aiModal = document.getElementById('voice-ai-answer-modal');
            const aiText = document.getElementById('voice-ai-answer-text');
            if (aiModal && aiText) {
                aiText.innerHTML = modalText;
                aiModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
                requestAnimationFrame(() => {
                    aiModal.classList.remove('opacity-0');
                    const content = aiModal.querySelector('.voice-modal-content');
                    if (content) {
                        content.classList.remove('scale-90');
                        content.classList.add('scale-100');
                    }
                });
            }
        }

        // Abort speech if user has disabled TTS (unless it's the confirmation message above)
        if (!isTTSActive && text !== 'Голосовое сопровождение включено.' && text !== 'Голосовое сопровождение выключено.') {
            // We still want to show modals if requested, so we just skip the synthesis part below
            if (showInModal) {
                renderAIModal(text);
            }
            return;
        }

        // Show AI Answer Modal if requested
        if (showInModal) {
            renderAIModal(text);
        }

        if (synth.speaking) synth.cancel();

        // Clean text for speech synthesis
        let spokenText = text;

        // 1. Remove Markdown image tags entirely: `![alt](url)`
        spokenText = spokenText.replace(/!\[.*?\]\(.*?\)/g, '');

        // 2. Remove standard Markdown link syntax but KEEP the link text: `[text](url)` -> `text`
        spokenText = spokenText.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');

        // 2.5. Remove raw URLs (http/https) to prevent dictating links
        spokenText = spokenText.replace(/https?:\/\/\S+/gi, '');

        // 3. Strip any HTML tags (in case there's raw HTML inside the markdown)
        spokenText = spokenText.replace(/<[^>]*>?/gm, '');

        // 4. Strip common markdown formatting characters (asterisks, underscores, hashes)
        spokenText = spokenText.replace(/[*_#`~]/g, '');

        // 5. Clean up multiple spaces and newlines
        spokenText = spokenText.replace(/\s+/g, ' ').trim();

        // If after stripping it's empty, don't speak
        if (!spokenText) return;

        // Truncate long responses to 3 sentences for better UX
        const sentences = spokenText.match(/[^.!?]+[.!?]+/g) || [spokenText];
        const utterance = new SpeechSynthesisUtterance(sentences.slice(0, 3).join(" "));
        utterance.lang = 'ru-RU';
        utterance.rate = 1.0;

        // Attempt to find a high-quality (Google/Microsoft) Russian voice
        let voices = synth.getVoices();

        // Sometimes voices aren't loaded immediately
        if (voices.length === 0) {
            synth.onvoiceschanged = () => {
                voices = synth.getVoices();
                setBestRussianVoice(utterance, voices);
                synth.speak(utterance);
            };
            return;
        }

        setBestRussianVoice(utterance, voices);
        synth.speak(utterance);
    }

    function setBestRussianVoice(utterance, voices) {
        // Filter for Russian voices
        const ruVoices = voices.filter(voice => voice.lang.includes('ru') || voice.lang.includes('RU'));
        if (ruVoices.length > 0) {
            // Prefer "Yandex", "Google", "Microsoft", or Apple's "Milena" (premium natural voices)
            const premiumVoice = ruVoices.find(voice =>
                voice.name.includes('Yandex') ||
                voice.name.includes('Google') ||
                voice.name.includes('Microsoft') ||
                voice.name.includes('Milena') ||
                voice.name.includes('Yuri')
            );

            // If premium found, use it. Otherwise default to first available Russian voice.
            utterance.voice = premiumVoice ? premiumVoice : ruVoices[0];

            // Apply customizer settings
            utterance.pitch = parseFloat(cl_voice_control.voice_pitch) || 1.1; // Slightly higher pitch for softer tone
            utterance.rate = parseFloat(cl_voice_control.voice_rate) || 0.95;  // Slightly slower for more natural human pacing
        }
    }

    // Yandex Maps Logic (Mobile specific)
    function openYandexMapModal(title, query, showAll = false) {
        if (!isMobileOrKiosk) return;

        const mapModal = document.getElementById('voice-map-modal');
        const mapTitle = document.getElementById('voice-map-title');
        const mapIframe = document.getElementById('voice-map-iframe');
        const mapLoader = document.getElementById('voice-map-loader');
        const customMapContainer = document.getElementById('voice-custom-map-container');

        if (mapModal && mapTitle && mapIframe) {
            mapTitle.textContent = title;
            if (mapLoader) mapLoader.classList.remove('opacity-0');

            mapModal.classList.remove('hidden');
            mapModal.classList.add('flex', 'flex-col'); // Add flex and flex-col to force column layout
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            document.documentElement.style.overflow = 'hidden';

            requestAnimationFrame(() => {
                mapModal.classList.remove('opacity-0');
            });

            if (showAll && customMapContainer) {
                // Handle "All Branches" view via AJAX
                mapIframe.classList.add('hidden');
                mapIframe.src = ''; // Stop any previous process
                customMapContainer.classList.remove('hidden');

                if (customMapContainer.innerHTML.trim() === '') {
                    // Fetch the map HTML container
                    fetch(cl_voice_control.ajax_url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'city_library_get_map_shortcode',
                            nonce: cl_voice_control.ai_nonce
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.html) {
                            customMapContainer.innerHTML = data.data.html;
                            // Ensure the map container script is executed
                            const scripts = customMapContainer.querySelectorAll('script');
                            scripts.forEach(script => {
                                const newScript = document.createElement('script');
                                Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                newScript.appendChild(document.createTextNode(script.innerHTML));
                                script.parentNode.replaceChild(newScript, script);
                            });
                        } else {
                            customMapContainer.innerHTML = '<div class="p-4 text-center text-slate-500">Не удалось загрузить карту.</div>';
                        }
                    })
                    .catch(err => {
                        console.error('Map loading error:', err);
                        customMapContainer.innerHTML = '<div class="p-4 text-center text-slate-500">Ошибка соединения.</div>';
                    })
                    .finally(() => {
                        if (mapLoader) {
                            mapLoader.classList.add('opacity-0');
                        }
                    });
                } else {
                    if (mapLoader) mapLoader.classList.add('opacity-0');
                }

            } else {
                // Single Branch View via Yandex Map Widget iframe
                if (customMapContainer) customMapContainer.classList.add('hidden');
                mapIframe.classList.remove('hidden');

                const mapUrl = `https://yandex.ru/map-widget/v1/?text=${encodeURIComponent(query)}&z=16`;
                mapIframe.src = mapUrl;

                mapIframe.onload = () => {
                    if (mapLoader) {
                        mapLoader.classList.add('opacity-0');
                    }
                };
            }

            // Handle close
            const closeBtn = document.getElementById('voice-map-close');
            if (closeBtn) {
                const newBtn = closeBtn.cloneNode(true);
                closeBtn.parentNode.replaceChild(newBtn, closeBtn);

                newBtn.addEventListener('click', () => {
                    mapModal.classList.add('opacity-0');
                    setTimeout(() => {
                        mapModal.classList.remove('flex', 'flex-col');
                        mapModal.classList.add('hidden');
                        document.body.style.overflow = '';
                        document.documentElement.style.overflow = '';
                        mapIframe.src = ''; // Stop map processes
                    }, 300);
                });
            }
        }
    }

    // Command Processing
    function processCommand(command) {
        const cmd = command.toLowerCase().trim();
        console.log('Voice Command Received:', cmd);

        // --- 1. LOCAL BRANCH DIRECTORY (Instant Mobile Response) ---
        // Normalize text numbers to digits for parsing
        const textNumbers = { 'один': 1, 'первая': 1, 'первый': 1, 'два': 2, 'вторая': 2, 'второй': 2, 'три': 3, 'третья': 3, 'третий': 3, 'четыре': 4, 'четвертая': 4, 'пять': 5, 'пятая': 5, 'шесть': 6, 'шестая': 6, 'семь': 7, 'седьмая': 7, 'восемь': 8, 'восьмая': 8, 'девять': 9, 'девятая': 9, 'десять': 10, 'десятая': 10, 'одиннадцать': 11, 'одиннадцатая': 11, 'двенадцать': 12, 'двенадцатая': 12, 'тринадцать': 13, 'тринадцатая': 13, 'четырнадцать': 14, 'четырнадцатая': 14, 'пятнадцать': 15, 'пятнадцатая': 15, 'шестнадцать': 16, 'шестнадцатая': 16 };

        let normalizedCmd = cmd;
        for (const [word, digit] of Object.entries(textNumbers)) {
            normalizedCmd = normalizedCmd.replace(new RegExp(`\\b${word}\\b`, 'gi'), digit);
        }

        // Use dynamically loaded addresses from WordPress Customizer, with fallback keys
        const addr = cl_voice_control.branch_addresses || {};

        const branchDB = {
            "цгб": { name: "Центральная городская библиотека", address: addr.cgb || "г. Владимир, Суздальский пр-кт, д. 2" },
            "цдб": { name: "Центральная детская библиотека", address: addr.cdb || "г. Владимир, ул. Белоконской, д. 10-а" },
            "1": { name: "Библиотека-филиал № 1", address: addr["1"] || "г. Владимир, пр-кт Строителей, д. 23" },
            "2": { name: "Библиотека-филиал № 2", address: addr["2"] || "г. Владимир, пр-кт Ленина, д. 12" },
            "3": { name: "Библиотека-филиал № 3", address: addr["3"] || "г. Владимир, ул. Добросельская, д. 2-в" },
            "4": { name: "Библиотека-филиал № 4", address: addr["4"] || "г. Владимир, ул. Комиссарова, д. 69" },
            "5": { name: "Библиотека-филиал № 5", address: addr["5"] || "г. Владимир, пр-кт Суздальский, д. 2" },
            "6": { name: "Библиотека-филиал № 6", address: addr["6"] || "г. Владимир, ул. Мира, д. 37" },
            "7": { name: "Библиотека-филиал № 7", address: addr["7"] || "г. Владимир, ул. Добросельская, д. 189-б" },
            "8": { name: "Библиотека-филиал № 8", address: addr["8"] || "г. Владимир, ул. Диктора Левитана, д. 36" },
            "9": { name: "Библиотека-филиал № 9", address: addr["9"] || "г. Владимир, ул. Горького, д. 85" },
            "10": { name: "Библиотека-филиал № 10", address: addr["10"] || "г. Владимир, ул. Егорова, д. 10" },
            "11": { name: "Библиотека-филиал № 11", address: addr["11"] || "г. Владимир, мкр. Юрьевец, ул. Институтский городок, д. 4" },
            "13": { name: "Библиотека-филиал № 13", address: addr["13"] || "г. Владимир, мкр. Юрьевец, ул. Ноябрьская, д. 2-а" },
            "14": { name: "Библиотека-филиал № 14", address: addr["14"] || "г. Владимир, мкр. Энергетик, ул. Энергетиков, д. 12" },
            "15": { name: "Библиотека-филиал № 15", address: addr["15"] || "г. Владимир, мкр. Энергетик, ул. Совхозная, д. 11" },
            "16": { name: "Библиотека-филиал № 16", address: addr["16"] || "г. Владимир, мкр. Коммунар, ул. Песочная, д. 2-а" }
        };

        let targetBranch = null;
        let branchName = '';

        // Match numeric branch queries
        const branchRegex = /(?:библиотека|филиал)(?:\s+(?:номер|№|номера))?\s+(\d+)/i;
        const branchMatch = normalizedCmd.match(branchRegex);

        if (branchMatch && branchMatch[1]) {
            const num = branchMatch[1];
            if (branchDB[num]) {
                targetBranch = branchDB[num];
                branchName = targetBranch.name;
            }
        }
        // Match specific named libraries based on the site menu structure
        else if (normalizedCmd.includes('экологическая') || normalizedCmd.includes('левитана')) {
            targetBranch = branchDB["8"]; branchName = targetBranch.name;
        } else if (normalizedCmd.includes('музей')) {
            targetBranch = branchDB["7"]; branchName = targetBranch.name;
        } else if (normalizedCmd.includes('досуговый центр') || normalizedCmd.includes('егорова')) {
            targetBranch = branchDB["10"]; branchName = targetBranch.name;
        } else if (normalizedCmd.includes('семейного чтения') || normalizedCmd.includes('совхозная')) {
            targetBranch = branchDB["15"]; branchName = targetBranch.name;
        } else if (normalizedCmd.includes('историко') || normalizedCmd.includes('возрождения') || normalizedCmd.includes('коммунар')) {
            targetBranch = branchDB["16"]; branchName = targetBranch.name;
        } else if (normalizedCmd.includes('центральная детская') || normalizedCmd.includes('белоконской')) {
            targetBranch = branchDB["цдб"]; branchName = targetBranch.name;
        } else if (normalizedCmd.includes('центральная') || normalizedCmd.includes('цгб') || normalizedCmd.includes('главная библиотека') || normalizedCmd.includes('суздальский')) {
            targetBranch = branchDB["цгб"]; branchName = targetBranch.name;
        }

        if (targetBranch) {
            // Check if user specifically asks for the map
            const wantsMap = cmd.includes('карта') || cmd.includes('карте') || cmd.includes('маршрут') || cmd.includes('где');

            const spokenAddress = targetBranch.address.replace('г. Владимир, ', '');
            if (wantsMap || isMobileOrKiosk) {
                 speak(`${branchName}. Адрес: ${spokenAddress}. Открываю карту.`);
            } else {
                 speak(`${branchName}. Адрес: ${spokenAddress}.`);
            }

            // Wait slightly for voice to start, then open modal
            setTimeout(() => {
                openYandexMapModal(branchName, targetBranch.address);
            }, 1000);
            return; // Intercept before AI
        }

        // --- 2. Check Custom Commands from Customizer ---
        if (cl_voice_control.custom_commands && cl_voice_control.custom_commands.length > 0) {
            for (let i = 0; i < cl_voice_control.custom_commands.length; i++) {
                const customCmd = cl_voice_control.custom_commands[i];
                // Check if any of the non-empty phrases match the spoken command
                const matched = customCmd.phrases.some(phrase => phrase.length > 2 && cmd.includes(phrase));
                if (matched) {
                    speak('Открываю: ' + customCmd.phrases[0]); // Use the first phrase as the friendly name
                    setTimeout(() => {
                        window.location.href = customCmd.url; // Use redirect instead of window.open to prevent mobile popup blockers
                    }, 1500);
                    return; // Stop processing further
                }
            }
        }

        // Predefined Commands Map
        if (cmd.includes('открой последние новости') || cmd.includes('открой последнюю новость')) {
            speak('Открываю последнюю новость');
            // Redirect to the first news item (assuming it's on the homepage or news archive)
            const firstNewsLink = document.querySelector('.news-slider a, .content-area a');
            if (firstNewsLink && firstNewsLink.href) {
                // Ensure URL contains a hash to scroll past hero
                let targetUrl = firstNewsLink.href;
                if (!targetUrl.includes('#')) {
                    targetUrl += '#primary';
                }
                window.location.href = targetUrl;
            } else {
                window.location.href = cl_voice_control.home_url + '/?news_archive=true#primary';
            }
            return;
        }

        if (cmd.includes('афиша новостей') || cmd.includes('афиша') || cmd.includes('мероприятия')) {
            speak('Перехожу к афише мероприятий');
            if (document.getElementById('afisha')) {
                document.getElementById('afisha').scrollIntoView({ behavior: 'smooth' });
            } else {
                window.location.href = cl_voice_control.home_url + '/#afisha';
            }
            return;
        }

        if (cmd.includes('на главную') || cmd.includes('главная страница')) {
            speak('Открываю главную страницу');
            // Append #primary to scroll past hero on homepage
            window.location.href = cl_voice_control.home_url + '/#primary';
            return;
        }

        if (cmd.includes('что ты умеешь') || cmd.includes('помощь') || cmd.includes('команд')) {
            speak('Открываю список доступных команд');
            const commandsModal = document.getElementById('voice-commands-modal');
            if (commandsModal) {
                commandsModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
                requestAnimationFrame(() => {
                    commandsModal.classList.remove('opacity-0');
                    const content = commandsModal.querySelector('.voice-modal-content');
                    if (content) {
                        content.classList.remove('scale-90');
                        content.classList.add('scale-100');
                    }
                });
            }
            return;
        }

        // We removed the hardcoded 'контакты' or 'где вы находитесь' catch-all.
        // We only catch specific global commands here. Branch address queries go to AI.

        if (cmd.includes('открой контакты') || cmd.includes('раздел контакты')) {
            speak('Открываю раздел контактов');

            const allLinks = Array.from(document.querySelectorAll('a'));
            const contactLink = allLinks.find(el => el.textContent.toLowerCase().includes('контакт'));

            if (contactLink && contactLink.href) {
                 window.location.href = contactLink.href;
            } else if (document.getElementById('branches')) {
                 document.getElementById('branches').scrollIntoView({ behavior: 'smooth' });
            } else {
                 window.location.href = cl_voice_control.home_url + '/#branches';
            }
            return;
        }

        if (cmd.includes('продление книг') || cmd.includes('продлить книгу')) {
            speak('Открываю форму продления книг');
            const renewBtn = document.getElementById('book-renewal-btn');
            if (renewBtn) {
                // Ensure it's not hidden due to visibility logic
                renewBtn.classList.remove('opacity-0', 'pointer-events-none', 'translate-x-full');
                renewBtn.click();
            } else {
                speak('Функция продления книг сейчас недоступна.');
            }
            return;
        }

        if (cmd.includes('электронный каталог') || cmd.includes('каталог книг') || cmd.includes('каталог')) {
            speak('Открываю электронный каталог');
            // Wait a bit to let the voice synthesize before opening new tab
            setTimeout(() => {
                window.location.href = 'http://library.vladimir.ru/rguest_vlad_cgb.htm';
            }, 1500);
            return;
        }

        if (cmd.includes('версия для слабовидящих') || cmd.includes('обычная версия')) {
             speak('Переключаю режим отображения');
             const a11yToggleBtn = document.getElementById('accessibility-button');
             if (a11yToggleBtn) a11yToggleBtn.click();
             return;
        }

        if (cmd.includes('как записаться') || cmd.includes('запись в библиотеку') || cmd.includes('записаться в библиотеку')) {
             const replyText = 'Записаться в библиотеку можно в любом из филиалов имея при себе паспорт.';
             speak(replyText, true);
             const aiText = document.getElementById('voice-ai-answer-text');
             if (aiText) aiText.innerHTML = replyText;
             return;
        }

        // Fallback: Send to Virtual Librarian AI
        speak('Секундочку, уточняю информацию...');
        askVirtualLibrarian(command);
    }

    function askVirtualLibrarian(query) {
        if (!cl_voice_control.ai_nonce) {
            speak('Извините, виртуальный помощник сейчас недоступен.');
            return;
        }

        // Show thinking indicator for AI
        const a11yToggleBtn = document.getElementById('accessibility-button');
        const iconBtn = mobileVoiceBtn.querySelector('span');
        if (iconBtn) {
            iconBtn.textContent = 'more_horiz';
            iconBtn.classList.add('animate-spin');
        }

        jQuery.ajax({
            url: cl_voice_control.ajax_url,
            type: 'POST',
            data: {
                action: 'city_library_ai_chat',
                nonce: cl_voice_control.ai_nonce,
                message: query,
                is_voice: 'true'
            },
            success: function(response) {
                if (response.success && response.data && response.data.reply) {
                    // Prepare text. We want plain text for speech, but we can allow basic formatting in the modal
                    const rawHtml = response.data.reply;

                    // Note: Instead of doing stripping here, we pass rawHtml to `speak()` directly
                    // `speak` will handle its own regex stripping of markdown tags for speech synthesis.
                    const plainText = rawHtml.replace(/<[^>]*>?/gm, '').trim();

                    // Update modal content with original Markdown/HTML
                    const aiText = document.getElementById('voice-ai-answer-text');
                    if (aiText) {
                        // Add Tailwind Typography prose classes if not present to correctly style parsed markdown
                        if (!aiText.classList.contains('prose')) {
                            aiText.classList.add('prose', 'prose-sm', 'prose-slate');
                        }
                        // Use marked.js for reliable markdown parsing
                        aiText.innerHTML = (typeof marked !== 'undefined') ? marked.parse(rawHtml) : rawHtml;
                    }

                    // Show modal
                    const aiModal = document.getElementById('voice-ai-answer-modal');
                    const aiContent = aiModal?.querySelector('.voice-modal-content');
                    if (aiModal && aiContent) {
                        aiModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                        document.documentElement.style.overflow = 'hidden';
                        void aiModal.offsetWidth;
                        aiModal.classList.remove('opacity-0');
                        aiContent.classList.remove('scale-90');
                        aiContent.classList.add('scale-100');
                    }

                    // Play Audio from API if available, else fallback to browser synthesis
                    if (response.data.audio_base64) {
                        const audioWav = "data:audio/wav;base64," + response.data.audio_base64;
                        const audio = new Audio(audioWav);
                        audio.play().catch(e => {
                             console.warn("Failed to play API audio, falling back to speech synthesis", e);
                             speak(rawHtml, false); // pass raw HTML/Markdown, speak() will handle stripping
                        });
                    } else {
                        speak(rawHtml, false); // pass raw HTML/Markdown
                    }

                    // Open Yandex Map full-screen modal if AI returns an exact Vladimir address and we are on mobile
                    // ONLY if the user explicitly asked about an address, branch, location, etc.
                    const userQuery = query.toLowerCase();
                    const askedForLocation = userQuery.includes('где') ||
                                             userQuery.includes('адрес') ||
                                             userQuery.includes('филиал') ||
                                             userQuery.includes('цгб') ||
                                             userQuery.includes('цдб') ||
                                             userQuery.includes('находится') ||
                                             userQuery.includes('добраться') ||
                                             userQuery.includes('доехать');

                    if (askedForLocation) {
                        const wantsAllMap = userQuery.includes('все') || userQuery.includes('карта') || userQuery.includes('покажи');
                        // Search in plain text for map extraction. Since data comes dynamically from pages,
                        // "г. Владимир" might be missing. We look for streets, prospekts, etc.
                        const addressMatch = plainText.match(/(?:г\.\s*Владимир,\s*)?(ул\.|пр-т|мкр\.|пр\.|Школьный пр\.)\s*([^.,]+),\s*(?:д\.\s*)?(\d+[а-яА-Я\-]*)/i);

                        if (wantsAllMap && !addressMatch) {
                            if (isMobileOrKiosk) {
                                setTimeout(() => openYandexMapModal('Карта библиотек г. Владимира', 'библиотеки Владимир', true), 1500);
                            } else if (document.getElementById('footer-yandex-map')) {
                                document.getElementById('footer-yandex-map').scrollIntoView({ behavior: 'smooth' });
                            }
                        } else if (addressMatch) {
                            const extractedAddress = addressMatch[0];

                            if (isMobileOrKiosk) {
                                setTimeout(() => {
                                    // Extract the branch name if available in the text, otherwise use generic title
                                    let mapTitle = "Адрес библиотеки";
                                    const titleMatch = plainText.match(/(Библиотека-филиал № \d+|Центральная [а-яА-Я\s]+библиотека)/i);
                                    if (titleMatch) mapTitle = titleMatch[0];

                                    openYandexMapModal(mapTitle, extractedAddress);
                                }, 1500);
                            } else if (document.getElementById('footer-yandex-map')) {
                                 // Fallback for Desktop (Kiosk): Scroll to footer map
                                 document.getElementById('footer-yandex-map').scrollIntoView({ behavior: 'smooth' });
                            }
                        }
                    }
                } else {
                    // Do not voice errors, just log them
                    const errorMsg = (response.data && response.data.reply) ? response.data.reply : 'Извините, я не смогла найти ответ на этот вопрос.';
                    console.error('AI Error: ' + errorMsg);
                }
            },
            error: function() {
                console.error('Произошла ошибка связи с сервером. Пожалуйста, проверьте подключение к интернету.');
            },
            complete: function() {
                if (iconBtn) {
                    iconBtn.textContent = 'mic';
                    iconBtn.classList.remove('animate-spin');
                }
            }
        });
    }

    // Recognition Events
    recognition.onstart = function() {
        isListening = true;
        showIndicator();
        if (synth.speaking) synth.cancel(); // Stop talking when starting to listen
        speak('Слушаю');
    };

    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        processCommand(transcript);
    };

    recognition.onerror = function(event) {
        console.error('Speech recognition error', event.error);
        isListening = false;
        hideIndicator();
    };

    recognition.onend = function() {
        isListening = false;
        hideIndicator();
    };

    // Global variable to hold API audio so we can stop it if the user closes the modal
    let currentApiAudio = null;

    // Generic Voice Modal Close Logic (handles both Commands and AI Answer modals)
    const voiceModals = [
        document.getElementById('voice-commands-modal'),
        document.getElementById('voice-ai-answer-modal')
    ];

    voiceModals.forEach(modal => {
        if (!modal) return;

        const closeBtn = modal.querySelector('.voice-modal-close') || modal.querySelector('.voice-ai-answer-close');

        function closeVoiceModal() {
            modal.classList.add('opacity-0');
            const content = modal.querySelector('.voice-modal-content');
            if (content) {
                content.classList.remove('scale-100');
                content.classList.add('scale-90');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                // Only reset overflow if NO voice modals are open
                const anyOpen = voiceModals.some(m => m && !m.classList.contains('hidden'));
                if (!anyOpen) {
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                }

                // If it's the AI answer modal, stop speech synthesis/audio on close
                if (modal.id === 'voice-ai-answer-modal') {
                    if (synth && synth.speaking) {
                        synth.cancel();
                    }
                    if (currentApiAudio) {
                        currentApiAudio.pause();
                        currentApiAudio.currentTime = 0;
                        currentApiAudio = null;
                    }
                }
            }, 500);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeVoiceModal);
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeVoiceModal();
            }
        });
    });

    // Single click to activate on mobile
    mobileVoiceBtn.addEventListener('click', (e) => {
        e.preventDefault();

        if (isListening) {
            recognition.stop();
        } else {
            try {
                recognition.start();
            } catch(err) {
                console.error('Failed to start recognition:', err);
            }
        }
    });

    // Also add a keyboard shortcut (Ctrl+Shift+V or Alt+V) as a fallback for accessibility
    document.addEventListener('keydown', (e) => {
        if (e.altKey && e.code === 'KeyV') {
            if (isListening) {
                recognition.stop();
            } else {
                try {
                    recognition.start();
                } catch(err) {
                    console.error('Failed to start recognition:', err);
                }
            }
        }
    });
});