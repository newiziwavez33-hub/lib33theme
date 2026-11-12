<?php get_header(); ?>

<!-- Info Cards Section -->
<?php get_template_part('template-parts/section-info-cards'); ?>

<div class="w-full lg:max-w-[80%] lg:mx-auto px-0 lg:px-8 py-8">

    <?php get_template_part('template-parts/section-promo'); ?>

    <?php $show_sidebar = get_theme_mod('show_sidebar', false); ?>

    <!-- Toggle Button -->
    <?php if ($show_sidebar) : ?>
    <div class="mb-6 px-4 lg:px-0">
         <button id="sidebar-toggle-btn" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg shadow hover:bg-opacity-90 transition-all focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <span class="material-symbols-outlined" aria-hidden="true">menu_open</span>
            <span class="text-sm font-bold uppercase"><?php _e('Скрыть/Показать сайдбар', 'city-library'); ?></span>
        </button>
    </div>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        <!-- Sidebar Column (30%) -->
        <?php if ($show_sidebar) : ?>
        <div id="sidebar-column" class="w-full lg:w-[30%] shrink-0 transition-all duration-300 px-4 lg:px-0">
             <?php get_sidebar(); ?>
        </div>
        <?php endif; ?>

        <!-- Main Content (70%) -->
        <div id="primary" class="w-full <?php echo $show_sidebar ? 'lg:w-[70%]' : ''; ?> transition-all duration-300">

            <div class="content-area overflow-hidden bg-white p-4 md:p-12 rounded-[2rem] border border-slate-100 shadow-lg md:shadow-2xl mx-4 lg:mx-0 <?php echo city_library_get_animation_class(); ?>">
                <div class="flex flex-col items-center justify-center mb-10 md:mb-14 gap-4 md:gap-6 text-center">
                <div class="space-y-3 md:space-y-5 max-w-2xl mx-auto">
                    <div class="h-1.5 w-16 md:w-24 bg-primary mx-auto rounded-full"></div>
                    <h2 class="text-4xl md:text-[56px] font-display font-extrabold leading-tight tracking-tight text-slate-900"><?php _e('Последние новости', 'city-library'); ?></h2>
                    <p class="text-slate-500 text-base md:text-xl px-2 font-medium"><?php _e('Узнайте о самых интересных событиях и мероприятиях нашей библиотеки', 'city-library'); ?></p>
                </div>
            </div>

            <?php if (have_posts()) : ?>
                <!-- Responsive Layout: Swiper for Mobile (< lg), Grid for Kiosk/Desktop (>= lg) -->
                <div id="posts-container" class="swiper news-slider w-full !overflow-visible">
                    <div class="swiper-wrapper lg:!grid lg:grid-cols-2 lg:portrait:grid-cols-2 xl:grid-cols-3 gap-0 lg:gap-6 xl:gap-8">
                        <?php
                        while (have_posts()) :
                            the_post();
                            echo '<div class="swiper-slide h-auto !w-[78vw] sm:!w-[280px] lg:!w-auto drop-shadow-md">';
                            get_template_part('template-parts/content-post-card');
                            echo '</div>';
                        endwhile;
                        ?>
                    </div>
                    <div class="swiper-pagination lg:hidden !bottom-0 !relative mt-10"></div>
                </div>
                    <div class="mt-12 text-center">
                    <?php the_posts_pagination(); ?>
                </div>

                <div class="mt-16 text-center border-t border-slate-200 pt-8">
                    <a href="<?php echo esc_url(add_query_arg('news_archive', 'true', home_url('/'))); ?>" class="help-link text-lg">
                        <?php _e('Архив новостей', 'city-library'); ?>
                        <span class="material-symbols-outlined ml-2 text-xl" aria-hidden="true">arrow_forward</span>
                    </a>
                </div>

            <?php else : ?>
                <p><?php _e('К сожалению, по вашему запросу ничего не найдено.', 'city-library'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>

<?php get_template_part('template-parts/section-afisha'); ?>

<?php get_template_part('template-parts/section-important'); ?>

<?php get_footer(); ?>
