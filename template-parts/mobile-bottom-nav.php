<?php
/**
 * Mobile Bottom Navigation Bar
 */
$bar_style = get_theme_mod('mobile_menu_style', 'default');
$btn_style = get_theme_mod('mobile_menu_btn_style', 'classic');
$icon_set = get_theme_mod('mobile_menu_icon_set', 'outlined');

// Icon Logic
$icon_base_class = 'material-symbols-' . $icon_set;
$icon_font_class = 'material-symbols-outlined';
if ($icon_set === 'rounded') $icon_font_class = 'material-symbols-rounded';
if ($icon_set === 'sharp') $icon_font_class = 'material-symbols-sharp';

// Base Classes
$nav_classes = 'block lg:landscape:hidden fixed w-full z-50 safe-area-bottom transition-all duration-300';
$grid_classes = 'grid grid-cols-4 items-center h-20 px-2'; // Added padding
$item_base_classes = 'group flex flex-col items-center justify-center h-full transition-all relative overflow-hidden rounded-xl'; // Added rounded
$icon_classes = 'material-symbols-outlined text-2xl mb-1 transition-transform z-10';
$text_classes = 'text-[10px] font-bold tracking-wide z-10';

/* --- 1. Bar Styles (Container) --- */
// The user requested "One background color". We rely on the Customizer 'mobile_menu_bg_color' variable.
// We apply specific shapes/shadows based on style, but override BG.

switch ($bar_style) {
    case 'default':
        // Enhanced default matching the dark pill-shaped glassmorphism image
        $nav_classes .= ' bottom-6 left-4 right-4 w-[calc(100%-2rem)] lg:portrait:max-w-xl lg:portrait:left-1/2 lg:portrait:right-auto lg:portrait:-translate-x-1/2 rounded-[2rem] bg-slate-900/85 backdrop-blur-3xl shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-white/10';
        $grid_classes = 'grid grid-cols-4 items-center h-[72px] px-2 rounded-[2rem]';
        $item_base_classes = 'group flex flex-col items-center justify-center h-full transition-all relative overflow-hidden rounded-xl';
        break;
    case 'ios-blur':
        // User asked for "one color", but blur implies translucency. We'll keep the class but the inline style might override if opacity is 1.
        $nav_classes .= ' bottom-0 left-0 backdrop-blur-xl border-t border-white/20';
        break;
    case 'material-pill':
        $nav_classes .= ' bottom-4 left-4 right-4 w-[calc(100%-2rem)] lg:portrait:max-w-xl lg:portrait:left-1/2 lg:portrait:right-auto lg:portrait:-translate-x-1/2 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-white/50 backdrop-blur-md bg-white/90';
        $grid_classes = 'grid grid-cols-4 items-center h-16 rounded-full overflow-hidden px-1';
        $item_base_classes = 'group flex flex-col items-center justify-center h-full transition-all relative overflow-hidden rounded-full mx-1 hover:bg-slate-100/50';
        break;
    case 'neon-glow':
        $nav_classes .= ' bottom-0 left-0 border-t border-slate-800 shadow-[0_-4px_20px_rgba(0,255,0,0.2)]';
        break;
    case 'minimal-border':
        $nav_classes .= ' bottom-0 left-0 border-t-2';
        $grid_classes = 'grid grid-cols-4 items-center h-16';
        break;
    case 'floating-island':
        $nav_classes .= ' bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-sm rounded-[2rem] shadow-xl border border-slate-200/50';
        $grid_classes = 'grid grid-cols-4 items-center h-20 rounded-[2rem] overflow-hidden';
        break;
    case 'glassmorphism':
        $nav_classes .= ' bottom-4 left-4 right-4 w-[calc(100%-2rem)] lg:portrait:max-w-xl lg:portrait:left-1/2 lg:portrait:right-auto lg:portrait:-translate-x-1/2 rounded-2xl shadow-xl border border-white/30 backdrop-blur-lg';
        $grid_classes = 'grid grid-cols-4 items-center h-20 rounded-2xl';
        break;
    case 'gradient-bar':
        $nav_classes .= ' bottom-0 left-0 shadow-lg';
        // Gradient logic handled in CSS
        break;
    case 'tab-bar':
        $nav_classes .= ' bottom-0 left-0 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]';
        $grid_classes = 'flex justify-around items-center h-16';
        $item_base_classes = 'group flex-1 flex flex-col items-center justify-center h-full relative';
        break;
    case 'floating-dock':
        $nav_classes .= ' bottom-4 left-1/2 -translate-x-1/2 w-auto min-w-[300px] px-6 rounded-3xl shadow-2xl backdrop-blur-md border border-slate-200/50';
        $grid_classes = 'flex gap-6 items-center h-20 justify-center';
        break;
    case 'minimal-icons':
        $nav_classes .= ' bottom-0 left-0 border-t border-slate-100';
        $grid_classes = 'flex justify-around items-center h-16';
        break;
    case 'text-only':
        $nav_classes .= ' bottom-0 left-0 border-t border-slate-200';
        $grid_classes = 'grid grid-cols-4 items-center h-14';
        break;
    case 'cyberpunk':
        $nav_classes .= ' bottom-0 left-0 border-t-2 border-primary clip-path-polygon';
        $grid_classes = 'grid grid-cols-4 items-center h-20';
        break;
    case 'neumorphism':
        $nav_classes .= ' bottom-0 left-0 shadow-[inset_0_1px_0_rgba(255,255,255,0.5)]';
        $grid_classes = 'flex justify-around items-center h-20';
        break;
    case 'retro-pixel':
        $nav_classes .= ' bottom-0 left-0 border-t-4 border-black';
        $grid_classes = 'grid grid-cols-4 items-center h-16';
        break;
    case 'sidebar-drawer':
        $nav_classes .= ' bottom-0 left-0 rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] border-t border-slate-100';
        $grid_classes = 'grid grid-cols-4 items-center h-24 pb-4';
        break;
}

/* --- 2. Button Styles (Items) --- */
$item_classes = $item_base_classes;

switch ($btn_style) {
    case 'classic':
        // Default: Icon + Text
        $item_classes .= ' hover:bg-black/5 active:scale-95';
        break;
    case 'minimal':
        // Icon Only (Hide Text)
        $text_classes .= ' hidden';
        $icon_classes .= ' text-3xl';
        $item_classes .= ' hover:bg-black/5 rounded-full aspect-square w-12 mx-auto';
        break;
    case 'bold':
        // Thick Icons
        $icon_classes .= ' font-bold'; // Requires font support or stroke adjustment
        $text_classes .= ' uppercase';
        $item_classes .= ' hover:-translate-y-1';
        break;
    case 'soft':
        // Pastel Colors handled in CSS
        $item_classes .= ' hover:bg-primary/10 rounded-xl mx-2 h-14 w-auto';
        break;
    case 'bubble':
        // Circle Background
        $item_classes = 'group flex flex-col items-center justify-center h-12 w-12 rounded-full mx-auto transition-all relative overflow-hidden hover:shadow-md';
        $text_classes .= ' hidden';
        $icon_classes .= ' mb-0';
        // Active state logic via CSS
        break;
    case 'square':
        // Square Background
        $item_classes = 'group flex flex-col items-center justify-center h-14 w-14 rounded-lg mx-auto transition-all relative overflow-hidden';
        $text_classes .= ' hidden';
        $icon_classes .= ' mb-0 text-3xl';
        break;
    case 'underline':
        // Active Underline
        // Handled in CSS
        $item_classes .= ' hover:bg-transparent';
        break;
    case 'glow':
        // Text Shadow
        $item_classes .= ' hover:scale-110';
        break;
    case 'floating':
        // Lift on Hover
        $item_classes .= ' hover:-translate-y-2 shadow-sm hover:shadow-lg rounded-2xl bg-white/50 border border-transparent hover:border-slate-100 mx-2 h-16';
        break;
    case 'glass-btn':
        // Glass Effect on Buttons
        $item_classes .= ' bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl mx-2 h-14 hover:bg-white/30';
        break;
}

// Ensure Text Only hides icons
if ($bar_style === 'text-only') {
    $icon_classes .= ' hidden';
}

?>
<style>
/* Dynamic Variables */
.mob-nav-custom {
    /* Only apply solid background if explicitly not using default transparent styles */
    <?php if ($bar_style !== 'default' && $bar_style !== 'ios-blur') : ?>
    background-color: var(--mob-menu-bg);
    <?php endif; ?>
    font-family: var(--mob-menu-font);
}

<?php if ($bar_style === 'default') : ?>
/* Custom dark pill styling matching the image */
.mob-nav-item {
    color: #64748b; /* slate-500 */
    border-right: 1px solid rgba(255, 255, 255, 0.05);
}
.mob-nav-item:last-child {
    border-right: none;
}
.mob-nav-item .text-[10px], .mob-nav-item .text-xs, .mob-nav-item .text-sm {
    color: #94a3b8; /* slate-400 */
}

/* Gradients for icons */
.mob-nav-item:hover .material-symbols-outlined,
.mob-nav-item:active .material-symbols-outlined,
.mob-nav-item.active .material-symbols-outlined {
    background: linear-gradient(180deg, #38bdf8, #0ea5e9);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
}

/* Home gets green */
.mob-nav-item.active-home .material-symbols-outlined,
.mob-nav-item.active-home .text-\\[10px\\] {
    background: linear-gradient(180deg, #4ade80, #22c55e) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    text-shadow: 0 4px 12px rgba(34, 197, 94, 0.3) !important;
    color: transparent !important; /* Override text color */
}

/* Cyan for Voice Assistant */
#mobile-voice-assistant-btn .material-symbols-outlined {
    background: linear-gradient(180deg, #2dd4bf, #06b6d4) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    text-shadow: 0 4px 12px rgba(6, 182, 212, 0.3) !important;
}
#mobile-voice-assistant-btn .text-\\[10px\\] {
    color: #a5f3fc !important;
}

<?php else : ?>
/* Standard Customizer Settings */
.mob-nav-item {
    color: var(--mob-menu-icon);
}
.mob-nav-item .text-[10px], .mob-nav-item .text-xs, .mob-nav-item .text-sm {
    color: var(--mob-menu-font-color);
}
.mob-nav-item:hover, .mob-nav-item:active, .mob-nav-item.active {
    color: var(--mob-menu-active);
}
<?php endif; ?>
.mob-nav-item:hover .text-[10px], .mob-nav-item:hover .text-xs {
    color: var(--mob-menu-active);
}

/* Button Style Specifics via CSS */
<?php if ($btn_style === 'bubble') : ?>
.mob-nav-item:hover, .mob-nav-item.active {
    background-color: var(--mob-menu-active);
    color: #ffffff !important;
}
.mob-nav-item:hover span, .mob-nav-item.active span {
    color: #ffffff !important;
}
<?php endif; ?>

<?php if ($btn_style === 'square') : ?>
.mob-nav-item:hover, .mob-nav-item.active {
    background-color: var(--mob-menu-active);
    color: #ffffff !important;
}
.mob-nav-item:hover span, .mob-nav-item.active span {
    color: #ffffff !important;
}
<?php endif; ?>

<?php if ($btn_style === 'soft') : ?>
.mob-nav-item.active {
    background-color: rgba(var(--primary-color-rgb), 0.1); /* Fallback or use opacity */
    background-color: #f0fdf4; /* Light green fallback */
}
<?php endif; ?>

<?php if ($btn_style === 'underline') : ?>
.mob-nav-item { position: relative; }
.mob-nav-item.active::after {
    content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
    width: 20px; height: 3px; background-color: var(--mob-menu-active); border-radius: 99px;
}
<?php endif; ?>

<?php if ($btn_style === 'glow') : ?>
.mob-nav-item.active {
    text-shadow: 0 0 8px var(--mob-menu-active);
}
<?php endif; ?>

/* Bar Style Specific Overrides */
<?php if ($bar_style === 'neon-glow') : ?>
.mob-nav-custom { background-color: #0f172a !important; color: #94a3b8; border-top: 1px solid #1e293b; }
<?php endif; ?>

<?php if ($bar_style === 'gradient-bar') : ?>
.mob-nav-custom { background: linear-gradient(to right, var(--primary-color), var(--secondary-color)) !important; }
.mob-nav-item, .mob-nav-item span { color: rgba(255,255,255,0.8) !important; }
.mob-nav-item:hover, .mob-nav-item.active { color: #ffffff !important; }
<?php endif; ?>

<?php if ($bar_style === 'retro-pixel') : ?>
.mob-nav-custom { font-family: 'Courier New', monospace; letter-spacing: -1px; }
.mob-nav-item { border-right: 2px solid #000; }
.mob-nav-item:last-child { border-right: none; }
<?php endif; ?>

</style>

<nav class="<?php echo esc_attr($nav_classes); ?> mob-nav-custom">
    <div class="<?php echo esc_attr($grid_classes); ?>">
        <!-- Home -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="<?php echo esc_attr($item_classes); ?> mob-nav-item <?php echo is_front_page() ? 'active active-home' : ''; ?>">
            <span class="<?php echo esc_attr($icon_classes); ?> <?php echo esc_attr($icon_font_class); ?> <?php echo esc_attr($icon_base_class); ?>">home</span>
            <span class="<?php echo esc_attr($text_classes); ?>"><?php _e('Главная', 'city-library'); ?></span>
        </a>

        <?php
        // Voice Assistant Button Logic
        $enable_voice = get_theme_mod('enable_voice_control', false);
        // User requested button should ONLY be visible to those who have access.
        // We will render BOTH buttons, and toggle visibility via JavaScript based on cookie/hash to avoid PHP caching issues
        // and to keep the 4-column grid perfectly balanced.
        ?>
        <div id="mob-nav-dynamic-slot" class="flex items-center justify-center relative w-full h-full">
            <!-- Fallback Events (Afisha) - Shown by default for guests -->
            <a href="#afisha" id="mobile-afisha-btn" class="<?php echo esc_attr($item_classes); ?> mob-nav-item absolute inset-0 w-full focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/50">
                <span class="<?php echo esc_attr($icon_classes); ?> <?php echo esc_attr($icon_font_class); ?> <?php echo esc_attr($icon_base_class); ?>" aria-hidden="true">calendar_month</span>
                <span class="<?php echo esc_attr($text_classes); ?>"><?php _e('Афиша', 'city-library'); ?></span>
            </a>

            <!-- Voice Assistant - Hidden by default unless JS reveals it -->
            <?php if ($enable_voice) : ?>
            <button id="mobile-voice-assistant-btn" class="<?php echo esc_attr($item_classes); ?> mob-nav-item focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-cyan-400/50 group absolute inset-0 w-full hidden" aria-label="<?php esc_attr_e('Голосовой помощник', 'city-library'); ?>">
                <div class="absolute inset-0 bg-cyan-400/5 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-500 group-active:scale-95 group-active:bg-cyan-400/20"></div>
                <span class="<?php echo esc_attr($icon_classes); ?> <?php echo esc_attr($icon_font_class); ?> <?php echo esc_attr($icon_base_class); ?>" aria-hidden="true">mic</span>
                <span class="<?php echo esc_attr($text_classes); ?> font-extrabold"><?php _e('Ассистент', 'city-library'); ?></span>
            </button>
            <?php endif; ?>
        </div>

        <!-- Search (Opens Modal) -->
        <button id="search-toggle-mobile" class="<?php echo esc_attr($item_classes); ?> mob-nav-item focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/50" aria-label="<?php esc_attr_e('Поиск', 'city-library'); ?>">
            <span class="<?php echo esc_attr($icon_classes); ?> <?php echo esc_attr($icon_font_class); ?> <?php echo esc_attr($icon_base_class); ?>" aria-hidden="true">search</span>
            <span class="<?php echo esc_attr($text_classes); ?>"><?php _e('Поиск', 'city-library'); ?></span>
        </button>

        <!-- Menu (Opens Header Overlay) -->
        <button class="mobile-menu-toggle-btn <?php echo esc_attr($item_classes); ?> mob-nav-item focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/50" aria-label="<?php esc_attr_e('Меню', 'city-library'); ?>">
            <span class="<?php echo esc_attr($icon_classes); ?> <?php echo esc_attr($icon_font_class); ?> <?php echo esc_attr($icon_base_class); ?>" aria-hidden="true">menu</span>
            <span class="<?php echo esc_attr($text_classes); ?>"><?php _e('Меню', 'city-library'); ?></span>
        </button>
    </div>
</nav>

<?php if ($enable_voice) : ?>

<!-- Voice Test Welcome Modal -->
<div id="voice-test-welcome-modal" class="fixed inset-0 z-[110] bg-black/80 backdrop-blur-xl hidden flex items-center justify-center p-4 transition-all duration-500 opacity-0" role="dialog" aria-modal="true" aria-labelledby="voice-test-welcome-title">
    <div class="bg-white rounded-[2rem] w-full max-w-sm shadow-2xl overflow-hidden transform scale-90 transition-all duration-500 relative test-modal-content">
        <div class="p-8 text-center space-y-4">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-2">
                <span class="material-symbols-outlined text-primary text-3xl" aria-hidden="true">mic</span>
            </div>
            <h3 id="voice-test-welcome-title" class="text-2xl font-display font-bold text-slate-900 leading-tight">
                Тест Голосового Ассистента
            </h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Вы получили доступ к тестированию интеллектуального голосового помощника. Тест автоматически завершится через <strong>24 часа</strong>.
            </p>
            <div class="bg-slate-50 rounded-xl p-4 text-xs text-slate-500 text-left border border-slate-100">
                <span class="block font-bold text-slate-700 mb-1">Как использовать:</span>
                Нажмите на появившуюся кнопку микрофона и скажите <strong>«Что ты умеешь»</strong> или <strong>«Команды»</strong>, чтобы узнать о возможностях.
            </div>
            <button id="voice-test-start-btn" class="w-full mt-4 py-3.5 bg-primary hover:bg-yellow-600 text-white font-bold rounded-xl transition-all duration-300 shadow-md shadow-primary/20 active:scale-95">
                Хорошо, понятно
            </button>
        </div>
    </div>
</div>

<!-- Voice Test Feedback Modal -->
<div id="voice-test-feedback-modal" class="fixed inset-0 z-[110] bg-black/80 backdrop-blur-xl hidden flex items-center justify-center p-4 transition-all duration-500 opacity-0" role="dialog" aria-modal="true" aria-labelledby="voice-test-feedback-title">
    <div class="bg-white rounded-[2rem] w-full max-w-sm shadow-2xl overflow-hidden transform scale-90 transition-all duration-500 relative test-modal-content">
        <div class="p-8 text-center space-y-5">
            <h3 id="voice-test-feedback-title" class="text-2xl font-display font-bold text-slate-900 leading-tight">
                Время теста вышло!
            </h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Спасибо за участие в тестировании голосового ассистента. Пожалуйста, оцените его работу и опишите найденные ошибки, если они были.
            </p>

            <form id="voice-test-feedback-form" class="space-y-4">
                <!-- Star Rating -->
                <div class="flex justify-center gap-2" id="voice-feedback-stars">
                    <span class="material-symbols-outlined text-4xl text-slate-300 cursor-pointer hover:text-yellow-400 transition-colors" data-value="1" aria-hidden="true">star</span>
                    <span class="material-symbols-outlined text-4xl text-slate-300 cursor-pointer hover:text-yellow-400 transition-colors" data-value="2" aria-hidden="true">star</span>
                    <span class="material-symbols-outlined text-4xl text-slate-300 cursor-pointer hover:text-yellow-400 transition-colors" data-value="3" aria-hidden="true">star</span>
                    <span class="material-symbols-outlined text-4xl text-slate-300 cursor-pointer hover:text-yellow-400 transition-colors" data-value="4" aria-hidden="true">star</span>
                    <span class="material-symbols-outlined text-4xl text-slate-300 cursor-pointer hover:text-yellow-400 transition-colors" data-value="5" aria-hidden="true">star</span>
                </div>
                <input type="hidden" name="rating" id="voice-feedback-rating-input" value="0">

                <textarea name="feedback" rows="3" placeholder="Опишите ошибки или пожелания..." class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm p-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>

                <div id="voice-feedback-message" class="hidden text-xs font-bold text-center mt-2 rounded-lg p-2"></div>

                <button type="submit" id="voice-feedback-submit-btn" class="w-full py-3.5 bg-primary hover:bg-yellow-600 text-white font-bold rounded-xl transition-all duration-300 shadow-md shadow-primary/20 active:scale-95 disabled:opacity-50">
                    Отправить отчет
                </button>
            </form>
        </div>
    </div>
</div>

<!-- AI Answer Text Modal -->
<div id="voice-ai-answer-modal" class="fixed inset-0 z-[120] bg-black/60 backdrop-blur-md hidden flex items-center justify-center p-4 transition-all duration-500 opacity-0" role="dialog" aria-modal="true" aria-labelledby="voice-ai-answer-title">
    <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden transform scale-90 transition-all duration-500 relative flex flex-col border border-slate-100 ring-1 ring-black/5 voice-modal-content">
        <!-- Header -->
        <div class="relative bg-white px-6 py-4 shrink-0 border-b border-slate-100 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-xl" aria-hidden="true">auto_awesome</span>
                </div>
                <h3 id="voice-ai-answer-title" class="text-base font-display font-bold text-slate-900 tracking-tight leading-none">
                    Ответ ассистента
                </h3>
            </div>
            <button type="button" class="voice-ai-answer-close p-2 rounded-full text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 group" aria-label="Закрыть">
                <span class="material-symbols-outlined text-xl group-hover:rotate-90 transition-transform duration-300" aria-hidden="true">close</span>
            </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-5 overflow-y-auto custom-scrollbar bg-slate-50 max-h-[60vh]">
            <div id="voice-ai-answer-text" class="text-sm text-slate-700 leading-relaxed font-medium prose prose-sm prose-slate max-w-none">
                <!-- AI text injected here -->
            </div>
        </div>
    </div>
</div>

<!-- Voice Yandex Map Modal (Full Screen Mobile) -->
<div id="voice-map-modal" class="fixed inset-0 z-[130] bg-black/90 hidden flex-col transition-all duration-300 opacity-0 lg:landscape:hidden w-full h-[100dvh]" role="dialog" aria-modal="true" aria-labelledby="voice-map-title">
    <!-- Header with Close Button -->
    <div class="flex items-center justify-between px-4 py-3 safe-area-top bg-white shadow-md z-10 shrink-0">
        <h3 id="voice-map-title" class="text-lg font-bold text-slate-900 truncate flex-1">Карта филиалов</h3>
        <button type="button" id="voice-map-close" class="p-2 -mr-2 text-slate-500 hover:text-slate-900 focus:outline-none" aria-label="Закрыть карту">
            <span class="material-symbols-outlined text-2xl" aria-hidden="true">close</span>
        </button>
    </div>
    <!-- Yandex Map iframe container -->
    <div class="flex-1 w-full h-full relative overflow-hidden flex flex-col bg-white">
        <!-- Single Branch iframe -->
        <iframe id="voice-map-iframe" class="absolute inset-0 w-full h-full border-0 object-cover" src="" allowfullscreen="true" style="position:absolute; width:100%; height:100%;"></iframe>

        <!-- All Branches Custom Container (AJAX loaded) -->
        <div id="voice-custom-map-container" class="absolute inset-0 w-full h-full hidden overflow-y-auto bg-slate-50 custom-scrollbar pb-16"></div>

        <!-- Loading Spinner -->
        <div id="voice-map-loader" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-0 pointer-events-none transition-opacity duration-300">
            <div class="w-12 h-12 border-4 border-primary/30 border-t-primary rounded-full animate-spin mb-3"></div>
            <span class="text-sm text-slate-500 font-medium">Загрузка карты...</span>
        </div>
    </div>
</div>

<!-- Voice Commands Modal -->
<div id="voice-commands-modal" class="fixed inset-0 z-[120] bg-black/80 backdrop-blur-xl hidden flex items-center justify-center p-4 transition-all duration-500 opacity-0" role="dialog" aria-modal="true" aria-labelledby="voice-commands-title">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl overflow-hidden transform scale-90 transition-all duration-500 relative max-h-[90vh] flex flex-col border border-slate-100 ring-1 ring-black/5 voice-modal-content">
        <!-- Header -->
        <div class="relative bg-white px-8 py-6 shrink-0 border-b border-slate-100 flex items-center justify-between z-10">
            <div>
                <h3 id="voice-commands-title" class="text-2xl font-display font-bold text-slate-900 tracking-tight leading-none">
                    Голосовой помощник
                </h3>
                <p class="text-xs text-slate-500 font-medium mt-1 uppercase tracking-wider">Доступные команды</p>
            </div>
            <button type="button" class="voice-modal-close p-2 rounded-full text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary/50 group" aria-label="Закрыть">
                <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform duration-300" aria-hidden="true">close</span>
            </button>
        </div>

        <!-- Body -->
        <div class="px-8 py-6 overflow-y-auto custom-scrollbar bg-slate-50 space-y-4">
            <p class="text-sm text-slate-600 mb-4 font-medium leading-relaxed">
                Нажмите кнопку с микрофоном и произнесите одну из команд:
            </p>

            <ul class="space-y-3">
                <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5" aria-hidden="true">article</span>
                    <div>
                        <strong class="text-sm text-slate-900 block font-bold">«Открой последние новости»</strong>
                        <span class="text-xs text-slate-500">Переход к свежим событиям</span>
                    </div>
                </li>
                <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5" aria-hidden="true">event</span>
                    <div>
                        <strong class="text-sm text-slate-900 block font-bold">«Афиша» или «Мероприятия»</strong>
                        <span class="text-xs text-slate-500">Показать расписание событий</span>
                    </div>
                </li>
                <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5" aria-hidden="true">auto_stories</span>
                    <div>
                        <strong class="text-sm text-slate-900 block font-bold">«Продление книг»</strong>
                        <span class="text-xs text-slate-500">Открыть форму продления онлайн</span>
                    </div>
                </li>
                <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5" aria-hidden="true">location_on</span>
                    <div>
                        <strong class="text-sm text-slate-900 block font-bold">«Контакты» или «Библиотека номер 2»</strong>
                        <span class="text-xs text-slate-500">Информация о филиалах</span>
                    </div>
                </li>
                <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5" aria-hidden="true">home</span>
                    <div>
                        <strong class="text-sm text-slate-900 block font-bold">«На главную»</strong>
                        <span class="text-xs text-slate-500">Вернуться в начало сайта</span>
                    </div>
                </li>
                <li class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-0.5" aria-hidden="true">menu_book</span>
                    <div>
                        <strong class="text-sm text-slate-900 block font-bold">«Электронный каталог»</strong>
                        <span class="text-xs text-slate-500">Открыть каталог книг в новой вкладке</span>
                    </div>
                </li>
            </ul>

            <div class="mt-6 p-4 bg-primary/10 rounded-2xl border border-primary/20">
                <p class="text-xs text-slate-700 leading-relaxed font-medium">
                    <span class="font-bold text-primary block mb-1">Задайте любой вопрос:</span>
                    Например: <em class="text-slate-500">«Кто автор Евгения Онегина?»</em> или <em class="text-slate-500">«График работы филиала номер 5»</em>. Наш Виртуальный библиотекарь найдет ответ!
                </p>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
