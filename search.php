<?php get_header(); ?>

<div class="w-full max-w-[95%] xl:max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div id="primary" class="w-full">

        <div class="content-area bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100 bg-pattern-slate">

            <header class="mb-12 text-center max-w-2xl mx-auto">
                <p class="text-primary font-bold uppercase tracking-widest mb-2"><?php _e('Результаты поиска', 'city-library'); ?></p>
                <h1 class="text-3xl md:text-5xl font-display font-bold text-slate-900 mb-6">
                    <?php printf(esc_html__('"%s"', 'city-library'), '<span>' . get_search_query() . '</span>'); ?>
                </h1>
                <div class="max-w-md mx-auto">
                    <?php get_search_form(); ?>
                </div>
            </header>

            <?php if (have_posts()) : ?>
                <div id="posts-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content-post-card');
                    endwhile;
                    ?>
                </div>

                <div class="mt-12 text-center">
                    <?php the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => '<span class="material-symbols-outlined" aria-hidden="true">chevron_left</span>',
                        'next_text' => '<span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>',
                        'screen_reader_text' => __('Навигация по записям', 'city-library'),
                    )); ?>
                </div>

            <?php else : ?>
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-6xl text-slate-300 mb-4 hover:scale-110 transition-transform duration-300" aria-hidden="true">sentiment_dissatisfied</span>
                    <h2 class="text-2xl font-bold text-slate-700 mb-2"><?php _e('Ничего не найдено', 'city-library'); ?></h2>
                    <p class="text-slate-500 mb-8"><?php _e('Попробуйте изменить запрос или вернитесь на главную.', 'city-library'); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center px-8 py-3 bg-primary text-white rounded-full font-bold hover:bg-primary/90 transition-colors hover:shadow-lg hover:-translate-y-1 duration-300">
                        <?php _e('На главную', 'city-library'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
