<?php get_header(); ?>

<div class="w-full max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-12">
    <div id="primary" class="w-full transition-all duration-300 max-w-6xl mx-auto">

        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white p-4 sm:p-8 md:p-12 rounded-2xl md:rounded-[2rem] shadow-xl border border-slate-100 relative break-words'); ?>>

                <!-- Decorative Background Blur -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex flex-col gap-8 relative z-10">

                    <header class="entry-header mb-6 text-center">
                        <?php the_title('<h1 class="entry-title text-3xl md:text-5xl font-bold font-display mb-4 text-slate-900 leading-tight">', '</h1>'); ?>
                    </header>

                    <!-- Featured Image (Full Width for Pages if exists) -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="w-full h-64 md:h-96 rounded-2xl overflow-hidden shadow-lg relative mb-8">
                            <?php
                            $full_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                            ?>
                            <a href="<?php echo esc_url($full_image_url[0]); ?>" class="glightbox block w-full h-full">
                                <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-cover transform transition-transform duration-700 hover:scale-105']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="entry-content prose prose-slate max-w-full md:max-w-none break-words overflow-x-hidden prose-headings:font-display prose-headings:font-bold prose-a:text-primary hover:prose-a:text-primary/80 prose-img:rounded-xl prose-img:shadow-lg mx-auto">
                        <?php
                            // Fix LearningApps iframes before rendering
                            $content = get_the_content();
                            $content = preg_replace('/<iframe([^>]+)src=["\']http:\/\/learningapps\.org\/([^"\']+)["\']([^>]*)>/i', '<iframe$1src="https://learningapps.org/$2"$3>', $content);
                            $content = preg_replace_callback('/<iframe([^>]+)src=["\']https:\/\/learningapps\.org\/([^"\']+)["\']([^>]*)>/i', function($m) {
                                $attrs = $m[1] . $m[3];
                                $w_val = '100%'; $h_val = '500px';
                                if (preg_match('/width=["\']([^"\']+)["\']/', $attrs, $w)) $w_val = (is_numeric($w[1]) ? $w[1].'px' : $w[1]);
                                if (preg_match('/height=["\']([^"\']+)["\']/', $attrs, $h)) $h_val = (is_numeric($h[1]) ? $h[1].'px' : $h[1]);
                                return "<iframe{$m[1]}src=\"https://learningapps.org/{$m[2]}\"{$m[3]} style=\"width:{$w_val} !important; height:{$h_val} !important; border:0; min-height:400px;\">";
                            }, $content);
                            echo apply_filters('the_content', $content);

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'city-library'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                </div>

            </article>

        <?php
        endwhile; // End of the loop.
        ?>
    </div>
</div>

<?php get_footer(); ?>
