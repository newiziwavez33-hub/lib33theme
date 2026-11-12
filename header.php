<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('font-sans antialiased bg-background-light text-slate-900 transition-colors duration-300'); ?>>
<?php wp_body_open(); ?>
<?php
// Render Adaptive Page Background Canvas (Behind Content Blocks)
if (function_exists('city_library_render_page_background')) {
    city_library_render_page_background();
}
?>
<a class="sr-only focus:not-sr-only focus:absolute focus:z-[100] focus:p-4 focus:bg-white focus:text-primary transition-all" href="#primary"><?php esc_html_e( 'Перейти к основному содержимому', 'city-library' ); ?></a>

<?php
$header_style = get_theme_mod('header_style', 'default');
$menu_style = get_theme_mod('menu_style', 'default');

$header_classes = 'hidden lg:block fixed top-[5px] w-full z-50 bg-white/70 backdrop-blur-xl border-b border-slate-200 hover:bg-white transition-colors duration-300 group';
$container_classes = 'w-full px-4 sm:px-6 lg:px-8';
$flex_classes = 'flex flex-nowrap justify-between items-center h-auto min-h-[4rem] py-1 lg:py-0';

// Header Style Logic
if ($header_style === 'centered') {
    $flex_classes = 'flex flex-nowrap justify-center items-center h-auto py-2 space-x-2 lg:space-x-8 xl:space-x-12';
} elseif ($header_style === 'minimal') {
    // Logic handled in layout structure below (nav hidden)
} elseif ($header_style === 'full-width') {
    $container_classes = 'w-full px-0';
} elseif ($header_style === 'transparent-overlay') {
    $header_classes = 'hidden lg:block absolute top-[5px] w-full z-50 bg-transparent border-b border-white/10 hover:bg-white/90 transition-all duration-300 group hover:text-slate-900 text-white';
} elseif ($header_style === 'floating') {
    $header_classes = 'hidden lg:block fixed top-[21px] left-4 right-4 z-50 bg-white/80 backdrop-blur-md rounded-2xl shadow-lg border border-slate-200 transition-all duration-300';
    $container_classes = 'w-full px-6';
} elseif ($header_style === 'dark-mode') {
    $header_classes = 'hidden lg:block fixed top-[5px] w-full z-50 bg-slate-900 border-b border-slate-800 text-white transition-colors duration-300';
}

// Menu Style Classes
$menu_item_classes = 'menu-style-' . $menu_style;
?>

<header id="masthead" class="<?php echo esc_attr($header_classes); ?>">
    <div class="<?php echo esc_attr($container_classes); ?>">
        <div class="<?php echo esc_attr($flex_classes); ?>">
            <!-- Site Identity (Logo + Title) - Hides on scroll -->
            <div id="site-identity" class="flex items-center space-x-1 xl:space-x-2 h-full shrink-0 transition-all duration-500 overflow-hidden max-w-[140px] lg:max-w-[180px] xl:max-w-[220px] opacity-100">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center space-x-1 xl:space-x-2 h-full shrink-0 group/logo focus:outline-none">
                    <?php if (has_custom_logo()) : ?>
                        <div class="custom-logo-wrapper h-7 xl:h-9 w-auto flex items-center [&_a]:h-full [&_a]:w-auto [&_img]:h-full [&_img]:w-auto [&_img]:object-contain">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php else : ?>
                        <div class="w-7 h-7 xl:w-9 xl:h-9 bg-secondary rounded-lg flex items-center justify-center group-hover/logo:bg-primary transition-colors duration-300 shrink-0">
                            <span class="material-symbols-outlined text-white text-sm" aria-hidden="true">menu_book</span>
                        </div>
                    <?php endif; ?>

                    <div class="block">
                        <p class="text-[9px] xl:text-[10px] font-bold uppercase tracking-widest text-secondary group-hover/logo:text-primary transition-colors duration-300 leading-tight"><?php echo esc_html(get_theme_mod('header_subtitle', __('Центральная городская', 'city-library'))); ?></p>
                        <p class="text-xs xl:text-sm font-display font-bold leading-tight group-hover/logo:text-primary transition-colors duration-300"><?php echo esc_html(get_theme_mod('header_title', __('Библиотека', 'city-library'))); ?></p>
                    </div>
                </a>
            </div>

            <?php if ($header_style !== 'minimal') : ?>
            <nav class="hidden lg:landscape:flex items-center justify-center flex-1 min-w-0 mx-2 xl:mx-4">
                 <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container'      => false,
                        'items_wrap'     => '<ul id="%1$s" class="flex flex-nowrap items-center gap-x-1 lg:gap-x-2 xl:gap-x-4 text-xs xl:text-sm %2$s list-none m-0 p-0 ' . esc_attr($menu_item_classes) . '">%3$s</ul>',
                        'walker'         => new City_Library_Walker_Nav_Menu(),
                    ));
                ?>
            </nav>
            <?php endif; ?>

            <!-- Header Actions (Right Side) -->
            <div class="flex items-center space-x-0.5 shrink-0">
                <!-- Search Button -->
                <button id="search-toggle"
                    class="p-2 rounded-lg transition-all duration-200 text-slate-500 hover:text-primary hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none active:scale-95"
                    aria-label="<?php esc_attr_e('Поиск', 'city-library'); ?>">
                    <span class="material-symbols-outlined block text-xl" aria-hidden="true">search</span>
                </button>

                <!-- Accessibility Button -->
                <button id="accessibility-button"
                    class="p-2 rounded-lg transition-all duration-200 text-slate-500 hover:text-amber-600 hover:bg-amber-50 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:outline-none active:scale-95"
                    aria-label="<?php esc_attr_e('Версия для слабовидящих', 'city-library'); ?>">
                    <span class="material-symbols-outlined block text-xl" aria-hidden="true">visibility</span>
                </button>

                <!-- Mobile Menu Toggle (Visible on mobile/kiosk, and always in minimal header style) -->
                <button class="mobile-menu-toggle-btn <?php echo ($header_style === 'minimal') ? '' : 'lg:landscape:hidden'; ?> p-2.5 lg:portrait:p-3 rounded-lg transition-all duration-200 text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none active:scale-95"
                    aria-label="<?php esc_attr_e('Меню', 'city-library'); ?>">
                    <span class="material-symbols-outlined block text-xl" aria-hidden="true">menu</span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay - Ultra AAA Design -->
<?php
// Determine Mobile Menu Theme Classes
$mob_menu_style = get_theme_mod('mobile_menu_style', 'default');
$mob_menu_container_classes = 'absolute right-0 top-0 h-full w-full sm:w-[400px] lg:portrait:w-[480px] bg-white/90 backdrop-blur-2xl shadow-[-10px_0_30px_rgba(0,0,0,0.05)] flex flex-col pointer-events-auto overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] sm:rounded-l-[2rem] sm:border-l sm:border-y sm:border-white/50 sm:my-2 sm:h-[calc(100%-1rem)]'; // Base 'inner' classes

// Theme Specific Overrides
if ($mob_menu_style === 'ios-blur') {
    $mob_menu_container_classes = 'absolute right-0 top-0 h-full w-full sm:w-[380px] lg:portrait:w-[480px] bg-white/80 backdrop-blur-3xl shadow-none border-l border-white/40 flex flex-col pointer-events-auto overflow-hidden sm:rounded-l-[2rem]';
} elseif ($mob_menu_style === 'neon-glow') {
    $mob_menu_container_classes .= ' bg-slate-900/95 text-white border-l border-primary/30 shadow-[0_0_50px_rgba(11,121,48,0.3)]';
} elseif ($mob_menu_style === 'glassmorphism') {
    $mob_menu_container_classes = 'absolute right-0 top-0 h-full w-full sm:w-[400px] lg:portrait:w-[480px] bg-white/50 backdrop-blur-3xl shadow-[-10px_0_40px_rgba(0,0,0,0.1)] border-l border-white/60 flex flex-col pointer-events-auto overflow-hidden sm:rounded-l-[2.5rem]';
} elseif ($mob_menu_style === 'sidebar-drawer') {
    $mob_menu_container_classes .= ' rounded-l-[2.5rem] my-4 mr-4 h-[calc(100%-2rem)] shadow-2xl border border-slate-100';
}
?>

<div id="mobile-menu" class="fixed inset-0 h-[100dvh] z-[9999] bg-slate-900/20 backdrop-blur-[2px] transition-opacity duration-300 <?php echo ($header_style === 'minimal') ? '' : 'lg:landscape:hidden'; ?> pointer-events-none opacity-0" aria-hidden="true">
    <div class="<?php echo esc_attr($mob_menu_container_classes); ?> mobile-menu-inner transform transition-transform duration-500 translate-x-full">

        <!-- Header -->
        <div class="flex justify-between items-center p-6 shrink-0 border-b border-slate-100/50">
            <div class="flex items-center space-x-3">
               <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined" aria-hidden="true">menu_open</span>
               </div>
               <div>
                   <span class="block text-sm font-bold font-display uppercase tracking-wider text-primary"><?php esc_html_e('Меню', 'city-library'); ?></span>
                   <span class="block text-[10px] text-slate-400 leading-none"><?php echo esc_html(get_theme_mod('header_subtitle', 'Навигация')); ?></span>
               </div>
            </div>
            <button id="mobile-menu-close" class="group p-2 bg-slate-50 hover:bg-red-50 text-slate-500 hover:text-red-500 rounded-full transition-all duration-300 transform hover:rotate-90 active:scale-95" aria-label="<?php esc_attr_e('Закрыть меню', 'city-library'); ?>">
                <span class="material-symbols-outlined text-2xl" aria-hidden="true">close</span>
            </button>
        </div>

        <!-- Content -->
        <nav class="flex-grow p-6 space-y-2 overflow-y-auto min-h-0 overscroll-contain custom-scrollbar">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '<ul class="space-y-3">%3$s</ul>',
                'walker'         => new City_Library_Walker_Nav_Menu(),
            ));
            ?>
        </nav>

        <!-- Footer / Contact -->
        <div class="mt-auto p-6 bg-slate-50/50 border-t border-slate-100/50 shrink-0 space-y-4">
             <!-- Quick Actions -->
             <div class="grid grid-cols-2 gap-3">
                 <a href="<?php echo esc_url(get_theme_mod('hero_primary_button_link', '#events')); ?>" class="flex items-center justify-center space-x-2 py-3 bg-white border border-slate-200 rounded-xl shadow-sm text-xs font-bold text-slate-700 hover:border-primary hover:text-primary transition-colors">
                     <span class="material-symbols-outlined text-lg" aria-hidden="true">event</span>
                     <span>Афиша</span>
                 </a>
                 <a href="<?php echo esc_url(home_url('/?s=')); ?>" class="flex items-center justify-center space-x-2 py-3 bg-white border border-slate-200 rounded-xl shadow-sm text-xs font-bold text-slate-700 hover:border-primary hover:text-primary transition-colors">
                     <span class="material-symbols-outlined text-lg" aria-hidden="true">search</span>
                     <span>Поиск</span>
                 </a>
                 <button type="button" onclick="document.getElementById('mobile-menu-close')?.click(); document.getElementById('accessibility-button')?.click();" class="col-span-2 flex items-center justify-center space-x-2 py-3 bg-white border border-slate-200 rounded-xl shadow-sm text-xs font-bold text-slate-700 hover:border-amber-500 hover:text-amber-600 transition-colors">
                     <span class="material-symbols-outlined text-lg" aria-hidden="true">visibility</span>
                     <span><?php esc_html_e('Версия для слабовидящих', 'city-library'); ?></span>
                 </button>
             </div>

             <!-- Copyright -->
             <div class="text-center">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest"><?php echo esc_html(get_theme_mod('footer_copyright')); ?></p>
             </div>
        </div>
    </div>
</div>

<main>
<?php
// Determine if we should show a custom hero for this specific post/page
$is_custom_hero = false;
$post_id = get_the_ID();
if ((is_single() || is_page()) && $post_id) {
    if (get_post_meta($post_id, '_hero_custom_enable', true) === 'yes' || get_post_meta($post_id, '_hero_gif_enable', true) === 'yes') {
        $is_custom_hero = true;
    }
}

// Global toggle logic: Show if Custom Hero is enabled, OR if the global setting allows it
if ($is_custom_hero || get_theme_mod('show_hero_section', true)) :
?>
<?php
    $hero_color = get_theme_mod('hero_overlay_color', '#1a3c34');
    $hero_opacity = get_theme_mod('hero_bg_opacity', '0.5');
    $hero_height_class = 'min-h-[70vh] sm:min-h-[65vh] lg:portrait:min-h-[52vh] lg:landscape:min-h-screen';
    $hero_font = 'montserrat';
    $hero_bg_mode = 'hero-fixed';

    // Fetch values: Use custom meta if enabled, else fallback to customizer defaults
    if ($is_custom_hero) {
        $meta_title = get_post_meta($post_id, '_hero_custom_title', true);
        $meta_subtitle = get_post_meta($post_id, '_hero_custom_subtitle', true);
        $meta_image = get_post_meta($post_id, '_hero_custom_image', true);
        $meta_font = get_post_meta($post_id, '_hero_custom_font', true);
        $meta_bg_mode = get_post_meta($post_id, '_hero_custom_bg_mode', true);
        $meta_btn1_text = get_post_meta($post_id, '_hero_custom_btn1_text', true);
        $meta_btn1_link = get_post_meta($post_id, '_hero_custom_btn1_link', true);

        $meta_color = get_post_meta($post_id, '_hero_custom_overlay_color', true);
        $meta_opacity = get_post_meta($post_id, '_hero_custom_overlay_opacity', true);
        $meta_align = get_post_meta($post_id, '_hero_custom_align', true);
        $meta_height = get_post_meta($post_id, '_hero_custom_height', true);

        // Hero Text Colors
        $meta_title_color = get_post_meta($post_id, '_hero_custom_title_color', true);
        $meta_subtitle_color = get_post_meta($post_id, '_hero_custom_subtitle_color', true);
        $hero_title_color = !empty($meta_title_color) ? $meta_title_color : '#ffffff';
        $hero_subtitle_color = !empty($meta_subtitle_color) ? $meta_subtitle_color : '#e2e8f0';

        // Hero to Content Fade Transition
        $meta_fade_mode = get_post_meta($post_id, '_hero_custom_fade_mode', true) ?: 'smooth';
        $meta_fade_color = get_post_meta($post_id, '_hero_custom_fade_color', true);
        $meta_fade_height = get_post_meta($post_id, '_hero_custom_fade_height', true) ?: '160px';

        $hero_fade_mode = $meta_fade_mode;
        $hero_fade_height = $meta_fade_height;

        if ($hero_fade_mode === 'custom' && !empty($meta_fade_color)) {
            $hero_fade_color = $meta_fade_color;
        } else {
            $page_bg_color_meta = get_post_meta($post_id, '_page_bg_color', true);
            if (get_post_meta($post_id, '_page_bg_enable', true) === 'yes' && !empty($page_bg_color_meta)) {
                $hero_fade_color = $page_bg_color_meta;
            } elseif (get_theme_mod('global_page_bg_enable', false)) {
                $hero_fade_color = get_theme_mod('global_page_bg_color', '#f6f8f6');
            } else {
                $hero_fade_color = get_theme_mod('bg_body_color', '#f6f8f6');
            }
        }

        $hero_title = !empty($meta_title) ? $meta_title : get_the_title();
        $hero_subtitle = !empty($meta_subtitle) ? $meta_subtitle : '';
        $hero_image_url = !empty($meta_image) ? $meta_image : get_theme_mod('hero_background_image', get_template_directory_uri() . '/images/hero-bg.jpg');
        $btn1_text = $meta_btn1_text;
        $btn1_link = !empty($meta_btn1_link) ? $meta_btn1_link : '#content';
        $btn2_text = ''; // Secondary button disabled for custom hero by default to keep it clean
        $hero_show_badge = false; // Hide badge on custom posts

        if (!empty($meta_font)) {
            $hero_font = $meta_font;
        }
        if (!empty($meta_bg_mode)) {
            $hero_bg_mode = $meta_bg_mode;
        }

        $hero_color = !empty($meta_color) ? $meta_color : get_theme_mod('hero_overlay_color', '#1a3c34');
        $hero_opacity = ($meta_opacity !== '') ? $meta_opacity : get_theme_mod('hero_bg_opacity', '0.5');
        $hero_align = !empty($meta_align) ? $meta_align : get_theme_mod('hero_align', 'center');
        $hero_height_class = (!empty($meta_height) && $meta_height !== 'min-h-screen') ? $meta_height : 'min-h-[70vh] sm:min-h-[65vh] lg:portrait:min-h-[52vh] lg:landscape:min-h-screen';
    } else {
        $hero_title = get_theme_mod('hero_title', 'Твой мир, <span class="text-primary italic text-glow">Твоя</span> <br/>библиотека');
        $hero_subtitle = get_theme_mod('hero_subtitle', 'Центральная городская библиотека — пространство для открытий, творчества и вдохновения. Мы объединяем традиции и современные технологии.');
        $hero_image_url = get_theme_mod('hero_background_image', get_template_directory_uri() . '/images/hero-bg.jpg');
        $btn1_text = get_theme_mod('hero_primary_button_text', 'АФИША МЕРОПРИЯТИЙ');
        $btn1_link = get_theme_mod('hero_primary_button_link', '#events');
        $btn2_text = get_theme_mod('hero_secondary_button_text', 'УЗНАТЬ БОЛЬШЕ');
        $btn2_link = get_theme_mod('hero_secondary_button_link', '#about');
        $hero_show_badge = get_theme_mod('hero_show_badge', true);

        $hero_color = get_theme_mod('hero_overlay_color', '#1a3c34');
        $hero_opacity = get_theme_mod('hero_bg_opacity', '0.5');
        $hero_align = get_theme_mod('hero_align', 'center');
        $hero_bg_mode = 'default';

        $hero_title_color = '#ffffff';
        $hero_subtitle_color = '#e2e8f0';
        $hero_fade_mode = 'smooth';
        $hero_fade_color = get_theme_mod('bg_body_color', '#f6f8f6');
        $hero_fade_height = '140px';
    }

    // Apply styling rules based on chosen properties
    list($r, $g, $b) = sscanf($hero_color, "#%02x%02x%02x") ?: [26, 60, 52]; // Default to #1a3c34 if parsing fails
    $hero_gradient = "linear-gradient(rgba($r, $g, $b, $hero_opacity), rgba($r, $g, $b, $hero_opacity))";

    $hero_font_class = 'font-hero-' . sanitize_html_class($hero_font);

    $hero_text_align_class = 'text-' . $hero_align;
    $hero_flex_align_class = ($hero_align === 'left') ? 'items-start justify-start text-left' : (($hero_align === 'right') ? 'items-end justify-end text-right' : 'items-center justify-center text-center');
    $hero_mx_class = ($hero_align === 'left' || $hero_align === 'right') ? 'mx-0' : 'mx-auto';
    $hero_title_size = get_theme_mod('hero_title_size', 'text-5xl md:text-7xl lg:text-8xl');
?>
<?php if ($is_custom_hero && $hero_bg_mode === 'full-page-fixed' && get_post_meta($post_id, '_page_bg_enable', true) !== 'yes') : ?>
<!-- Full-Page Fixed Background Canvas -->
<div class="single-post-fixed-canvas" aria-hidden="true">
    <img src="<?php echo esc_url($hero_image_url); ?>" class="single-post-fixed-img" alt="">
    <div class="single-post-fixed-overlay" style="background: <?php echo esc_attr($hero_gradient); ?>;"></div>
</div>
<?php endif; ?>

<section class="relative <?php echo esc_attr($hero_height_class); ?> flex <?php echo esc_attr($hero_flex_align_class); ?> hero-gradient pt-24 lg:pt-20 overflow-hidden <?php echo ($hero_fade_mode === 'none') ? 'no-hero-fade' : ''; ?>" style="--hero-fade-color: <?php echo esc_attr($hero_fade_color); ?>; --hero-fade-height: <?php echo esc_attr($hero_fade_height); ?>;">
    <!-- Adaptive Image / Fixed Background -->
    <?php if ($is_custom_hero && $hero_bg_mode === 'hero-fixed') : ?>
        <div class="hero-parallax-wrapper">
            <img src="<?php echo esc_url($hero_image_url); ?>" alt="<?php echo esc_attr(strip_tags($hero_title)); ?>" class="hero-bg-fixed-canvas">
        </div>
    <?php elseif ($is_custom_hero && $hero_bg_mode === 'full-page-fixed') : ?>
        <!-- Full-page canvas is active behind entire page -->
    <?php else : ?>
        <img src="<?php echo esc_url($hero_image_url); ?>" alt="<?php echo esc_attr(strip_tags($hero_title)); ?>" class="absolute inset-0 w-full h-full object-cover -z-20">
    <?php endif; ?>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 -z-10" style="background: <?php echo $hero_gradient; ?>;"></div>

    <?php
    // Hero Animated GIF Overlay Layer
    $gif_enable  = get_post_meta($post_id, '_hero_gif_enable', true);
    $gif_url     = get_post_meta($post_id, '_hero_gif_url', true);
    $gif_type    = get_post_meta($post_id, '_hero_gif_type', true) ?: 'fullscreen';
    $gif_pos     = get_post_meta($post_id, '_hero_gif_position', true) ?: 'bottom-right';
    $gif_blend   = get_post_meta($post_id, '_hero_gif_blend_mode', true) ?: 'screen';
    $gif_opacity = get_post_meta($post_id, '_hero_gif_opacity', true);
    $gif_mobile  = get_post_meta($post_id, '_hero_gif_mobile', true) ?: 'hide';
    $gif_size    = get_post_meta($post_id, '_hero_gif_size', true) ?: '340px';

    if ($gif_opacity === '' || !is_numeric($gif_opacity)) {
        $gif_opacity = '0.70';
    }

    $has_hero_gif = ($gif_enable === 'yes' && !empty($gif_url));

    if ($has_hero_gif) :
        $gif_classes = [
            'hero-gif-overlay',
            'respect-reduced-motion',
            ($gif_type === 'fullscreen') ? 'hero-gif-overlay--fullscreen' : 'hero-gif-overlay--accent',
            ($gif_type === 'accent') ? 'hero-gif-pos--' . sanitize_html_class($gif_pos) : '',
            'hero-gif-blend--' . sanitize_html_class($gif_blend),
            ($gif_mobile !== 'keep') ? 'hero-gif-mobile--' . sanitize_html_class($gif_mobile) : '',
        ];
    ?>
    <!-- Hero Animated GIF Layer -->
    <div class="<?php echo esc_attr(implode(' ', array_filter($gif_classes))); ?>"
         style="opacity: <?php echo esc_attr($gif_opacity); ?>; --hero-gif-opacity: <?php echo esc_attr($gif_opacity); ?>; --hero-gif-max-width: <?php echo esc_attr($gif_size); ?>;"
         aria-hidden="true">
        <img src="<?php echo esc_url($gif_url); ?>" alt="" class="hero-gif-media" loading="eager" decoding="async">
    </div>
    <?php endif; ?>

    <div class="relative z-10 max-w-4xl <?php echo esc_attr($hero_mx_class); ?> px-4 space-y-8 w-full">
        <?php if ($hero_show_badge) : ?>
        <div class="inline-flex items-center bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20 animate-fade-in-up">
            <span class="w-2 h-2 bg-primary rounded-full mr-3 animate-pulse"></span>
            <span class="text-xs font-bold text-white uppercase tracking-widest"><?php echo esc_html(get_theme_mod('hero_badge_text', 'Добро пожаловать в мир знаний')); ?></span>
        </div>
        <?php endif; ?>

        <h1 class="<?php echo esc_attr($hero_title_size); ?> <?php echo esc_attr($hero_font_class); ?> hero-title-shadow font-bold text-white leading-tight animate-fade-in-up delay-100" style="color: <?php echo esc_attr($hero_title_color); ?>;">
            <?php echo wp_kses_post($hero_title); ?>
        </h1>

        <?php if (!empty($hero_subtitle)) : ?>
        <p class="text-lg md:text-xl text-slate-200 max-w-2xl <?php echo esc_attr($hero_mx_class); ?> <?php echo esc_attr($hero_font_class); ?> font-light leading-relaxed animate-fade-in-up delay-200" style="color: <?php echo esc_attr($hero_subtitle_color); ?>;">
            <?php echo esc_html($hero_subtitle); ?>
        </p>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row <?php echo esc_attr($hero_flex_align_class); ?> gap-4 pt-4 <?php echo city_library_get_animation_class(); ?>">
            <?php if (!empty($btn1_text)) : ?>
            <a id="hero-primary-btn" class="w-full sm:w-auto px-6 py-3 sm:px-8 sm:py-4 bg-primary hover:bg-yellow-600 text-slate-900 font-bold rounded-xl transition-all flex items-center justify-center space-x-2 shadow-lg shadow-primary/20 text-center premium-hover btn-glow" href="<?php echo esc_url($btn1_link); ?>">
                <?php if (!$is_custom_hero): ?><span class="material-symbols-outlined text-xl shrink-0" aria-hidden="true">event</span><?php endif; ?>
                <span class="whitespace-normal sm:whitespace-nowrap"><?php echo esc_html($btn1_text); ?></span>
                <?php if (!$is_custom_hero): ?><span class="material-symbols-outlined shrink-0" aria-hidden="true">arrow_forward</span><?php endif; ?>
            </a>
            <?php endif; ?>

            <?php if (!empty($btn2_text)) : ?>
            <a id="hero-secondary-btn" class="w-full sm:w-auto px-6 py-3 sm:px-8 sm:py-4 bg-white/15 hover:bg-white/25 backdrop-blur-lg text-white font-bold rounded-xl border border-white/30 transition-all flex items-center justify-center text-center hover:shadow-lg" href="<?php echo esc_url($btn2_link); ?>">
                <span class="whitespace-normal sm:whitespace-nowrap"><?php echo esc_html($btn2_text); ?></span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
        <span class="material-symbols-outlined text-white text-3xl" aria-hidden="true">expand_more</span>
    </div>
</section>
<?php endif; ?>
