<?php
/**
 * Shortcode to insert an image slider within post content.
 * Usage: [city_library_slider] (shows all attached images except thumbnail)
 * Or: [city_library_slider ids="12,34,56"] (shows specific image IDs)
 */

function city_library_post_slider_shortcode($atts) {
    // Parse attributes
    $atts = shortcode_atts(array(
        'ids' => '',
        'ratio' => 'auto',
        'effect' => 'fade',
        'object_fit' => 'contain',
        'autoplay' => 'true',
        'style' => 'default',
        'show_caption' => 'true',
    ), $atts, 'city_library_slider');

    $attachments = [];
    $slider_id = 'slider-' . wp_generate_uuid4();

    // Validate aspect ratio
    $ratio = sanitize_text_field($atts['ratio']);
    // Treat 'auto', 'adaptive', legacy '21/9' default, or empty as responsive adaptive height
    $is_adaptive = ($ratio === 'auto' || $ratio === 'adaptive' || $ratio === '21/9' || empty($ratio));

    $effect = sanitize_text_field($atts['effect']);
    if (!in_array($effect, ['fade', 'slide', 'cube', 'flip'], true)) {
        $effect = 'fade';
    }
    $object_fit = in_array($atts['object_fit'], ['cover', 'contain'], true) ? $atts['object_fit'] : 'contain';
    $autoplay = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    $style = in_array($atts['style'], ['default', 'shadow-card', 'brutalism', 'minimal'], true) ? $atts['style'] : 'default';
    $show_caption = filter_var($atts['show_caption'], FILTER_VALIDATE_BOOLEAN);

    // Base CSS rules
    if ($is_adaptive) {
        $slider_style_css = 'height: clamp(340px, 55vh, 520px);';
    } else {
        $slider_style_css = 'aspect-ratio: ' . esc_attr($ratio) . '; max-height: clamp(340px, 55vh, 520px); height: auto;';
    }

    $img_classes = ($object_fit === 'cover')
        ? 'slider-img w-full h-full object-cover'
        : 'slider-img max-w-full max-h-full w-auto h-auto object-contain mx-auto';

    // Apply visual styles
    $container_classes = 'swiper inline-post-slider overflow-hidden' . ($object_fit === 'cover' ? ' is-cover' : '') . ($style === 'minimal' ? ' is-minimal' : '');

    if ($style === 'shadow-card') {
        $container_classes .= ' rounded-2xl shadow-2xl border border-slate-100 bg-white';
    } elseif ($style === 'brutalism') {
        $container_classes .= ' border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] bg-white rounded-none';
    } elseif ($style === 'minimal') {
        $container_classes .= ' bg-transparent border-0 shadow-none';
    } else {
        // default
        $container_classes .= ' rounded-2xl shadow-md border border-slate-200/80 bg-slate-50';
    }

    if (!empty($atts['ids'])) {
        // Fetch specific IDs
        $id_array = array_filter(array_map('intval', explode(',', $atts['ids'])));
        if (!empty($id_array)) {
            $attachments = get_posts(array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'post__in'       => $id_array,
                'orderby'        => 'post__in',
                'posts_per_page' => -1,
            ));
        }
    } else {
        // Fallback: Fetch all images attached to the current post (excluding featured image)
        global $post;
        if (!$post) return '';

        $attachments = get_posts(array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'post_parent'    => $post->ID,
            'posts_per_page' => -1,
            'exclude'        => get_post_thumbnail_id($post->ID),
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ));
    }

    // If no images found, return nothing
    if (empty($attachments)) {
        return '';
    }

    $has_multiple = count($attachments) > 1;
    $gallery_group = 'gallery-' . $slider_id;

    // Generate HTML
    ob_start();
    ?>
    <div class="not-prose inline-post-slider-wrapper my-8 relative group/slider mx-auto w-full max-w-4xl" id="<?php echo esc_attr($slider_id); ?>">
        <div class="<?php echo esc_attr($container_classes); ?>" style="<?php echo esc_attr($slider_style_css); ?>">
            <div class="swiper-wrapper">
                <?php foreach ($attachments as $attachment) :
                    $img_full = wp_get_attachment_image_src($attachment->ID, 'full');
                    $img_large = wp_get_attachment_image_src($attachment->ID, 'large') ?: $img_full;
                    if (!$img_full) continue;

                    // Retrieve caption and alt text from WordPress attachment
                    $caption = wp_get_attachment_caption($attachment->ID);
                    $alt_text = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);

                    $caption_display = !empty($caption) ? trim($caption) : '';
                    if (empty($caption_display) && !empty($alt_text)) {
                        $caption_display = trim($alt_text);
                    }
                    if (empty($alt_text)) {
                        $alt_text = !empty($caption_display) ? $caption_display : $attachment->post_title;
                    }
                ?>
                    <div class="swiper-slide flex items-center justify-center <?php echo ($style === 'minimal') ? 'bg-transparent' : 'bg-slate-50/50'; ?>">
                        <figure class="slider-figure relative w-full h-full flex flex-col items-center justify-center m-0 p-0 overflow-hidden">
                            <div class="slider-media-container relative flex-1 min-h-0 w-full flex items-center justify-center overflow-hidden">
                                <a href="<?php echo esc_url($img_full[0]); ?>"
                                   class="glightbox slider-link flex items-center justify-center w-full h-full relative cursor-zoom-in group/slide select-none"
                                   data-gallery="<?php echo esc_attr($gallery_group); ?>"
                                   data-title="<?php echo esc_attr($alt_text); ?>"
                                   <?php if (!empty($caption_display)) : ?>data-description="<?php echo esc_attr($caption_display); ?>"<?php endif; ?>>
                                    <img src="<?php echo esc_url($img_large[0]); ?>"
                                         alt="<?php echo esc_attr($alt_text); ?>"
                                         class="<?php echo esc_attr($img_classes); ?> transition-transform duration-300 group-hover/slide:scale-[1.01]"
                                         loading="lazy"
                                         draggable="false">
                                    <div class="slider-zoom-badge absolute inset-0 bg-black/0 group-hover/slide:bg-black/10 transition-colors duration-300 flex items-center justify-center pointer-events-none opacity-0 group-hover/slide:opacity-100">
                                        <span class="material-symbols-outlined text-white bg-black/60 backdrop-blur-sm p-3 rounded-full drop-shadow-lg scale-90 group-hover/slide:scale-100 transition-transform duration-200" aria-hidden="true">zoom_in</span>
                                    </div>
                                </a>
                            </div>
                            <?php if ($show_caption && !empty($caption_display)) : ?>
                                <figcaption class="slider-caption-bar w-full py-2 px-4 text-center text-xs sm:text-sm text-slate-600 dark:text-slate-300 bg-slate-100/90 dark:bg-slate-800/90 border-t border-slate-200/60 dark:border-slate-700/60 font-medium italic transition-opacity line-clamp-2">
                                    <?php echo esc_html($caption_display); ?>
                                </figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($has_multiple) : ?>
                <!-- Navigation buttons -->
                <button type="button" class="swiper-button-prev !text-white !w-11 !h-11 !bg-slate-900/50 hover:!bg-primary !rounded-full opacity-90 lg:opacity-0 lg:group-hover/slider:opacity-100 transition-all duration-300 backdrop-blur-md shadow-lg after:!text-sm ml-3 cursor-pointer z-10" aria-label="<?php esc_attr_e('Предыдущий слайд', 'city-library'); ?>"></button>
                <button type="button" class="swiper-button-next !text-white !w-11 !h-11 !bg-slate-900/50 hover:!bg-primary !rounded-full opacity-90 lg:opacity-0 lg:group-hover/slider:opacity-100 transition-all duration-300 backdrop-blur-md shadow-lg after:!text-sm mr-3 cursor-pointer z-10" aria-label="<?php esc_attr_e('Следующий слайд', 'city-library'); ?>"></button>
            <?php endif; ?>
        </div>

        <?php if ($has_multiple) : ?>
            <!-- Pagination BELOW the slider (never overlaps image) -->
            <div class="inline-post-slider-pagination swiper-pagination"></div>
        <?php endif; ?>

        <!-- Script to initialize this specific slider -->
        <script>
            (function() {
                function initSlider() {
                    if (typeof Swiper === 'undefined') {
                        setTimeout(initSlider, 50);
                        return;
                    }
                    var sliderWrapper = document.getElementById('<?php echo esc_js($slider_id); ?>');
                    if (!sliderWrapper) return;

                    var sliderElement = sliderWrapper.querySelector('.swiper');
                    if (!sliderElement || sliderElement.classList.contains('swiper-initialized')) return;

                    var hasMultiple = <?php echo $has_multiple ? 'true' : 'false'; ?>;
                    var autoplayConfig = (hasMultiple && <?php echo $autoplay; ?>) ? {
                        delay: 5000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    } : false;

                    var swiperInstance = new Swiper(sliderElement, {
                        loop: hasMultiple,
                        speed: 600,
                        autoHeight: false,
                        autoplay: autoplayConfig,
                        slidesPerView: 1,
                        spaceBetween: 0,
                        effect: '<?php echo esc_js($effect); ?>',
                        fadeEffect: {
                            crossFade: true
                        },
                        touchRatio: 1,
                        touchAngle: 45,
                        grabCursor: true,
                        simulateTouch: true,
                        preventClicks: true,
                        preventClicksPropagation: true,
                        watchOverflow: true,
                        observer: true,
                        observeParents: true,
                        keyboard: {
                            enabled: true,
                            onlyInViewport: true,
                        },
                        pagination: hasMultiple ? {
                            el: sliderWrapper.querySelector('.inline-post-slider-pagination'),
                            clickable: true,
                            dynamicBullets: <?php echo count($attachments) > 8 ? 'true' : 'false'; ?>,
                        } : false,
                        navigation: hasMultiple ? {
                            nextEl: sliderWrapper.querySelector('.swiper-button-next'),
                            prevEl: sliderWrapper.querySelector('.swiper-button-prev'),
                        } : false,
                        on: {
                            init: function(swiper) {
                                // Strip .glightbox from duplicate slides created by loop to prevent clone duplicates in lightbox gallery
                                swiper.el.querySelectorAll('.swiper-slide-duplicate a.glightbox').forEach(function(dupLink) {
                                    dupLink.classList.remove('glightbox');
                                    dupLink.setAttribute('data-duplicate', 'true');
                                    dupLink.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        var slide = dupLink.closest('.swiper-slide-duplicate');
                                        var realIndex = slide ? slide.getAttribute('data-swiper-slide-index') : null;
                                        if (realIndex !== null) {
                                            var realLink = swiper.el.querySelector('.swiper-slide:not(.swiper-slide-duplicate)[data-swiper-slide-index="' + realIndex + '"] a.glightbox');
                                            if (realLink) {
                                                realLink.click();
                                            }
                                        }
                                    });
                                });
                            }
                        }
                    });
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initSlider);
                } else {
                    initSlider();
                }
            })();
        </script>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('city_library_slider', 'city_library_post_slider_shortcode');

/**
 * Register Gutenberg Block for the Slider
 */
function city_library_register_slider_block() {
    // Check if Gutenberg is active
    if (!function_exists('register_block_type')) {
        return;
    }

    // Register script
    wp_register_script(
        'city-library-slider-block',
        get_template_directory_uri() . '/js/block-slider.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-block-editor'),
        filemtime(get_template_directory() . '/js/block-slider.js')
    );

    // Register block
    register_block_type('city-library/slider', array(
        'editor_script' => 'city-library-slider-block',
        // The frontend rendering is handled by the shortcode generated in save()
    ));
}
add_action('init', 'city_library_register_slider_block');
