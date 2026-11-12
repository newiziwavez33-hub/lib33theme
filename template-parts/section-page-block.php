<?php
/**
 * Page Content Block Template Part
 */

$show = get_theme_mod('show_page_block', false);
$page_id = get_theme_mod('page_block_id', 0);

if (!$show || !$page_id) {
    return;
}

$post = get_post($page_id);
if (!$post || $post->post_status !== 'publish') {
    return;
}

$override_title = get_theme_mod('page_block_title', '');
$title = $override_title ? $override_title : $post->post_title;
$content = apply_filters('the_content', $post->post_content);
?>

<section class="mb-24 w-full max-w-[95%] sm:max-w-[90%] xl:max-w-[85%] 2xl:max-w-[80%] mx-auto px-4 sm:px-6 lg:px-8 <?php echo city_library_get_animation_class(); ?>">
    <div class="bg-white dark:bg-slate-900/50 rounded-[2.5rem] p-6 sm:p-8 md:p-12 shadow-xl border border-slate-100 dark:border-slate-800 relative overflow-hidden bg-pattern-slate">

         <!-- Decorative Blur -->
         <div class="absolute -top-20 -right-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

         <div class="relative z-10 flex flex-col gap-6 md:gap-8">
            <h2 class="text-2xl sm:text-3xl md:text-5xl font-display font-bold text-slate-800 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-4">
                <?php echo esc_html($title); ?>
            </h2>

            <!-- Featured Image -->
            <?php if (has_post_thumbnail($page_id)) : ?>
                <div class="w-full min-h-[240px] sm:min-h-[300px] lg:portrait:min-h-[340px] lg:landscape:min-h-[500px] h-64 md:h-96 rounded-2xl overflow-hidden shadow-lg relative">
                    <?php echo get_the_post_thumbnail($page_id, 'full', ['class' => 'w-full h-full object-cover']); ?>
                </div>
            <?php endif; ?>

            <div class="prose prose-base sm:prose-lg prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                <?php echo $content; ?>
            </div>
         </div>
    </div>
</section>
