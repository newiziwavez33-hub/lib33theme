<?php
/**
 * Template part for displaying page content in page.php
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white dark:bg-slate-900/50 p-8 md:p-12 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-800 relative overflow-hidden'); ?>>

    <!-- Decorative Background Blur -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex flex-col gap-8 relative z-10">

        <header class="entry-header mb-6 text-center">
            <?php the_title('<h1 class="entry-title text-3xl md:text-5xl font-bold font-display mb-4 text-slate-900 dark:text-white leading-tight">', '</h1>'); ?>
        </header>

        <!-- Featured Image (Full Width for Pages if exists) -->
        <?php if (has_post_thumbnail()) : ?>
            <div class="w-full h-64 md:h-96 rounded-2xl overflow-hidden shadow-lg relative mb-8">
                <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover']); ?>
            </div>
        <?php endif; ?>

        <!-- Content -->
        <div class="entry-content prose prose-slate dark:prose-invert max-w-none prose-headings:font-display prose-headings:font-bold prose-a:text-primary hover:prose-a:text-primary/80 prose-img:rounded-xl prose-img:shadow-lg mx-auto">
            <?php
            the_content();

            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'city-library'),
                'after'  => '</div>',
            ));
            ?>
        </div>

    </div>

</article>
