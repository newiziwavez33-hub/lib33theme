<?php
/**
 * Theme setup.
 */
// Include Branches Map Shortcode
require_once get_template_directory() . '/inc/branches-map.php';

// Include Inline Post Slider Shortcode
require_once get_template_directory() . '/inc/post-slider.php';

// Include Hero Custom Meta Box
require_once get_template_directory() . '/inc/hero-meta-box.php';

// Include Presentation Embed Shortcode
require_once get_template_directory() . '/inc/presentation-embed.php';

// Include Virtual Librarian AI
require_once get_template_directory() . '/inc/virtual-librarian.php';

// Include Live Search & Ghost Autocomplete Engine
require_once get_template_directory() . '/inc/live-search.php';

function city_library_setup() {
    // Make theme available for translation.
    load_theme_textdomain('city-library', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Enable support for Custom Logo.
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register navigation menus.
    register_nav_menus(
        array(
            'primary' => esc_html__('Primary Menu', 'city-library'),
            'footer'  => esc_html__('Footer Menu', 'city-library'),
        )
    );

    // Switch default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for core block styles.
    add_theme_support('wp-block-styles');

    // Add support for editor styles.
    add_theme_support('editor-styles');

    // Enqueue editor styles (including Google Fonts for full WYSIWYG parity).
    add_editor_style(array(
        'https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Comfortaa:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700&family=Monomakh&family=Montserrat:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Ruslan+Display&display=swap',
        'css/editor-style.css'
    ));

    // Add support for wide alignment.
    add_theme_support('align-wide');

    // Add support for responsive embeds.
    add_theme_support('responsive-embeds');

    // Add support for theme color palette in block editor
    add_theme_support('editor-color-palette', array(
        array('name' => esc_html__('Основной синий', 'city-library'), 'slug' => 'primary', 'color' => '#1c77d9'),
        array('name' => esc_html__('Темно-зеленый (акцент)', 'city-library'), 'slug' => 'secondary', 'color' => '#1A3C34'),
        array('name' => esc_html__('Библиотечный зеленый', 'city-library'), 'slug' => 'library-green', 'color' => '#0b7930'),
        array('name' => esc_html__('Темный заголовок (slate-900)', 'city-library'), 'slug' => 'slate-900', 'color' => '#0f172a'),
        array('name' => esc_html__('Текст статьи (slate-700)', 'city-library'), 'slug' => 'slate-700', 'color' => '#334155'),
        array('name' => esc_html__('Приглушенный серый (slate-500)', 'city-library'), 'slug' => 'slate-500', 'color' => '#64748b'),
        array('name' => esc_html__('Светлый фон (slate-50)', 'city-library'), 'slug' => 'slate-50', 'color' => '#f8fafc'),
        array('name' => esc_html__('Белый', 'city-library'), 'slug' => 'white', 'color' => '#ffffff'),
        array('name' => esc_html__('Янтарный', 'city-library'), 'slug' => 'amber', 'color' => '#f59e0b'),
        array('name' => esc_html__('Красный', 'city-library'), 'slug' => 'red', 'color' => '#ef4444'),
    ));

    // Add support for font sizes in block editor (16px base, calibrated -2px)
    add_theme_support('editor-font-sizes', array(
        array('name' => esc_html__('Маленький (13px)', 'city-library'), 'size' => 13, 'slug' => 'small'),
        array('name' => esc_html__('Обычный (16px)', 'city-library'), 'size' => 16, 'slug' => 'normal'),
        array('name' => esc_html__('Средний (18px)', 'city-library'), 'size' => 18, 'slug' => 'medium'),
        array('name' => esc_html__('Большой (24px)', 'city-library'), 'size' => 24, 'slug' => 'large'),
        array('name' => esc_html__('Очень большой (32px)', 'city-library'), 'size' => 32, 'slug' => 'x-large'),
    ));
}
add_action('after_setup_theme', 'city_library_setup');

/**
 * Enqueue Block Editor assets for AI Integration.
 */
function city_library_block_editor_assets() {
    if (!is_user_logged_in() || !current_user_can('edit_posts')) return;

    wp_enqueue_script(
        'city-library-editor-ai',
        get_template_directory_uri() . '/js/editor-librarian.js',
        array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-block-editor', 'wp-rich-text', 'wp-i18n', 'jquery'),
        filemtime(get_template_directory() . '/js/editor-librarian.js'),
        true
    );

    wp_localize_script('city-library-editor-ai', 'cityLibraryEditorAI', array(
        'nonce' => wp_create_nonce('ai_chat_nonce')
    ));

    // Pass dynamic Customizer color variables to Gutenberg editor
    $primary_color   = get_theme_mod('primary_color', '#1c77d9');
    $secondary_color = get_theme_mod('secondary_color', '#1A3C34');
    $btn_bg          = get_theme_mod('global_btn_bg_color', '#1c77d9');
    $btn_text        = get_theme_mod('global_btn_text_color', '#FFFFFF');
    $btn_hover_bg    = get_theme_mod('global_btn_hover_bg_color', '#1668c2');
    $btn_hover_text  = get_theme_mod('global_btn_hover_text_color', '#FFFFFF');
    $editor_vars = "
        :root, body, .editor-styles-wrapper {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
            --btn-bg: {$btn_bg};
            --btn-text: {$btn_text};
            --btn-hover-bg: {$btn_hover_bg};
            --btn-hover-text: {$btn_hover_text};
        }
    ";
    wp_add_inline_style('wp-block-editor', $editor_vars);
}
add_action('enqueue_block_editor_assets', 'city_library_block_editor_assets');

/**
 * Allow extra mime types for upload.
 */
function city_library_add_mime_types($mimes) {
    $mimes['webm'] = 'video/webm';
    $mimes['mp4'] = 'video/mp4';
    return $mimes;
}
add_filter('upload_mimes', 'city_library_add_mime_types');


/**
 * Enqueue scripts and styles.
 */
function city_library_scripts() {
    // Main stylesheet.
    // Use filemtime for version to force cache clear on update
    wp_enqueue_style('city-library-style', get_stylesheet_uri(), array(), filemtime(get_stylesheet_directory() . '/style.css'));

    // Scrollbar Fix
    wp_enqueue_style('city-library-scrollbar-fix', get_template_directory_uri() . '/css/scrollbar-fix.css', array(), wp_get_theme()->get('Version'));
    wp_enqueue_style('city-library-search-modal-css', get_template_directory_uri() . '/css/search-modal.css', array(), file_exists(get_template_directory() . '/css/search-modal.css') ? filemtime(get_template_directory() . '/css/search-modal.css') : wp_get_theme()->get('Version'));

    // Google Fonts (Including Magic Mode fonts) — full Montserrat range for thin/thick weights
    wp_enqueue_style('city-library-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&family=Merriweather:wght@300;400;700&display=swap', array(), null);

    // Google Fonts for Hero Section (Monomakh, Cormorant Garamond, Playfair Display, Ruslan Display, Oswald, Comfortaa, Caveat)
    wp_enqueue_style(
        'city-library-hero-google-fonts',
        'https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Comfortaa:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Monomakh&family=Oswald:wght@500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Ruslan+Display&display=swap',
        array(),
        null
    );

    // Material Symbols
    wp_enqueue_style('material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0', array(), null);

    // Magic Mode CSS (Removed)
    // wp_enqueue_style('city-library-magic-mode-css', get_template_directory_uri() . '/css/magic-mode.css', array(), wp_get_theme()->get('Version'));

    // Tailwind CSS
    wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com?plugins=forms,typography', array(), null, false);

    // Swiper CSS & JS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);

    // GLightbox CSS & JS
    wp_enqueue_style('glightbox-css', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', array(), '3.3.0');
    wp_enqueue_script('glightbox-js', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', array(), '3.3.0', true);
    wp_enqueue_script('city-library-lightbox-init', get_template_directory_uri() . '/js/lightbox-init.js', array('glightbox-js'), wp_get_theme()->get('Version'), true);

    // Custom JS files
    wp_enqueue_script('city-library-view-toggle', get_template_directory_uri() . '/js/view-toggle.js', array('jquery'), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('city-library-sidebar', get_template_directory_uri() . '/js/sidebar.js', array(), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('city-library-accessibility', get_template_directory_uri() . '/js/accessibility.js', array(), wp_get_theme()->get('Version'), true);

    // Voice Control Enqueue
    $enable_voice = get_theme_mod('enable_voice_control', false);
    $voice_test_mode = get_theme_mod('voice_control_test_mode', true);

    // We now always enqueue the script if voice is globally enabled, so the JS can check for #voicetest and cookies
    if ($enable_voice) {
        // Enqueue marked.js for voice control markdown parsing
        if (!wp_script_is('marked-js', 'enqueued')) {
            wp_enqueue_script('marked-js', 'https://cdn.jsdelivr.net/npm/marked/marked.min.js', array(), null, true);
        }

        wp_enqueue_script('city-library-voice', get_template_directory_uri() . '/js/voice-control.js', array('jquery', 'marked-js'), wp_get_theme()->get('Version'), true);

        $custom_commands = array();
        for ($i = 1; $i <= 20; $i++) {
            $phrases = get_theme_mod("voice_cmd_phrases_$i", '');
            $url = get_theme_mod("voice_cmd_url_$i", '');
            if (!empty(trim($phrases)) && !empty(trim($url))) {
                $phrases_array = array_filter(array_map('trim', explode(',', strtolower($phrases))));
                if (!empty($phrases_array)) {
                    $custom_commands[] = array(
                        'phrases' => array_values($phrases_array),
                        'url' => esc_url($url)
                    );
                }
            }
        }

        // Prepare branch addresses for Maps
        $branch_keys = array('cgb', 'cdb', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '13', '14', '15', '16');
        $branch_addresses = array();
        foreach ($branch_keys as $key) {
             $default = '';
             if ($key === 'cgb') $default = 'г. Владимир, Суздальский пр-кт, д. 2';
             elseif ($key === 'cdb') $default = 'г. Владимир, ул. Белоконской, д. 10-а';
             elseif ($key === '1') $default = 'г. Владимир, пр-кт Строителей, д. 23';
             elseif ($key === '2') $default = 'г. Владимир, пр-кт Ленина, д. 12';
             elseif ($key === '3') $default = 'г. Владимир, ул. Добросельская, д. 2-в';
             elseif ($key === '4') $default = 'г. Владимир, ул. Комиссарова, д. 69';
             elseif ($key === '5') $default = 'г. Владимир, пр-кт Суздальский, д. 2';
             elseif ($key === '6') $default = 'г. Владимир, ул. Мира, д. 37';
             elseif ($key === '7') $default = 'г. Владимир, ул. Добросельская, д. 189-б';
             elseif ($key === '8') $default = 'г. Владимир, ул. Диктора Левитана, д. 36';
             elseif ($key === '9') $default = 'г. Владимир, ул. Горького, д. 85';
             elseif ($key === '10') $default = 'г. Владимир, ул. Егорова, д. 10';
             elseif ($key === '11') $default = 'г. Владимир, мкр. Юрьевец, ул. Институтский городок, д. 4';
             elseif ($key === '13') $default = 'г. Владимир, мкр. Юрьевец, ул. Ноябрьская, д. 2-а';
             elseif ($key === '14') $default = 'г. Владимир, мкр. Энергетик, ул. Энергетиков, д. 12';
             elseif ($key === '15') $default = 'г. Владимир, мкр. Энергетик, ул. Совхозная, д. 11';
             elseif ($key === '16') $default = 'г. Владимир, мкр. Коммунар, ул. Песочная, д. 2-а';

             $branch_addresses[$key] = get_theme_mod("branch_address_$key", $default);
        }

        wp_localize_script('city-library-voice', 'cl_voice_control', array(
            'enabled' => true,
            'test_mode' => $voice_test_mode,
            'is_logged_in' => is_user_logged_in(),
            'home_url' => home_url(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'ai_nonce' => wp_create_nonce('ai_chat_nonce'),
            'voice_pitch' => get_theme_mod('voice_pitch', '1.0'),
            'voice_rate' => get_theme_mod('voice_rate', '1.05'),
            'custom_commands' => $custom_commands,
            'branch_addresses' => $branch_addresses
        ));
    }
    wp_enqueue_script('city-library-modal-popup', get_template_directory_uri() . '/js/modal-popup.js', array(), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('city-library-mobile-menu', get_template_directory_uri() . '/js/mobile-menu.js', array(), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('city-library-mobile-sliders', get_template_directory_uri() . '/js/mobile-sliders.js', array('swiper-js'), wp_get_theme()->get('Version'), true);
    // Magic Mode removed/replaced by Renewal
    // wp_enqueue_script('city-library-magic-mode', get_template_directory_uri() . '/js/magic-mode.js', array(), wp_get_theme()->get('Version'), true);

    // Book Renewal & Cookies
    wp_enqueue_script('city-library-book-renewal', get_template_directory_uri() . '/js/book-renewal.js', array('jquery'), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('city-library-cookie-consent', get_template_directory_uri() . '/js/cookie-consent.js', array(), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('city-library-search-modal', get_template_directory_uri() . '/js/search-modal.js', array(), file_exists(get_template_directory() . '/js/search-modal.js') ? filemtime(get_template_directory() . '/js/search-modal.js') : wp_get_theme()->get('Version'), true);
    wp_localize_script('city-library-search-modal', 'city_library_search_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('city_library_search_nonce'),
        'home_url' => home_url('/'),
    ));

    // Yandex Map
    if (get_theme_mod('footer_show_map', false)) {
        $apikey = get_theme_mod('footer_map_apikey', '');
        $api_url = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU';
        if ($apikey) {
            $api_url .= '&apikey=' . esc_attr($apikey);
        }
        wp_enqueue_script('yandex-maps-api', $api_url, array(), null, true);
        wp_enqueue_script('city-library-yandex-map', get_template_directory_uri() . '/js/yandex-map-init.js', array('yandex-maps-api'), wp_get_theme()->get('Version'), true);

        wp_localize_script('city-library-yandex-map', 'yandex_map_params', array(
            'lat' => get_theme_mod('footer_map_lat', '56.162458'),
            'lon' => get_theme_mod('footer_map_lon', '40.470598'),
            'zoom' => get_theme_mod('footer_map_zoom', 15),
        ));
    }

    // Scroll Animations
    wp_enqueue_script('city-library-scroll-animations', get_template_directory_uri() . '/js/scroll-animations.js', array(), wp_get_theme()->get('Version'), true);

    if (is_single()) {
        wp_enqueue_script('city-library-reading-progress', get_template_directory_uri() . '/js/reading-progress.js', array(), wp_get_theme()->get('Version'), true);
    }

    wp_localize_script('city-library-view-toggle', 'ajax_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('view_toggle_nonce')
    ));

    // Prepare Renewal Button Settings for JS
    $renewal_settings = array(
        'btn_text' => get_theme_mod('renewal_btn_text', 'Продлить книгу'),
        'btn_bg' => get_theme_mod('renewal_btn_bg_color', '#1c77d9'),
        'btn_text_color' => get_theme_mod('renewal_btn_text_color', '#ffffff'),
        'btn_radius' => get_theme_mod('renewal_btn_radius', 'medium'),
        'btn_visibility' => get_theme_mod('renewal_btn_visibility', 'all'),
        'btn_position' => get_theme_mod('renewal_btn_position', 'bottom-right'),
    );

    wp_localize_script('city-library-book-renewal', 'renewal_params', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'nonce'        => wp_create_nonce('book_renewal_nonce'),
        'branches'     => city_library_get_branches_data(),
        'settings'     => $renewal_settings,
        'privacy_link' => get_theme_mod('footer_privacy_link', '#'),
    ));

    // Sticky Header
    wp_enqueue_script('city-library-sticky-header', get_template_directory_uri() . '/js/sticky-header.js', array(), wp_get_theme()->get('Version'), true);

    $sticky_css = file_get_contents(get_template_directory() . '/css/sticky-header.css');
    if ($sticky_css) {
        wp_add_inline_style('city-library-style', $sticky_css);
    }
}
add_action('wp_enqueue_scripts', 'city_library_scripts');

/**
 * Preconnect for Google Fonts to accelerate Hero typography rendering.
 */
function city_library_enqueue_hero_fonts_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'city_library_enqueue_hero_fonts_preconnect', 1);

/**
 * Add custom body class for full-page fixed background mode, adaptive page backgrounds, and card styles.
 */
function city_library_post_fixed_bg_body_class($classes) {
    if (is_single() || is_page()) {
        $post_id = get_the_ID();
        if ($post_id) {
            $page_bg_enable = get_post_meta($post_id, '_page_bg_enable', true);
            if ($page_bg_enable === 'yes') {
                $mode = get_post_meta($post_id, '_page_bg_mode', true) ?: 'fixed';
                $card_style = get_post_meta($post_id, '_page_bg_card_style', true) ?: 'glass';
                $classes[] = 'has-custom-page-bg';
                $classes[] = 'page-bg-mode-' . sanitize_html_class($mode);
                $classes[] = 'card-style-' . sanitize_html_class($card_style);
            } elseif (get_theme_mod('global_page_bg_enable', false)) {
                $mode = get_theme_mod('global_page_bg_mode', 'fixed');
                $card_style = get_theme_mod('global_page_bg_card_style', 'solid');
                $classes[] = 'has-custom-page-bg';
                $classes[] = 'page-bg-mode-' . sanitize_html_class($mode);
                $classes[] = 'card-style-' . sanitize_html_class($card_style);
            }

            // Backward compatibility for legacy hero full-page-fixed mode
            if (get_post_meta($post_id, '_hero_custom_enable', true) === 'yes') {
                $bg_mode = get_post_meta($post_id, '_hero_custom_bg_mode', true);
                if ($bg_mode === 'full-page-fixed') {
                    $classes[] = 'has-post-fixed-bg';
                    if (!in_array('has-custom-page-bg', $classes, true)) {
                        $classes[] = 'has-custom-page-bg';
                        $classes[] = 'page-bg-mode-fixed';
                        $classes[] = 'card-style-glass';
                    }
                }
            }
        }
    }
    return $classes;
}
add_filter('body_class', 'city_library_post_fixed_bg_body_class');

/**
 * Output dynamic page background color inline style in wp_head for instant loading.
 */
function city_library_page_background_inline_css() {
    $post_id = get_the_ID();
    $color = '';
    if ((is_single() || is_page()) && $post_id) {
        if (get_post_meta($post_id, '_page_bg_enable', true) === 'yes') {
            $color = get_post_meta($post_id, '_page_bg_color', true);
        }
    }
    if (empty($color) && get_theme_mod('global_page_bg_enable', false)) {
        $color = get_theme_mod('global_page_bg_color', '');
    }
    if (!empty($color)) {
        echo '<style id="cl-page-bg-custom-css">body.has-custom-page-bg { background-color: ' . esc_attr($color) . ' !important; }</style>' . "\n";
    }
}
add_action('wp_head', 'city_library_page_background_inline_css', 100);

/**
 * Render Adaptive Page Background Canvas (Behind Content Blocks)
 */
function city_library_render_page_background() {
    $post_id = get_the_ID();
    $page_bg_active = false;
    $page_bg_color = '';
    $page_bg_image = '';
    $page_bg_mode = 'fixed';
    $page_bg_fit = 'cover';
    $page_bg_ov_color = '#000000';
    $page_bg_ov_opacity = '0.0';

    if ((is_single() || is_page()) && $post_id) {
        if (get_post_meta($post_id, '_page_bg_enable', true) === 'yes') {
            $page_bg_active     = true;
            $page_bg_color      = get_post_meta($post_id, '_page_bg_color', true) ?: '#f6f8f6';
            $page_bg_image      = get_post_meta($post_id, '_page_bg_image', true);
            $page_bg_mode       = get_post_meta($post_id, '_page_bg_mode', true) ?: 'fixed';
            $page_bg_fit        = get_post_meta($post_id, '_page_bg_fit', true) ?: 'cover';
            $page_bg_ov_color   = get_post_meta($post_id, '_page_bg_overlay_color', true) ?: '#000000';
            $page_bg_ov_opacity = get_post_meta($post_id, '_page_bg_overlay_opacity', true);
        }
    }

    // Global customizer fallback if post does not have page bg enabled
    if (!$page_bg_active && get_theme_mod('global_page_bg_enable', false)) {
        $page_bg_active     = true;
        $page_bg_color      = get_theme_mod('global_page_bg_color', '#f6f8f6');
        $page_bg_image      = get_theme_mod('global_page_bg_image', '');
        $page_bg_mode       = get_theme_mod('global_page_bg_mode', 'fixed');
        $page_bg_fit        = get_theme_mod('global_page_bg_fit', 'cover');
        $page_bg_ov_color   = get_theme_mod('global_page_bg_overlay_color', '#000000');
        $page_bg_ov_opacity = get_theme_mod('global_page_bg_overlay_opacity', '0.0');
    }

    if (!$page_bg_active) {
        return;
    }

    $ov_op = ($page_bg_ov_opacity !== '') ? floatval($page_bg_ov_opacity) : 0.0;
    list($or, $og, $ob) = sscanf($page_bg_ov_color, "#%02x%02x%02x") ?: [0, 0, 0];
    $overlay_style = ($ov_op > 0) ? "background-color: rgba($or, $og, $ob, $ov_op);" : "";

    $bg_color_style = !empty($page_bg_color) ? "background-color: " . esc_attr($page_bg_color) . ";" : "";

    if ($page_bg_mode === 'repeat') : ?>
        <div class="cl-page-bg-pattern-canvas" aria-hidden="true" style="<?php echo esc_attr($bg_color_style); ?><?php if (!empty($page_bg_image)) echo ' background-image: url(\'' . esc_url($page_bg_image) . '\');'; ?>">
            <?php if ($ov_op > 0) : ?>
                <div class="cl-page-bg-overlay" style="<?php echo esc_attr($overlay_style); ?>"></div>
            <?php endif; ?>
        </div>
    <?php elseif ($page_bg_mode === 'scroll') : ?>
        <div class="cl-page-bg-scroll-canvas" aria-hidden="true" style="<?php echo esc_attr($bg_color_style); ?>">
            <?php if (!empty($page_bg_image)) : ?>
                <img src="<?php echo esc_url($page_bg_image); ?>" class="cl-page-bg-img fit-<?php echo esc_attr($page_bg_fit); ?>" alt="">
            <?php endif; ?>
            <?php if ($ov_op > 0) : ?>
                <div class="cl-page-bg-overlay" style="<?php echo esc_attr($overlay_style); ?>"></div>
            <?php endif; ?>
        </div>
    <?php else : // default: 'fixed' ?>
        <div class="cl-page-bg-fixed-canvas" aria-hidden="true" style="<?php echo esc_attr($bg_color_style); ?>">
            <?php if (!empty($page_bg_image)) : ?>
                <img src="<?php echo esc_url($page_bg_image); ?>" class="cl-page-bg-img fit-<?php echo esc_attr($page_bg_fit); ?>" alt="">
            <?php endif; ?>
            <?php if ($ov_op > 0) : ?>
                <div class="cl-page-bg-overlay" style="<?php echo esc_attr($overlay_style); ?>"></div>
            <?php endif; ?>
        </div>
    <?php endif;
}

/**
 * Helper: Get Branches Data (динамически из CPT library_branch с fallback на базовый список)
 */
function city_library_get_branches_data() {
    $branches = array();

    // 1. Проверяем наличие опубликованных записей CPT library_branch
    $cpt_branches = get_posts(array(
        'post_type'      => 'library_branch',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ));

    if (!empty($cpt_branches)) {
        foreach ($cpt_branches as $post) {
            $email = get_post_meta($post->ID, '_library_email', true);
            // Если в CPT пусто, проверяем, нет ли настройки в Customizer для этого ID
            if (empty($email)) {
                $email = get_theme_mod("branch_email_{$post->ID}", '');
            }
            // Также проверяем, нет ли номера филиала в заголовке (например "Филиал №5" -> branch_email_5)
            if (empty($email) && preg_match('/(?:филиал|№)\s*(\d+)/ui', $post->post_title, $m)) {
                $num = $m[1];
                $email = get_theme_mod("branch_email_$num", '');
            }
            $branches[] = array(
                'id'    => (string)$post->ID,
                'name'  => $post->post_title,
                'email' => $email,
            );
        }
        return $branches;
    }

    // 2. Резервный список (если CPT еще не наполнен)
    $default_list = array(
        'cgb' => 'Центральная городская библиотека (ЦГБ)',
        'cdb' => 'Центральная детская библиотека (ЦДБ)',
    );
    // Список филиалов ЦБС г. Владимира (1..11, 13..16)
    $active_branch_numbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16);
    foreach ($active_branch_numbers as $i) {
        $default_list[$i] = "Филиал №$i";
    }

    foreach ($default_list as $id => $name) {
        $branches[] = array(
            'id'    => (string)$id,
            'name'  => $name,
            'email' => get_theme_mod("branch_email_$id", ''),
        );
    }

    return $branches;
}

/**
 * Helper: Get Branches List (для обратной совместимости)
 */
function city_library_get_branches_list() {
    $branches_data = city_library_get_branches_data();
    $list = array();
    foreach ($branches_data as $b) {
        $list[$b['id']] = $b['name'];
    }
    return $list;
}

/**
 * AJAX Handler for Voice Test Feedback
 */
function city_library_voice_feedback() {
    check_ajax_referer('ai_chat_nonce', 'nonce');

    $rating = intval($_POST['rating']);
    $feedback = sanitize_textarea_field($_POST['feedback']);

    $to = 'xxoleg6@yandex.ru';
    $subject = 'Отчет о тестировании Голосового Ассистента (Оценка: ' . $rating . '/5)';
    $message = "Оценка: $rating из 5\n\nОтзыв/Ошибки:\n$feedback";
    $headers = array('Content-Type: text/plain; charset=UTF-8');

    // Attempt to send email
    wp_mail($to, $subject, $message, $headers);

    // We clear the cookie by setting expiration to the past
    setcookie('cl_voice_test_active', '', time() - 3600, '/');

    wp_send_json_success(array('message' => 'Спасибо! Ваш отзыв отправлен.'));
}
add_action('wp_ajax_city_library_voice_feedback', 'city_library_voice_feedback');
add_action('wp_ajax_nopriv_city_library_voice_feedback', 'city_library_voice_feedback');

/**
 * AJAX Handler for Book Renewal
 */
function city_library_send_book_renewal() {
    check_ajax_referer('book_renewal_nonce', 'nonce');

    $fio         = sanitize_text_field($_POST['fio']);
    $card_number = sanitize_text_field($_POST['card_number']);
    $branch_id   = sanitize_text_field($_POST['branch']);
    $email       = sanitize_email($_POST['email']);
    $books       = sanitize_textarea_field($_POST['books']);

    if (empty($fio) || empty($card_number) || empty($branch_id) || empty($email) || empty($books)) {
        wp_send_json_error(['message' => 'Пожалуйста, заполните все поля.']);
    }

    $branch_email = '';
    $branch_name  = '';

    // 1. Если $branch_id — это Post ID CPT library_branch
    if (is_numeric($branch_id)) {
        $post_id = intval($branch_id);
        $post_obj = get_post($post_id);
        if ($post_obj && $post_obj->post_type === 'library_branch') {
            $branch_name = $post_obj->post_title;
            $branch_email = get_post_meta($post_id, '_library_email', true);
            // Если в meta пусто, проверим Customizer по ID поста
            if (empty($branch_email)) {
                $branch_email = get_theme_mod("branch_email_$post_id");
            }
            // Или если в заголовке есть номер (например "Филиал №5"), проверим branch_email_5
            if (empty($branch_email) && preg_match('/(?:филиал|№)\s*(\d+)/ui', $branch_name, $m)) {
                $branch_email = get_theme_mod("branch_email_{$m[1]}");
            }
        }
    }

    // 2. Поиск по общему реестру city_library_get_branches_data()
    if (empty($branch_name) || empty($branch_email)) {
        $all_branches = city_library_get_branches_data();
        foreach ($all_branches as $branch) {
            if ((string)$branch['id'] === (string)$branch_id) {
                if (empty($branch_name)) {
                    $branch_name = $branch['name'];
                }
                if (empty($branch_email) && !empty($branch['email'])) {
                    $branch_email = $branch['email'];
                }
                break;
            }
        }
    }

    // 3. Поиск по Customizer: branch_email_$branch_id (например branch_email_5, branch_email_cgb)
    if (empty($branch_email)) {
        $branch_email = get_theme_mod("branch_email_$branch_id");
    }

    // 4. Если $branch_id — число или строка с номером (например "5"), ищем CPT запись филиала
    if (empty($branch_email) && is_numeric($branch_id)) {
        $cpt_query = new WP_Query(array(
            'post_type'      => 'library_branch',
            'posts_per_page' => 1,
            's'              => '№' . $branch_id,
            'post_status'    => 'publish',
        ));
        if ($cpt_query->have_posts()) {
            $found_post = $cpt_query->posts[0];
            $meta_email = get_post_meta($found_post->ID, '_library_email', true);
            if (!empty($meta_email)) {
                $branch_email = $meta_email;
            }
            if (empty($branch_name)) {
                $branch_name = $found_post->post_title;
            }
        }
    }

    // 5. Определение имени филиала, если оно все еще не определено
    if (empty($branch_name)) {
        if ($branch_id === 'cgb') {
            $branch_name = 'Центральная городская библиотека (ЦГБ)';
        } elseif ($branch_id === 'cdb') {
            $branch_name = 'Центральная детская библиотека (ЦДБ)';
        } elseif (is_numeric($branch_id)) {
            $branch_name = 'Филиал №' . $branch_id;
        } else {
            $branch_name = 'Филиал: ' . $branch_id;
        }
    }

    // 6. Fallback, если email филиала не задан ни в CPT, ни в Customizer
    if (empty($branch_email) || !is_email($branch_email)) {
        $branch_email = get_theme_mod('renewal_fallback_email');
        if (empty($branch_email) || !is_email($branch_email)) {
            $branch_email = get_theme_mod('footer_email');
            if (empty($branch_email) || !is_email($branch_email)) {
                $branch_email = get_option('admin_email');
            }
        }
    }

    $subject = 'Заявка на продление книг: ' . $fio . ' (' . $branch_name . ')';

    $message = "
    <html>
    <head>
        <title>Заявка на продление книг</title>
    </head>
    <body style='font-family: sans-serif; color: #333; line-height: 1.5;'>
        <h2 style='color: #1c77d9;'>Новая заявка на продление</h2>
        <p><strong>ФИО читателя:</strong> " . esc_html($fio) . "</p>
        <p><strong>№ Читательского билета:</strong> " . esc_html($card_number) . "</p>
        <p><strong>Email читателя:</strong> <a href='mailto:" . esc_attr($email) . "'>" . esc_html($email) . "</a></p>
        <p><strong>Выбранный филиал:</strong> " . esc_html($branch_name) . "</p>
        <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
        <h3 style='color: #1e293b;'>Список книг:</h3>
        <p style='white-space: pre-wrap; background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;'>" . esc_html($books) . "</p>
    </body>
    </html>
    ";

    $headers = array('Content-Type: text/html; charset=UTF-8');
    $site_host = wp_parse_url(home_url(), PHP_URL_HOST);
    if (!empty($site_host)) {
        $headers[] = 'From: Продление книг онлайн <wordpress@' . sanitize_text_field($site_host) . '>';
    }
    $headers[] = 'Reply-To: ' . $fio . ' <' . $email . '>';

    $sent = wp_mail($branch_email, $subject, $message, $headers);

    if ($sent) {
        wp_send_json_success(['message' => 'Ваша заявка успешно отправлена!']);
    } else {
        wp_send_json_error(['message' => 'Ошибка отправки письма. Попробуйте позже.']);
    }
}
add_action('wp_ajax_city_library_send_book_renewal', 'city_library_send_book_renewal');
add_action('wp_ajax_nopriv_city_library_send_book_renewal', 'city_library_send_book_renewal');

// Note: Comments are disabled via named functions below (city_library_disable_comments_*)

/**
 * Modify main query for homepage and archives.
 */
function city_library_homepage_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (isset($_GET['news_archive'])) {
            $query->set('posts_per_page', 8);
        } elseif ($query->is_home()) {
            $news_count = get_theme_mod('news_count', 8);
            $query->set('posts_per_page', $news_count);
        } elseif ($query->is_archive() || $query->is_post_type_archive('post')) {
            $query->set('posts_per_page', 16);
        }
    }
}
add_action('pre_get_posts', 'city_library_homepage_query');

/**
 * Force load archive template for news archive view.
 */
function city_library_news_archive_template($template) {
    if (isset($_GET['news_archive'])) {
        $archive_template = locate_template('archive.php');
        if ($archive_template) {
            return $archive_template;
        }
    }
    return $template;
}
add_filter('template_include', 'city_library_news_archive_template');

/**
 * Register widget areas.
 */
function city_library_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Main Sidebar', 'city-library' ),
        'id'            => 'sidebar-1',
        'description'   => esc_html__( 'Add widgets here.', 'city-library' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s mb-8 p-6 bg-white rounded-2xl shadow-sm border border-slate-100">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-lg font-bold font-display mb-4 text-primary border-b border-slate-100 pb-2">',
        'after_title'   => '</h2>',
    ) );

    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(esc_html__('Footer %d', 'city-library'), $i),
            'id'            => 'footer-' . $i,
            'description'   => esc_html__('Add widgets here to appear in your footer.', 'city-library'),
            'before_widget' => '<div id="%1$s" class="widget %2$s space-y-4">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="font-bold mb-6 text-primary uppercase text-xs tracking-widest">',
            'after_title'   => '</h4>',
        ));
    }

    // Counters & Analytics Widget Area in Footer Bottom
    register_sidebar(array(
        'name'          => esc_html__('Счетчики и аналитика (Footer Bottom)', 'city-library'),
        'id'            => 'footer-counters',
        'description'   => esc_html__('Область в нижней строке подвала (рядом с копирайтом) для счетчиков посещаемости и информеров (ПРО.Культура.РФ, Яндекс.Метрика, LiveInternet).', 'city-library'),
        'before_widget' => '<div id="%1$s" class="footer-counter-item widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<span class="sr-only">',
        'after_title'   => '</span>',
    ));

    // Custom Widget Area for Shortcode
    register_sidebar( array(
        'name'          => esc_html__( 'Content Widgets (Shortcode)', 'city-library' ),
        'id'            => 'content-widgets',
        'description'   => esc_html__( 'Widgets added here can be displayed inside any page or post using the shortcode [city_library_widgets]', 'city-library' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-200">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title text-xl font-bold font-display mb-4 text-slate-800">',
        'after_title'   => '</h3>',
    ) );
}
add_action('widgets_init', 'city_library_widgets_init');

// Disable Gutenberg block widget editor to prevent JavaScript validation crashes in HTML blocks (counters, analytics)
add_filter('use_widgets_block_editor', '__return_false', 100);
add_filter('gutenberg_use_widgets_block_editor', '__return_false', 100);

/**
 * Shortcode to output the 'Content Widgets' sidebar anywhere in content.
 */
function city_library_widgets_shortcode() {
    ob_start();
    if ( is_active_sidebar( 'content-widgets' ) ) {
        echo '<div class="city-library-content-widgets-wrapper my-8">';
        dynamic_sidebar( 'content-widgets' );
        echo '</div>';
    }
    return ob_get_clean();
}
add_shortcode( 'city_library_widgets', 'city_library_widgets_shortcode' );

/**
 * Sanitize custom HTML/JS code for customizer fields (analytics, counters).
 * Allows raw HTML/scripts for administrators with edit_theme_options / unfiltered_html.
 */
function city_library_sanitize_custom_code($input) {
    if (current_user_can('edit_theme_options') || current_user_can('unfiltered_html')) {
        return $input;
    }
    return wp_kses_post($input);
}

/**
 * Customizer additions.
 */
function city_library_customize_register($wp_customize) {
    // Global Colors (AAA WYSIWYG)
    $wp_customize->add_section('global_colors_section', array(
        'title'    => __('Глобальные цвета (Theme)', 'city-library'),
        'priority' => 10,
    ));

    $wp_customize->add_setting('primary_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Основной цвет (Primary)', 'city-library'), 'section' => 'global_colors_section',
    )));

    $wp_customize->add_setting('secondary_color', array('default' => '#1A3C34', 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Вторичный цвет (Secondary)', 'city-library'), 'section' => 'global_colors_section',
    )));

    $wp_customize->add_setting('bg_body_color', array('default' => '#f6f8f6', 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bg_body_color', array(
        'label' => __('Цвет фона страницы', 'city-library'), 'section' => 'global_colors_section',
    )));

    // Page Background Section (Behind Blocks - Lead Web Designer Spec)
    $wp_customize->add_section('page_background_section', array(
        'title'       => __('Фон страницы (за блоками)', 'city-library'),
        'priority'    => 12,
        'description' => __('Глобальная настройка адаптивного фона сайта позади контентных блоков и карточек.', 'city-library'),
    ));

    $wp_customize->add_setting('global_page_bg_enable', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('global_page_bg_enable', array(
        'label'       => __('Включить кастомный фон позади блоков по умолчанию', 'city-library'),
        'description' => __('Будет применяться ко всем записям и страницам, если в их индивидуальных настройках не включено иное.', 'city-library'),
        'section'     => 'page_background_section',
        'type'        => 'checkbox',
    ));

    $wp_customize->add_setting('global_page_bg_color', array(
        'default'           => '#f6f8f6',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_page_bg_color', array(
        'label'   => __('Цвет фона страницы по умолчанию', 'city-library'),
        'section' => 'page_background_section',
    )));

    $wp_customize->add_setting('global_page_bg_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'global_page_bg_image', array(
        'label'       => __('Фоновое изображение / паттерн по умолчанию', 'city-library'),
        'section'     => 'page_background_section',
        'description' => __('Фон за карточками контента сайта.', 'city-library'),
    )));

    $wp_customize->add_setting('global_page_bg_mode', array(
        'default'           => 'fixed',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('global_page_bg_mode', array(
        'label'   => __('Режим фиксации по умолчанию', 'city-library'),
        'section' => 'page_background_section',
        'type'    => 'select',
        'choices' => array(
            'fixed'  => __('Фиксированный (Fixed Screen)', 'city-library'),
            'scroll' => __('Не фиксированный (Scroll)', 'city-library'),
            'repeat' => __('Бесшовный паттерн (Pattern Repeat)', 'city-library'),
        ),
    ));

    $wp_customize->add_setting('global_page_bg_fit', array(
        'default'           => 'cover',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('global_page_bg_fit', array(
        'label'   => __('Масштабирование по умолчанию', 'city-library'),
        'section' => 'page_background_section',
        'type'    => 'select',
        'choices' => array(
            'cover'   => __('Заполнить экран (Cover)', 'city-library'),
            'contain' => __('По размеру экрана (Contain)', 'city-library'),
            'auto'    => __('Оригинальный размер / Паттерн (Auto)', 'city-library'),
        ),
    ));

    $wp_customize->add_setting('global_page_bg_overlay_color', array(
        'default'           => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_page_bg_overlay_color', array(
        'label'   => __('Цвет затемнения / тонирования', 'city-library'),
        'section' => 'page_background_section',
    )));

    $wp_customize->add_setting('global_page_bg_overlay_opacity', array(
        'default'           => '0.0',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('global_page_bg_overlay_opacity', array(
        'label'       => __('Плотность затемнения (0.0 — 0.9)', 'city-library'),
        'description' => __('Например: 0.25 для мягкого тонирования фона.', 'city-library'),
        'section'     => 'page_background_section',
        'type'        => 'number',
        'input_attrs' => array('min' => 0, 'max' => 0.9, 'step' => 0.05),
    ));

    $wp_customize->add_setting('global_page_bg_card_style', array(
        'default'           => 'solid',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('global_page_bg_card_style', array(
        'label'   => __('Стиль карточки контента по умолчанию', 'city-library'),
        'section' => 'page_background_section',
        'type'    => 'select',
        'choices' => array(
            'solid' => __('Классический плотный белый (Solid White)', 'city-library'),
            'glass' => __('Премиальное матовое стекло (Glassmorphism)', 'city-library'),
            'warm'  => __('Книжная бумага (Warm Ivory)', 'city-library'),
            'dark'  => __('Тёмное контрастное стекло (Dark Glass)', 'city-library'),
        ),
    ));

    // Global Link Colors
    $wp_customize->add_setting('global_link_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_link_color', array(
        'label' => __('Глобальный цвет ссылок', 'city-library'), 'section' => 'global_colors_section',
    )));

    $wp_customize->add_setting('global_link_hover_color', array('default' => '#1668c2', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_link_hover_color', array(
        'label' => __('Глобальный цвет ссылок (при наведении)', 'city-library'), 'section' => 'global_colors_section',
    )));

    // Global Button Settings
    $wp_customize->add_section('global_buttons_section', array(
        'title'    => __('Глобальные настройки кнопок', 'city-library'),
        'priority' => 18,
    ));

    $wp_customize->add_setting('global_btn_bg_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_btn_bg_color', array(
        'label' => __('Основной цвет фона', 'city-library'), 'section' => 'global_buttons_section',
    )));

    $wp_customize->add_setting('global_btn_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_btn_text_color', array(
        'label' => __('Основной цвет текста', 'city-library'), 'section' => 'global_buttons_section',
    )));

    $wp_customize->add_setting('global_btn_hover_bg_color', array('default' => '#1668c2', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_btn_hover_bg_color', array(
        'label' => __('Цвет фона при наведении', 'city-library'), 'section' => 'global_buttons_section',
    )));

    $wp_customize->add_setting('global_btn_hover_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'global_btn_hover_text_color', array(
        'label' => __('Цвет текста при наведении', 'city-library'), 'section' => 'global_buttons_section',
    )));

    // Slider Buttons Settings (Navigation)
    $wp_customize->add_section('slider_buttons_section', array(
        'title'    => __('Кнопки слайдера (Навигация)', 'city-library'),
        'priority' => 18,
    ));

    $wp_customize->add_setting('slider_btn_bg_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'slider_btn_bg_color', array(
        'label' => __('Цвет фона кнопки', 'city-library'), 'section' => 'slider_buttons_section',
    )));

    $wp_customize->add_setting('slider_btn_icon_color', array('default' => '#334155', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'slider_btn_icon_color', array(
        'label' => __('Цвет иконки/стрелки', 'city-library'), 'section' => 'slider_buttons_section',
    )));

    $wp_customize->add_setting('slider_btn_hover_bg_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'slider_btn_hover_bg_color', array(
        'label' => __('Цвет фона при наведении', 'city-library'), 'section' => 'slider_buttons_section',
    )));

    $wp_customize->add_setting('slider_btn_hover_icon_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'slider_btn_hover_icon_color', array(
        'label' => __('Цвет иконки при наведении', 'city-library'), 'section' => 'slider_buttons_section',
    )));

    $wp_customize->add_setting('slider_btn_radius', array('default' => '9999px', 'sanitize_callback' => 'sanitize_text_field')); // Default circle
    $wp_customize->add_control('slider_btn_radius', array(
        'label' => __('Скругление углов', 'city-library'),
        'section' => 'slider_buttons_section',
        'type' => 'select',
        'choices' => array(
            '0' => 'Квадратные (0)',
            '0.5rem' => 'Скругленные (Small)',
            '1rem' => 'Скругленные (Medium)',
            '9999px' => 'Круглые (Circle)',
        ),
    ));


    // "Read More" / "Details" Buttons Settings
    $wp_customize->add_section('read_more_buttons_section', array(
        'title'    => __('Кнопки "Подробнее" / "Читать далее"', 'city-library'),
        'priority' => 18,
    ));

    $wp_customize->add_setting('read_more_btn_bg_color', array('default' => 'transparent', 'sanitize_callback' => 'sanitize_text_field')); // Can be transparent
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'read_more_btn_bg_color', array(
        'label' => __('Цвет фона кнопки', 'city-library'), 'section' => 'read_more_buttons_section',
    )));

    $wp_customize->add_setting('read_more_btn_text_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'read_more_btn_text_color', array(
        'label' => __('Цвет текста', 'city-library'), 'section' => 'read_more_buttons_section',
    )));

    $wp_customize->add_setting('read_more_btn_hover_bg_color', array('default' => 'transparent', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'read_more_btn_hover_bg_color', array(
        'label' => __('Цвет фона при наведении', 'city-library'), 'section' => 'read_more_buttons_section',
    )));

    $wp_customize->add_setting('read_more_btn_hover_text_color', array('default' => '#1668c2', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'read_more_btn_hover_text_color', array(
        'label' => __('Цвет текста при наведении', 'city-library'), 'section' => 'read_more_buttons_section',
    )));

    $wp_customize->add_setting('read_more_show_underline', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('read_more_show_underline', array(
        'label' => __('Подчеркивание ссылки', 'city-library'),
        'section' => 'read_more_buttons_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('read_more_btn_radius', array('default' => '9999px', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('read_more_btn_radius', array(
        'label' => __('Скругление углов', 'city-library'),
        'section' => 'read_more_buttons_section',
        'type' => 'select',
        'choices' => array(
            '0' => 'Квадратные (0)',
            '0.5rem' => 'Скругленные (Small)',
            '1rem' => 'Скругленные (Medium)',
            '9999px' => 'Круглые (Circle)',
        ),
    ));


    // News Settings
    $wp_customize->add_section('news_section', array(
        'title'      => __('Новости', 'city-library'),
        'priority'   => 25,
    ));
    $wp_customize->add_setting('news_count', array('default' => 8, 'sanitize_callback' => 'absint'));
    $wp_customize->add_control('news_count', array(
        'label' => __('Количество карточек новостей', 'city-library'),
        'description' => __('Количество новостей на главной странице.', 'city-library'),
        'section' => 'news_section',
        'type' => 'number',
        'input_attrs' => array('min' => 1, 'max' => 48, 'step' => 1)
    ));

    // Layout Settings
    $wp_customize->add_section('layout_section', array(
        'title'    => __('Настройки макета (Layout)', 'city-library'),
        'priority' => 19,
    ));

    // Default sidebar to false as requested
    $wp_customize->add_setting('show_sidebar', array('default' => false, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_sidebar', array(
        'label' => __('Показать сайдбар', 'city-library'),
        'section' => 'layout_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('global_border_radius', array('default' => '2rem', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('global_border_radius', array(
        'label' => __('Скругление углов (Global Border Radius)', 'city-library'),
        'section' => 'layout_section',
        'type' => 'select',
        'choices' => array(
            '0' => '0 (Sharp)',
            '0.5rem' => 'Small (0.5rem)',
            '1rem' => 'Medium (1rem)',
            '2rem' => 'Large (2rem - Default)',
        ),
    ));

    $wp_customize->add_setting('global_container_width', array('default' => '80%', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('global_container_width', array(
        'label' => __('Ширина контента (Desktop)', 'city-library'),
        'section' => 'layout_section',
        'type' => 'select',
        'choices' => array(
            '100%' => 'Full Width (100%)',
            '90%' => 'Wide (90%)',
            '80%' => 'Standard (80%)',
            '1280px' => 'Fixed (1280px)',
        ),
    ));

    // Single Post Settings
    $wp_customize->add_section('single_post_section', array(
        'title'       => __('Настройки статьи (Single Post)', 'city-library'),
        'priority'    => 20,
        'description' => __('Настройки внешнего вида и отображения элементов на странице отдельной статьи.', 'city-library'),
    ));

    $wp_customize->add_setting('show_single_featured_image', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('show_single_featured_image', array(
        'label'       => __('Отображать миниатюру записи в полной статье', 'city-library'),
        'description' => __('Показывать или скрывать главное изображение записи (featured image) в начале статьи.', 'city-library'),
        'section'     => 'single_post_section',
        'type'        => 'checkbox',
    ));

    // Branch Emails Section
    $wp_customize->add_section('branches_email_section', array(
        'title' => __('Email адреса филиалов', 'city-library'),
        'priority' => 140,
        'description' => __('Введите Email для каждого филиала для получения заявок на продление книг. Также email можно указать в карточке конкретной библиотеки в разделе «Библиотеки (Карта)».', 'city-library'),
    ));

    $wp_customize->add_setting('renewal_fallback_email', array('default' => '', 'sanitize_callback' => 'sanitize_email'));
    $wp_customize->add_control('renewal_fallback_email', array(
        'label' => __('Резервный Email для продления книг', 'city-library'),
        'description' => __('Используется, если у выбранного филиала не указан персональный email (по умолчанию используется email администратора сайта).', 'city-library'),
        'section' => 'branches_email_section',
        'type' => 'email',
    ));

    $branches_data = city_library_get_branches_data();
    foreach ($branches_data as $b) {
        $id = $b['id'];
        $name = $b['name'];
        $default_val = !empty($b['email']) ? $b['email'] : '';
        $wp_customize->add_setting("branch_email_$id", array('default' => $default_val, 'sanitize_callback' => 'sanitize_email'));
        $wp_customize->add_control("branch_email_$id", array(
            'label' => $name,
            'section' => 'branches_email_section',
            'type' => 'email',
        ));
    }

    // Book Renewal Button Settings
    $wp_customize->add_section('renewal_button_section', array(
        'title' => __('Кнопка продления книг', 'city-library'),
        'priority' => 145,
        'description' => __('Настройки внешнего вида и поведения кнопки продления книг.', 'city-library'),
    ));

    $wp_customize->add_setting('renewal_btn_text', array('default' => 'Продлить книгу', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('renewal_btn_text', array(
        'label' => __('Текст кнопки', 'city-library'),
        'section' => 'renewal_button_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('renewal_btn_bg_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'renewal_btn_bg_color', array(
        'label' => __('Цвет фона', 'city-library'),
        'section' => 'renewal_button_section',
    )));

    $wp_customize->add_setting('renewal_btn_text_color', array('default' => '#ffffff', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'renewal_btn_text_color', array(
        'label' => __('Цвет текста/иконки', 'city-library'),
        'section' => 'renewal_button_section',
    )));

    $wp_customize->add_setting('renewal_btn_radius', array('default' => 'medium', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('renewal_btn_radius', array(
        'label' => __('Скругление углов', 'city-library'),
        'section' => 'renewal_button_section',
        'type' => 'select',
        'choices' => array(
            'square' => 'Square (0)',
            'small' => 'Small (Rounded-md)',
            'medium' => 'Medium (Rounded-xl) [Default]',
            'circle' => 'Circle (Full)',
        ),
    ));

    $wp_customize->add_setting('renewal_btn_visibility', array('default' => 'all', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('renewal_btn_visibility', array(
        'label' => __('Видимость кнопки', 'city-library'),
        'section' => 'renewal_button_section',
        'type' => 'select',
        'choices' => array(
            'mobile-only' => 'Только на мобильных',
            'desktop-only' => 'Только на ПК',
            'all' => 'На всех устройствах',
            'hidden' => 'Скрыть полностью',
        ),
    ));

    $wp_customize->add_setting('renewal_btn_position', array('default' => 'bottom-right', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('renewal_btn_position', array(
        'label' => __('Позиция на экране', 'city-library'),
        'section' => 'renewal_button_section',
        'type' => 'select',
        'choices' => array(
            'bottom-right' => 'Справа внизу',
            'bottom-left' => 'Слева внизу',
        ),
    ));

    // Mobile Bottom Menu Section
    $wp_customize->add_section('mobile_menu_section', array(
        'title'    => __('Нижнее мобильное меню', 'city-library'),
        'priority' => 21,
    ));

    $wp_customize->add_setting('mobile_menu_bg_color', array('default' => '#ffffff', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mobile_menu_bg_color', array(
        'label' => __('Цвет фона', 'city-library'), 'section' => 'mobile_menu_section',
    )));

    $wp_customize->add_setting('mobile_menu_icon_color', array('default' => '#64748b', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mobile_menu_icon_color', array(
        'label' => __('Цвет иконок', 'city-library'), 'section' => 'mobile_menu_section',
    )));

    $wp_customize->add_setting('mobile_menu_active_color', array('default' => '#0b7930', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mobile_menu_active_color', array(
        'label' => __('Цвет активного элемента', 'city-library'), 'section' => 'mobile_menu_section',
    )));

    $wp_customize->add_setting('mobile_menu_font_color', array('default' => '#64748b', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'mobile_menu_font_color', array(
        'label' => __('Цвет шрифта (текст)', 'city-library'), 'section' => 'mobile_menu_section',
    )));

    $wp_customize->add_setting('mobile_menu_font_family', array('default' => 'Montserrat', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('mobile_menu_font_family', array(
        'label' => __('Шрифт меню', 'city-library'),
        'section' => 'mobile_menu_section',
        'type' => 'select',
        'choices' => array(
            'Montserrat' => 'Montserrat',
            'Inter' => 'Inter',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
        ),
    ));

    $wp_customize->add_setting('mobile_menu_icon_set', array('default' => 'outlined', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('mobile_menu_icon_set', array(
        'label' => __('Набор иконок', 'city-library'),
        'section' => 'mobile_menu_section',
        'type' => 'select',
        'choices' => array(
            'outlined' => 'Material Outlined (Контурные)',
            'rounded' => 'Material Rounded (Скругленные)',
            'sharp' => 'Material Sharp (Острые)',
        ),
    ));

    $wp_customize->add_setting('mobile_menu_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('mobile_menu_style', array(
        'label' => __('Стиль меню', 'city-library'),
        'section' => 'mobile_menu_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (Fixed Bottom)',
            'ios-blur' => 'iOS Blur (Translucent)',
            'material-pill' => 'Material Pill (Floating)',
            'neon-glow' => 'Neon Glow (Dark Mode)',
            'minimal-border' => 'Minimal Border (Top Line)',
            'floating-island' => 'Floating Island (Rounded)',
            'glassmorphism' => 'Glassmorphism (Frosted Glass)',
            'gradient-bar' => 'Gradient Bar (Color Flow)',
            'tab-bar' => 'Tab Bar (Android Style)',
            'floating-dock' => 'Floating Dock (macOS Style)',
            'minimal-icons' => 'Minimal Icons (No Text)',
            'text-only' => 'Text Only (No Icons)',
            'cyberpunk' => 'Cyberpunk (Futuristic)',
            'neumorphism' => 'Neumorphism (Soft UI)',
            'retro-pixel' => 'Retro Pixel (8-bit Vibe)',
            'sidebar-drawer' => 'Bottom Drawer (Slide Up)',
        ),
    ));

    $wp_customize->add_setting('mobile_menu_btn_style', array('default' => 'classic', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('mobile_menu_btn_style', array(
        'label' => __('Стиль кнопок меню', 'city-library'),
        'section' => 'mobile_menu_section',
        'type' => 'select',
        'choices' => array(
            'classic' => 'Classic (Icon + Text)',
            'minimal' => 'Minimal (Icon Only)',
            'bold' => 'Bold (Thick Icons)',
            'soft' => 'Soft (Pastel Colors)',
            'bubble' => 'Bubble (Circle BG)',
            'square' => 'Square (Rounded-md BG)',
            'underline' => 'Underline (Active State)',
            'glow' => 'Glow (Text Shadow)',
            'floating' => 'Floating (Lift on Hover)',
            'glass-btn' => 'Glass Button (Blur)',
        ),
    ));

    // Header Section
    $wp_customize->add_section('header_section', array(
        'title'    => __('Настройки шапки (Header)', 'city-library'),
        'priority' => 20,
    ));

    // Header Style
    $wp_customize->add_setting('header_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('header_style', array(
        'label' => __('Стиль шапки (Header Style)', 'city-library'),
        'section' => 'header_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (Left Logo, Right Menu)',
            'centered' => 'Centered (Logo Top, Menu Bottom)',
            'minimal' => 'Minimal (Logo Left, Menu Hidden/Hamburger)',
            'full-width' => 'Full Width (No Container)',
            'transparent-overlay' => 'Transparent Overlay (Absolute)',
            'floating' => 'Floating (Rounded, Detached)',
            'dark-mode' => 'Dark Mode (Inverted Colors)',
        ),
    ));

    // Menu Style
    $wp_customize->add_setting('menu_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('menu_style', array(
        'label' => __('Стиль меню (Menu Style)', 'city-library'),
        'section' => 'header_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (Simple Hover)',
            'underline' => 'Underline Animation',
            'pill' => 'Pill Background',
            'bracket' => 'Brackets [ Link ]',
            'bold' => 'Bold on Hover',
        ),
    ));

    $wp_customize->add_setting('header_bg_color', array('default' => '#ffffff', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_bg_color', array(
        'label' => __('Цвет фона шапки', 'city-library'), 'section' => 'header_section',
    )));

    $wp_customize->add_setting('header_text_color', array('default' => '#1A3C34', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_text_color', array(
        'label' => __('Цвет текста шапки', 'city-library'), 'section' => 'header_section',
    )));

     $wp_customize->add_setting('header_font_family', array('default' => 'Montserrat', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('header_font_family', array(
        'label' => __('Шрифт шапки', 'city-library'),
        'section' => 'header_section',
        'type' => 'select',
        'choices' => array(
            'Montserrat' => 'Montserrat',
            'Inter' => 'Inter',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
        ),
    ));

    // Header Content Settings (Title/Subtitle)
    $wp_customize->add_setting('header_subtitle', array('default' => 'Центральная городская', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('header_subtitle', array('label' => __('Подзаголовок (верхняя строка)', 'city-library'), 'section' => 'header_section', 'type' => 'text'));

    $wp_customize->add_setting('header_title', array('default' => 'Библиотека', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('header_title', array('label' => __('Название сайта (нижняя строка)', 'city-library'), 'section' => 'header_section', 'type' => 'text'));

    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title'    => __('Hero Section', 'city-library'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('show_hero_section', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_hero_section', array(
        'label' => __('Show Hero Section', 'city-library'),
        'section' => 'hero_section',
        'type' => 'checkbox',
    ));

    // ... other settings
    $wp_customize->add_setting('hero_badge_text', array('default' => 'Добро пожаловать в мир знаний', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('hero_badge_text', array('label' => __('Badge Text', 'city-library'), 'section' => 'hero_section', 'type' => 'text'));

    $wp_customize->add_setting('hero_title', array('default' => 'Твой мир, <span class="text-primary italic text-glow">Твоя</span> <br/>библиотека', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('hero_title', array('label' => __('Title', 'city-library'), 'section' => 'hero_section', 'type' => 'textarea'));

    $wp_customize->add_setting('hero_subtitle', array('default' => 'Центральная городская библиотека — пространство для открытий...', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('hero_subtitle', array('label' => __('Subtitle', 'city-library'), 'section' => 'hero_section', 'type' => 'textarea'));

    $wp_customize->add_setting('hero_primary_button_text', array('default' => 'АФИША МЕРОПРИЯТИЙ', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('hero_primary_button_text', array('label' => __('Primary Button Text', 'city-library'), 'section' => 'hero_section', 'type' => 'text'));

    $wp_customize->add_setting('hero_primary_button_link', array('default' => '#events', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('hero_primary_button_link', array('label' => __('Primary Button Link', 'city-library'), 'section' => 'hero_section', 'type' => 'url'));

    $wp_customize->add_setting('hero_secondary_button_text', array('default' => 'УЗНАТЬ БОЛЬШЕ', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('hero_secondary_button_text', array('label' => __('Secondary Button Text', 'city-library'), 'section' => 'hero_section', 'type' => 'text'));

    $wp_customize->add_setting('hero_secondary_button_link', array('default' => '#about', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('hero_secondary_button_link', array('label' => __('Secondary Button Link', 'city-library'), 'section' => 'hero_section', 'type' => 'url'));

    $wp_customize->add_setting('hero_background_image', array('default' => get_template_directory_uri() . '/images/hero-bg.jpg', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_background_image', array(
        'label' => __('Background Image', 'city-library'),
        'section' => 'hero_section',
    )));

    $wp_customize->add_setting('hero_overlay_color', array('default' => '#1a3c34', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_overlay_color', array(
        'label' => __('Цвет наложения (Overlay Color)', 'city-library'),
        'section' => 'hero_section',
    )));

    $wp_customize->add_setting('hero_bg_opacity', array('default' => '0.5', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('hero_bg_opacity', array(
        'label' => __('Прозрачность наложения (Overlay Opacity)', 'city-library'),
        'section' => 'hero_section',
        'type' => 'range',
        'input_attrs' => array('min' => 0, 'max' => 1, 'step' => 0.1),
    ));

    $wp_customize->add_setting('hero_align', array('default' => 'center', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('hero_align', array(
        'label' => __('Выравнивание текста (Alignment)', 'city-library'),
        'section' => 'hero_section',
        'type' => 'select',
        'choices' => array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
    ));

    $wp_customize->add_setting('hero_title_size', array('default' => 'text-5xl md:text-7xl lg:text-8xl', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('hero_title_size', array(
        'label' => __('Размер заголовка (Title Size)', 'city-library'),
        'section' => 'hero_section',
        'type' => 'select',
        'choices' => array(
            'text-4xl md:text-6xl lg:text-7xl' => 'Small',
            'text-5xl md:text-7xl lg:text-8xl' => 'Medium (Default)',
            'text-6xl md:text-8xl lg:text-9xl' => 'Large',
        ),
    ));

    // Footer Section
    $wp_customize->add_section('footer_section', array('title' => __('Footer', 'city-library'), 'priority' => 120));

    $wp_customize->add_setting('footer_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_style', array(
        'label' => __('Стиль футера (Footer Style)', 'city-library'),
        'section' => 'footer_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (Dark Simple)',
            'light-clean' => 'Light Clean (White BG)',
            'centered' => 'Centered Layout',
            'multi-column' => 'Multi-Column (Grid)',
            'minimal' => 'Minimal (Copyright Only)',
        ),
    ));

    $wp_customize->add_setting('footer_copyright', array('default' => '© ' . date('Y') . ' Центральная городская библиотека. Все права защищены.', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_copyright', array('label' => __('Copyright Text', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    $wp_customize->add_setting('footer_show_privacy', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('footer_show_privacy', array('label' => __('Показать Политику конфиденциальности', 'city-library'), 'section' => 'footer_section', 'type' => 'checkbox'));

    $wp_customize->add_setting('footer_privacy_link', array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('footer_privacy_link', array('label' => __('Privacy Policy Link', 'city-library'), 'section' => 'footer_section', 'type' => 'url'));

    $wp_customize->add_setting('footer_show_sitemap', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('footer_show_sitemap', array('label' => __('Показать Карту сайта', 'city-library'), 'section' => 'footer_section', 'type' => 'checkbox'));

    $wp_customize->add_setting('footer_sitemap_link', array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('footer_sitemap_link', array('label' => __('Sitemap Link', 'city-library'), 'section' => 'footer_section', 'type' => 'url'));

    $wp_customize->add_setting('footer_bg_color', array('default' => '#F1F5F9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_bg_color', array(
        'label' => __('Footer Background Color', 'city-library'), 'section' => 'footer_section',
    )));
    $wp_customize->add_setting('footer_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_text_color', array(
        'label' => __('Footer Text Color', 'city-library'), 'section' => 'footer_section',
    )));

    // New Footer Customizations
    $wp_customize->add_setting('footer_description', array('default' => '', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('footer_description', array('label' => __('Описание в футере', 'city-library'), 'section' => 'footer_section', 'type' => 'textarea'));

    $wp_customize->add_setting('footer_phone', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_phone', array('label' => __('Телефон', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    $wp_customize->add_setting('footer_email', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_email', array('label' => __('Email', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    $wp_customize->add_setting('footer_address', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_address', array('label' => __('Адрес', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    $wp_customize->add_setting('footer_work_hours', array('default' => "Пн-Пт: 9:00 - 20:00\nСб-Вс: 10:00 - 18:00", 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('footer_work_hours', array('label' => __('Режим работы', 'city-library'), 'section' => 'footer_section', 'type' => 'textarea'));

    $wp_customize->add_setting('footer_logo_text', array('default' => 'DIANA', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_logo_text', array('label' => __('Текст логотипа (если нет custom_logo)', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    $wp_customize->add_setting('footer_desc', array('default' => 'Центральная городская библиотека — пространство для открытий, творчества и вдохновения.', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('footer_desc', array('label' => __('Описание библиотеки', 'city-library'), 'section' => 'footer_section', 'type' => 'textarea'));

    // Column Visibility & Titles
    $wp_customize->add_setting('footer_show_brand', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('footer_show_brand', array('label' => __('Показать колонку бренда', 'city-library'), 'section' => 'footer_section', 'type' => 'checkbox'));

    $wp_customize->add_setting('footer_show_nav', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('footer_show_nav', array('label' => __('Показать навигацию', 'city-library'), 'section' => 'footer_section', 'type' => 'checkbox'));

    $wp_customize->add_setting('footer_nav_title', array('default' => 'Навигация', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_nav_title', array('label' => __('Заголовок навигации', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    $wp_customize->add_setting('footer_show_contacts', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('footer_show_contacts', array('label' => __('Показать контакты', 'city-library'), 'section' => 'footer_section', 'type' => 'checkbox'));

    $wp_customize->add_setting('footer_contacts_title', array('default' => 'Контакты', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_contacts_title', array('label' => __('Заголовок контактов', 'city-library'), 'section' => 'footer_section', 'type' => 'text'));

    // Social Links
    $wp_customize->add_setting('footer_social_vk', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('footer_social_vk', array('label' => __('VKontakte URL', 'city-library'), 'section' => 'footer_section', 'type' => 'url'));

    $wp_customize->add_setting('footer_social_telegram', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('footer_social_telegram', array('label' => __('Telegram URL', 'city-library'), 'section' => 'footer_section', 'type' => 'url'));

    $wp_customize->add_setting('footer_social_ok', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('footer_social_ok', array('label' => __('Odnoklassniki URL', 'city-library'), 'section' => 'footer_section', 'type' => 'url'));

    $wp_customize->add_setting('footer_social_youtube', array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('footer_social_youtube', array('label' => __('YouTube URL', 'city-library'), 'section' => 'footer_section', 'type' => 'url'));

    // Footer Map Section
    $wp_customize->add_section('footer_map_section', array(
        'title' => __('Яндекс Карта', 'city-library'),
        'priority' => 125,
        'description' => __('Настройки карты в футере.', 'city-library'),
    ));

    $wp_customize->add_setting('footer_show_map', array('default' => false, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('footer_show_map', array(
        'label' => __('Показать карту', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('footer_map_apikey', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_map_apikey', array(
        'label' => __('API Key (Optional for basic use)', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('footer_map_lat', array('default' => '56.162458', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_map_lat', array(
        'label' => __('Широта (Latitude)', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('footer_map_lon', array('default' => '40.470598', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_map_lon', array(
        'label' => __('Долгота (Longitude)', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('footer_map_zoom', array('default' => 15, 'sanitize_callback' => 'absint'));
    $wp_customize->add_control('footer_map_zoom', array(
        'label' => __('Масштаб (Zoom)', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'number',
        'input_attrs' => array('min' => 1, 'max' => 19, 'step' => 1),
    ));

    $wp_customize->add_setting('footer_map_height', array('default' => '300px', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_map_height', array(
        'label' => __('Высота карты (например, 300px)', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('footer_map_width_desktop', array('default' => '250px', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('footer_map_width_desktop', array(
        'label' => __('Ширина карты на ПК (Desktop Width)', 'city-library'),
        'section' => 'footer_map_section',
        'type' => 'text',
        'description' => 'Например: 250px, 300px, 100%'
    ));

    // ==========================================
    // Analytics & Counters Section («Счетчики и аналитика»)
    // ==========================================
    $wp_customize->add_section('analytics_section', array(
        'title'       => __('Счетчики и аналитика', 'city-library'),
        'priority'    => 126,
        'description' => __('Настройка счетчика «ПРО.Культура.РФ» (ЕИПСК / culture.ru), Яндекс.Метрики и кодов веб-аналитики.', 'city-library'),
    ));

    // 1. Включение счетчика ПРО.Культура.РФ
    $wp_customize->add_setting('pro_culture_enable', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('pro_culture_enable', array(
        'label'       => __('Включить счетчик «ПРО.Культура.РФ»', 'city-library'),
        'description' => __('Активирует интеграцию счетчика «ПРО.Культура.РФ» (ЕИПСК / culture.ru).', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'checkbox',
    ));

    // 2. ID учреждения в ПРО.Культура.РФ (ЕИПСК)
    $wp_customize->add_setting('pro_culture_id', array(
        'default'           => '1984',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('pro_culture_id', array(
        'label'       => __('ID учреждения в ПРО.Культура.РФ (ЕИПСК)', 'city-library'),
        'description' => __('Идентификатор учреждения или номер счетчика (например, 1984). При отсутствии отдельного HTML-кода будет выведен официальный бейдж с этим ID.', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'text',
    ));

    // 3. Полный HTML/JS код счетчика ПРО.Культура.РФ
    $wp_customize->add_setting('pro_culture_code', array(
        'default'           => '<script async src="https://culturaltracking.ru/static/js/spxl.js?pixelId=1984" data-pixel-id="1984"></script>',
        'sanitize_callback' => 'city_library_sanitize_custom_code',
    ));
    $wp_customize->add_control('pro_culture_code', array(
        'label'       => __('HTML/JS код счетчика «ПРО.Культура.РФ»', 'city-library'),
        'description' => __('Вставьте сюда готовый код счетчика, информера или виджета, полученный в личном кабинете PRO.Культура.РФ (ЕИПСК).', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'textarea',
    ));

    // 4. Позиция счетчика ПРО.Культура.РФ
    $wp_customize->add_setting('pro_culture_position', array(
        'default'           => 'footer-bottom',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('pro_culture_position', array(
        'label'       => __('Расположение счетчика «ПРО.Культура.РФ»', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'select',
        'choices'     => array(
            'footer-bottom' => __('В нижней строке подвала (рядом с копирайтом)', 'city-library'),
            'footer-body'   => __('Перед закрывающим тегом </body> (скрытый счетчик)', 'city-library'),
        ),
    ));

    // 5. Ссылка при клике на бейдж ПРО.Культура.РФ
    $wp_customize->add_setting('pro_culture_url', array(
        'default'           => 'https://www.culture.ru/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('pro_culture_url', array(
        'label'       => __('Ссылка при клике на бейдж', 'city-library'),
        'description' => __('Официальный публичный портал (по умолчанию https://www.culture.ru/) или прямая ссылка на страницу вашей библиотеки на портале Культура.РФ.', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'url',
    ));

    // 5. Произвольный код счетчиков в подвале (footer-bottom)
    $wp_customize->add_setting('analytics_footer_bottom_code', array(
        'default'           => '',
        'sanitize_callback' => 'city_library_sanitize_custom_code',
    ));
    $wp_customize->add_control('analytics_footer_bottom_code', array(
        'label'       => __('Код счетчиков в подвале (footer-bottom)', 'city-library'),
        'description' => __('HTML/JS код для видимых счетчиков и информеров (Яндекс.Метрика информер, Спутник, Рейтинг Mail.ru, LiveInternet), отображаемых в нижней полосе подвала.', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'textarea',
    ));

    // 6. Код счетчиков в <head>
    $wp_customize->add_setting('analytics_head_code', array(
        'default'           => '',
        'sanitize_callback' => 'city_library_sanitize_custom_code',
    ));
    $wp_customize->add_control('analytics_head_code', array(
        'label'       => __('Код счетчиков и скриптов в <head>', 'city-library'),
        'description' => __('Скрипты аналитики для секции <head> (Яндекс.Метрика, Google Analytics, Google Tag Manager).', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'textarea',
    ));

    // 7. Код счетчиков перед </body>
    $wp_customize->add_setting('analytics_footer_code', array(
        'default'           => '',
        'sanitize_callback' => 'city_library_sanitize_custom_code',
    ));
    $wp_customize->add_control('analytics_footer_code', array(
        'label'       => __('Код счетчиков и скриптов перед </body>', 'city-library'),
        'description' => __('Скрипты отслеживания и пиксели, загружаемые перед закрывающим тегом </body>.', 'city-library'),
        'section'     => 'analytics_section',
        'type'        => 'textarea',
    ));

    // Hero Button Colors
    $wp_customize->add_setting('hero_primary_btn_bg_color', array('default' => '#0b7930', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_primary_btn_bg_color', array(
        'label' => __('Primary Button BG', 'city-library'), 'section' => 'hero_section',
    )));
    $wp_customize->add_setting('hero_primary_btn_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_primary_btn_text_color', array(
        'label' => __('Primary Button Text', 'city-library'), 'section' => 'hero_section',
    )));
    $wp_customize->add_setting('hero_primary_btn_hover_bg_color', array('default' => '#d4af37', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_primary_btn_hover_bg_color', array(
        'label' => __('Primary Button Hover BG', 'city-library'), 'section' => 'hero_section',
    )));

    $wp_customize->add_setting('hero_secondary_btn_bg_color', array('default' => 'rgba(255, 255, 255, 0.1)', 'sanitize_callback' => 'sanitize_text_field')); // RGBA support
    $wp_customize->add_control('hero_secondary_btn_bg_color', array(
        'label' => __('Secondary Button BG', 'city-library'), 'section' => 'hero_section', 'type' => 'text',
    ));
     $wp_customize->add_setting('hero_secondary_btn_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'hero_secondary_btn_text_color', array(
        'label' => __('Secondary Button Text', 'city-library'), 'section' => 'hero_section',
    )));
    $wp_customize->add_setting('hero_secondary_btn_hover_bg_color', array('default' => 'rgba(255, 255, 255, 0.2)', 'sanitize_callback' => 'sanitize_text_field')); // RGBA support
    $wp_customize->add_control('hero_secondary_btn_hover_bg_color', array(
        'label' => __('Secondary Button Hover BG', 'city-library'), 'section' => 'hero_section', 'type' => 'text',
    ));


    // Partners Section
    $wp_customize->add_section('partners_section', array(
        'title'    => __('Our Partners', 'city-library'),
        'priority' => 110,
    ));

    $wp_customize->add_setting('show_partners_section', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_partners_section', array(
        'label' => __('Show Partners Section', 'city-library'),
        'section' => 'partners_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('partners_title', array('default' => 'Наши партнеры', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('partners_title', array('label' => __('Title', 'city-library'), 'section' => 'partners_section', 'type' => 'text'));

    $wp_customize->add_setting('partners_subtitle', array('default' => 'Мы гордимся сотрудничеством с ведущими организациями', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('partners_subtitle', array('label' => __('Subtitle', 'city-library'), 'section' => 'partners_section', 'type' => 'text'));

    $wp_customize->add_setting('partners_bg_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'partners_bg_color', array(
        'label' => __('Цвет фона блока', 'city-library'), 'section' => 'partners_section',
    )));

    // Partner Logo Size Settings
    $wp_customize->add_setting('partners_logo_size', array('default' => 'medium', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('partners_logo_size', array(
        'label' => __('Размер логотипов партнеров', 'city-library'),
        'section' => 'partners_section',
        'type' => 'select',
        'choices' => array(
            'xs' => 'XS (Very Small - h-8)',
            'sm' => 'S (Small - h-12)',
            'medium' => 'M (Medium - h-16) [Default]',
            'lg' => 'L (Large - h-20)',
            'xl' => 'XL (Extra Large - h-24)',
            '2xl' => '2XL (Huge - h-32)',
            '3xl' => '3XL (Gigantic - h-40)',
            'original' => 'Original (Auto)',
        ),
    ));

    // Partner Grid Layout Settings (2 rows of 4 vs 1 row)
    $wp_customize->add_setting('partners_layout', array('default' => '2_rows_4', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('partners_layout', array(
        'label' => __('Расположение логотипов (Десктоп)', 'city-library'),
        'section' => 'partners_section',
        'type' => 'select',
        'choices' => array(
            '2_rows_4' => __('2 ряда по 4 логотипа (Рекомендуется)', 'city-library'),
            '1_row'    => __('1 ряд (все логотипы в один ряд)', 'city-library'),
        ),
    ));

    for ($i = 1; $i <= 8; $i++) {
        $wp_customize->add_setting("partner_logo_$i", array('sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "partner_logo_$i", array(
            'label' => sprintf(__('Partner Logo %d', 'city-library'), $i),
            'section' => 'partners_section',
        )));
        $wp_customize->add_setting("partner_link_$i", array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("partner_link_$i", array('label' => sprintf(__('Partner Link %d', 'city-library'), $i), 'section' => 'partners_section', 'type' => 'url'));
    }

    // News Card Section
    $wp_customize->add_section('news_card_section', array(
        'title' => __('News Card Styles', 'city-library'),
        'priority' => 100,
    ));

    $wp_customize->add_setting('news_card_grid_bg_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_grid_bg_color', array(
        'label' => __('Grid: Card Background', 'city-library'), 'section' => 'news_card_section',
    )));
    $wp_customize->add_setting('news_card_grid_title_color', array('default' => '#1A3C34', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_grid_title_color', array(
        'label' => __('Grid: Title Color', 'city-library'), 'section' => 'news_card_section',
    )));
    $wp_customize->add_setting('news_card_grid_text_color', array('default' => '#334155', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_grid_text_color', array(
        'label' => __('Grid: Text Color', 'city-library'), 'section' => 'news_card_section',
    )));
    $wp_customize->add_setting('news_card_grid_link_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_grid_link_color', array(
        'label' => __('Grid: Link Color', 'city-library'), 'section' => 'news_card_section',
    )));

    $wp_customize->add_setting('news_card_list_bg_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_list_bg_color', array(
        'label' => __('List: Card Background', 'city-library'), 'section' => 'news_card_section',
    )));
    $wp_customize->add_setting('news_card_list_title_color', array('default' => '#1A3C34', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_list_title_color', array(
        'label' => __('List: Title Color', 'city-library'), 'section' => 'news_card_section',
    )));
    $wp_customize->add_setting('news_card_list_text_color', array('default' => '#334155', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_list_text_color', array(
        'label' => __('List: Text Color', 'city-library'), 'section' => 'news_card_section',
    )));
    $wp_customize->add_setting('news_card_list_link_color', array('default' => '#1c77d9', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'news_card_list_link_color', array(
        'label' => __('List: Link Color', 'city-library'), 'section' => 'news_card_section',
    )));

    // Typography Settings
    $wp_customize->add_section('typography_section', array(
        'title' => __('Typography', 'city-library'),
        'priority' => 20,
    ));
    $wp_customize->add_setting('heading_font', array('default' => 'Montserrat', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('heading_font', array(
        'label' => __('Heading Font', 'city-library'),
        'section' => 'typography_section',
        'type' => 'select',
        'choices' => array(
            'Montserrat' => 'Montserrat (Geometric)',
            'Inter' => 'Inter (Modern)',
            'Playfair Display' => 'Playfair Display (Journal)',
            'Merriweather' => 'Merriweather (Serif)',
        ),
    ));
    $wp_customize->add_setting('body_font', array('default' => 'Montserrat', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('body_font', array(
        'label' => __('Body Font', 'city-library'),
        'section' => 'typography_section',
        'type' => 'select',
        'choices' => array(
            'Montserrat' => 'Montserrat (Geometric)',
            'Inter' => 'Inter (Modern)',
            'Playfair Display' => 'Playfair Display (Journal)',
            'Merriweather' => 'Merriweather (Serif)',
        ),
    ));

    // Afisha (Events) Section
    $wp_customize->add_section('afisha_section', array(
        'title' => __('Afisha (Events)', 'city-library'),
        'priority' => 105,
    ));
    $wp_customize->add_setting('show_afisha_section', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_afisha_section', array(
        'label' => __('Show Afisha Section', 'city-library'),
        'section' => 'afisha_section',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('afisha_title', array('default' => 'Афиша Мероприятий', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('afisha_title', array('label' => __('Section Title', 'city-library'), 'section' => 'afisha_section', 'type' => 'text'));

    for ($i = 1; $i <= 5; $i++) {
        $wp_customize->add_setting("afisha_image_$i", array('sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "afisha_image_$i", array(
            'label' => sprintf(__('Event Image %d', 'city-library'), $i),
            'section' => 'afisha_section',
        )));
        $wp_customize->add_setting("afisha_title_$i", array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("afisha_title_$i", array('label' => sprintf(__('Event Title %d', 'city-library'), $i), 'section' => 'afisha_section', 'type' => 'text'));

        $wp_customize->add_setting("afisha_link_$i", array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("afisha_link_$i", array('label' => sprintf(__('Event Link %d', 'city-library'), $i), 'section' => 'afisha_section', 'type' => 'url'));

        $wp_customize->add_setting("afisha_ribbon_$i", array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("afisha_ribbon_$i", array('label' => sprintf(__('Ribbon Text %d (e.g. NEW)', 'city-library'), $i), 'section' => 'afisha_section', 'type' => 'text'));

        $wp_customize->add_setting("afisha_badge_$i", array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("afisha_badge_$i", array('label' => sprintf(__('Badge Text %d (e.g. Featured)', 'city-library'), $i), 'section' => 'afisha_section', 'type' => 'text'));

        $wp_customize->add_setting("afisha_date_$i", array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("afisha_date_$i", array('label' => sprintf(__('Event Date %d (e.g. 12 OCT)', 'city-library'), $i), 'section' => 'afisha_section', 'type' => 'text'));
    }

    $wp_customize->add_setting('afisha_bg_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('afisha_bg_style', array(
        'label' => __('Background Style', 'city-library'),
        'section' => 'afisha_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (SVG Pattern)',
            'gradient' => 'Modern Gradient',
        ),
    ));

    $wp_customize->add_setting('afisha_card_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('afisha_card_style', array(
        'label' => __('Стиль карточек слайдера (Card Style)', 'city-library'),
        'section' => 'afisha_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (Rounded, Shadow)',
            'card' => 'Classic Card (White Border)',
            'clean' => 'Clean (No Shadow, Minimal)',
            'overlay' => 'Full Overlay (Text on Image)',
            'glass' => 'Glassmorphism (Blur)',
            'gradient' => 'Gradient Overlay (Dark)',
            'brutalism' => 'Brutalism (Hard Borders)',
            'minimal-text' => 'Minimal Text Focus',
            'cyberpunk' => 'Cyberpunk (Neon Accents)',
            'rounded-image' => 'Circular Image (Portrait style)',
        ),
    ));

    $wp_customize->add_setting('afisha_font_family', array('default' => 'Montserrat', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('afisha_font_family', array(
        'label' => __('Шрифт заголовка афиши', 'city-library'),
        'section' => 'afisha_section',
        'type' => 'select',
        'choices' => array(
            'Montserrat' => 'Montserrat',
            'Inter' => 'Inter',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
            'Cinzel' => 'Cinzel',
            'MedievalSharp' => 'MedievalSharp',
            'Crimson Text' => 'Crimson Text',
            'Great Vibes' => 'Great Vibes',
            'Comforter' => 'Comforter',
            'Marck Script' => 'Marck Script',
        ),
    ));

    // Important Section
    $wp_customize->add_section('important_section', array(
        'title' => __('Важная информация (Блок)', 'city-library'),
        'priority' => 106,
    ));

    $wp_customize->add_setting('show_important_section', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_important_section', array(
        'label' => __('Показать блок "Важная информация"', 'city-library'),
        'section' => 'important_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('important_title', array('default' => 'Важная информация', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('important_title', array('label' => __('Заголовок', 'city-library'), 'section' => 'important_section', 'type' => 'text'));

    $wp_customize->add_setting('important_text', array('default' => 'Внимание! В связи с санитарным днем библиотека работает по измененному графику.', 'sanitize_callback' => 'wp_kses_post'));
    $wp_customize->add_control('important_text', array('label' => __('Текст', 'city-library'), 'section' => 'important_section', 'type' => 'textarea'));

    $wp_customize->add_setting('important_btn_text', array('default' => 'Подробнее', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('important_btn_text', array('label' => __('Текст кнопки', 'city-library'), 'section' => 'important_section', 'type' => 'text'));

    $wp_customize->add_setting('important_btn_link', array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('important_btn_link', array('label' => __('Ссылка кнопки', 'city-library'), 'section' => 'important_section', 'type' => 'url'));

    // Important Button Colors
    $wp_customize->add_setting('important_btn_bg_color', array('default' => '#0b7930', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'important_btn_bg_color', array(
        'label' => __('Цвет фона кнопки', 'city-library'), 'section' => 'important_section',
    )));
    $wp_customize->add_setting('important_btn_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'important_btn_text_color', array(
        'label' => __('Цвет текста кнопки', 'city-library'), 'section' => 'important_section',
    )));

    $wp_customize->add_setting('important_bg_color', array('default' => '#fef2f2', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'important_bg_color', array(
        'label' => __('Цвет фона блока', 'city-library'), 'section' => 'important_section',
    )));

    $wp_customize->add_setting('important_inter_block_text', array('default' => '', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('important_inter_block_text', array('label' => __('Текст между блоками', 'city-library'), 'section' => 'important_section', 'type' => 'textarea'));

    $wp_customize->add_setting('important_link_radius', array('default' => 'medium', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('important_link_radius', array(
        'label' => __('Скругление изображений ссылок', 'city-library'),
        'section' => 'important_section',
        'type' => 'select',
        'choices' => array(
            'none' => 'Square (None)',
            'small' => 'Small',
            'medium' => 'Medium (Default)',
            'full' => 'Circle (Full)',
        ),
    ));

    $wp_customize->add_setting('important_link_style', array('default' => 'default', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('important_link_style', array(
        'label' => __('Стиль изображений ссылок', 'city-library'),
        'section' => 'important_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default (Simple)',
            'shadow' => 'Drop Shadow',
            'border' => 'Bordered',
            'grayscale' => 'Grayscale Hover',
        ),
    ));

    $wp_customize->add_setting('important_links_image_orientation', array('default' => 'rectangular', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('important_links_image_orientation', array(
        'label' => __('Ориентация изображений', 'city-library'),
        'description' => __('Выберите пропорции для 8 изображений ссылок ниже.', 'city-library'),
        'section' => 'important_section',
        'type' => 'select',
        'choices' => array(
            'square' => 'Квадратные (1:1)',
            'horizontal' => 'Горизонтальные (16:9)',
            'rectangular' => 'Прямоугольные (4:3)',
        ),
    ));

    $wp_customize->add_setting('important_links_fit', array('default' => 'contain', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('important_links_fit', array(
        'label' => __('Режим отображения изображений', 'city-library'),
        'description' => __('Выберите "Вмещать (без обрезки)", чтобы картинки полностью умещались в блок без обрезания краев.', 'city-library'),
        'section' => 'important_section',
        'type' => 'select',
        'choices' => array(
            'contain' => 'Вмещать полностью (без обрезки)',
            'cover' => 'Заполнять блок (с обрезкой)',
        ),
    ));

    // Important Section Links (8 items)
    for ($i = 1; $i <= 8; $i++) {
        $wp_customize->add_setting("important_link_image_$i", array('sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "important_link_image_$i", array(
            'label' => sprintf(__('Link Image %d', 'city-library'), $i),
            'section' => 'important_section',
        )));

        $wp_customize->add_setting("important_link_url_$i", array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("important_link_url_$i", array(
            'label' => sprintf(__('Link URL %d', 'city-library'), $i),
            'section' => 'important_section',
            'type' => 'url'
        ));
    }

    // Modal Popup Section
    $wp_customize->add_section('modal_section', array(
        'title' => __('Модальные окна', 'city-library'),
        'priority' => 120,
    ));

    $wp_customize->add_setting('show_modal', array('default' => false, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_modal', array(
        'label' => __('Включить всплывающее окно', 'city-library'),
        'section' => 'modal_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('modal_image', array('sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'modal_image', array(
        'label' => __('Изображение', 'city-library'),
        'section' => 'modal_section',
    )));

    $wp_customize->add_setting('modal_video', array('sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'modal_video', array(
        'label' => __('Видео файл (заменяет изображение)', 'city-library'),
        'section' => 'modal_section',
        'mime_type' => 'video',
    )));

    $wp_customize->add_setting('modal_title', array('default' => 'Специальное предложение!', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('modal_title', array('label' => __('Заголовок', 'city-library'), 'section' => 'modal_section', 'type' => 'text'));

    $wp_customize->add_setting('modal_text', array('default' => 'Подпишитесь на нашу рассылку новостей.', 'sanitize_callback' => 'city_library_sanitize_html'));
    $wp_customize->add_control('modal_text', array('label' => __('Текст (HTML/Video)', 'city-library'), 'section' => 'modal_section', 'type' => 'textarea'));

    $wp_customize->add_setting('modal_delay', array('default' => 3000, 'sanitize_callback' => 'absint'));
    $wp_customize->add_control('modal_delay', array('label' => __('Задержка (мс)', 'city-library'), 'section' => 'modal_section', 'type' => 'number'));

    // Featured Cards Section
    $wp_customize->add_section('featured_cards_section', array(
        'title' => __('Выделенные карточки (Featured Cards)', 'city-library'),
        'priority' => 103,
    ));

    $wp_customize->add_setting('show_featured_cards', array('default' => false, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_featured_cards', array(
        'label' => __('Показать блок', 'city-library'),
        'section' => 'featured_cards_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('featured_cards_title', array('default' => 'Наши направления', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('featured_cards_title', array('label' => __('Заголовок блока', 'city-library'), 'section' => 'featured_cards_section', 'type' => 'text'));

    $wp_customize->add_setting('featured_cards_design', array('default' => 'design-1', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('featured_cards_design', array(
        'label' => __('Дизайн карточек', 'city-library'),
        'section' => 'featured_cards_section',
        'type' => 'select',
        'choices' => array(
            'design-1' => 'Дизайн 1 (Стандартный с рамкой)',
            'design-2' => 'Дизайн 2 (Текст поверх изображения)',
            'design-3' => 'Дизайн 3 (Минималистичный без фона)',
            'design-4' => 'Дизайн 4 (С тенью и скруглением)',
            'design-5' => 'Дизайн 5 (Градиентный фон)',
            'design-6' => 'Дизайн 6 (Glassmorphism)',
            'design-7' => 'Дизайн 7 (Тонкая линия сверху)',
            'design-8' => 'Дизайн 8 (Сплошная заливка, иконки)',
            'design-9' => 'Дизайн 9 (Крупный заголовок, без фото)',
            'design-10' => 'Дизайн 10 (Стиль Polaroid)',
            'design-11' => 'Дизайн 11 (Только фото, адаптивное)',
            'design-12' => 'Дизайн 12 (Двухцветный Duotone)',
            'design-13' => 'Дизайн 13 (Неоморфизм / Выпуклый)',
            'design-14' => 'Дизайн 14 (Брутализм / Журнальный)',
            'design-15' => 'Дизайн 15 (Круглые блоки)',
            'design-16' => 'Дизайн 16 (Черно-белый контраст)',
            'design-17' => 'Дизайн 17 (Абстрактные формы)',
            'design-18' => 'Дизайн 18 (Элегантная типографика)',
            'design-19' => 'Дизайн 19 (Киберпанк / Неон)',
            'design-20' => 'Дизайн 20 (Смещенные блоки)'
        ),
    ));

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("fc_image_$i", array('sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "fc_image_$i", array(
            'label' => sprintf(__('Картинка карточки %d', 'city-library'), $i),
            'section' => 'featured_cards_section',
        )));

        $wp_customize->add_setting("fc_title_$i", array('default' => sprintf('Карточка %d', $i), 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("fc_title_$i", array('label' => sprintf(__('Заголовок карточки %d', 'city-library'), $i), 'section' => 'featured_cards_section', 'type' => 'text'));

        $wp_customize->add_setting("fc_desc_$i", array('default' => 'Краткое описание карточки.', 'sanitize_callback' => 'sanitize_textarea_field'));
        $wp_customize->add_control("fc_desc_$i", array('label' => sprintf(__('Описание карточки %d', 'city-library'), $i), 'section' => 'featured_cards_section', 'type' => 'textarea'));

        $wp_customize->add_setting("fc_link_$i", array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("fc_link_$i", array('label' => sprintf(__('Ссылка карточки %d', 'city-library'), $i), 'section' => 'featured_cards_section', 'type' => 'url'));
    }

    // Promo Section
    $wp_customize->add_section('promo_section', array(
        'title' => __('Промо Блок (Promo)', 'city-library'),
        'priority' => 104,
    ));

    $wp_customize->add_setting('show_promo_section', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_promo_section', array(
        'label' => __('Показать Промо Блок', 'city-library'),
        'section' => 'promo_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('promo_image', array('sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'promo_image', array(
        'label' => __('Изображение (400x300)', 'city-library'),
        'section' => 'promo_section',
    )));

    $wp_customize->add_setting('promo_title', array('default' => 'Добро пожаловать', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('promo_title', array('label' => __('Заголовок', 'city-library'), 'section' => 'promo_section', 'type' => 'text'));

    $wp_customize->add_setting('promo_text', array('default' => 'Узнайте больше о наших услугах и мероприятиях.', 'sanitize_callback' => 'city_library_sanitize_html'));
    $wp_customize->add_control('promo_text', array('label' => __('Текст', 'city-library'), 'section' => 'promo_section', 'type' => 'textarea'));

    // Promo Section Link Settings
    $wp_customize->add_setting('promo_btn_text', array('default' => 'Подробнее', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('promo_btn_text', array('label' => __('Текст кнопки', 'city-library'), 'section' => 'promo_section', 'type' => 'text'));

    $wp_customize->add_setting('promo_link', array('default' => '#', 'sanitize_callback' => 'esc_url_raw'));
    $wp_customize->add_control('promo_link', array('label' => __('Ссылка', 'city-library'), 'section' => 'promo_section', 'type' => 'url'));

    // Promo Button Colors
    $wp_customize->add_setting('promo_btn_bg_color', array('default' => '#0b7930', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'promo_btn_bg_color', array(
        'label' => __('Цвет фона кнопки', 'city-library'), 'section' => 'promo_section',
    )));
    $wp_customize->add_setting('promo_btn_text_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'promo_btn_text_color', array(
        'label' => __('Цвет текста кнопки', 'city-library'), 'section' => 'promo_section',
    )));

    $wp_customize->add_setting('promo_btn_hover_bg_color', array('default' => '#096328', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'promo_btn_hover_bg_color', array(
        'label' => __('Цвет фона кнопки (Hover)', 'city-library'), 'section' => 'promo_section',
    )));

    // Animation Settings
    $wp_customize->add_section('animation_section', array(
        'title' => __('Настройки анимаций', 'city-library'),
        'priority' => 150,
    ));

    $wp_customize->add_setting('enable_animations', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('enable_animations', array(
        'label' => __('Включить анимации при прокрутке', 'city-library'),
        'section' => 'animation_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('animation_type', array('default' => 'fade-up', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('animation_type', array(
        'label' => __('Тип анимации', 'city-library'),
        'section' => 'animation_section',
        'type' => 'select',
        'choices' => array(
            'fade-up' => 'Fade Up (Default)',
            'fade-down' => 'Fade Down',
            'fade-left' => 'Fade Left',
            'fade-right' => 'Fade Right',
            'zoom-in' => 'Zoom In',
            'zoom-out' => 'Zoom Out',
            'flip-up' => 'Flip Up',
            'slide-up' => 'Slide Up (Bounce)',
            'blur-in' => 'Blur In',
            'rotate-in' => 'Rotate In',
        ),
    ));

    // ===== INFO CARDS SECTION =====
    $wp_customize->add_section('info_cards_section', array(
        'title'    => __('Инфо-карточки под Hero', 'city-library'),
        'priority' => 155,
    ));

    $wp_customize->add_setting('show_info_cards', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('show_info_cards', array(
        'label' => __('Показать инфо-карточки', 'city-library'),
        'section' => 'info_cards_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('info_cards_count', array('default' => 5, 'sanitize_callback' => 'absint'));
    $wp_customize->add_control('info_cards_count', array(
        'label' => __('Количество карточек (1-5)', 'city-library'),
        'section' => 'info_cards_section',
        'type' => 'number',
        'input_attrs' => array('min' => 1, 'max' => 5, 'step' => 1),
    ));

    $wp_customize->add_setting('info_cards_bg_color', array('default' => '#FFFFFF', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'info_cards_bg_color', array(
        'label' => __('Цвет фона карточек', 'city-library'), 'section' => 'info_cards_section',
    )));

    $wp_customize->add_setting('info_cards_border_color', array('default' => '#e2e8f0', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'info_cards_border_color', array(
        'label' => __('Цвет рамки карточек', 'city-library'), 'section' => 'info_cards_section',
    )));

    $wp_customize->add_setting('info_cards_icon_bg', array('default' => 'rgba(11, 121, 48, 0.1)', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'info_cards_icon_bg', array(
        'label' => __('Цвет фона иконки', 'city-library'), 'section' => 'info_cards_section',
    )));

    $wp_customize->add_setting('info_cards_icon_color', array('default' => '#0b7930', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'info_cards_icon_color', array(
        'label' => __('Цвет иконки', 'city-library'), 'section' => 'info_cards_section',
    )));

    $wp_customize->add_setting('info_cards_title_color', array('default' => '#0f172a', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'info_cards_title_color', array(
        'label' => __('Цвет заголовка', 'city-library'), 'section' => 'info_cards_section',
    )));

    $wp_customize->add_setting('info_cards_desc_color', array('default' => '#64748b', 'sanitize_callback' => 'sanitize_hex_color'));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'info_cards_desc_color', array(
        'label' => __('Цвет описания', 'city-library'), 'section' => 'info_cards_section',
    )));

    $wp_customize->add_setting('info_cards_font_family', array('default' => 'Inter', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('info_cards_font_family', array(
        'label' => __('Шрифт карточек', 'city-library'),
        'section' => 'info_cards_section',
        'type' => 'select',
        'choices' => array(
            'Inter' => 'Inter',
            'Montserrat' => 'Montserrat',
            'Merriweather' => 'Merriweather',
        ),
    ));

    $wp_customize->add_setting('info_cards_font_size_title', array('default' => 14, 'sanitize_callback' => 'absint'));
    $wp_customize->add_control('info_cards_font_size_title', array(
        'label' => __('Размер шрифта заголовка (px)', 'city-library'),
        'section' => 'info_cards_section',
        'type' => 'number',
        'input_attrs' => array('min' => 10, 'max' => 24, 'step' => 1),
    ));

    $wp_customize->add_setting('info_cards_font_size_desc', array('default' => 12, 'sanitize_callback' => 'absint'));
    $wp_customize->add_control('info_cards_font_size_desc', array(
        'label' => __('Размер шрифта описания (px)', 'city-library'),
        'section' => 'info_cards_section',
        'type' => 'number',
        'input_attrs' => array('min' => 8, 'max' => 18, 'step' => 1),
    ));

    $default_cards = array(
        array('title' => 'Электронный каталог', 'desc' => 'Поиск книг в каталоге', 'icon' => 'search', 'link' => '#'),
        array('title' => 'AI-библиотекарь', 'desc' => 'Задайте вопрос', 'icon' => 'smart_toy', 'link' => '#'),
        array('title' => 'Читательский билет', 'desc' => 'Электронный билет', 'icon' => 'credit_card', 'link' => '#'),
        array('title' => 'Поддержка', 'desc' => 'Ответим на вопросы', 'icon' => 'support_agent', 'link' => '#'),
        array('title' => 'Режим чтения', 'desc' => 'Удобный просмотр', 'icon' => 'menu_book', 'link' => '#'),
    );

    for ($i = 1; $i <= 5; $i++) {
        $default = $default_cards[$i - 1];

        $wp_customize->add_setting("info_card_{$i}_title", array(
            'default'           => $default['title'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("info_card_{$i}_title", array(
            'label'   => sprintf(__('Карточка %d: Заголовок', 'city-library'), $i),
            'section' => 'info_cards_section',
            'type'    => 'text',
        ));

        $wp_customize->add_setting("info_card_{$i}_desc", array(
            'default'           => $default['desc'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("info_card_{$i}_desc", array(
            'label'   => sprintf(__('Карточка %d: Описание', 'city-library'), $i),
            'section' => 'info_cards_section',
            'type'    => 'text',
        ));

        $wp_customize->add_setting("info_card_{$i}_icon", array(
            'default'           => $default['icon'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("info_card_{$i}_icon", array(
            'label'       => sprintf(__('Карточка %d: Иконка (Material Symbols)', 'city-library'), $i),
            'description' => sprintf(__('Название иконки из <a href="%s" target="_blank">Material Symbols</a>', 'city-library'), 'https://fonts.google.com/icons'),
            'section'     => 'info_cards_section',
            'type'        => 'text',
        ));

        $wp_customize->add_setting("info_card_{$i}_link", array(
            'default'           => $default['link'],
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("info_card_{$i}_link", array(
            'label'   => sprintf(__('Карточка %d: Ссылка', 'city-library'), $i),
            'section' => 'info_cards_section',
            'type'    => 'url',
        ));

        $wp_customize->add_setting("info_card_{$i}_image", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "info_card_{$i}_image", array(
            'label'       => sprintf(__('Карточка %d: Изображение (вместо иконки)', 'city-library'), $i),
            'description' => __('Если загружено, отображается вместо иконки. Макс. 200x200 пикселей.', 'city-library'),
            'section'     => 'info_cards_section',
        )));
    }
}
add_action('customize_register', 'city_library_customize_register');

/**
 * Helper to get animation classes.
 */
function city_library_get_animation_class() {
    if (get_theme_mod('enable_animations', true)) {
        $type = get_theme_mod('animation_type', 'fade-up');
        return 'animate-on-scroll aos-' . esc_attr($type);
    }
    return '';
}


/**
 * Disable comments.
 */
function city_library_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'city_library_disable_comments_post_types_support');

function city_library_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'city_library_disable_comments_status', 20, 2);
add_filter('pings_open', 'city_library_disable_comments_status', 20, 2);

function city_library_disable_comments_hide_existing_comments($comments) {
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'city_library_disable_comments_hide_existing_comments', 10, 2);

function city_library_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'city_library_disable_comments_admin_menu');

function city_library_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'city_library_disable_comments_admin_bar');


/**
 * Custom Walker for Nav Menu to apply Tailwind classes.
 */
class City_Library_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $link_classes = $depth === 0
            ? 'inline-flex items-center gap-1 px-1.5 xl:px-2 py-2 lg:landscape:py-1 xl:py-1.5 text-sm lg:landscape:text-xs xl:text-sm font-medium whitespace-normal lg:landscape:whitespace-nowrap transition-all duration-200 rounded-lg hover:bg-slate-100 hover:text-primary'
            : 'block w-full px-3 py-2 text-sm whitespace-normal lg:landscape:whitespace-nowrap transition-all duration-200 rounded-lg hover:bg-slate-100 hover:text-primary';

        $li_classes = 'relative py-0';
        if ($args->has_children) {
            $li_classes .= ' has-children';
        }
        $output .= '<li class="' . esc_attr($li_classes) . '">';

        if ($args->has_children) {
            $output .= '<div class="flex items-center justify-between gap-1 w-full">';
            $output .= '<a href="' . esc_url($item->url) . '" class="' . esc_attr($link_classes) . '">' . esc_html($item->title) . '</a>';
            $output .= '<button type="button" class="submenu-toggle p-2 min-w-[40px] min-h-[40px] flex items-center justify-center shrink-0 rounded-lg hover:bg-slate-100/80 lg:landscape:hidden focus:outline-none" aria-expanded="false" aria-label="Toggle submenu"><span class="material-symbols-outlined text-base transition-transform duration-300" aria-hidden="true">expand_more</span></button>';
            $output .= '<span class="material-symbols-outlined text-sm hidden lg:landscape:inline-block ml-0 text-slate-400 transition-all duration-200" aria-hidden="true">expand_more</span>';
            $output .= '</div>';
        } else {
            $output .= '<a href="' . esc_url($item->url) . '" class="' . esc_attr($link_classes) . '">' . esc_html($item->title) . '</a>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>";
    }

    function start_lvl(&$output, $depth = 0, $args = null) {
        $ul_classes = 'submenu hidden z-50 transition-all duration-200 ease-out space-y-0.5 pl-4 ml-2 border-l-2 border-slate-200/70 mt-1 lg:landscape:pl-0 lg:landscape:ml-0 lg:landscape:border-l-0 lg:landscape:mt-0';

        $output .= '<ul class="' . esc_attr($ul_classes) . '">';
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= "</ul>";
    }

    function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        $id_field = $this->db_fields['id'];
        if (is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        }
        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }
}


/**
 * Custom HTML sanitization to allow iframes.
 */
function city_library_sanitize_html($html) {
    global $allowedposttags;
    $allowed_html = $allowedposttags;
    $allowed_html['iframe'] = array(
        'src'             => true,
        'width'           => true,
        'height'          => true,
        'frameborder'     => true,
        'allow'           => true,
        'allowfullscreen' => true,
        'style'           => true,
    );
    $allowed_html['button'] = array(
        'class' => true,
        'onclick' => true,
    );
    $allowed_html['a']['class'] = true;
    $allowed_html['a']['target'] = true;

    // Allow Media
    $allowed_html['img'] = array(
        'src' => true,
        'alt' => true,
        'class' => true,
        'width' => true,
        'height' => true,
    );
    $allowed_html['video'] = array(
        'src' => true,
        'class' => true,
        'width' => true,
        'height' => true,
        'controls' => true,
        'autoplay' => true,
        'muted' => true,
        'loop' => true,
        'playsinline' => true,
    );
    $allowed_html['source'] = array(
        'src' => true,
        'type' => true,
    );
    $allowed_html['iframe'] = array(
        'src' => true,
        'width' => true,
        'height' => true,
        'frameborder' => true,
        'allow' => true,
        'allowfullscreen' => true,
        'style' => true,
        'class' => true,
    );

    return wp_kses($html, $allowed_html);
}

/**
 * AJAX handler for post view toggle.
 */
function load_posts_by_view() {
    check_ajax_referer('view_toggle_nonce', 'nonce');
    $view = sanitize_text_field($_POST['view']);
    $template_part = ($view === 'list') ? 'template-parts/content-post-card-list' : 'template-parts/content-post-card';

    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 10,
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part($template_part);
        }
    } else {
        echo '<p>' . esc_html__('No posts found.', 'city-library') . '</p>';
    }

    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_load_posts_by_view', 'load_posts_by_view');
add_action('wp_ajax_nopriv_load_posts_by_view', 'load_posts_by_view');

/**
* Add custom script to head to configure TailwindCSS
*/
function city_library_tailwind_config() {
    $heading_font = get_theme_mod('heading_font', 'Montserrat');
    $body_font = get_theme_mod('body_font', 'Montserrat');
    ?>
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#1c77d9",
                        secondary: "#1A3C34",
                        "background-light": "#f6f8f6",
                    },
                    fontFamily: {
                        display: "<?php echo esc_js($heading_font); ?>",
                        sans: ["<?php echo esc_js($body_font); ?>", "sans-serif"]
                    },
                }
            }
        }
    </script>
    <?php
}
add_action('wp_head', 'city_library_tailwind_config', 1);

/**
 * Generate custom CSS from Customizer settings.
 */
function city_library_dynamic_styles() {
    $btn_bg = get_theme_mod('global_btn_bg_color', '#1c77d9');
    $btn_text = get_theme_mod('global_btn_text_color', '#FFFFFF');
    $btn_hover_bg = get_theme_mod('global_btn_hover_bg_color', '#1668c2');
    $btn_hover_text = get_theme_mod('global_btn_hover_text_color', '#FFFFFF');

    // Slider Buttons
    $slider_btn_bg = get_theme_mod('slider_btn_bg_color', '#FFFFFF');
    $slider_btn_icon = get_theme_mod('slider_btn_icon_color', '#334155');
    $slider_btn_hover_bg = get_theme_mod('slider_btn_hover_bg_color', '#1c77d9');
    $slider_btn_hover_icon = get_theme_mod('slider_btn_hover_icon_color', '#FFFFFF');
    $slider_btn_radius = get_theme_mod('slider_btn_radius', '9999px');

    // Read More Buttons
    $read_more_bg = get_theme_mod('read_more_btn_bg_color', 'transparent');
    $read_more_text = get_theme_mod('read_more_btn_text_color', '#1c77d9');
    $read_more_hover_bg = get_theme_mod('read_more_btn_hover_bg_color', 'transparent');
    $read_more_hover_text = get_theme_mod('read_more_btn_hover_text_color', '#1668c2');
    $read_more_underline = get_theme_mod('read_more_show_underline', true) ? 'underline' : 'none';
    $read_more_radius = get_theme_mod('read_more_btn_radius', '9999px');

    $primary_color = get_theme_mod('primary_color', '#1c77d9');
    $secondary_color = get_theme_mod('secondary_color', '#1A3C34');
    $bg_body = get_theme_mod('bg_body_color', '#f6f8f6');
    $link_color = get_theme_mod('global_link_color', '#1c77d9');
    $link_hover_color = get_theme_mod('global_link_hover_color', '#1668c2');
    $radius = get_theme_mod('global_border_radius', '2rem');
    $width = get_theme_mod('global_container_width', '80%');

    // Mobile Menu
    $mob_menu_bg = get_theme_mod('mobile_menu_bg_color', '#ffffff');
    $mob_menu_icon = get_theme_mod('mobile_menu_icon_color', '#64748b');
    $mob_menu_active = get_theme_mod('mobile_menu_active_color', '#0b7930');
    $mob_menu_font_color = get_theme_mod('mobile_menu_font_color', '#64748b');
    $mob_menu_font = get_theme_mod('mobile_menu_font_family', 'Montserrat');
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr($primary_color); ?>;
            --secondary-color: <?php echo esc_attr($secondary_color); ?>;
            --bg-body: <?php echo esc_attr($bg_body); ?>;

            --global-link-color: <?php echo esc_attr($link_color); ?>;
            --global-link-hover-color: <?php echo esc_attr($link_hover_color); ?>;

            --mob-menu-bg: <?php echo esc_attr($mob_menu_bg); ?>;
            --mob-menu-icon: <?php echo esc_attr($mob_menu_icon); ?>;
            --mob-menu-active: <?php echo esc_attr($mob_menu_active); ?>;
            --mob-menu-font-color: <?php echo esc_attr($mob_menu_font_color); ?>;
            --mob-menu-font: "<?php echo esc_js($mob_menu_font); ?>", sans-serif;

            --btn-bg: <?php echo esc_attr($btn_bg); ?>;
            --btn-text: <?php echo esc_attr($btn_text); ?>;
            --btn-hover-bg: <?php echo esc_attr($btn_hover_bg); ?>;
            --btn-hover-text: <?php echo esc_attr($btn_hover_text); ?>;

            --slider-btn-bg: <?php echo esc_attr($slider_btn_bg); ?>;
            --slider-btn-icon: <?php echo esc_attr($slider_btn_icon); ?>;
            --slider-btn-hover-bg: <?php echo esc_attr($slider_btn_hover_bg); ?>;
            --slider-btn-hover-icon: <?php echo esc_attr($slider_btn_hover_icon); ?>;
            --slider-btn-radius: <?php echo esc_attr($slider_btn_radius); ?>;

            --read-more-bg: <?php echo esc_attr($read_more_bg); ?>;
            --read-more-text: <?php echo esc_attr($read_more_text); ?>;
            --read-more-hover-bg: <?php echo esc_attr($read_more_hover_bg); ?>;
            --read-more-hover-text: <?php echo esc_attr($read_more_hover_text); ?>;
            --read-more-underline: <?php echo esc_attr($read_more_underline); ?>;
            --read-more-radius: <?php echo esc_attr($read_more_radius); ?>;

            --global-radius: <?php echo esc_attr($radius); ?>;
        }

        /* Global Color Override for Tailwind Config (via CSS Var) */
        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .hover\:bg-primary:hover { background-color: var(--primary-color) !important; }
        .focus\:ring-primary:focus { --tw-ring-color: var(--primary-color) !important; }

        body {
            background-color: var(--bg-body) !important;
        }

        /* Global Links */
        /* Applying to general content areas to avoid breaking specific navs */
        .content-area a:not(.btn):not(.button):not(.read-more-btn):not(.slider-nav-btn):not(.wp-block-button__link):not(.important-btn):not(.promo-btn),
        .entry-content a:not(.btn):not(.button):not(.read-more-btn):not(.slider-nav-btn):not(.wp-block-button__link):not(.important-btn):not(.promo-btn),
        .text-link {
            color: var(--global-link-color);
            transition: color 0.3s ease;
        }
        .content-area a:not(.btn):not(.button):not(.read-more-btn):not(.slider-nav-btn):not(.wp-block-button__link):not(.important-btn):not(.promo-btn):hover,
        .entry-content a:not(.btn):not(.button):not(.read-more-btn):not(.slider-nav-btn):not(.wp-block-button__link):not(.important-btn):not(.promo-btn):hover,
        .text-link:hover {
            color: var(--global-link-hover-color);
        }

        /* Global Radius Apply */
        .rounded-\[2rem\], .rounded-\[2\.5rem\], .rounded-2xl {
            border-radius: var(--global-radius) !important;
        }

        /* Container Width Apply */
        @media (min-width: 1024px) {
            .lg\:max-w-\[80\%\] {
                max-width: <?php echo esc_attr($width); ?> !important;
            }
        }
        @media (min-width: 1024px) and (orientation: portrait) {
            .lg\:max-w-\[80\%\] {
                max-width: 94% !important;
            }
        }

        /* Global Buttons — scoped to content/form buttons only, not nav/UI chrome */
        .entry-content .button,
        .content-area input[type="button"],
        .content-area input[type="reset"],
        .content-area input[type="submit"],
        .wp-block-button__link,
        .global-btn {
            background-color: var(--btn-bg) !important;
            color: var(--btn-text) !important;
        }
        .entry-content .button:hover,
        .content-area input[type="button"]:hover,
        .content-area input[type="reset"]:hover,
        .content-area input[type="submit"]:hover,
        .wp-block-button__link:hover,
        .global-btn:hover {
            background-color: var(--btn-hover-bg) !important;
            color: var(--btn-hover-text) !important;
        }

        /* Slider Navigation Buttons */
        .slider-nav-btn {
            background-color: var(--slider-btn-bg) !important;
            color: var(--slider-btn-icon) !important;
            border-radius: var(--slider-btn-radius) !important;
        }
        .slider-nav-btn:hover {
            background-color: var(--slider-btn-hover-bg) !important;
            color: var(--slider-btn-hover-icon) !important;
        }
        .slider-nav-btn span {
            color: inherit !important;
        }

        /* Read More / Details Buttons */
        .read-more-btn {
            background-color: var(--read-more-bg) !important;
            color: var(--read-more-text) !important;
            text-decoration: var(--read-more-underline) !important;
            border-radius: var(--read-more-radius) !important;
            padding: 0.5rem 1rem !important; /* py-2 px-4 */
        }
        .read-more-btn:hover {
            background-color: var(--read-more-hover-bg) !important;
            color: var(--read-more-hover-text) !important;
        }
        .read-more-btn span,
        .news-card-link span { /* For arrows */
             color: inherit !important;
        }
        .news-card-link {
            color: #1c77d9 !important;
            transition: all 0.25s ease;
        }
        .news-card-link:hover {
            color: #1668c2 !important;
        }

        /* Header Settings */
        #masthead {
            background-color: <?php echo esc_attr(get_theme_mod('header_bg_color', '#ffffff')); ?> !important;
        }
        #masthead nav a, #masthead .material-symbols-outlined:not(#site-identity .material-symbols-outlined), #masthead p {
            color: <?php echo esc_attr(get_theme_mod('header_text_color', '#1A3C34')); ?> !important;
        }
        #masthead {
             font-family: "<?php echo esc_js(get_theme_mod('header_font_family', 'Montserrat')); ?>", sans-serif !important;
        }

        /* Hero Primary Button */
        #hero-primary-btn {
            background-color: <?php echo esc_attr(get_theme_mod('hero_primary_btn_bg_color', $btn_bg)); ?> !important;
            color: <?php echo esc_attr(get_theme_mod('hero_primary_btn_text_color', $btn_text)); ?> !important;
        }
        #hero-primary-btn:hover {
            background-color: <?php echo esc_attr(get_theme_mod('hero_primary_btn_hover_bg_color', $btn_hover_bg)); ?> !important;
        }

        /* Hero Secondary Button */
        #hero-secondary-btn {
            background-color: <?php echo esc_attr(get_theme_mod('hero_secondary_btn_bg_color', 'rgba(255, 255, 255, 0.1)')); ?> !important;
            color: <?php echo esc_attr(get_theme_mod('hero_secondary_btn_text_color', '#FFFFFF')); ?> !important;
        }
        #hero-secondary-btn:hover {
            background-color: <?php echo esc_attr(get_theme_mod('hero_secondary_btn_hover_bg_color', 'rgba(255, 255, 255, 0.2)')); ?> !important;
        }

        /* Promo Button */
        .promo-btn {
            background-color: <?php echo esc_attr(get_theme_mod('promo_btn_bg_color', $btn_bg)); ?> !important;
            color: <?php echo esc_attr(get_theme_mod('promo_btn_text_color', $btn_text)); ?> !important;
        }
        .promo-btn:hover {
            background-color: <?php echo esc_attr(get_theme_mod('promo_btn_hover_bg_color', $btn_hover_bg)); ?> !important;
        }

        /* Important Button */
        .important-btn {
            background-color: <?php echo esc_attr(get_theme_mod('important_btn_bg_color', $btn_bg)); ?> !important;
            color: <?php echo esc_attr(get_theme_mod('important_btn_text_color', $btn_text)); ?> !important;
        }
        .important-btn:hover {
            background-color: <?php echo esc_attr($btn_hover_bg); ?> !important; /* Fallback/Global Hover */
            opacity: 0.9;
        }

        /* Afisha Font */
        .afisha-custom-title {
            font-family: "<?php echo esc_js(get_theme_mod('afisha_font_family', 'Montserrat')); ?>", sans-serif !important;
        }

        /* Pagination Styling */
        .navigation.pagination {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
        }
        .nav-links {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .page-numbers {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.5rem;
            border-radius: 0.5rem;
            background-color: #fff;
            color: #1c77d9;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            text-decoration: none;
        }
        .page-numbers:hover {
            background-color: #1c77d9;
            color: #fff;
            border-color: #1c77d9;
            box-shadow: 0 4px 12px rgba(28, 119, 217, 0.25);
        }
        .page-numbers.current {
            background-color: #1c77d9;
            color: #fff;
            border-color: #1c77d9;
            box-shadow: 0 4px 12px rgba(28, 119, 217, 0.3);
        }
        .page-numbers.dots {
            border: none;
            background: transparent;
            color: #64748b;
        }

        /* Category Badges, Entry Meta & Post Navigation */
        .entry-category-badge,
        .single .entry-header span.bg-primary\/10,
        .single .entry-header span[class*="bg-primary"],
        .post-category-badge {
            background-color: rgba(28, 119, 217, 0.1) !important;
            color: #1c77d9 !important;
        }
        .entry-category-badge a,
        .single .entry-header span.bg-primary\/10 a,
        .single .entry-header span[class*="bg-primary"] a,
        .post-category-badge a {
            color: #1c77d9 !important;
            text-decoration: none !important;
        }
        .entry-category-badge a:hover,
        .single .entry-header span.bg-primary\/10 a:hover,
        .single .entry-header span[class*="bg-primary"] a:hover {
            color: #1668c2 !important;
        }
        .entry-meta .entry-meta-icon,
        .entry-meta .material-symbols-outlined,
        .entry-meta .material-symbols-outlined.text-primary {
            color: #1c77d9 !important;
        }
        .post-navigation-wrapper .nav-label {
            color: #1c77d9 !important;
        }
        .post-navigation-wrapper .nav-label .material-symbols-outlined {
            color: inherit !important;
        }
        .post-navigation-wrapper .nav-link:hover {
            border-color: #1c77d9 !important;
            box-shadow: 0 8px 25px rgba(28, 119, 217, 0.15) !important;
        }
        .post-navigation-wrapper .nav-link:hover .nav-title {
            color: #1c77d9 !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'city_library_dynamic_styles');

/**
 * Add disableRemotePlayback to video tags to prevent local network scanning prompts.
 */
function city_library_disable_remote_playback() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var videos = document.querySelectorAll('video');
            videos.forEach(function(video) {
                video.setAttribute('disableRemotePlayback', '');
                video.setAttribute('controlsList', 'nodownload noremoteplayback');
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'city_library_disable_remote_playback');

/**
 * Helper: Get Default Theme Logo URL.
 */
function city_library_get_default_logo_url() {
    return get_template_directory_uri() . '/assets/images/logo.jpg';
}

/**
 * Helper: Get Theme Logo URL with Customizer custom_logo fallback.
 */
function city_library_get_logo_url() {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_src = wp_get_attachment_image_src($custom_logo_id, 'full');
        if (!empty($logo_src[0])) {
            return $logo_src[0];
        }
    }
    return city_library_get_default_logo_url();
}

/**
 * Filter site_icon_url / get_site_icon_url:
 * Ensures WordPress core functions (wp_site_icon, has_site_icon, get_site_icon_url)
 * return our library icon if one has not yet been set in the WordPress Customizer.
 */
function city_library_fallback_site_icon_url($url, $size = 512, $blog_id = 0) {
    if (!empty($url)) {
        return $url;
    }

    // Check if Customizer has custom_logo
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_data = wp_get_attachment_image_src($custom_logo_id, $size >= 512 ? 'full' : array($size, $size));
        if (!empty($logo_data[0])) {
            return $logo_data[0];
        }
    }

    // Fallback to high-res square site icon
    $vk_square = get_template_directory() . '/assets/images/vk-icon-square.png';
    if (file_exists($vk_square)) {
        return get_template_directory_uri() . '/assets/images/vk-icon-square.png';
    }
    $site_icon_file = get_template_directory() . '/assets/images/site-icon.png';
    if (file_exists($site_icon_file)) {
        return get_template_directory_uri() . '/assets/images/site-icon.png';
    }

    return city_library_get_default_logo_url();
}
add_filter('get_site_icon_url', 'city_library_fallback_site_icon_url', 10, 3);
add_filter('site_icon_url', 'city_library_fallback_site_icon_url', 10, 3);

/**
 * Output Open Graph and VK (VKontakte) meta tags into wp_head.
 * Automatically resolves absolute URLs and falls back to theme logo/site_icon.
 */
function city_library_open_graph_meta_tags() {
    $site_title = get_bloginfo('name');
    $tagline    = get_bloginfo('description');

    if (is_front_page() || is_home()) {
        $title       = $site_title . ($tagline ? ' — ' . $tagline : '');
        $description = $tagline ? $tagline : $site_title;
        $url         = home_url('/');
        $type        = 'website';
    } elseif (is_singular()) {
        global $post;
        $title = get_the_title();
        if (has_excerpt()) {
            $description = get_the_excerpt();
        } elseif (!empty($post->post_content)) {
            $description = wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 35, '...');
        } else {
            $description = $tagline ? $tagline : $site_title;
        }
        $url  = get_permalink();
        $type = 'article';
    } elseif (is_category() || is_tag() || is_tax()) {
        $title       = single_term_title('', false) . ' — ' . $site_title;
        $description = term_description();
        if (empty($description)) {
            $description = $tagline ? $tagline : $site_title;
        }
        $url  = get_term_link(get_queried_object());
        $type = 'website';
    } elseif (is_archive()) {
        $title       = get_the_archive_title() . ' — ' . $site_title;
        $description = $tagline ? $tagline : $site_title;
        $url         = get_post_type_archive_link(get_post_type());
        $type        = 'website';
    } else {
        $title       = wp_get_document_title();
        $description = $tagline ? $tagline : $site_title;
        $url         = home_url(add_query_arg(array(), $GLOBALS['wp']->request ?? ''));
        $type        = 'website';
    }

    $title       = esc_attr(wp_strip_all_tags($title));
    $description = esc_attr(wp_strip_all_tags($description));
    $url         = esc_url($url);

    // Determine image (Featured image -> Custom Logo -> Site Icon -> Theme Default Logo)
    $image_url    = '';
    $image_width  = 0;
    $image_height = 0;

    if (is_singular() && has_post_thumbnail()) {
        $thumb_id   = get_post_thumbnail_id();
        $thumb_data = wp_get_attachment_image_src($thumb_id, 'full');
        if (!empty($thumb_data[0])) {
            $image_url    = $thumb_data[0];
            $image_width  = (int) $thumb_data[1];
            $image_height = (int) $thumb_data[2];
        }
    }

    if (empty($image_url)) {
        $custom_logo_id = get_theme_mod('custom_logo');
        if ($custom_logo_id) {
            $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
            if (!empty($logo_data[0])) {
                $image_url    = $logo_data[0];
                $image_width  = (int) $logo_data[1];
                $image_height = (int) $logo_data[2];
            }
        }
    }

    if (empty($image_url) && function_exists('has_site_icon') && has_site_icon()) {
        $site_icon_id = get_option('site_icon');
        if ($site_icon_id) {
            $icon_data = wp_get_attachment_image_src($site_icon_id, 'full');
            if (!empty($icon_data[0])) {
                $image_url    = $icon_data[0];
                $image_width  = (int) $icon_data[1];
                $image_height = (int) $icon_data[2];
            }
        }
    }

    if (empty($image_url)) {
        $og_vk_banner = get_template_directory() . '/assets/images/og-image-vk.jpg';
        if (file_exists($og_vk_banner)) {
            $image_url    = get_template_directory_uri() . '/assets/images/og-image-vk.jpg';
            $image_width  = 1200;
            $image_height = 630;
        } else {
            $image_url    = city_library_get_default_logo_url();
            $image_width  = 211;
            $image_height = 173;
        }
    }

    $image_url   = esc_url($image_url);
    $favicon_url = esc_url(get_template_directory_uri() . '/assets/images/favicon.ico');
    $apple_touch = esc_url(get_template_directory_uri() . '/assets/images/apple-touch-icon.png');
    ?>

    <!-- Open Graph & VK (VKontakte) Meta Tags -->
    <meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>" />
    <meta property="og:type" content="<?php echo esc_attr($type); ?>" />
    <meta property="og:title" content="<?php echo $title; ?>" />
    <meta property="og:description" content="<?php echo $description; ?>" />
    <meta property="og:url" content="<?php echo $url; ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr($site_title); ?>" />
    <meta property="og:image" content="<?php echo $image_url; ?>" />
    <?php if (is_ssl()) : ?>
    <meta property="og:image:secure_url" content="<?php echo $image_url; ?>" />
    <?php endif; ?>
    <?php if ($image_width && $image_height) : ?>
    <meta property="og:image:width" content="<?php echo $image_width; ?>" />
    <meta property="og:image:height" content="<?php echo $image_height; ?>" />
    <?php endif; ?>
    <!-- VKontakte Specific Link Preview Tags -->
    <meta name="vk:image" content="<?php echo $image_url; ?>" />
    <link rel="image_src" href="<?php echo $image_url; ?>" />
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $title; ?>" />
    <meta name="twitter:description" content="<?php echo $description; ?>" />
    <meta name="twitter:image" content="<?php echo $image_url; ?>" />
    <!-- Favicon & Mobile App Icons -->
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url(get_template_directory_uri() . '/favicon.ico'); ?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon-32x32.png'); ?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon-16x16.png'); ?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $apple_touch; ?>" />
    <link rel="shortcut icon" href="<?php echo $favicon_url; ?>" />
    <?php
}
add_action('wp_head', 'city_library_open_graph_meta_tags', 2);

/**
 * Output Schema.org JSON-LD structured data.
 */
function city_library_schema_json_ld() {
    $schema = [];

    if (is_front_page()) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Library',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'logo' => city_library_get_logo_url(),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => get_theme_mod('footer_address', ''),
                'addressLocality' => 'City', // Ideally dynamic
                'addressCountry' => 'RU'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => get_theme_mod('footer_phone', ''),
                'contactType' => 'customer service'
            ]
        ];
    } elseif (is_single()) {
        global $post;
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => get_the_title(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => [
                '@type' => 'Person',
                'name' => get_the_author()
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => city_library_get_logo_url()
                ]
            ],
            'description' => get_the_excerpt()
        ];
        if (has_post_thumbnail()) {
            $schema['image'] = [
                get_the_post_thumbnail_url($post->ID, 'full')
            ];
        }
    }

    if (!empty($schema)) {
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP) . '</script>';
    }
}
add_action('wp_head', 'city_library_schema_json_ld');

/**
 * AJAX handler for Voice Control: "Все библиотеки" Custom Map Fetch
 */
function city_library_get_map_shortcode() {
    check_ajax_referer('ai_chat_nonce', 'nonce');

    ob_start();
    // Render the `city_library_branches_map` shortcode
    echo do_shortcode('[city_library_branches_map]');

    $html = ob_get_clean();

    // Fallback if shortcode output is empty
    if (empty(trim($html))) {
        $html = '<div class="p-8 text-center text-slate-500 bg-slate-50 rounded-2xl">
            <span class="material-symbols-outlined text-4xl mb-2 text-slate-400">map</span>
            <p>Карта со всеми филиалами загружается...</p>
            <p class="text-sm mt-2">Пожалуйста, перейдите в раздел "Контакты" для просмотра.</p>
        </div>';
    }

    wp_send_json_success(['html' => $html]);
}
add_action('wp_ajax_city_library_get_map_shortcode', 'city_library_get_map_shortcode');
add_action('wp_ajax_nopriv_city_library_get_map_shortcode', 'city_library_get_map_shortcode');

// Enqueue Chat Theme CSS
function city_library_enqueue_chat_themes() {
    if (get_theme_mod('enable_ai_librarian', true)) {
        wp_enqueue_style('ai-chat-themes', get_template_directory_uri() . '/css/ai-chat-themes.css', array(), wp_get_theme()->get('Version'));
    }
}
add_action('wp_enqueue_scripts', 'city_library_enqueue_chat_themes', 20);

/**
 * Render PRO.Культура.РФ counter/badge based on Customizer settings.
 *
 * @param string $context 'footer-bottom' or 'footer-body'
 */
function city_library_render_pro_culture_counter($context = 'footer-bottom') {
    if (!get_theme_mod('pro_culture_enable', true)) {
        return;
    }

    $position = get_theme_mod('pro_culture_position', 'footer-bottom');
    if ($position !== $context) {
        return;
    }

    $code = get_theme_mod('pro_culture_code', '<script async src="https://culturaltracking.ru/static/js/spxl.js?pixelId=1984" data-pixel-id="1984"></script>');
    $id   = get_theme_mod('pro_culture_id', '1984');

    // Automatically extract pixel ID from tracking code if empty
    if (empty($id) && !empty($code)) {
        if (preg_match('/(?:pixelId|pixel-id)=["\']?(\d+)/i', $code, $m)) {
            $id = $m[1];
        }
    }

    if (empty($code) && empty($id)) {
        return;
    }

    echo '<div class="pro-culture-counter-wrap footer-counter-item inline-flex items-center gap-2" data-source="pro-culture">';

    // 1. Output the tracking code (executes culturaltracking.ru pixel to collect visits)
    if (!empty($code)) {
        echo $code;
    }

    // 2. Check if the provided code already has visible HTML/IMG content
    $has_visual = false;
    if (!empty($code)) {
        $cleaned = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $code);
        $cleaned = preg_replace('#<noscript(.*?)>(.*?)</noscript>#is', '', $cleaned);
        if (preg_match('/<(?:img|svg|picture|canvas)|[a-zA-Zа-яА-Я0-9]{3,}/u', trim($cleaned))) {
            $has_visual = true;
        }
    }

    // 3. If code is an invisible tracking script (like spxl.js), ALSO display the official vector badge!
    if (!$has_visual) {
        $badge_id = !empty($id) ? $id : '1984';
        $id_str = !empty($badge_id) ? ' № ' . esc_html($badge_id) : '';
        $badge_url = get_theme_mod('pro_culture_url', 'https://www.culture.ru/');
        if (empty($badge_url)) {
            $badge_url = 'https://www.culture.ru/';
        }
        ?>
        <a href="<?php echo esc_url($badge_url); ?>" target="_blank" rel="noopener noreferrer" class="pro-culture-badge group inline-flex items-center transition-all duration-300 hover:scale-105" title="Участник проекта «ПРО.Культура.РФ»<?php echo $id_title; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130 31" width="130" height="31" class="h-[31px] w-auto inline-block drop-shadow-sm">
                <rect x="0.5" y="0.5" width="129" height="30" rx="5" fill="#ffffff" stroke="#cbd5e1" stroke-width="1"/>
                <rect x="4" y="5" width="30" height="21" rx="3.5" fill="#e11d48"/>
                <text x="19" y="19" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" font-size="10" font-weight="900" fill="#ffffff" text-anchor="middle" letter-spacing="0.5">ПРО</text>
                <text x="38" y="14.5" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" font-size="8.5" font-weight="800" fill="#0f172a" letter-spacing="0.2">КУЛЬТУРА</text>
                <text x="38" y="24" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" font-size="7.5" font-weight="800" fill="#e11d48">.РФ</text>
                <text x="60" y="24" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" font-size="6.5" font-weight="600" fill="#64748b"><?php echo $id_str; ?></text>
            </svg>
        </a>
        <?php
    }

    echo '</div>';
}

/**
 * Render custom footer bottom counters code.
 */
function city_library_render_footer_bottom_counters() {
    $counters_code = get_theme_mod('analytics_footer_bottom_code', '');
    if (!empty($counters_code)) {
        echo '<div class="custom-bottom-counters footer-counter-item">';
        echo $counters_code;
        echo '</div>';
    }
}

/**
 * Output head analytics scripts.
 */
function city_library_output_head_analytics() {
    $head_code = get_theme_mod('analytics_head_code', '');
    if (!empty($head_code)) {
        echo "\n<!-- City Library Analytics Head -->\n" . $head_code . "\n<!-- /City Library Analytics Head -->\n";
    }
}
add_action('wp_head', 'city_library_output_head_analytics', 20);

/**
 * Output footer analytics scripts and body counters.
 */
function city_library_output_footer_analytics() {
    // Hidden / body position for PRO.Культура.РФ
    city_library_render_pro_culture_counter('footer-body');

    $footer_code = get_theme_mod('analytics_footer_code', '');
    if (!empty($footer_code)) {
        echo "\n<!-- City Library Analytics Footer -->\n" . $footer_code . "\n<!-- /City Library Analytics Footer -->\n";
    }
}
add_action('wp_footer', 'city_library_output_footer_analytics', 99);
