<?php
/**
 * City Library Branches Map & CPT
 *
 * Usage: [city_library_branches_map]
 * Usage (Single): [city_library_branch id="123"]
 */

/**
 * 1. Register Custom Post Type 'library_branch'
 */
function city_library_register_branches_cpt() {
    $labels = array(
        'name'                  => _x('Библиотеки', 'Post Type General Name', 'city-library'),
        'singular_name'         => _x('Библиотека', 'Post Type Singular Name', 'city-library'),
        'menu_name'             => __('Библиотеки (Карта)', 'city-library'),
        'name_admin_bar'        => __('Библиотеку', 'city-library'),
        'archives'              => __('Архив библиотек', 'city-library'),
        'attributes'            => __('Атрибуты', 'city-library'),
        'parent_item_colon'     => __('Родительская библиотека:', 'city-library'),
        'all_items'             => __('Все библиотеки', 'city-library'),
        'add_new_item'          => __('Добавить новую библиотеку', 'city-library'),
        'add_new'               => __('Добавить новую', 'city-library'),
        'new_item'              => __('Новая библиотека', 'city-library'),
        'edit_item'             => __('Редактировать библиотеку', 'city-library'),
        'update_item'           => __('Обновить библиотеку', 'city-library'),
        'view_item'             => __('Просмотреть библиотеку', 'city-library'),
        'view_items'            => __('Просмотреть библиотеки', 'city-library'),
        'search_items'          => __('Искать библиотеку', 'city-library'),
        'not_found'             => __('Не найдено', 'city-library'),
        'not_found_in_trash'    => __('Не найдено в корзине', 'city-library'),
    );
    $args = array(
        'label'                 => __('Библиотека', 'city-library'),
        'description'           => __('Каталог библиотек для отображения на карте', 'city-library'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail'),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-location',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
    );
    register_post_type('library_branch', $args);
}
add_action('init', 'city_library_register_branches_cpt');

/**
 * 2. Add Meta Boxes for Coordinates, Address, Phone
 */
function city_library_branches_add_meta_boxes() {
    add_meta_box(
        'library_branch_details',
        __('Детали библиотеки', 'city-library'),
        'city_library_branches_meta_callback',
        'library_branch',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'city_library_branches_add_meta_boxes');

function city_library_branches_meta_callback($post) {
    wp_nonce_field('city_library_branches_save_meta', 'city_library_branches_nonce');

    $coords = get_post_meta($post->ID, '_library_coords', true);
    $address = get_post_meta($post->ID, '_library_address', true);
    $phone = get_post_meta($post->ID, '_library_phone', true);
    $email = get_post_meta($post->ID, '_library_email', true);

    echo '<table class="form-table">';

    // Coordinates
    echo '<tr><th><label for="library_coords">' . __('Координаты (lat, lon)', 'city-library') . '</label></th>';
    echo '<td><input type="text" id="library_coords" name="library_coords" value="' . esc_attr($coords) . '" class="regular-text" placeholder="56.123456, 40.123456" />';
    echo '<p class="description">' . __('Скопируйте координаты из Яндекс.Карт.', 'city-library') . '</p></td></tr>';

    // Address
    echo '<tr><th><label for="library_address">' . __('Адрес', 'city-library') . '</label></th>';
    echo '<td><input type="text" id="library_address" name="library_address" value="' . esc_attr($address) . '" class="regular-text" /></td></tr>';

    // Phone
    echo '<tr><th><label for="library_phone">' . __('Телефон', 'city-library') . '</label></th>';
    echo '<td><input type="text" id="library_phone" name="library_phone" value="' . esc_attr($phone) . '" class="regular-text" /></td></tr>';

    // Email
    echo '<tr><th><label for="library_email">' . __('Email', 'city-library') . '</label></th>';
    echo '<td><input type="email" id="library_email" name="library_email" value="' . esc_attr($email) . '" class="regular-text" /></td></tr>';

    // Shortcode Hint (Added as requested)
    echo '<tr><th>' . __('Шорткод для вставки', 'city-library') . '</th>';
    echo '<td><code>[city_library_branch id="' . $post->ID . '"]</code><br><span class="description">' . __('Используйте этот код, чтобы вставить информацию о библиотеке в любую запись или страницу.', 'city-library') . '</span></td></tr>';


    echo '</table>';
}

/**
 * 3. Save Meta Data
 */
function city_library_branches_save_meta($post_id) {
    if (!isset($_POST['city_library_branches_nonce']) || !wp_verify_nonce($_POST['city_library_branches_nonce'], 'city_library_branches_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['library_coords', 'library_address', 'library_phone', 'library_email'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'city_library_branches_save_meta');


/**
 * 4. Shortcode Implementation: Map + List
 */
function city_library_branches_map_shortcode($atts) {
    $atts = shortcode_atts(array(
        'height' => '500px',
        'zoom' => '12',
    ), $atts);

    // Query Branches
    $args = array(
        'post_type' => 'library_branch',
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    );
    $query = new WP_Query($args);

    $branches_data = array();
    $list_html = '';

    if ($query->have_posts()) {
        $list_html .= '<div class="library-accordion-list mt-8 space-y-4">';

        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();
            $coords_str = get_post_meta($id, '_library_coords', true);
            $address = get_post_meta($id, '_library_address', true);
            $phone = get_post_meta($id, '_library_phone', true);
            $email = get_post_meta($id, '_library_email', true);
            $title = get_the_title();
            $content = get_the_content();
            $thumbnail = get_the_post_thumbnail_url($id, 'medium');

            if ($coords_str) {
                $coords = array_map('floatval', explode(',', $coords_str));
                if (count($coords) === 2) {
                    $branches_data[] = array(
                        'coords' => $coords,
                        'name' => $title,
                        'address' => $address,
                        'phone' => $phone,
                        'id' => $id
                    );
                }
            }

            // Accordion Item
            $list_html .= '<div class="library-item border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow duration-300" id="library-item-' . esc_attr($id) . '">';

            // Header
            $list_html .= '<div class="library-header p-5 bg-slate-50 cursor-pointer flex justify-between items-center gap-3 select-none focus-within:ring-2 focus-within:ring-primary focus-within:ring-inset" onclick="toggleLibraryItem(this)" role="button" tabindex="0" aria-expanded="false" onkeydown="if(event.key===\'Enter\'||event.key===\' \'){event.preventDefault();toggleLibraryItem(this);}">';
            $list_html .= '<div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-4 min-w-0">';
            $list_html .= '<h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 break-words">' . esc_html($title) . '</h3>';
            if ($address) {
                $list_html .= '<span class="text-xs sm:text-sm text-slate-500 block md:inline-block break-words"><span class="material-symbols-outlined align-middle text-base mr-1" aria-hidden="true">location_on</span>' . esc_html($address) . '</span>';
            }
            $list_html .= '</div>';
            $list_html .= '<span class="material-symbols-outlined transform transition-transform duration-300 text-slate-400 shrink-0" aria-hidden="true">expand_more</span>';
            $list_html .= '</div>'; // End Header

            // Body (Hidden)
            $list_html .= '<div class="library-body hidden border-t border-slate-100">';
            $list_html .= '<div class="p-6 flex flex-col md:flex-row gap-8">';

            // Image
            if ($thumbnail) {
                $list_html .= '<div class="w-full md:w-1/3 shrink-0 library-branch-image-wrapper">';
                $list_html .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '" class="w-full h-48 object-cover rounded-xl shadow-sm">';
                $list_html .= '</div>';
            }

            // Content + Details
            $list_html .= '<div class="w-full">';

            // Mobile Address/Phone (Visible here too for clarity)
            $list_html .= '<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-600 bg-slate-50 p-4 rounded-xl">';
            if ($address) $list_html .= '<div class="flex items-center"><span class="material-symbols-outlined mr-2 text-primary" aria-hidden="true">location_on</span>' . esc_html($address) . '</div>';
            if ($phone) $list_html .= '<div class="flex items-center"><span class="material-symbols-outlined mr-2 text-primary" aria-hidden="true">call</span>' . esc_html($phone) . '</div>';
            if ($email) $list_html .= '<div class="flex items-center"><span class="material-symbols-outlined mr-2 text-primary" aria-hidden="true">mail</span><a href="mailto:'.esc_attr($email).'" class="hover:text-primary transition-colors break-all sm:break-words">' . esc_html($email) . '</a></div>';
            $list_html .= '</div>';

            $list_html .= '<div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">';
            $list_html .= wpautop($content);
            $list_html .= '</div>';

            $list_html .= '</div>'; // End Content Column

            $list_html .= '</div></div>'; // End Body & Flex
            $list_html .= '</div>'; // End Item
        }
        $list_html .= '</div>';
        wp_reset_postdata();
    } else {
        $list_html .= '<p class="text-center text-slate-500 py-8">' . __('Библиотеки пока не добавлены.', 'city-library') . '</p>';
    }

    // Enqueue scripts
    wp_enqueue_script('yandex-maps-api', 'https://api-maps.yandex.ru/2.1/?lang=ru_RU', array(), null, true);
    wp_register_script('city-library-branches-map', get_template_directory_uri() . '/js/branches-map.js', array('yandex-maps-api'), '2.0', true);

    // Pass Data to JS
    wp_localize_script('city-library-branches-map', 'branches_map_data', array(
        'branches' => $branches_data,
        'center' => !empty($branches_data) ? $branches_data[0]['coords'] : [56.145, 40.405],
        'zoom' => intval($atts['zoom'])
    ));
    wp_enqueue_script('city-library-branches-map');

    // Output
    $output = '<div class="city-library-map-widget w-full">';
    // Use an inline style block to force Yandex map inner tags to also take full width/height
    $output .= '<style>
        #branches-yandex-map { width: 100% !important; display: block !important; min-height: 300px !important; }
        #branches-yandex-map > ymaps { width: 100% !important; height: 100% !important; }
    </style>';
    $output .= '<div id="branches-yandex-map" class="w-full" style="width: 100%; min-height: 300px; display: block; height: ' . esc_attr($atts['height']) . '; background: #e2e8f0; border-radius: 1.5rem; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);"></div>';
    $output .= $list_html;
    $output .= '</div>';

    return $output;
}
add_shortcode('city_library_branches_map', 'city_library_branches_map_shortcode');


/**
 * 5. Shortcode for Single Library Accordion
 */
function city_library_branch_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
    ), $atts);

    $id = intval($atts['id']);
    if (!$id) return ''; // No ID provided

    $post = get_post($id);
    if (!$post || $post->post_type !== 'library_branch') return __('Библиотека не найдена.', 'city-library');

    $address = get_post_meta($id, '_library_address', true);
    $phone = get_post_meta($id, '_library_phone', true);
    $email = get_post_meta($id, '_library_email', true);
    $title = get_the_title($id);
    $content = $post->post_content; // Raw content, apply filter later
    $thumbnail = get_the_post_thumbnail_url($id, 'medium');

    $output = '';

    // Accordion Item (Single)
    $output .= '<div class="library-item border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow duration-300 my-4" id="library-item-' . esc_attr($id) . '">';

    // Header
    $output .= '<div class="library-header p-5 bg-slate-50 cursor-pointer flex justify-between items-center gap-3 select-none focus-within:ring-2 focus-within:ring-primary focus-within:ring-inset" onclick="toggleLibraryItem(this)" role="button" tabindex="0" aria-expanded="false" onkeydown="if(event.key===\'Enter\'||event.key===\' \'){event.preventDefault();toggleLibraryItem(this);}">';
    $output .= '<div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-4 min-w-0">';
    $output .= '<h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 break-words">' . esc_html($title) . '</h3>';
    if ($address) {
        $output .= '<span class="text-xs sm:text-sm text-slate-500 block md:inline-block break-words"><span class="material-symbols-outlined align-middle text-base mr-1" aria-hidden="true">location_on</span>' . esc_html($address) . '</span>';
    }
    $output .= '</div>';
    $output .= '<span class="material-symbols-outlined transform transition-transform duration-300 text-slate-400 shrink-0" aria-hidden="true">expand_more</span>';
    $output .= '</div>'; // End Header

    // Body (Hidden)
    $output .= '<div class="library-body hidden border-t border-slate-100">';
    $output .= '<div class="p-6 flex flex-col md:flex-row gap-8">';

    // Image
    if ($thumbnail) {
        $output .= '<div class="w-full md:w-1/3 shrink-0 library-branch-image-wrapper">';
        $output .= '<img src="' . esc_url($thumbnail) . '" alt="' . esc_attr($title) . '" class="w-full h-48 object-cover rounded-xl shadow-sm">';
        $output .= '</div>';
    }

    // Content + Details
    $output .= '<div class="w-full">';

    // Mobile Address/Phone
    $output .= '<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-600 bg-slate-50 p-4 rounded-xl">';
    if ($address) $output .= '<div class="flex items-center"><span class="material-symbols-outlined mr-2 text-primary" aria-hidden="true">location_on</span>' . esc_html($address) . '</div>';
    if ($phone) $output .= '<div class="flex items-center"><span class="material-symbols-outlined mr-2 text-primary" aria-hidden="true">call</span>' . esc_html($phone) . '</div>';
    if ($email) $output .= '<div class="flex items-center"><span class="material-symbols-outlined mr-2 text-primary" aria-hidden="true">mail</span><a href="mailto:'.esc_attr($email).'" class="hover:text-primary transition-colors break-all sm:break-words">' . esc_html($email) . '</a></div>';
    $output .= '</div>';

    $output .= '<div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">';
    $output .= wpautop($content);
    $output .= '</div>';

    $output .= '</div>'; // End Content Column

    $output .= '</div></div>'; // End Body & Flex
    $output .= '</div>'; // End Item

    // Ensure JS is loaded for toggle
    if (!wp_script_is('city-library-branches-map', 'registered')) {
        wp_register_script('city-library-branches-map', get_template_directory_uri() . '/js/branches-map.js', array(), '2.0', true);
    }
    wp_enqueue_script('city-library-branches-map');

    return $output;
}
add_shortcode('city_library_branch', 'city_library_branch_shortcode');
