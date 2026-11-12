<?php
/**
 * Important Information Section
 */

$mods = get_theme_mods() ?: [];

if (!($mods['show_important_section'] ?? true)) {
    return;
}

$title = $mods['important_title'] ?? __('Важная информация', 'city-library');
$text = $mods['important_text'] ?? __('Внимание! В связи с санитарным днем библиотека работает по измененному графику.', 'city-library');
$btn_text = $mods['important_btn_text'] ?? __('Подробнее', 'city-library');
$btn_link = $mods['important_btn_link'] ?? '#';
// $bg_color is unused now as we enforce standardized style
$inter_block_text = $mods['important_inter_block_text'] ?? '';

// Принудительно задаем стили из макета для соответствия скриншоту (без фона, как на скриншоте)
$link_radius_class = 'rounded-none';
$link_style_class = 'hover:opacity-80 hover:-translate-y-1';
$aspect_class = 'aspect-[4/3]';
$fit_class = 'max-w-full max-h-full w-auto h-auto object-contain';
$link_wrapper_class = "flex items-center justify-center group relative overflow-hidden transition-all duration-300 w-full h-full bg-transparent $aspect_class $link_radius_class $link_style_class important-section-link";
?>

<section class="py-12 md:py-16 bg-white <?php echo city_library_get_animation_class(); ?>">
    <!-- Width Correction: 80% to match other blocks -->
    <div class="w-full lg:max-w-[80%] lg:mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Main Alert Block - Standardized Style -->
        <?php if (!empty(trim(strip_tags($text)))) : ?>
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8 p-6 sm:p-8 md:p-10 bg-white rounded-none shadow-none border-0 lg:rounded-[2rem] lg:shadow-xl lg:border border-slate-100 overflow-hidden group transition-shadow duration-300 bg-pattern-slate">

            <!-- Decorative Background Element (Subtle, not red) -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary/5 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row gap-5 md:gap-6 items-start md:items-center flex-grow w-full">
                <!-- Icon (Changed from Red to Primary/Standard) -->
                <div class="shrink-0">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-primary/10 to-primary/5 rounded-2xl flex items-center justify-center shadow-sm border border-primary/10">
                        <span class="material-symbols-outlined text-3xl md:text-4xl text-primary animate-pulse" aria-hidden="true">warning</span>
                    </div>
                </div>

                <!-- Content -->
                <div class="space-y-2 md:space-y-3">
                    <h2 class="text-2xl md:text-3xl font-bold font-display uppercase tracking-wider text-slate-800"><?php echo esc_html($title); ?></h2>
                    <div class="prose prose-sm md:prose-lg prose-slate max-w-none text-slate-600 leading-relaxed">
                        <?php echo wp_kses_post(wpautop($text)); ?>
                    </div>
                </div>
            </div>

            <?php if ($btn_link) : ?>
                <div class="relative z-10 shrink-0 mt-4 md:mt-0 w-full md:w-auto">
                    <a href="<?php echo esc_url($btn_link); ?>" class="important-btn inline-flex items-center justify-center w-full md:w-auto px-8 md:px-10 py-3.5 md:py-4 bg-gradient-to-r from-primary to-primary/80 text-white font-bold text-base md:text-lg rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1 active:scale-95">
                        <?php echo esc_html($btn_text); ?>
                        <span class="material-symbols-outlined ml-2" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Spacer / Inter-block text -->
        <?php if ($inter_block_text) : ?>
            <div class="py-10 flex items-center justify-center w-full gap-4">
                <div class="flex-1 h-px bg-slate-200/80"></div>
                <div class="bg-white text-center py-3 px-4 sm:px-8 max-w-full rounded-2xl border border-slate-200/70 shadow-[0_2px_16px_rgba(0,0,0,0.04)] bg-pattern-slate shrink sm:shrink-0">
                    <span class="block text-xs sm:text-sm font-bold uppercase whitespace-normal sm:whitespace-nowrap text-center tracking-wider sm:tracking-[0.25em] text-[#0f2d59] leading-snug sm:leading-none max-w-full"><?php echo esc_html($inter_block_text); ?></span>
                </div>
                <div class="flex-1 h-px bg-slate-200/80"></div>
            </div>
        <?php endif; ?>

        <!-- Links Grid (8 items) -->
        <?php
        $active_links = [];
        for ($i = 1; $i <= 8; $i++) {
            $img = $mods["important_link_image_$i"] ?? '';
            $url = $mods["important_link_url_$i"] ?? '#';
            if ($img) {
                $active_links[] = [
                    'img' => $img,
                    'url' => $url,
                    'index' => $i
                ];
            }
        }
        $link_count = count($active_links);

        if ($link_count > 0) : 
            $grid_cols_class = 'grid-cols-2 sm:grid-cols-3 md:grid-cols-4';
            if ($link_count === 5) {
                $grid_cols_class .= ' lg:grid-cols-3 xl:grid-cols-5';
            } elseif ($link_count === 6) {
                $grid_cols_class .= ' lg:grid-cols-3 xl:grid-cols-6';
            } elseif ($link_count === 7) {
                $grid_cols_class .= ' lg:grid-cols-4 xl:grid-cols-7';
            } elseif ($link_count >= 8) {
                $grid_cols_class .= ' lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8';
            } else {
                $grid_cols_class .= ' lg:grid-cols-4';
            }
        ?>
            <div class="swiper important-links-slider w-full px-4 lg:px-0">
                <div class="swiper-wrapper flex lg:!grid <?php echo esc_attr($grid_cols_class); ?> gap-0 lg:gap-4 xl:gap-6">
                    <?php foreach ($active_links as $link) : ?>
                        <div class="swiper-slide !h-auto !w-[150px] sm:!w-[170px] lg:!w-auto min-w-0 flex flex-col">
                            <a href="<?php echo esc_url($link['url']); ?>" class="<?php echo esc_attr($link_wrapper_class); ?>" style="min-height: 130px;">
                                <img src="<?php echo esc_url($link['img']); ?>" alt="<?php echo esc_attr("Link " . $link['index']); ?>" class="<?php echo esc_attr($fit_class); ?> transition-transform duration-500 group-hover:scale-105">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination lg:hidden !bottom-0 !relative mt-6"></div>
            </div>
        <?php endif; ?>

    </div>
</section>
