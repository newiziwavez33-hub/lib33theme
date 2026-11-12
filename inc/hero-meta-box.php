<?php
/**
 * Custom Meta Boxes for Post/Page specific Hero Section & Adaptive Background customization.
 *
 * Fully compliant with WordPress coding standards, sanitization, nonces, and media library API.
 */

// Register the Meta Box
function city_library_add_hero_meta_box() {
    $screens = ['post', 'page'];
    foreach ($screens as $screen) {
        add_meta_box(
            'city_library_hero_box_id',
            __('Настройки Hero секции и кастомного фона', 'city-library'),
            'city_library_hero_meta_box_html',
            $screen,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'city_library_add_hero_meta_box');

// Enqueue Admin Scripts, Styles & Media Uploader
function city_library_hero_meta_box_admin_scripts($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, ['post', 'page'], true)) {
        return;
    }

    // WordPress Media Uploader Scripts & Styles
    wp_enqueue_media();

    // WordPress Native Color Picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    // Google Fonts for live typography preview in the admin (including Eco fonts Comfortaa & Caveat)
    wp_enqueue_style(
        'city-library-admin-hero-fonts',
        'https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Comfortaa:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=Monomakh&family=Montserrat:wght@400;500;600;700;800&family=Merriweather:wght@300;400;700&family=Oswald:wght@500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Ruslan+Display&display=swap',
        array(),
        null
    );

    // Theme stylesheet for Shihan @font-face and font-hero-* classes
    wp_enqueue_style(
        'city-library-admin-theme-style',
        get_stylesheet_uri(),
        array(),
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('admin_enqueue_scripts', 'city_library_hero_meta_box_admin_scripts');

// Render the Meta Box HTML
function city_library_hero_meta_box_html($post) {
    wp_nonce_field('city_library_save_hero_meta_box_data', 'city_library_hero_meta_box_nonce');

    $enable          = get_post_meta($post->ID, '_hero_custom_enable', true);
    $font            = get_post_meta($post->ID, '_hero_custom_font', true) ?: 'montserrat';
    $bg_mode         = get_post_meta($post->ID, '_hero_custom_bg_mode', true) ?: 'hero-fixed';
    $image           = get_post_meta($post->ID, '_hero_custom_image', true);
    $title           = get_post_meta($post->ID, '_hero_custom_title', true);
    $subtitle        = get_post_meta($post->ID, '_hero_custom_subtitle', true);
    $btn1_text       = get_post_meta($post->ID, '_hero_custom_btn1_text', true);
    $btn1_link       = get_post_meta($post->ID, '_hero_custom_btn1_link', true);
    $overlay_color   = get_post_meta($post->ID, '_hero_custom_overlay_color', true);
    $overlay_opacity = get_post_meta($post->ID, '_hero_custom_overlay_opacity', true);
    $align           = get_post_meta($post->ID, '_hero_custom_align', true);
    $height          = get_post_meta($post->ID, '_hero_custom_height', true);

    // Hero Text Colors
    $title_color     = get_post_meta($post->ID, '_hero_custom_title_color', true);
    $subtitle_color  = get_post_meta($post->ID, '_hero_custom_subtitle_color', true);
    if (empty($title_color)) {
        $title_color = '#ffffff';
    }
    if (empty($subtitle_color)) {
        $subtitle_color = '#e2e8f0';
    }

    // Hero to Content Fade Transition
    $fade_mode   = get_post_meta($post->ID, '_hero_custom_fade_mode', true) ?: 'smooth';
    $fade_color  = get_post_meta($post->ID, '_hero_custom_fade_color', true);
    $fade_height = get_post_meta($post->ID, '_hero_custom_fade_height', true) ?: '160px';

    // Hero GIF Animation Overlay Meta
    $gif_enable    = get_post_meta($post->ID, '_hero_gif_enable', true);
    $gif_url       = get_post_meta($post->ID, '_hero_gif_url', true);
    $gif_type      = get_post_meta($post->ID, '_hero_gif_type', true) ?: 'fullscreen';
    $gif_pos       = get_post_meta($post->ID, '_hero_gif_position', true) ?: 'bottom-right';
    $gif_blend     = get_post_meta($post->ID, '_hero_gif_blend_mode', true) ?: 'screen';
    $gif_opacity   = get_post_meta($post->ID, '_hero_gif_opacity', true);
    if ($gif_opacity === '' || !is_numeric($gif_opacity)) {
        $gif_opacity = '0.70';
    }
    $gif_size      = get_post_meta($post->ID, '_hero_gif_size', true) ?: '340px';
    $gif_mobile    = get_post_meta($post->ID, '_hero_gif_mobile', true) ?: 'hide';

    // Page Background (Behind Blocks) Meta
    $page_bg_enable          = get_post_meta($post->ID, '_page_bg_enable', true);
    $page_bg_color           = get_post_meta($post->ID, '_page_bg_color', true) ?: '#f6f8f6';
    $page_bg_image           = get_post_meta($post->ID, '_page_bg_image', true);
    $page_bg_mode            = get_post_meta($post->ID, '_page_bg_mode', true) ?: 'fixed';
    $page_bg_fit             = get_post_meta($post->ID, '_page_bg_fit', true) ?: 'cover';
    $page_bg_overlay_color   = get_post_meta($post->ID, '_page_bg_overlay_color', true) ?: '#000000';
    $page_bg_overlay_opacity = get_post_meta($post->ID, '_page_bg_overlay_opacity', true);
    $page_bg_card_style      = get_post_meta($post->ID, '_page_bg_card_style', true) ?: 'glass';

    if (empty($fade_color)) {
        $fade_color = !empty($page_bg_color) ? $page_bg_color : '#f6f8f6';
    }

    if ($page_bg_overlay_opacity === '') {
        $page_bg_overlay_opacity = '0.0';
    }

    if ($overlay_opacity === '') {
        $overlay_opacity = '0.55';
    }
    if (empty($overlay_color)) {
        $overlay_color = '#1a3c34';
    }
    ?>
    <style>
        .cl-hero-admin-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            color: #1e293b;
        }
        .cl-hero-admin-enable-box {
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .cl-hero-admin-enable-box label {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .cl-hero-tab-nav {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .cl-hero-tab-btn {
            background: none;
            border: none;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
        }
        .cl-hero-tab-btn:hover {
            color: #1c77d9;
        }
        .cl-hero-tab-btn.is-active {
            color: #1c77d9;
            border-bottom-color: #1c77d9;
            font-weight: 700;
        }
        .cl-hero-tab-content {
            display: none;
        }
        .cl-hero-tab-content.is-active {
            display: block;
        }
        .cl-hero-admin-row {
            margin-bottom: 18px;
        }
        .cl-hero-admin-row label.cl-row-label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
            font-size: 13px;
        }
        .cl-hero-admin-row input[type="text"],
        .cl-hero-admin-row textarea,
        .cl-hero-admin-row select {
            width: 100%;
            max-width: 620px;
            padding: 7px 10px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .cl-hero-admin-desc {
            font-size: 12px;
            color: #64748b;
            font-style: italic;
            display: block;
            margin-top: 5px;
            line-height: 1.4;
        }
        .cl-hero-bg-mode-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            max-width: 620px;
        }
        .cl-hero-bg-mode-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }
        .cl-hero-bg-mode-card.is-selected {
            border-color: #1c77d9;
            background: #f0f7ff;
        }
        .cl-hero-bg-mode-card input[type="radio"] {
            margin-top: 3px;
        }
        .cl-font-preview-wrap {
            margin-top: 10px;
            max-width: 620px;
        }
        #hero_font_preview_box {
            padding: 16px 20px;
            border-radius: 8px;
            background: #0f172a;
            color: #ffffff;
            font-size: 20px;
            line-height: 1.3;
            min-height: 56px;
            display: flex;
            align-items: center;
            border: 1px solid #334155;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
    </style>

    <div class="cl-hero-admin-wrap">
        <!-- Main Toggle -->
        <div class="cl-hero-admin-enable-box">
            <label for="hero_custom_enable">
                <input type="checkbox" id="hero_custom_enable" name="hero_custom_enable" value="yes" <?php checked($enable, 'yes'); ?> />
                <span><?php _e('Включить уникальный Hero экран / кастомное оформление для этой записи', 'city-library'); ?></span>
            </label>
            <span class="cl-hero-admin-desc"><?php _e('Если включено, указанные настройки шрифта, Hero баннера и фона страницы за блоками заменят оформление темы по умолчанию.', 'city-library'); ?></span>
        </div>

        <div id="cl-hero-settings-container" style="<?php echo (!$enable && !$page_bg_enable && $gif_enable !== 'yes') ? 'display: none;' : ''; ?>">
            <!-- Tabs Navigation -->
            <div class="cl-hero-tab-nav" role="tablist">
                <button type="button" class="cl-hero-tab-btn is-active" data-tab="typography">
                    <?php _e('1. Шрифт и Текст Hero', 'city-library'); ?>
                </button>
                <button type="button" class="cl-hero-tab-btn" data-tab="background">
                    <?php _e('2. Баннер секции Hero', 'city-library'); ?>
                </button>
                <button type="button" class="cl-hero-tab-btn" data-tab="page-background" style="position: relative;">
                    <?php _e('3. Фон страницы (за блоками)', 'city-library'); ?>
                    <?php if ($page_bg_enable === 'yes') : ?>
                        <span style="display: inline-block; width: 7px; height: 7px; background: #16a34a; border-radius: 50%; margin-left: 4px; vertical-align: middle;"></span>
                    <?php endif; ?>
                </button>
                <button type="button" class="cl-hero-tab-btn" data-tab="overlay">
                    <?php _e('4. Оверлей и Кнопки Hero', 'city-library'); ?>
                </button>
                <button type="button" class="cl-hero-tab-btn" data-tab="gif-animation" style="position: relative;">
                    <?php _e('5. GIF-анимация фона ✨', 'city-library'); ?>
                    <?php if ($gif_enable === 'yes') : ?>
                        <span style="display: inline-block; width: 7px; height: 7px; background: #eab308; border-radius: 50%; margin-left: 4px; vertical-align: middle;"></span>
                    <?php endif; ?>
                </button>
            </div>

            <!-- TAB 1: TYPOGRAPHY & TEXT -->
            <div id="cl-tab-typography" class="cl-hero-tab-content is-active">
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_custom_font"><?php _e('Типографика заголовка Hero (Шрифт):', 'city-library'); ?></label>
                    <select id="hero_custom_font" name="hero_custom_font">
                        <optgroup label="Фирменные и национальные аутентичные шрифты (Локальные)">
                            <option value="dygyn" <?php selected($font, 'dygyn'); ?>>Dygyn / Дыгын (Аутентичный акцидентный шрифт — локальный)</option>
                            <option value="shihan" <?php selected($font, 'shihan'); ?>>Shihan (Башкирский геометрический орнаментальный акцидент — локальный)</option>
                            <option value="monomakh" <?php selected($font, 'monomakh'); ?>>Monomakh / Мономахъ (Древнеславянский полуустав XIII–XVI вв.)</option>
                            <option value="ruslan" <?php selected($font, 'ruslan'); ?>>Ruslan Display (Славянский модерн / неорусский стиль)</option>
                        </optgroup>
                        <optgroup label="Экология, природа и органика (Eco & Nature)">
                            <option value="comfortaa" <?php selected($font, 'comfortaa'); ?>>Comfortaa (Эко-шрифт, мягкие природные формы — кириллица)</option>
                            <option value="caveat" <?php selected($font, 'caveat'); ?>>Caveat (Живой органический крафт / полевой дневник — кириллица)</option>
                        </optgroup>
                        <optgroup label="Гротески и современные шрифты">
                            <option value="montserrat" <?php selected($font, 'montserrat'); ?>>Montserrat (Современный геометрический гротеск — по умолчанию)</option>
                        </optgroup>
                        <optgroup label="Литературная и книжная классика (Антиквы)">
                            <option value="cormorant" <?php selected($font, 'cormorant'); ?>>Cormorant Garamond (Утонченная ренессансная антиква)</option>
                            <option value="playfair" <?php selected($font, 'playfair'); ?>>Playfair Display (Журнальная благородная антиква)</option>
                            <option value="merriweather" <?php selected($font, 'merriweather'); ?>>Merriweather (Академический книжный сериф)</option>
                        </optgroup>
                        <optgroup label="Плакатные и событийные шрифты">
                            <option value="oswald" <?php selected($font, 'oswald'); ?>>Oswald (Монументальный плотный гротеск для афиш)</option>
                        </optgroup>
                    </select>

                    <div class="cl-font-preview-wrap">
                        <span class="cl-hero-admin-desc" style="font-weight: 600; margin-bottom: 6px; display: inline-block; font-style: normal; color: #475569;">
                            <?php _e('Живой предпросмотр шрифта и цвета:', 'city-library'); ?>
                        </span>
                        <div id="hero_font_preview_box" class="font-hero-<?php echo esc_attr($font); ?>" style="color: <?php echo esc_attr($title_color); ?>;">
                            ЦЕНТРАЛЬНАЯ ГОРОДСКАЯ БИБЛИОТЕКА: КНИГИ И ВРЕМЯ
                        </div>
                    </div>
                </div>

                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_custom_title"><?php _e('Заголовок Hero (Title)', 'city-library'); ?></label>
                    <input type="text" id="hero_custom_title" name="hero_custom_title" value="<?php echo esc_attr($title); ?>" placeholder="<?php esc_attr_e('По умолчанию используется заголовок записи', 'city-library'); ?>" />
                    <span class="cl-hero-admin-desc"><?php _e('Оставьте пустым для заголовка статьи. Разрешены теги &lt;br&gt; или &lt;span class="text-primary italic"&gt;.', 'city-library'); ?></span>
                </div>

                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_custom_subtitle"><?php _e('Подзаголовок Hero (Subtitle)', 'city-library'); ?></label>
                    <textarea id="hero_custom_subtitle" name="hero_custom_subtitle" rows="3" placeholder="<?php esc_attr_e('Краткий анонс или вводная цитата для Hero секции...', 'city-library'); ?>"><?php echo esc_textarea($subtitle); ?></textarea>
                </div>

                <div class="cl-hero-admin-row" style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <div>
                        <label class="cl-row-label" for="hero_custom_title_color"><?php _e('Цвет заголовка Hero:', 'city-library'); ?></label>
                        <input type="text" id="hero_custom_title_color" name="hero_custom_title_color" class="cl-color-picker" value="<?php echo esc_attr($title_color); ?>" data-default-color="#ffffff" />
                        <span class="cl-hero-admin-desc"><?php _e('По умолчанию: #ffffff (белый). Мгновенно отражается в предпросмотре выше.', 'city-library'); ?></span>
                    </div>
                    <div>
                        <label class="cl-row-label" for="hero_custom_subtitle_color"><?php _e('Цвет подзаголовка Hero:', 'city-library'); ?></label>
                        <input type="text" id="hero_custom_subtitle_color" name="hero_custom_subtitle_color" class="cl-color-picker" value="<?php echo esc_attr($subtitle_color); ?>" data-default-color="#e2e8f0" />
                        <span class="cl-hero-admin-desc"><?php _e('По умолчанию: #e2e8f0 (светло-серый).', 'city-library'); ?></span>
                    </div>
                </div>
            </div>

            <!-- TAB 2: BACKGROUND & FIXED MODES -->
            <div id="cl-tab-background" class="cl-hero-tab-content">
                <!-- Background Mode Selector -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label"><?php _e('Режим фона для этой записи:', 'city-library'); ?></label>

                    <label class="cl-hero-bg-mode-card <?php echo ($bg_mode === 'hero-fixed') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="hero_custom_bg_mode" value="hero-fixed" <?php checked($bg_mode, 'hero-fixed'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Адаптивный фиксированный фон Hero (Parallax-эффект окна — рекомендуется)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Изображение закреплено на экране внутри блока Hero. По мере скролла контент плавно перекрывает баннер. Аппаратное ускорение на ПК и надежный fallback на смартфонах без багов Safari.', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($bg_mode === 'full-page-fixed') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="hero_custom_bg_mode" value="full-page-fixed" <?php checked($bg_mode, 'full-page-fixed'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Атмосферный фиксированный фон на ВСЮ страницу статьи', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Выбранное фоновое изображение фиксируется на весь экран страницы. Карточка статьи скользит поверх него с эффектом премиального матового стекла (backdrop-blur).', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($bg_mode === 'scroll') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="hero_custom_bg_mode" value="scroll" <?php checked($bg_mode, 'scroll'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Обычный фон (прокручивается вместе со страницей)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Классический фоновый баннер Hero, прокручивается естественно вместе со всей страницей.', 'city-library'); ?></span>
                        </div>
                    </label>
                </div>

                <!-- Background Image with WP Media Uploader -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_custom_image"><?php _e('Фоновое изображение:', 'city-library'); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center; max-width: 620px;">
                        <input type="text" id="hero_custom_image" name="hero_custom_image" value="<?php echo esc_attr($image); ?>" placeholder="https://..." style="flex: 1;" />
                        <button type="button" class="button button-secondary" id="cl_hero_upload_image_btn" style="white-space: nowrap; height: 34px; display: inline-flex; align-items: center; gap: 4px;">
                            <span class="dashicons dashicons-admin-media" style="margin-top: -2px;"></span>
                            <?php _e('Медиабиблиотека', 'city-library'); ?>
                        </button>
                    </div>

                    <!-- Interactive Image Preview -->
                    <div id="cl_hero_image_preview_wrapper" style="margin-top: 12px; <?php echo empty($image) ? 'display: none;' : ''; ?>">
                        <div style="position: relative; display: inline-block; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 320px; background: #0f172a;">
                            <img id="cl_hero_image_preview_img" src="<?php echo esc_url($image); ?>" alt="<?php esc_attr_e('Превью фона', 'city-library'); ?>" style="display: block; width: 100%; height: 160px; object-fit: cover;" />
                            <button type="button" id="cl_hero_remove_image_btn" class="button button-small" style="position: absolute; top: 8px; right: 8px; background: rgba(220,38,38,0.92); color: #ffffff; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; padding: 2px 8px;">
                                &times; <?php _e('Удалить', 'city-library'); ?>
                            </button>
                        </div>
                    </div>
                    <span class="cl-hero-admin-desc"><?php _e('Загрузите изображение через медиабиблиотеку WP или вставьте прямую ссылку. Если пусто, используется фон темы по умолчанию.', 'city-library'); ?></span>
                </div>
            </div>

            <!-- TAB 3: PAGE BACKGROUND (BEHIND BLOCKS) -->
            <div id="cl-tab-page-background" class="cl-hero-tab-content">
                <!-- Page Background Activation Box -->
                <div class="cl-hero-admin-enable-box" style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; margin-bottom: 20px;">
                    <label for="page_bg_enable">
                        <input type="checkbox" id="page_bg_enable" name="page_bg_enable" value="yes" <?php checked($page_bg_enable, 'yes'); ?> />
                        <span style="font-size: 14px; font-weight: 700; color: #166534;"><?php _e('Включить кастомный фон страницы позади контентных блоков', 'city-library'); ?></span>
                    </label>
                    <span class="cl-hero-admin-desc" style="color: #15803d; font-style: normal; margin-top: 5px;">
                        <?php _e('Позволяет изменить стандартный белый/светлый (#f6f8f6) фон вокруг и позади карточки статьи. Карточка статьи получит выбранное оформление (матовое стекло, плотный белый, книжная бумага), а фон позади нее станет адаптивным.', 'city-library'); ?>
                    </span>
                </div>

                <!-- Page Background Color -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="page_bg_color"><?php _e('Цвет фона страницы (за блоками):', 'city-library'); ?></label>
                    <input type="text" id="page_bg_color" name="page_bg_color" class="cl-color-picker" value="<?php echo esc_attr($page_bg_color); ?>" data-default-color="#f6f8f6" />
                    <span class="cl-hero-admin-desc"><?php _e('Базовый заливающий цвет. Служит как самостоятельным фоном, так и мгновенной подложкой до загрузки фонового изображения.', 'city-library'); ?></span>
                </div>

                <!-- Page Background Image with Media Uploader -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="page_bg_image"><?php _e('Фоновое изображение / Паттерн страницы:', 'city-library'); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center; max-width: 620px;">
                        <input type="text" id="page_bg_image" name="page_bg_image" value="<?php echo esc_attr($page_bg_image); ?>" placeholder="https://..." style="flex: 1;" />
                        <button type="button" class="button button-secondary" id="cl_page_bg_upload_btn" style="white-space: nowrap; height: 34px; display: inline-flex; align-items: center; gap: 4px;">
                            <span class="dashicons dashicons-admin-media" style="margin-top: -2px;"></span>
                            <?php _e('Медиатека', 'city-library'); ?>
                        </button>
                    </div>

                    <!-- Interactive Image Preview -->
                    <div id="cl_page_bg_preview_wrapper" style="margin-top: 12px; <?php echo empty($page_bg_image) ? 'display: none;' : ''; ?>">
                        <div style="position: relative; display: inline-block; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 320px; background: #0f172a;">
                            <img id="cl_page_bg_preview_img" src="<?php echo esc_url($page_bg_image); ?>" alt="<?php esc_attr_e('Превью фона страницы', 'city-library'); ?>" style="display: block; width: 100%; height: 160px; object-fit: cover;" />
                            <button type="button" id="cl_page_bg_remove_btn" class="button button-small" style="position: absolute; top: 8px; right: 8px; background: rgba(220,38,38,0.92); color: #ffffff; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; padding: 2px 8px;">
                                &times; <?php _e('Удалить', 'city-library'); ?>
                            </button>
                        </div>
                    </div>
                    <span class="cl-hero-admin-desc"><?php _e('Загрузите фотографию, бесшовную текстуру или орнамент. Если изображение не выбрано, используется только выбранный цвет фона.', 'city-library'); ?></span>
                </div>

                <!-- Fixation Mode -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label"><?php _e('Режим фиксации фона страницы:', 'city-library'); ?></label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_mode === 'fixed') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_mode" value="fixed" <?php checked($page_bg_mode, 'fixed'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('ФИКСИРОВАННЫЙ (Fixed Screen) — Рекомендуется', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Фон зафиксирован в окне экрана (100vw × 100dvh). Контентные блоки и карточки плавно скользят поверх него. Аппаратно ускорен на GPU, без дерганий на iPhone/iPad (Safari) и Android.', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_mode === 'scroll') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_mode" value="scroll" <?php checked($page_bg_mode, 'scroll'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('НЕ ФИКСИРОВАННЫЙ (Scroll)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Фон прокручивается синхронно со скроллом всей страницы и контентных блоков.', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_mode === 'repeat') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_mode" value="repeat" <?php checked($page_bg_mode, 'repeat'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('БЕСШОВНЫЙ ПАТТЕРН (Pattern Repeat)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Текстура или узор бесшовно замащивают всю площадь страницы.', 'city-library'); ?></span>
                        </div>
                    </label>
                </div>

                <!-- Sizing & Fit -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="page_bg_fit"><?php _e('Масштабирование растрового фона страницы:', 'city-library'); ?></label>
                    <select id="page_bg_fit" name="page_bg_fit" style="max-width: 400px;">
                        <option value="cover" <?php selected($page_bg_fit, 'cover'); ?>><?php _e('Заполнить экран (Cover — без полей, авто-масштаб)', 'city-library'); ?></option>
                        <option value="contain" <?php selected($page_bg_fit, 'contain'); ?>><?php _e('По размеру экрана (Contain — пропорционально)', 'city-library'); ?></option>
                        <option value="auto" <?php selected($page_bg_fit, 'auto'); ?>><?php _e('Оригинальный размер / Паттерн (Auto / Repeat)', 'city-library'); ?></option>
                    </select>
                </div>

                <!-- Overlay Tint & Opacity -->
                <div class="cl-hero-admin-row" style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <div>
                        <label class="cl-row-label" for="page_bg_overlay_color"><?php _e('Цвет затемнения / тонирования фона:', 'city-library'); ?></label>
                        <input type="text" id="page_bg_overlay_color" name="page_bg_overlay_color" class="cl-color-picker" value="<?php echo esc_attr($page_bg_overlay_color); ?>" data-default-color="#000000" />
                    </div>
                    <div>
                        <label class="cl-row-label" for="page_bg_overlay_opacity"><?php _e('Плотность затемнения (0.0 — 0.9):', 'city-library'); ?></label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="range" min="0" max="0.9" step="0.05" id="page_bg_overlay_opacity_range" value="<?php echo esc_attr($page_bg_overlay_opacity); ?>" style="width: 140px;" />
                            <input type="number" step="0.05" min="0" max="0.9" id="page_bg_overlay_opacity" name="page_bg_overlay_opacity" value="<?php echo esc_attr($page_bg_overlay_opacity); ?>" style="width: 90px;" />
                        </div>
                        <span class="cl-hero-admin-desc"><?php _e('0.0 — без затемнения. Рекомендуется 0.20 — 0.35 для мягкого контраста контента.', 'city-library'); ?></span>
                    </div>
                </div>

                <!-- Content Card Style -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label"><?php _e('Стиль контентной карточки статьи поверх фона:', 'city-library'); ?></label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_card_style === 'glass') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_card_style" value="glass" <?php checked($page_bg_card_style, 'glass'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Премиальное матовое стекло (Glassmorphism 94% + Blur) — Рекомендуется', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Полупрозрачный белый фон (94%) с размытием заднего плана (backdrop-filter: blur(20px)). Фон страницы мягко просвечивает сквозь края карточки статьи в стиле macOS/iOS.', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_card_style === 'solid') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_card_style" value="solid" <?php checked($page_bg_card_style, 'solid'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Классический плотный белый (Solid White 100%)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Полностью непрозрачная белая бумага с четкой тенью и тонким контуром. Максимальная строгость и контраст для длинных документов.', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_card_style === 'warm') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_card_style" value="warm" <?php checked($page_bg_card_style, 'warm'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Тёплая книжная бумага (Warm Ivory Parchment 96%)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Тёплый книжный оттенок слоновой кости с лёгкой сепией для краеведческих, исторических и литературных материалов.', 'city-library'); ?></span>
                        </div>
                    </label>

                    <label class="cl-hero-bg-mode-card <?php echo ($page_bg_card_style === 'dark') ? 'is-selected' : ''; ?>">
                        <input type="radio" name="page_bg_card_style" value="dark" <?php checked($page_bg_card_style, 'dark'); ?> />
                        <div>
                            <strong style="color: #0f172a; font-size: 13px;"><?php _e('Тёмное контрастное стекло (Dark Glass Mode 90%)', 'city-library'); ?></strong>
                            <span class="cl-hero-admin-desc"><?php _e('Тёмный графитовый фон (90%) с белым текстом для ночных событий, астрономии или арт-рубрик.', 'city-library'); ?></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- TAB 4: OVERLAY & BUTTON -->
            <div id="cl-tab-overlay" class="cl-hero-tab-content">
                <div class="cl-hero-admin-row" style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <div>
                        <label class="cl-row-label" for="hero_custom_overlay_color"><?php _e('Цвет затемнения (Hex):', 'city-library'); ?></label>
                        <input type="text" id="hero_custom_overlay_color" name="hero_custom_overlay_color" value="<?php echo esc_attr($overlay_color); ?>" data-default-color="#1a3c34" />
                    </div>
                    <div>
                        <label class="cl-row-label" for="hero_custom_overlay_opacity"><?php _e('Непрозрачность затемнения (0.0 - 1.0):', 'city-library'); ?></label>
                        <input type="number" step="0.05" min="0" max="1" id="hero_custom_overlay_opacity" name="hero_custom_overlay_opacity" value="<?php echo esc_attr($overlay_opacity); ?>" style="width: 140px;" />
                        <span class="cl-hero-admin-desc"><?php _e('Рекомендуется 0.5 - 0.7 для идеального контраста белого текста.', 'city-library'); ?></span>
                    </div>
                </div>

                <div class="cl-hero-admin-row" style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <div>
                        <label class="cl-row-label" for="hero_custom_align"><?php _e('Выравнивание текста:', 'city-library'); ?></label>
                        <select id="hero_custom_align" name="hero_custom_align" style="width: 180px;">
                            <option value="" <?php selected($align, ''); ?>><?php _e('По умолчанию', 'city-library'); ?></option>
                            <option value="left" <?php selected($align, 'left'); ?>><?php _e('По левому краю', 'city-library'); ?></option>
                            <option value="center" <?php selected($align, 'center'); ?>><?php _e('По центру', 'city-library'); ?></option>
                            <option value="right" <?php selected($align, 'right'); ?>><?php _e('По правому краю', 'city-library'); ?></option>
                        </select>
                    </div>
                    <div>
                        <label class="cl-row-label" for="hero_custom_height"><?php _e('Высота блока Hero:', 'city-library'); ?></label>
                        <select id="hero_custom_height" name="hero_custom_height" style="width: 240px;">
                            <option value="" <?php selected($height, ''); ?>><?php _e('По умолчанию (Во весь экран)', 'city-library'); ?></option>
                            <option value="min-h-[60vh]" <?php selected($height, 'min-h-[60vh]'); ?>><?php _e('Средняя (60% экрана)', 'city-library'); ?></option>
                            <option value="py-32" <?php selected($height, 'py-32'); ?>><?php _e('Компактная (Только по контенту)', 'city-library'); ?></option>
                        </select>
                    </div>
                </div>

                <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;" />

                <!-- Hero to Content Bottom Transition -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label"><?php _e('Плавный переход от Hero к контенту (Растворение нижней границы):', 'city-library'); ?></label>
                    <span class="cl-hero-admin-desc" style="margin-bottom: 10px; display: block;">
                        <?php _e('Устраняет резкую границу или белую плашку между баннером Hero и контентом статьи/хлебными крошками.', 'city-library'); ?>
                    </span>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
                        <label class="cl-hero-bg-mode-card <?php echo ($fade_mode === 'smooth') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 190px;">
                            <input type="radio" name="hero_custom_fade_mode" value="smooth" <?php checked($fade_mode, 'smooth'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Плавный переход (Авто)', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Бесшовное растворение в фон страницы (рекомендуется).', 'city-library'); ?></span>
                            </div>
                        </label>
                        <label class="cl-hero-bg-mode-card <?php echo ($fade_mode === 'none') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 190px;">
                            <input type="radio" name="hero_custom_fade_mode" value="none" <?php checked($fade_mode, 'none'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Без перехода (Отключить)', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Полностью убрать нижний градиент. Сквозной стык.', 'city-library'); ?></span>
                            </div>
                        </label>
                        <label class="cl-hero-bg-mode-card <?php echo ($fade_mode === 'custom') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 190px;">
                            <input type="radio" name="hero_custom_fade_mode" value="custom" <?php checked($fade_mode, 'custom'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Пользовательский цвет', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Выбрать индивидуальный цвет и глубину растворения.', 'city-library'); ?></span>
                            </div>
                        </label>
                    </div>

                    <div id="cl-hero-fade-custom-options" style="display: flex; gap: 24px; flex-wrap: wrap; margin-top: 10px; <?php echo ($fade_mode !== 'custom') ? 'display: none;' : ''; ?>">
                        <div>
                            <label class="cl-row-label" for="hero_custom_fade_color"><?php _e('Цвет растворения градиента:', 'city-library'); ?></label>
                            <input type="text" id="hero_custom_fade_color" name="hero_custom_fade_color" class="cl-color-picker" value="<?php echo esc_attr($fade_color); ?>" data-default-color="#f6f8f6" />
                        </div>
                        <div>
                            <label class="cl-row-label" for="hero_custom_fade_height"><?php _e('Глубина растворения:', 'city-library'); ?></label>
                            <select id="hero_custom_fade_height" name="hero_custom_fade_height" style="width: 200px;">
                                <option value="100px" <?php selected($fade_height, '100px'); ?>><?php _e('Стандартная (100px)', 'city-library'); ?></option>
                                <option value="160px" <?php selected($fade_height, '160px'); ?>><?php _e('Мягкая (160px)', 'city-library'); ?></option>
                                <option value="240px" <?php selected($fade_height, '240px'); ?>><?php _e('Глубокая (240px)', 'city-library'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;" />

                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_custom_btn1_text"><?php _e('Текст главной кнопки Hero:', 'city-library'); ?></label>
                    <input type="text" id="hero_custom_btn1_text" name="hero_custom_btn1_text" value="<?php echo esc_attr($btn1_text); ?>" placeholder="<?php esc_attr_e('Например: Читать далее', 'city-library'); ?>" />
                </div>

                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_custom_btn1_link"><?php _e('Ссылка главной кнопки:', 'city-library'); ?></label>
                    <input type="text" id="hero_custom_btn1_link" name="hero_custom_btn1_link" value="<?php echo esc_attr($btn1_link); ?>" placeholder="#content" />
                </div>
            </div>

            <!-- TAB 5: GIF ANIMATION OVERLAY -->
            <div id="cl-tab-gif-animation" class="cl-hero-tab-content">
                <!-- GIF Activation Box -->
                <div class="cl-hero-admin-enable-box" style="background: #fefce8; border: 1px solid #fde047; border-radius: 8px; margin-bottom: 20px;">
                    <label for="hero_gif_enable">
                        <input type="checkbox" id="hero_gif_enable" name="hero_gif_enable" value="yes" <?php checked($gif_enable, 'yes'); ?> />
                        <span style="font-size: 14px; font-weight: 700; color: #854d0e;"><?php _e('Включить живую GIF-анимацию поверх фона Hero ✨', 'city-library'); ?></span>
                    </label>
                    <span class="cl-hero-admin-desc" style="color: #a16207; font-style: normal; margin-top: 5px;">
                        <?php _e('Накладывает кинематографичный слой анимации (падающие искры, снег, мерцающие звезды, живая книга) поверх баннера Hero с аппаратным ускорением на GPU.', 'city-library'); ?>
                    </span>
                </div>

                <!-- GIF Media Uploader -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label" for="hero_gif_url"><?php _e('Файл GIF-анимации:', 'city-library'); ?></label>
                    <div style="display: flex; gap: 10px; align-items: center; max-width: 620px;">
                        <input type="text" id="hero_gif_url" name="hero_gif_url" value="<?php echo esc_attr($gif_url); ?>" placeholder="https://.../sparkles.gif" style="flex: 1;" />
                        <button type="button" class="button button-secondary" id="cl_hero_gif_upload_btn" style="white-space: nowrap; height: 34px; display: inline-flex; align-items: center; gap: 4px;">
                            <span class="dashicons dashicons-format-video" style="margin-top: -2px;"></span>
                            <?php _e('Медиатека GIF', 'city-library'); ?>
                        </button>
                    </div>

                    <!-- Interactive GIF Preview -->
                    <div id="cl_hero_gif_preview_wrapper" style="margin-top: 12px; <?php echo empty($gif_url) ? 'display: none;' : ''; ?>">
                        <div style="position: relative; display: inline-block; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 320px; background: #0f172a;">
                            <img id="cl_hero_gif_preview_img" src="<?php echo esc_url($gif_url); ?>" alt="<?php esc_attr_e('Превью GIF', 'city-library'); ?>" style="display: block; width: 100%; height: 160px; object-fit: contain; background: #0b1120;" />
                            <button type="button" id="cl_hero_gif_remove_btn" class="button button-small" style="position: absolute; top: 8px; right: 8px; background: rgba(220,38,38,0.92); color: #ffffff; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; padding: 2px 8px;">
                                &times; <?php _e('Удалить', 'city-library'); ?>
                            </button>
                        </div>
                    </div>
                    <span class="cl-hero-admin-desc"><?php _e('Загрузите анимированный GIF-файл через медиабиблиотеку или укажите прямую ссылку. Рекомендуются оптимизированные GIF до 2–3 МБ.', 'city-library'); ?></span>
                </div>

                <!-- Overlay Type -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label"><?php _e('Тип наложения анимации:', 'city-library'); ?></label>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <label class="cl-hero-bg-mode-card <?php echo ($gif_type === 'fullscreen') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 240px;">
                            <input type="radio" name="hero_gif_type" value="fullscreen" <?php checked($gif_type, 'fullscreen'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Полноэкранный атмосферный эффект (Cover)', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Покрывает весь баннер Hero. Идеально для падающих искр, снега, дождя, мерцающих звезд, боке и пылинок.', 'city-library'); ?></span>
                            </div>
                        </label>
                        <label class="cl-hero-bg-mode-card <?php echo ($gif_type === 'accent') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 240px;">
                            <input type="radio" name="hero_gif_type" value="accent" <?php checked($gif_type, 'accent'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Локальный акцент (Spot Accent)', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Компактный анимированный элемент (перелистывающаяся книга, читающий кот, старинный свиток, неоновая вывеска) с выбором позиции и размера.', 'city-library'); ?></span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Blend Mode & Opacity -->
                <div class="cl-hero-admin-row" style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 240px;">
                        <label class="cl-row-label" for="hero_gif_blend_mode"><?php _e('Режим смешивания (mix-blend-mode):', 'city-library'); ?></label>
                        <select id="hero_gif_blend_mode" name="hero_gif_blend_mode" style="width: 100%;">
                            <option value="screen" <?php selected($gif_blend, 'screen'); ?>><?php _e('Screen (Осветление — скрывает черный фон: искры, снег, огонь) ⭐', 'city-library'); ?></option>
                            <option value="normal" <?php selected($gif_blend, 'normal'); ?>><?php _e('Normal (Обычный — для GIF с собственной прозрачностью)', 'city-library'); ?></option>
                            <option value="multiply" <?php selected($gif_blend, 'multiply'); ?>><?php _e('Multiply (Умножение — скрывает белый фон: чернила, силуэты)', 'city-library'); ?></option>
                            <option value="overlay" <?php selected($gif_blend, 'overlay'); ?>><?php _e('Overlay (Перекрытие — кинематографичный контраст и блики)', 'city-library'); ?></option>
                            <option value="soft-light" <?php selected($gif_blend, 'soft-light'); ?>><?php _e('Soft-Light (Мягкий свет — нежная дымка, туман, свечение)', 'city-library'); ?></option>
                        </select>
                        <span class="cl-hero-admin-desc"><?php _e('Режим «Screen» делает абсолютно прозрачным любой черный фон у эффектов частиц.', 'city-library'); ?></span>
                    </div>

                    <div style="flex: 1; min-width: 240px;">
                        <label class="cl-row-label" for="hero_gif_opacity"><?php _e('Прозрачность анимации:', 'city-library'); ?> <span id="cl_hero_gif_opacity_val" style="color: #1c77d9; font-weight: 700;"><?php echo round(floatval($gif_opacity) * 100); ?>%</span></label>
                        <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px;">
                            <input type="range" id="hero_gif_opacity_range" min="0.10" max="1.00" step="0.05" value="<?php echo esc_attr($gif_opacity); ?>" style="flex: 1;" />
                            <input type="number" id="hero_gif_opacity" name="hero_gif_opacity" min="0.10" max="1.00" step="0.05" value="<?php echo esc_attr($gif_opacity); ?>" style="width: 75px; text-align: center;" />
                        </div>
                        <span class="cl-hero-admin-desc"><?php _e('Рекомендуется 60–80% для гармоничного слияния с фото и читаемости текста.', 'city-library'); ?></span>
                    </div>
                </div>

                <!-- Accent-only options (Position & Max Width) -->
                <div id="cl-hero-gif-accent-options" style="<?php echo ($gif_type !== 'accent') ? 'display: none;' : ''; ?> background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 18px;">
                    <h4 style="margin: 0 0 12px; font-size: 13px; font-weight: 700; color: #1e293b;"><?php _e('Настройки локального акцента:', 'city-library'); ?></h4>
                    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 220px;">
                            <label class="cl-row-label" for="hero_gif_position"><?php _e('Позиция акцента на баннере:', 'city-library'); ?></label>
                            <select id="hero_gif_position" name="hero_gif_position" style="width: 100%;">
                                <option value="bottom-right" <?php selected($gif_pos, 'bottom-right'); ?>><?php _e('Справа внизу (Рекомендуется для книг и персонажей)', 'city-library'); ?></option>
                                <option value="center-right" <?php selected($gif_pos, 'center-right'); ?>><?php _e('Справа по центру', 'city-library'); ?></option>
                                <option value="bottom-left" <?php selected($gif_pos, 'bottom-left'); ?>><?php _e('Слева внизу', 'city-library'); ?></option>
                                <option value="center-left" <?php selected($gif_pos, 'center-left'); ?>><?php _e('Слева по центру', 'city-library'); ?></option>
                                <option value="bottom-center" <?php selected($gif_pos, 'bottom-center'); ?>><?php _e('По центру внизу', 'city-library'); ?></option>
                                <option value="center" <?php selected($gif_pos, 'center'); ?>><?php _e('Строго по центру', 'city-library'); ?></option>
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 220px;">
                            <label class="cl-row-label" for="hero_gif_size"><?php _e('Максимальная ширина акцента:', 'city-library'); ?></label>
                            <select id="hero_gif_size" name="hero_gif_size" style="width: 100%;">
                                <option value="200px" <?php selected($gif_size, '200px'); ?>><?php _e('Компактный (200px)', 'city-library'); ?></option>
                                <option value="280px" <?php selected($gif_size, '280px'); ?>><?php _e('Средний (280px)', 'city-library'); ?></option>
                                <option value="340px" <?php selected($gif_size, '340px'); ?>><?php _e('Стандартный (340px) — Рекомендуется', 'city-library'); ?></option>
                                <option value="420px" <?php selected($gif_size, '420px'); ?>><?php _e('Крупный (420px)', 'city-library'); ?></option>
                                <option value="520px" <?php selected($gif_size, '520px'); ?>><?php _e('Максимальный (520px)', 'city-library'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Mobile Behavior -->
                <div class="cl-hero-admin-row">
                    <label class="cl-row-label"><?php _e('Отображение на мобильных устройствах (< 768px):', 'city-library'); ?></label>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <label class="cl-hero-bg-mode-card <?php echo ($gif_mobile === 'hide') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 200px;">
                            <input type="radio" name="hero_gif_mobile" value="hide" <?php checked($gif_mobile, 'hide'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Скрыть на смартфонах (Рекомендуется)', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Экономит заряд батареи, процессор и мобильный интернет посетителя.', 'city-library'); ?></span>
                            </div>
                        </label>
                        <label class="cl-hero-bg-mode-card <?php echo ($gif_mobile === 'scale') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 200px;">
                            <input type="radio" name="hero_gif_mobile" value="scale" <?php checked($gif_mobile, 'scale'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Адаптивно уменьшить', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Уменьшает размер анимации до 50% от исходного, сохраняя атмосферу.', 'city-library'); ?></span>
                            </div>
                        </label>
                        <label class="cl-hero-bg-mode-card <?php echo ($gif_mobile === 'keep') ? 'is-selected' : ''; ?>" style="flex: 1; min-width: 200px;">
                            <input type="radio" name="hero_gif_mobile" value="keep" <?php checked($gif_mobile, 'keep'); ?> />
                            <div>
                                <strong style="color: #0f172a; font-size: 13px;"><?php _e('Показывать как на ПК', 'city-library'); ?></strong>
                                <span class="cl-hero-admin-desc"><?php _e('Анимация выводится без принудительного масштабирования.', 'city-library'); ?></span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Interactive Live Mini Preview -->
                <div class="cl-hero-admin-row" style="margin-top: 24px;">
                    <label class="cl-row-label"><?php _e('Интерактивный Live Preview в админке:', 'city-library'); ?></label>
                    <span class="cl-hero-admin-desc" style="margin-bottom: 10px; display: block;"><?php _e('Мгновенно отображает эффект наложения выбранного GIF поверх фонового баннера с текущей прозрачностью и режимом смешивания.', 'city-library'); ?></span>
                    <div id="cl-hero-live-preview-box" style="position: relative; width: 100%; max-width: 620px; height: 180px; border-radius: 12px; overflow: hidden; background: #0f172a; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); border: 1px solid #334155;">
                        <!-- Background photo -->
                        <img id="cl-live-preview-bg" src="<?php echo esc_url(!empty($image) ? $image : get_template_directory_uri() . '/images/hero-bg.jpg'); ?>" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; -webkit-user-select: none;" alt="" />
                        <!-- Gradient tint -->
                        <div id="cl-live-preview-tint" style="position: absolute; inset: 0; background: <?php echo esc_attr($overlay_color); ?>; opacity: <?php echo esc_attr($overlay_opacity); ?>; pointer-events: none;"></div>
                        <!-- GIF overlay in preview -->
                        <div id="cl-live-preview-gif-wrap" style="position: absolute; inset: 0; pointer-events: none; opacity: <?php echo esc_attr($gif_opacity); ?>; <?php echo empty($gif_url) ? 'display: none;' : ''; ?>">
                            <img id="cl-live-preview-gif" src="<?php echo esc_url($gif_url); ?>" style="width: 100%; height: 100%; object-fit: cover; mix-blend-mode: <?php echo esc_attr($gif_blend); ?>;" alt="" />
                        </div>
                        <!-- Mock Content on top -->
                        <div style="position: relative; z-index: 10; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 16px; pointer-events: none;">
                            <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(4px); padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; color: #ffffff; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 1px;">Центральная библиотека</div>
                            <div id="cl-live-preview-title" style="color: #ffffff; font-size: 16px; font-weight: 700; text-shadow: 0 2px 8px rgba(0,0,0,0.8); line-height: 1.2;">Пример заголовка Hero</div>
                            <div style="display: inline-block; margin-top: 8px; background: #eab308; color: #0f172a; padding: 4px 14px; border-radius: 6px; font-size: 11px; font-weight: 700;">Кнопка действия</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Meta Box Script -->
    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.cl-hero-tab-btn').on('click', function(e) {
            e.preventDefault();
            var tabId = $(this).data('tab');
            $('.cl-hero-tab-btn').removeClass('is-active');
            $(this).addClass('is-active');
            $('.cl-hero-tab-content').removeClass('is-active');
            $('#cl-tab-' + tabId).addClass('is-active');
        });

        // Toggle visibility when checkbox changes
        function updateContainerVisibility() {
            if ($('#hero_custom_enable').is(':checked') || $('#page_bg_enable').is(':checked') || $('#hero_gif_enable').is(':checked')) {
                $('#cl-hero-settings-container').slideDown(200);
            } else {
                $('#cl-hero-settings-container').slideUp(200);
            }
        }
        $('#hero_custom_enable, #page_bg_enable, #hero_gif_enable').on('change', updateContainerVisibility);

        // Opacity slider & number sync for Page Background
        $('#page_bg_overlay_opacity_range').on('input', function() {
            $('#page_bg_overlay_opacity').val($(this).val());
        });
        $('#page_bg_overlay_opacity').on('input change', function() {
            $('#page_bg_overlay_opacity_range').val($(this).val());
        });

        // Live Font & Color Preview Update
        function updateFontPreview() {
            var selectedFont = $('#hero_custom_font').val() || 'montserrat';
            var previewBox = $('#hero_font_preview_box');
            previewBox.removeClass(function(index, className) {
                return (className.match(/(^|\s)font-hero-\S+/g) || []).join(' ');
            });
            previewBox.addClass('font-hero-' + selectedFont);

            var titleColor = $('#hero_custom_title_color').val() || '#ffffff';
            previewBox.css('color', titleColor);
        }
        $('#hero_custom_font').on('change', updateFontPreview);
        updateFontPreview();

        // Background mode cards active class (Hero)
        $('input[name="hero_custom_bg_mode"]').on('change', function() {
            $('input[name="hero_custom_bg_mode"]').closest('.cl-hero-bg-mode-card').removeClass('is-selected');
            $(this).closest('.cl-hero-bg-mode-card').addClass('is-selected');
        });

        // Fade mode cards active class & custom options toggle
        $('input[name="hero_custom_fade_mode"]').on('change', function() {
            $('input[name="hero_custom_fade_mode"]').closest('.cl-hero-bg-mode-card').removeClass('is-selected');
            $(this).closest('.cl-hero-bg-mode-card').addClass('is-selected');
            if ($(this).val() === 'custom') {
                $('#cl-hero-fade-custom-options').slideDown(200);
            } else {
                $('#cl-hero-fade-custom-options').slideUp(200);
            }
        });

        // Page background mode cards active class
        $('input[name="page_bg_mode"]').on('change', function() {
            $('input[name="page_bg_mode"]').closest('.cl-hero-bg-mode-card').removeClass('is-selected');
            $(this).closest('.cl-hero-bg-mode-card').addClass('is-selected');
        });

        // Page background card style active class
        $('input[name="page_bg_card_style"]').on('change', function() {
            $('input[name="page_bg_card_style"]').closest('.cl-hero-bg-mode-card').removeClass('is-selected');
            $(this).closest('.cl-hero-bg-mode-card').addClass('is-selected');
        });

        // Native Color Pickers
        if ($.fn.wpColorPicker) {
            $('#hero_custom_title_color').wpColorPicker({
                change: function(event, ui) {
                    $('#hero_font_preview_box').css('color', ui.color.toString());
                },
                clear: function() {
                    $('#hero_font_preview_box').css('color', '#ffffff');
                }
            });
            $('#hero_custom_subtitle_color').wpColorPicker();
            $('#hero_custom_fade_color').wpColorPicker();
            $('.cl-color-picker:not(#hero_custom_title_color):not(#hero_custom_subtitle_color):not(#hero_custom_fade_color)').wpColorPicker();
            $('#hero_custom_overlay_color').wpColorPicker();
        }

        // Manual Title Color Input Change
        $('#hero_custom_title_color').on('input change', function() {
            var col = $(this).val();
            if (col) {
                $('#hero_font_preview_box').css('color', col);
            }
        });

        // Media Uploader: Hero Background Image
        var mediaFrame;
        $('#cl_hero_upload_image_btn').on('click', function(e) {
            e.preventDefault();
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: '<?php echo esc_js(__('Выберите или загрузите фоновое изображение для Hero', 'city-library')); ?>',
                button: {
                    text: '<?php echo esc_js(__('Использовать это изображение', 'city-library')); ?>'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            mediaFrame.on('select', function() {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                var imageUrl = attachment.url;
                $('#hero_custom_image').val(imageUrl).trigger('change');
                $('#cl_hero_image_preview_img').attr('src', imageUrl);
                $('#cl_hero_image_preview_wrapper').show();
            });

            mediaFrame.open();
        });

        // Remove Image Button: Hero
        $('#cl_hero_remove_image_btn').on('click', function(e) {
            e.preventDefault();
            $('#hero_custom_image').val('').trigger('change');
            $('#cl_hero_image_preview_img').attr('src', '');
            $('#cl_hero_image_preview_wrapper').hide();
        });

        // Manual Image URL Input Change: Hero
        $('#hero_custom_image').on('input change', function() {
            var val = $(this).val().trim();
            if (val) {
                $('#cl_hero_image_preview_img').attr('src', val);
                $('#cl_hero_image_preview_wrapper').show();
            } else {
                $('#cl_hero_image_preview_img').attr('src', '');
                $('#cl_hero_image_preview_wrapper').hide();
            }
        });

        // Media Uploader: Page Background Image
        var pageBgFrame;
        $('#cl_page_bg_upload_btn').on('click', function(e) {
            e.preventDefault();
            if (pageBgFrame) {
                pageBgFrame.open();
                return;
            }

            pageBgFrame = wp.media({
                title: '<?php echo esc_js(__('Выберите или загрузите фоновое изображение для страницы', 'city-library')); ?>',
                button: {
                    text: '<?php echo esc_js(__('Использовать для фона страницы', 'city-library')); ?>'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            pageBgFrame.on('select', function() {
                var attachment = pageBgFrame.state().get('selection').first().toJSON();
                var imageUrl = attachment.url;
                $('#page_bg_image').val(imageUrl).trigger('change');
                $('#cl_page_bg_preview_img').attr('src', imageUrl);
                $('#cl_page_bg_preview_wrapper').show();
            });

            pageBgFrame.open();
        });

        // Remove Image Button: Page Background
        $('#cl_page_bg_remove_btn').on('click', function(e) {
            e.preventDefault();
            $('#page_bg_image').val('').trigger('change');
            $('#cl_page_bg_preview_img').attr('src', '');
            $('#cl_page_bg_preview_wrapper').hide();
        });

        // Manual Image URL Input Change: Page Background
        $('#page_bg_image').on('input change', function() {
            var val = $(this).val().trim();
            if (val) {
                $('#cl_page_bg_preview_img').attr('src', val);
                $('#cl_page_bg_preview_wrapper').show();
            } else {
                $('#cl_page_bg_preview_img').attr('src', '');
                $('#cl_page_bg_preview_wrapper').hide();
            }
        });

        // Media Uploader: Hero GIF Animation
        var gifMediaFrame;
        $('#cl_hero_gif_upload_btn').on('click', function(e) {
            e.preventDefault();
            if (gifMediaFrame) {
                gifMediaFrame.open();
                return;
            }

            gifMediaFrame = wp.media({
                title: '<?php echo esc_js(__('Выберите или загрузите GIF-анимацию для Hero', 'city-library')); ?>',
                button: {
                    text: '<?php echo esc_js(__('Использовать эту анимацию', 'city-library')); ?>'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            gifMediaFrame.on('select', function() {
                var attachment = gifMediaFrame.state().get('selection').first().toJSON();
                var gifUrl = attachment.url;
                $('#hero_gif_url').val(gifUrl).trigger('change');
            });

            gifMediaFrame.open();
        });

        // Remove GIF Button
        $('#cl_hero_gif_remove_btn').on('click', function(e) {
            e.preventDefault();
            $('#hero_gif_url').val('').trigger('change');
        });

        // Manual GIF URL Input Change
        $('#hero_gif_url').on('input change', function() {
            var val = $(this).val().trim();
            if (val) {
                $('#cl_hero_gif_preview_img').attr('src', val);
                $('#cl_hero_gif_preview_wrapper').show();
                $('#cl-live-preview-gif').attr('src', val);
                $('#cl-live-preview-gif-wrap').show();
            } else {
                $('#cl_hero_gif_preview_img').attr('src', '');
                $('#cl_hero_gif_preview_wrapper').hide();
                $('#cl-live-preview-gif').attr('src', '');
                $('#cl-live-preview-gif-wrap').hide();
            }
        });

        // GIF Opacity Range Sync & Preview
        $('#hero_gif_opacity_range').on('input', function() {
            var val = $(this).val();
            $('#hero_gif_opacity').val(val);
            $('#cl_hero_gif_opacity_val').text(Math.round(val * 100) + '%');
            $('#cl-live-preview-gif-wrap').css('opacity', val);
        });

        $('#hero_gif_opacity').on('input change', function() {
            var val = $(this).val();
            $('#hero_gif_opacity_range').val(val);
            $('#cl_hero_gif_opacity_val').text(Math.round(val * 100) + '%');
            $('#cl-live-preview-gif-wrap').css('opacity', val);
        });

        // GIF Blend Mode Change & Preview
        $('#hero_gif_blend_mode').on('change', function() {
            var mode = $(this).val();
            $('#cl-live-preview-gif').css('mix-blend-mode', mode);
        });

        // GIF Type toggle & Accent options
        $('input[name="hero_gif_type"]').on('change', function() {
            $('input[name="hero_gif_type"]').closest('.cl-hero-bg-mode-card').removeClass('is-selected');
            $(this).closest('.cl-hero-bg-mode-card').addClass('is-selected');
            if ($(this).val() === 'accent') {
                $('#cl-hero-gif-accent-options').slideDown(200);
            } else {
                $('#cl-hero-gif-accent-options').slideUp(200);
            }
        });

        // GIF Mobile active card
        $('input[name="hero_gif_mobile"]').on('change', function() {
            $('input[name="hero_gif_mobile"]').closest('.cl-hero-bg-mode-card').removeClass('is-selected');
            $(this).closest('.cl-hero-bg-mode-card').addClass('is-selected');
        });

        // Sync background photo in Live Preview on change
        $('#hero_custom_image').on('input change', function() {
            var val = $(this).val().trim();
            if (val) {
                $('#cl-live-preview-bg').attr('src', val);
            }
        });
    });
    </script>
    <?php
}

// Save the Meta Box data
function city_library_save_hero_meta_box_data($post_id) {
    if (!isset($_POST['city_library_hero_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['city_library_hero_meta_box_nonce'], 'city_library_save_hero_meta_box_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'page' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Enable toggle
    if (isset($_POST['hero_custom_enable']) && $_POST['hero_custom_enable'] === 'yes') {
        update_post_meta($post_id, '_hero_custom_enable', 'yes');
    } else {
        delete_post_meta($post_id, '_hero_custom_enable');
    }

    // Font selection
    $allowed_fonts = ['dygyn', 'shihan', 'comfortaa', 'caveat', 'montserrat', 'monomakh', 'ruslan', 'cormorant', 'playfair', 'merriweather', 'oswald'];
    if (isset($_POST['hero_custom_font']) && in_array($_POST['hero_custom_font'], $allowed_fonts, true)) {
        update_post_meta($post_id, '_hero_custom_font', sanitize_text_field($_POST['hero_custom_font']));
    } else {
        update_post_meta($post_id, '_hero_custom_font', 'montserrat');
    }

    // Background Mode
    $allowed_modes = ['hero-fixed', 'full-page-fixed', 'scroll'];
    if (isset($_POST['hero_custom_bg_mode']) && in_array($_POST['hero_custom_bg_mode'], $allowed_modes, true)) {
        update_post_meta($post_id, '_hero_custom_bg_mode', sanitize_text_field($_POST['hero_custom_bg_mode']));
    } else {
        update_post_meta($post_id, '_hero_custom_bg_mode', 'hero-fixed');
    }

    // Title (allow safe tags)
    if (isset($_POST['hero_custom_title']) && trim($_POST['hero_custom_title']) !== '') {
        update_post_meta($post_id, '_hero_custom_title', wp_kses_post($_POST['hero_custom_title']));
    } else {
        delete_post_meta($post_id, '_hero_custom_title');
    }

    // Subtitle
    if (isset($_POST['hero_custom_subtitle']) && trim($_POST['hero_custom_subtitle']) !== '') {
        update_post_meta($post_id, '_hero_custom_subtitle', sanitize_textarea_field($_POST['hero_custom_subtitle']));
    } else {
        delete_post_meta($post_id, '_hero_custom_subtitle');
    }

    // Title Color
    if (isset($_POST['hero_custom_title_color']) && trim($_POST['hero_custom_title_color']) !== '') {
        update_post_meta($post_id, '_hero_custom_title_color', sanitize_hex_color($_POST['hero_custom_title_color']));
    } else {
        delete_post_meta($post_id, '_hero_custom_title_color');
    }

    // Subtitle Color
    if (isset($_POST['hero_custom_subtitle_color']) && trim($_POST['hero_custom_subtitle_color']) !== '') {
        update_post_meta($post_id, '_hero_custom_subtitle_color', sanitize_hex_color($_POST['hero_custom_subtitle_color']));
    } else {
        delete_post_meta($post_id, '_hero_custom_subtitle_color');
    }

    // Hero to Content Fade Transition
    $allowed_fade_modes = ['smooth', 'none', 'custom'];
    if (isset($_POST['hero_custom_fade_mode']) && in_array($_POST['hero_custom_fade_mode'], $allowed_fade_modes, true)) {
        update_post_meta($post_id, '_hero_custom_fade_mode', sanitize_text_field($_POST['hero_custom_fade_mode']));
    } else {
        update_post_meta($post_id, '_hero_custom_fade_mode', 'smooth');
    }

    if (isset($_POST['hero_custom_fade_color']) && trim($_POST['hero_custom_fade_color']) !== '') {
        update_post_meta($post_id, '_hero_custom_fade_color', sanitize_hex_color($_POST['hero_custom_fade_color']));
    } else {
        delete_post_meta($post_id, '_hero_custom_fade_color');
    }

    $allowed_fade_heights = ['100px', '160px', '240px'];
    if (isset($_POST['hero_custom_fade_height']) && in_array($_POST['hero_custom_fade_height'], $allowed_fade_heights, true)) {
        update_post_meta($post_id, '_hero_custom_fade_height', sanitize_text_field($_POST['hero_custom_fade_height']));
    } else {
        update_post_meta($post_id, '_hero_custom_fade_height', '160px');
    }

    // Image URL
    if (isset($_POST['hero_custom_image']) && trim($_POST['hero_custom_image']) !== '') {
        update_post_meta($post_id, '_hero_custom_image', esc_url_raw($_POST['hero_custom_image']));
    } else {
        delete_post_meta($post_id, '_hero_custom_image');
    }

    // Overlay Color
    if (isset($_POST['hero_custom_overlay_color']) && trim($_POST['hero_custom_overlay_color']) !== '') {
        update_post_meta($post_id, '_hero_custom_overlay_color', sanitize_hex_color($_POST['hero_custom_overlay_color']));
    } else {
        delete_post_meta($post_id, '_hero_custom_overlay_color');
    }

    // Overlay Opacity
    if (isset($_POST['hero_custom_overlay_opacity']) && $_POST['hero_custom_overlay_opacity'] !== '') {
        $opacity = floatval($_POST['hero_custom_overlay_opacity']);
        if ($opacity < 0) $opacity = 0;
        if ($opacity > 1) $opacity = 1;
        update_post_meta($post_id, '_hero_custom_overlay_opacity', (string)$opacity);
    } else {
        delete_post_meta($post_id, '_hero_custom_overlay_opacity');
    }

    // Align
    $allowed_align = ['left', 'center', 'right', ''];
    if (isset($_POST['hero_custom_align']) && in_array($_POST['hero_custom_align'], $allowed_align, true)) {
        update_post_meta($post_id, '_hero_custom_align', sanitize_text_field($_POST['hero_custom_align']));
    } else {
        delete_post_meta($post_id, '_hero_custom_align');
    }

    // Height
    $allowed_heights = ['min-h-[60vh]', 'py-32', ''];
    if (isset($_POST['hero_custom_height']) && in_array($_POST['hero_custom_height'], $allowed_heights, true)) {
        update_post_meta($post_id, '_hero_custom_height', sanitize_text_field($_POST['hero_custom_height']));
    } else {
        delete_post_meta($post_id, '_hero_custom_height');
    }

    // Action button text and link
    if (isset($_POST['hero_custom_btn1_text']) && trim($_POST['hero_custom_btn1_text']) !== '') {
        update_post_meta($post_id, '_hero_custom_btn1_text', sanitize_text_field($_POST['hero_custom_btn1_text']));
    } else {
        delete_post_meta($post_id, '_hero_custom_btn1_text');
    }

    if (isset($_POST['hero_custom_btn1_link']) && trim($_POST['hero_custom_btn1_link']) !== '') {
        update_post_meta($post_id, '_hero_custom_btn1_link', sanitize_text_field($_POST['hero_custom_btn1_link']));
    } else {
        delete_post_meta($post_id, '_hero_custom_btn1_link');
    }

    // --- Page Background (Behind Blocks) Meta Fields ---

    // Page Background Enable
    if (isset($_POST['page_bg_enable']) && $_POST['page_bg_enable'] === 'yes') {
        update_post_meta($post_id, '_page_bg_enable', 'yes');
    } else {
        delete_post_meta($post_id, '_page_bg_enable');
    }

    // Page Background Color
    if (isset($_POST['page_bg_color']) && trim($_POST['page_bg_color']) !== '') {
        update_post_meta($post_id, '_page_bg_color', sanitize_hex_color($_POST['page_bg_color']) ?: '#f6f8f6');
    } else {
        delete_post_meta($post_id, '_page_bg_color');
    }

    // Page Background Image URL
    if (isset($_POST['page_bg_image']) && trim($_POST['page_bg_image']) !== '') {
        update_post_meta($post_id, '_page_bg_image', esc_url_raw($_POST['page_bg_image']));
    } else {
        delete_post_meta($post_id, '_page_bg_image');
    }

    // Page Background Mode (fixed, scroll, repeat)
    $allowed_page_modes = ['fixed', 'scroll', 'repeat'];
    if (isset($_POST['page_bg_mode']) && in_array($_POST['page_bg_mode'], $allowed_page_modes, true)) {
        update_post_meta($post_id, '_page_bg_mode', sanitize_text_field($_POST['page_bg_mode']));
    } else {
        update_post_meta($post_id, '_page_bg_mode', 'fixed');
    }

    // Page Background Fit (cover, contain, auto)
    $allowed_fits = ['cover', 'contain', 'auto'];
    if (isset($_POST['page_bg_fit']) && in_array($_POST['page_bg_fit'], $allowed_fits, true)) {
        update_post_meta($post_id, '_page_bg_fit', sanitize_text_field($_POST['page_bg_fit']));
    } else {
        update_post_meta($post_id, '_page_bg_fit', 'cover');
    }

    // Page Background Overlay Color
    if (isset($_POST['page_bg_overlay_color']) && trim($_POST['page_bg_overlay_color']) !== '') {
        update_post_meta($post_id, '_page_bg_overlay_color', sanitize_hex_color($_POST['page_bg_overlay_color']) ?: '#000000');
    } else {
        delete_post_meta($post_id, '_page_bg_overlay_color');
    }

    // Page Background Overlay Opacity
    if (isset($_POST['page_bg_overlay_opacity']) && $_POST['page_bg_overlay_opacity'] !== '') {
        $bg_op = floatval($_POST['page_bg_overlay_opacity']);
        if ($bg_op < 0) $bg_op = 0;
        if ($bg_op > 1) $bg_op = 1;
        update_post_meta($post_id, '_page_bg_overlay_opacity', (string)$bg_op);
    } else {
        delete_post_meta($post_id, '_page_bg_overlay_opacity');
    }

    // Page Background Card Style (glass, solid, warm, dark)
    $allowed_card_styles = ['glass', 'solid', 'warm', 'dark'];
    if (isset($_POST['page_bg_card_style']) && in_array($_POST['page_bg_card_style'], $allowed_card_styles, true)) {
        update_post_meta($post_id, '_page_bg_card_style', sanitize_text_field($_POST['page_bg_card_style']));
    } else {
        update_post_meta($post_id, '_page_bg_card_style', 'glass');
    }

    // --- Hero GIF Animation Overlay Meta Fields ---
    if (isset($_POST['hero_gif_enable']) && $_POST['hero_gif_enable'] === 'yes') {
        update_post_meta($post_id, '_hero_gif_enable', 'yes');
    } else {
        delete_post_meta($post_id, '_hero_gif_enable');
    }

    if (isset($_POST['hero_gif_url']) && trim($_POST['hero_gif_url']) !== '') {
        update_post_meta($post_id, '_hero_gif_url', esc_url_raw($_POST['hero_gif_url']));
    } else {
        delete_post_meta($post_id, '_hero_gif_url');
    }

    $allowed_gif_types = ['fullscreen', 'accent'];
    if (isset($_POST['hero_gif_type']) && in_array($_POST['hero_gif_type'], $allowed_gif_types, true)) {
        update_post_meta($post_id, '_hero_gif_type', sanitize_text_field($_POST['hero_gif_type']));
    } else {
        update_post_meta($post_id, '_hero_gif_type', 'fullscreen');
    }

    $allowed_gif_positions = ['bottom-right', 'center-right', 'bottom-left', 'center-left', 'bottom-center', 'center'];
    if (isset($_POST['hero_gif_position']) && in_array($_POST['hero_gif_position'], $allowed_gif_positions, true)) {
        update_post_meta($post_id, '_hero_gif_position', sanitize_text_field($_POST['hero_gif_position']));
    } else {
        update_post_meta($post_id, '_hero_gif_position', 'bottom-right');
    }

    $allowed_gif_blends = ['screen', 'normal', 'multiply', 'overlay', 'soft-light'];
    if (isset($_POST['hero_gif_blend_mode']) && in_array($_POST['hero_gif_blend_mode'], $allowed_gif_blends, true)) {
        update_post_meta($post_id, '_hero_gif_blend_mode', sanitize_text_field($_POST['hero_gif_blend_mode']));
    } else {
        update_post_meta($post_id, '_hero_gif_blend_mode', 'screen');
    }

    if (isset($_POST['hero_gif_opacity']) && $_POST['hero_gif_opacity'] !== '') {
        $g_op = floatval($_POST['hero_gif_opacity']);
        if ($g_op < 0.10) $g_op = 0.10;
        if ($g_op > 1.0) $g_op = 1.0;
        update_post_meta($post_id, '_hero_gif_opacity', (string)$g_op);
    } else {
        delete_post_meta($post_id, '_hero_gif_opacity');
    }

    $allowed_gif_sizes = ['200px', '280px', '340px', '420px', '520px'];
    if (isset($_POST['hero_gif_size']) && in_array($_POST['hero_gif_size'], $allowed_gif_sizes, true)) {
        update_post_meta($post_id, '_hero_gif_size', sanitize_text_field($_POST['hero_gif_size']));
    } else {
        update_post_meta($post_id, '_hero_gif_size', '340px');
    }

    $allowed_gif_mobiles = ['hide', 'scale', 'keep'];
    if (isset($_POST['hero_gif_mobile']) && in_array($_POST['hero_gif_mobile'], $allowed_gif_mobiles, true)) {
        update_post_meta($post_id, '_hero_gif_mobile', sanitize_text_field($_POST['hero_gif_mobile']));
    } else {
        update_post_meta($post_id, '_hero_gif_mobile', 'hide');
    }
}
add_action('save_post', 'city_library_save_hero_meta_box_data');
