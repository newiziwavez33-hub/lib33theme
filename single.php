<?php get_header(); ?>

<div class="w-full lg:max-w-[80%] lg:mx-auto px-0 lg:px-8 py-4 md:py-8 relative z-0">
    <div id="primary" class="w-full transition-all duration-300 relative z-10">

        <?php
        while (have_posts()) :
            the_post();
            ?>

            <!-- Breadcrumbs -->
            <div class="breadcrumbs-wrapper px-4 lg:px-0 mb-4">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">home</span>
                </a>
                <span class="separator material-symbols-outlined text-xs" aria-hidden="true">chevron_right</span>
                <?php if (has_category()) :
                    $cats = get_the_category(); ?>
                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>"><?php echo esc_html($cats[0]->name); ?></a>
                    <span class="separator material-symbols-outlined text-xs" aria-hidden="true">chevron_right</span>
                <?php endif; ?>
                <span class="current"><?php echo esc_html(wp_trim_words(get_the_title(), 8)); ?></span>
            </div>

            <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white p-4 sm:p-6 md:p-12 rounded-none lg:rounded-[2rem] shadow-none lg:shadow-xl border-x-0 lg:border border-slate-100 relative'); ?>>

                <!-- Decorative Background Blur -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 clearfix">

                    <!-- Header (Category, Title, Meta) -->
                    <header class="entry-header mb-6">
                        <!-- Category -->
                        <?php if (has_category()) : ?>
                            <div class="mb-4">
                                <span class="entry-category-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                    <?php the_category(', '); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php the_title('<h1 class="entry-title text-3xl md:text-5xl font-extrabold font-display mb-6 text-slate-900 leading-tight tracking-tight break-words">', '</h1>'); ?>

                        <div class="entry-meta flex items-center text-slate-500 text-sm font-semibold border-b border-slate-100 pb-6 mb-8">
                            <span class="inline-flex items-center text-slate-600 bg-slate-50/80 px-3 py-1.5 rounded-lg border border-slate-200/60 shadow-sm">
                                <span class="material-symbols-outlined text-base mr-2 text-primary entry-meta-icon" aria-hidden="true">calendar_today</span>
                                <?php echo esc_html(get_the_date()); ?>
                            </span>
                        </div>
                    </header>

                    <!-- Floated Image (300x200 on desktop/tablet, centered on mobile) -->
                    <?php if (has_post_thumbnail() && get_theme_mod('show_single_featured_image', true)) : ?>
                        <div class="mx-auto md:mx-0 w-full sm:w-[300px] mb-6 md:float-left md:mr-8 md:mb-4">
                            <?php
                            $full_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                            ?>
                            <a href="<?php echo esc_url($full_image_url[0]); ?>" class="glightbox block rounded-2xl overflow-hidden shadow-lg relative group w-full h-[200px] sm:w-[300px]">
                                <?php the_post_thumbnail('medium', ['class' => 'w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105']); ?>
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 flex items-center justify-center">
                                     <span class="material-symbols-outlined text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" aria-hidden="true">zoom_in</span>
                                </div>
                            </a>
                            <?php if (get_the_post_thumbnail_caption()) : ?>
                                <figcaption class="text-center text-slate-500 text-xs mt-2 italic w-full sm:w-[300px]">
                                    <?php the_post_thumbnail_caption(); ?>
                                </figcaption>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="min-w-0">
                        <div class="entry-content prose prose-slate max-w-full md:max-w-none break-words prose-headings:font-display prose-headings:font-bold prose-a:text-primary hover:prose-a:text-primary/80 prose-img:rounded-2xl prose-img:shadow-lg">
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
                                'before' => '<div class="page-links clear-both">' . esc_html__('Pages:', 'city-library'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>
                    </div>

                </div>

                <!-- Gallery Section (Bottom) -->
                <?php
                // Get other attached images
                $attachments = get_posts(array(
                    'post_type'      => 'attachment',
                    'post_mime_type' => 'image',
                    'post_parent'    => get_the_ID(),
                    'posts_per_page' => -1,
                    'exclude'        => get_post_thumbnail_id(),
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                ));

                if ($attachments) : ?>
                    <div class="mt-12 border-t border-slate-200 pt-8">
                        <details class="group bg-slate-50 border border-slate-200 rounded-xl overflow-hidden [&_summary::-webkit-details-marker]:hidden">
                            <summary class="flex items-center justify-between p-4 cursor-pointer text-xl font-bold font-display text-slate-900 bg-white hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-[#1c77d9] text-2xl" aria-hidden="true">photo_library</span>
                                    <?php esc_html_e('Галерея изображений', 'city-library'); ?>
                                </div>
                                <span class="material-symbols-outlined transition-transform duration-300 group-open:rotate-180" aria-hidden="true">expand_more</span>
                            </summary>
                            <div class="p-6 border-t border-slate-200 bg-white">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <?php foreach ($attachments as $attachment) :
                                        $img_url = wp_get_attachment_image_src($attachment->ID, 'full');
                                        $thumb_url = wp_get_attachment_image_src($attachment->ID, 'medium');
                                    ?>
                                        <a href="<?php echo esc_url($img_url[0]); ?>" class="glightbox group/img relative overflow-hidden rounded-xl aspect-square shadow-md border border-slate-100 cursor-zoom-in">
                                            <img src="<?php echo esc_url($thumb_url[0]); ?>" alt="<?php echo esc_attr($attachment->post_title); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover/img:scale-110">
                                            <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white opacity-0 group-hover/img:opacity-100 transition-opacity duration-300 transform scale-75 group-hover/img:scale-100" aria-hidden="true">zoom_in</span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </details>
                    </div>
                <?php endif; ?>

                <!-- Post Navigation (Prev / Next) -->
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();
                if ($prev_post || $next_post) : ?>
                    <div class="post-navigation-wrapper">
                        <?php if ($prev_post) : ?>
                            <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="nav-link nav-prev">
                                <span class="nav-label flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_back</span>
                                    <?php esc_html_e('Предыдущая', 'city-library'); ?>
                                </span>
                                <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>

                        <?php if ($next_post) : ?>
                            <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="nav-link nav-next">
                                <span class="nav-label flex items-center justify-end gap-1">
                                    <?php esc_html_e('Следующая', 'city-library'); ?>
                                    <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                                </span>
                                <span class="nav-title"><?php echo esc_html($next_post->post_title); ?></span>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </article>

        <?php
        endwhile; // End of the loop.
        ?>
    </div>
</div>

<?php get_footer(); ?>
