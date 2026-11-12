<?php $mods = get_theme_mods() ?: []; ?>
<section class="py-24 bg-white <?php echo city_library_get_animation_class(); ?>">
    <div class="w-full lg:max-w-[80%] lg:mx-auto px-0 lg:px-8">

        <?php
        $bg_color = $mods['partners_bg_color'] ?? '#FFFFFF';
        // Logo Size Logic
        $size_setting = $mods['partners_logo_size'] ?? 'medium';
        $size_class = 'h-12 md:h-16'; // Default Fallback

        switch ($size_setting) {
            case 'xs': $size_class = 'h-6 md:h-8'; break;
            case 'sm': $size_class = 'h-8 md:h-12'; break;
            case 'medium': $size_class = 'h-12 md:h-16'; break;
            case 'lg': $size_class = 'h-16 md:h-20'; break;
            case 'xl': $size_class = 'h-20 md:h-24'; break;
            case '2xl': $size_class = 'h-24 md:h-32'; break;
            case '3xl': $size_class = 'h-32 md:h-40'; break;
            case 'original': $size_class = 'h-auto w-auto max-h-32'; break;
        }

        // Layout setting: 2 rows of 4 (default) vs 1 row
        $layout_setting = $mods['partners_layout'] ?? '2_rows_4';
        if ($layout_setting === '1_row') {
            $grid_classes = 'partners-grid-1-row lg:!grid lg:grid-cols-4 xl:grid-cols-8 lg:justify-items-center items-center gap-4 lg:gap-4 xl:gap-6 !h-auto';
        } else {
            // Default: 2 rows of 4
            $grid_classes = 'partners-grid-2-rows lg:!grid lg:grid-cols-4 xl:grid-cols-4 lg:justify-items-center items-center gap-6 lg:gap-8 xl:gap-10 !h-auto';
        }
        ?>

        <div class="relative p-0 lg:p-12 md:p-16 rounded-none shadow-none border-0 lg:rounded-[2.5rem] lg:shadow-xl lg:border border-slate-100 overflow-hidden" style="background-color: <?php echo esc_attr($bg_color); ?>;">

            <div class="text-center space-y-6 mb-8 lg:mb-16 relative z-10 px-6 py-12 lg:px-0 lg:py-0">
                <div class="h-1.5 w-20 bg-gradient-to-r from-primary to-green-300 mx-auto rounded-full"></div>
                <h2 class="text-4xl md:text-5xl font-display font-bold text-slate-900 tracking-tight">
                    <?php echo esc_html($mods['partners_title'] ?? 'Наши партнеры'); ?>
                </h2>
                <p class="text-slate-500 text-lg md:text-xl font-light max-w-2xl mx-auto leading-relaxed">
                    <?php echo esc_html($mods['partners_subtitle'] ?? 'Мы гордимся сотрудничеством с ведущими организациями'); ?>
                </p>
            </div>

            <div class="swiper partners-slider w-full px-4 lg:px-0 pb-12 lg:pb-0">
                <div class="swiper-wrapper flex flex-nowrap <?php echo esc_attr($grid_classes); ?>">
                    <?php for ($i = 1; $i <= 8; $i++) : ?>
                        <?php if (!empty($mods['partner_logo_' . $i])) : ?>
                            <div class="swiper-slide group relative !h-auto !w-[160px] sm:!w-[190px] lg:!w-full flex justify-center items-center">
                                <a href="<?php echo esc_url($mods['partner_link_' . $i] ?? '#'); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center p-3 lg:p-4 w-full h-full transition-all duration-300 transform group-hover:-translate-y-2">
                                    <img class="<?php echo esc_attr($size_class); ?> max-w-full max-h-full w-auto object-contain transition-all duration-500"
                                        src="<?php echo esc_url($mods['partner_logo_' . $i]); ?>"
                                        alt="<?php printf(esc_attr__('Partner %d Logo', 'city-library'), $i); ?>">
                                </a>
                                <!-- Subtle Glow on Hover -->
                                <div class="absolute inset-0 bg-primary/0 group-hover:bg-primary/5 rounded-xl blur-xl transition-all duration-300 -z-10"></div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div class="swiper-pagination lg:hidden !bottom-0 !relative mt-6"></div>
            </div>

        </div>
    </div>
</section>
