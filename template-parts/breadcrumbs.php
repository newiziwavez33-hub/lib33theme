<?php
/**
 * Breadcrumbs Template Part
 */

// Don't show on front page
if (is_front_page()) {
    return;
}

$items = array();

// 1. Home
$items[] = array(
    'title' => '<span class="material-symbols-outlined text-sm">home</span>',
    'url'   => home_url('/'),
    'class' => '',
);

// 2. Archive / Category / Parent
if (is_single()) {
    $categories = get_the_category();
    if ($categories) {
        $category = $categories[0];
        $items[] = array(
            'title' => esc_html($category->name),
            'url'   => get_category_link($category->term_id),
            'class' => '',
        );
    }
} elseif (is_page()) {
    global $post;
    if ($post->post_parent) {
        $parent_id  = $post->post_parent;
        $breadcrumbs = array();
        while ($parent_id) {
            $page = get_page($parent_id);
            $breadcrumbs[] = array(
                'title' => get_the_title($page->ID),
                'url'   => get_permalink($page->ID),
                'class' => '',
            );
            $parent_id = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        $items = array_merge($items, $breadcrumbs);
    }
} elseif (is_category()) {
    // Current category is the last item, handled below as title
} elseif (is_search()) {
    // Search result is last item
}

// 3. Current Page (Last Item)
$current_title = '';
if (is_search()) {
    $current_title = sprintf(__('Search: %s', 'city-library'), get_search_query());
} elseif (is_archive()) {
    $current_title = get_the_archive_title();
} elseif (is_404()) {
    $current_title = __('Error 404', 'city-library');
} else {
    $current_title = get_the_title();
    // Truncate long titles
    if (strlen($current_title) > 30) {
        $current_title = mb_substr($current_title, 0, 30) . '...';
    }
}

$items[] = array(
    'title' => $current_title,
    'url'   => '', // No link for current
    'class' => 'active',
);

?>

<div class="w-full max-w-[95%] sm:max-w-[90%] xl:max-w-[85%] 2xl:max-w-[80%] mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <nav aria-label="Breadcrumb" class="breadcrumb-container">
        <?php foreach ($items as $index => $item) : ?>
            <?php
            $is_last = ($index === count($items) - 1);
            $class = 'breadcrumb-item ' . ($is_last ? 'active' : '');
            ?>

            <?php if (!$is_last) : ?>
                <a href="<?php echo esc_url($item['url']); ?>" class="<?php echo esc_attr($class); ?>">
                    <?php echo $item['title']; // Allow HTML for icons ?>
                </a>
            <?php else : ?>
                <span class="<?php echo esc_attr($class); ?>">
                    <?php echo esc_html($item['title']); ?>
                </span>
            <?php endif; ?>

        <?php endforeach; ?>
    </nav>
</div>
