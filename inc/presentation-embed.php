<?php
/**
 * Shortcode to embed adaptive PPT/PPTX presentations using Microsoft Office Web Viewer.
 * Usage: [city_library_presentation url="https://example.com/path/to/file.pptx"]
 */

function city_library_presentation_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'ratio' => '16:9', // E.g., '16:9', '4:3'
    ), $atts, 'city_library_presentation');

    $url = esc_url($atts['url']);

    // Calculate padding-bottom based on ratio
    $padding_bottom = '56.25%'; // default 16:9
    if (strpos($atts['ratio'], ':') !== false) {
        list($w, $h) = explode(':', $atts['ratio']);
        if (is_numeric($w) && is_numeric($h) && $w > 0) {
            $padding_bottom = number_format(($h / $w) * 100, 2) . '%';
        }
    }

    if (empty($url)) {
        return '<p class="text-red-500 font-bold p-4 border border-red-200 rounded bg-red-50">' . __('Ошибка: Не указан URL презентации в шорткоде.', 'city-library') . '</p>';
    }

    // Verify it's likely a PPT file (basic check)
    $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ['ppt', 'pptx'])) {
        // We still allow it, but add a wrapper class in case it's a docx/xls
        $is_ppt = false;
    } else {
        $is_ppt = true;
    }

    // Use Google Docs Viewer for better responsive embedding and scaling
    $embed_url = 'https://docs.google.com/gview?url=' . urlencode($url) . '&embedded=true';

    ob_start();
    ?>
    <div class="my-8 w-full city-library-presentation">
        <div class="relative bg-white rounded-xl overflow-hidden shadow-lg border border-slate-200 group" style="position: relative; height: 0; padding-bottom: <?php echo esc_attr($padding_bottom); ?>; overflow: hidden;">

            <!-- Loading State Placeholder -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 z-0 bg-slate-50" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                <span class="material-symbols-outlined text-4xl animate-spin mb-2" aria-hidden="true">sync</span>
                <span class="text-sm font-medium uppercase tracking-widest"><?php _e('Загрузка презентации...', 'city-library'); ?></span>
            </div>

            <iframe
                src="<?php echo esc_url($embed_url); ?>"
                width="100%"
                height="100%"
                frameborder="0"
                title="<?php esc_attr_e('Презентация', 'city-library'); ?>"
                class="absolute inset-0 z-10 w-full h-full bg-white"
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                allowfullscreen="true"
                webkitallowfullscreen="true"
                mozallowfullscreen="true">
            </iframe>

            <!-- Fullscreen helper overlay (visible on hover) -->
            <div class="absolute top-4 right-4 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none hidden md:block">
                <div class="bg-black/70 backdrop-blur-md text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-2 shadow-xl border border-white/10">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">open_in_new</span>
                    <?php _e('Используйте кнопку поп-аута в правом верхнем углу для полного экрана', 'city-library'); ?>
                </div>
            </div>
        </div>

        <!-- Fallback/Action Link -->
        <div class="mt-3 flex justify-end">
             <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-primary transition-colors focus:outline-none focus:underline">
                 <span class="material-symbols-outlined text-base" aria-hidden="true">download</span>
                 <?php _e('Скачать / Открыть в новой вкладке', 'city-library'); ?>
             </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('city_library_presentation', 'city_library_presentation_shortcode');