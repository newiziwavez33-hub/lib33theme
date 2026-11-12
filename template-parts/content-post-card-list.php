<?php
$bg_color = get_theme_mod('news_card_list_bg_color', '#FFFFFF');
$title_color = get_theme_mod('news_card_list_title_color', '#1A3C34');
$text_color = get_theme_mod('news_card_list_text_color', '#334155');
$link_color = get_theme_mod('news_card_list_link_color', '#1c77d9');
?>
<article class="group relative flex flex-col md:flex-row bg-white rounded-[2rem] overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 border border-slate-100 isolate min-h-[220px]" style="background-color: <?php echo esc_attr($bg_color); ?>;">

    <!-- Image Container -->
    <div class="md:w-[280px] shrink-0 relative overflow-hidden h-48 md:h-full md:min-h-full">
        <a href="<?php the_permalink(); ?>" class="block w-full h-full relative" tabindex="-1" aria-hidden="true">
            <?php if (has_post_thumbnail()) : ?>
                <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>" alt="<?php the_title_attribute(); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <?php else : ?>
                <div class="absolute inset-0 bg-slate-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300" aria-hidden="true">image</span>
                </div>
            <?php endif; ?>
        </a>

        <!-- Category Badge (Top Left) -->
        <?php
        $categories = get_the_category();
        if (!empty($categories)) : ?>
            <div class="absolute top-4 left-4 z-10">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/90 backdrop-blur-sm text-[#1c77d9] shadow-sm border border-white/20">
                    <?php echo esc_html($categories[0]->name); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Content -->
    <div class="flex flex-col flex-grow p-6 md:p-8 relative justify-center">
        <!-- Date -->
        <div class="flex items-center text-slate-500 text-[11px] font-bold tracking-widest uppercase mb-3">
            <span class="material-symbols-outlined text-sm mr-1.5" aria-hidden="true">calendar_month</span>
            <?php echo get_the_date(); ?>
        </div>

        <!-- Title -->
        <h3 class="text-2xl font-bold font-display leading-tight mb-3">
            <a href="<?php the_permalink(); ?>" class="transition-colors hover:text-primary focus:outline-none focus:underline text-inherit [&_p]:text-[inherit] news-card-title-link" style="--title-color: <?php echo esc_attr($title_color); ?>;">
                <?php the_title(); ?>
                <span class="absolute inset-0 md:hidden" aria-hidden="true"></span>
            </a>
        </h3>

        <!-- Excerpt -->
        <div class="text-base leading-relaxed line-clamp-2 md:line-clamp-3 mb-6 flex-grow text-inherit [&_p]:text-[inherit] news-card-excerpt-text" style="--text-color: <?php echo esc_attr($text_color); ?>;">
            <?php the_excerpt(); ?>
        </div>

        <!-- Link -->
        <div class="mt-auto">
             <a href="<?php the_permalink(); ?>" class="read-more-btn inline-flex items-center text-sm font-bold uppercase tracking-wide transition-colors relative z-10 px-4 py-2 rounded-full -ml-4">
                <?php _e('Читать полностью', 'city-library'); ?>
                <span class="material-symbols-outlined ml-2 text-lg transform transition-transform duration-300 group-hover:translate-x-1" aria-hidden="true">arrow_forward</span>
            </a>
        </div>
    </div>
</article>
