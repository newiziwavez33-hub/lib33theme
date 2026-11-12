<?php
/**
 * Template part for displaying the Promo section.
 */

$show_promo = get_theme_mod('show_promo_section', true);

if (!$show_promo) {
    return;
}

$image = get_theme_mod('promo_image');
$title = get_theme_mod('promo_title', __('Специальное предложение', 'city-library'));
$text = get_theme_mod('promo_text', __('Описание вашего специального предложения или важной новости.', 'city-library'));
$btn_text = get_theme_mod('promo_btn_text', __('Подробнее', 'city-library'));
$link = get_theme_mod('promo_link', '#');
$aspect_ratio = get_theme_mod('promo_aspect_ratio', 'auto');
$aspect_class = '';
if ($aspect_ratio === '16/9' || $aspect_ratio === 'video') {
    $aspect_class = 'md:aspect-video';
} elseif ($aspect_ratio === '21/9') {
    $aspect_class = 'md:aspect-[21/9]';
}
?>

<section class="mb-12 content-area bg-white p-5 sm:p-8 rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden relative isolate bg-pattern-slate <?php echo esc_attr($aspect_class); ?> <?php echo city_library_get_animation_class(); ?>">
    <!-- Decorative Glow (optional, keeping it subtle) -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl pointer-events-none z-0"></div>

    <div class="flex flex-col md:flex-row relative z-10 gap-6 md:gap-8">
        <!-- Image Column -->
        <?php if ($image) : ?>
            <div class="shrink-0 w-full md:w-[320px] lg:portrait:w-[300px] lg:landscape:w-[400px] md:h-[300px] relative overflow-hidden group rounded-2xl flex items-center justify-center bg-slate-50 promo-section-image-wrapper">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-auto max-h-[360px] md:max-h-none md:absolute md:inset-0 md:h-full object-contain md:object-cover transition-transform duration-700 group-hover:scale-105">
            </div>
        <?php else : ?>
            <!-- Placeholder if no image is set but block is enabled -->
             <div class="shrink-0 w-full md:w-[320px] lg:portrait:w-[300px] lg:landscape:w-[400px] h-48 md:h-[300px] bg-slate-200 flex items-center justify-center relative overflow-hidden rounded-2xl">
                <span class="material-symbols-outlined text-6xl text-slate-400" aria-hidden="true">image</span>
             </div>
        <?php endif; ?>

        <!-- Content Column -->
        <div class="flex-grow flex flex-col justify-center min-w-0">
            <?php if ($title) : ?>
                <h2 class="text-2xl sm:text-3xl font-display font-bold mb-4 text-slate-900 leading-tight">
                    <?php echo esc_html($title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($text) : ?>
                <div class="prose prose-slate max-w-none text-slate-600 mb-6">
                    <?php echo wp_kses_post(wpautop($text)); ?>
                </div>
            <?php endif; ?>

            <?php if ($link && $btn_text) : ?>
                <div>
                    <a href="<?php echo esc_url($link); ?>" class="promo-btn inline-flex items-center justify-center w-full sm:w-auto px-6 sm:px-8 py-3.5 rounded-xl bg-gradient-to-r from-primary to-primary/80 text-white font-bold text-sm uppercase tracking-wider hover:shadow-xl hover:-translate-y-0.5 transition-all shadow-lg group text-center max-w-full">
                        <span class="whitespace-normal"><?php echo esc_html($btn_text); ?></span>
                        <span class="material-symbols-outlined ml-2 shrink-0 group-hover:translate-x-1 transition-transform" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
