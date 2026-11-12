<?php
/**
 * Live Search AJAX Handler
 *
 * Implements high-precision, relevant search across posts, pages, and library branches:
 * - Exact phrase/sentence matching in post_title, post_content, post_excerpt
 * - Strict multi-word filtering: all significant words must be present (no junk results)
 * - Automatic Russian stop-word and preposition filtering
 * - Contextual snippet generation (80-120 chars around query)
 * - Intelligent Ghost Autocomplete (prefix, after quotes, and word-in-title/content extraction)
 * - Full protection against SQL injection and XSS
 *
 * @package CityLibrary
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Helper: Extract clean contextual snippet (80-120 chars) around matched query or words.
 *
 * @param string       $content        Raw content or excerpt.
 * @param string|array $query_or_words Search phrase or array of significant words.
 * @param int          $max_len        Maximum snippet length.
 * @return string Clean snippet with ellipsis.
 */
function city_library_search_extract_snippet($content, $query_or_words, $max_len = 110) {
    if (empty($content)) {
        return '';
    }

    // Strip shortcodes, comments, HTML, and extra whitespace
    $text = strip_shortcodes($content);
    $text = preg_replace('/<!--(.|\s)*?-->/', ' ', $text);
    $text = wp_strip_all_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);
    $text = trim($text);

    if (empty($text)) {
        return '';
    }

    $lower_text = mb_strtolower($text);
    $pos = false;
    $match_len = 0;

    // 1. Try finding full query phrase first
    if (is_string($query_or_words)) {
        $phrase = trim($query_or_words);
        if (!empty($phrase)) {
            $pos = mb_strpos($lower_text, mb_strtolower($phrase));
            if ($pos !== false) {
                $match_len = mb_strlen($phrase);
            }
        }
    }

    // 2. Fallback: try individual significant words
    if ($pos === false && is_array($query_or_words)) {
        foreach ($query_or_words as $word) {
            $word = trim($word);
            if (mb_strlen($word) >= 3) {
                $pos = mb_strpos($lower_text, mb_strtolower($word));
                if ($pos !== false) {
                    $match_len = mb_strlen($word);
                    break;
                }
            }
        }
    }

    // If query not in text, return beginning of text
    if ($pos === false) {
        if (mb_strlen($text) <= $max_len) {
            return $text;
        }
        return mb_substr($text, 0, $max_len) . '…';
    }

    // Compute window around match
    $half = (int) (($max_len - $match_len) / 2);
    $start = max(0, $pos - $half);

    // Snap to word boundary before match
    if ($start > 0) {
        $space_before = mb_strpos($text, ' ', $start);
        if ($space_before !== false && $space_before < $pos) {
            $start = $space_before + 1;
        }
    }

    $snippet = mb_substr($text, $start, $max_len);

    // Snap to word boundary after match
    $last_space = mb_strrpos($snippet, ' ');
    if ($last_space !== false && ($start + $max_len < mb_strlen($text))) {
        $snippet = mb_substr($snippet, 0, $last_space);
    }

    $prefix = ($start > 0) ? '…' : '';
    $suffix = ($start + mb_strlen($snippet) < mb_strlen($text)) ? '…' : '';

    return $prefix . trim($snippet) . $suffix;
}

/**
 * Handle Live Search AJAX requests.
 */
function city_library_live_search_handler() {
    // 1. Security Check: Nonce Verification
    if (!check_ajax_referer('city_library_search_nonce', 'nonce', false)) {
        wp_send_json_error(
            array(
                'message' => esc_html__('Неверный проверочный код безопасности (nonce).', 'city-library'),
            ),
            403
        );
    }

    // 2. Input Sanitization
    $raw_query = isset($_POST['query']) ? wp_unslash($_POST['query']) : '';
    if (empty($raw_query) && isset($_GET['query'])) {
        $raw_query = wp_unslash($_GET['query']);
    } elseif (empty($raw_query) && isset($_REQUEST['s'])) {
        $raw_query = wp_unslash($_REQUEST['s']);
    }

    $query = sanitize_text_field($raw_query);
    $query = preg_replace('/\s+/u', ' ', trim($query));
    $query_len = mb_strlen($query);

    // Minimum 2 characters required for live suggestions
    if (empty($query) || $query_len < 2) {
        wp_send_json_success(
            array(
                'query'           => $query,
                'results'         => array(),
                'suggestion_text' => '',
                'best_match'      => '',
                'total'           => 0,
            )
        );
    }

    global $wpdb;
    $lower_query = mb_strtolower($query);

    // 3. Filter Significant Words (Strip Russian prepositions, conjunctions, short noise)
    $russian_stopwords = array(
        'в', 'во', 'и', 'или', 'но', 'а', 'к', 'ко', 'о', 'об', 'обо',
        'по', 'с', 'со', 'у', 'за', 'на', 'над', 'из', 'из-за', 'под',
        'от', 'до', 'для', 'без', 'при', 'про', 'через', 'как', 'так',
        'же', 'ли', 'бы', 'то', 'что', 'он', 'она', 'оно', 'они', 'мы',
        'вы', 'я', 'ты', 'не', 'ни', 'да', 'нет', 'где', 'чем', 'кто',
        'ее', 'его', 'их', 'ему', 'ей', 'им', 'ими', 'том', 'этом', 'эта',
        'это', 'этот', 'эти', 'все', 'всё', 'вся', 'всех', 'всем', 'уже'
    );

    $tokens = preg_split('/[\s,\.\?!:;\(\)\[\]"«»\-\+\/]+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
    $significant_words = array();
    foreach ($tokens as $token) {
        $lower_token = mb_strtolower($token);
        if (mb_strlen($lower_token) >= 3 && !in_array($lower_token, $russian_stopwords, true)) {
            $significant_words[] = $lower_token;
        }
    }

    // If query consisted only of short words, use all tokens >= 2 chars
    if (empty($significant_words)) {
        foreach ($tokens as $token) {
            if (mb_strlen($token) >= 2) {
                $significant_words[] = mb_strtolower($token);
            }
        }
    }

    // Searchable post types
    $post_types = array('post', 'page', 'library_branch');
    $post_types = apply_filters('city_library_search_post_types', $post_types);
    $post_types_placeholders = implode(', ', array_fill(0, count($post_types), '%s'));

    $results = array();
    $seen_ids = array();
    $suggestion_text = '';

    /**
     * Helper to format a single search result item
     */
    $format_item = function ($post_obj) use ($query, $significant_words) {
        $post_id   = (int) $post_obj->ID;
        $post_type = $post_obj->post_type;
        $raw_title = $post_obj->post_title;
        $title     = html_entity_decode(wp_strip_all_tags($raw_title), ENT_QUOTES, 'UTF-8');
        $date      = get_the_date('j F Y', $post_id);
        $url       = get_permalink($post_id);

        $type = 'article';
        $type_label = __('Статья', 'city-library');
        $meta = $date;

        if ($post_type === 'page') {
            $type = 'page';
            $type_label = __('Страница', 'city-library');
            $meta = __('Информация для читателей', 'city-library');
        } elseif ($post_type === 'library_branch') {
            $type = 'branch';
            $type_label = __('Филиал', 'city-library');
            $address = get_post_meta($post_id, '_library_address', true);
            $meta = !empty($address) ? $address : __('Городская библиотека', 'city-library');
        } elseif ($post_type === 'post') {
            $categories = get_the_category($post_id);
            if (!empty($categories)) {
                $cat_slugs = wp_list_pluck($categories, 'slug');
                $cat_names = wp_list_pluck($categories, 'name');

                if (in_array('knigi', $cat_slugs, true) || in_array('books', $cat_slugs, true) || in_array('novye-postupleniya', $cat_slugs, true)) {
                    $type = 'book';
                    $type_label = __('Книга', 'city-library');
                } elseif (in_array('afisha', $cat_slugs, true) || in_array('events', $cat_slugs, true) || in_array('meropriyatiya', $cat_slugs, true)) {
                    $type = 'event';
                    $type_label = __('Событие', 'city-library');
                } elseif (in_array('novosti', $cat_slugs, true) || in_array('news', $cat_slugs, true)) {
                    $type = 'news';
                    $type_label = __('Новость', 'city-library');
                }

                $meta = implode(' • ', array_slice($cat_names, 0, 2));
            }
        }

        // Generate contextual snippet if query is mentioned in content/excerpt
        $content_to_scan = !empty($post_obj->post_excerpt) ? $post_obj->post_excerpt : $post_obj->post_content;
        $snippet = city_library_search_extract_snippet($content_to_scan, !empty($query) ? $query : $significant_words, 110);

        $thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');

        return array(
            'id'          => $post_id,
            'title'       => $title,
            'url'         => esc_url($url),
            'post_type'   => $post_type,
            'type'        => $type,
            'type_label'  => $type_label,
            'label'       => $type_label,
            'date'        => $date,
            'meta'        => $meta,
            'snippet'     => $snippet,
            'thumbnail'   => $thumb ? esc_url($thumb) : null,
        );
    };

    // 4. STEP A: Exact Phrase Match (word or sentence mentioned as a whole)
    $like_exact = '%' . $wpdb->esc_like($query) . '%';
    $like_prefix = $wpdb->esc_like($query) . '%';

    $phrase_sql = "SELECT ID, post_title, post_content, post_excerpt, post_type, post_date,
        (CASE
            WHEN post_title LIKE %s THEN 100
            WHEN post_title LIKE %s THEN 80
            WHEN post_title LIKE %s THEN 60
            WHEN post_excerpt LIKE %s THEN 35
            ELSE 15
        END) AS relevance_score
        FROM {$wpdb->posts}
        WHERE post_status = 'publish'
          AND post_type IN ($post_types_placeholders)
          AND (post_title LIKE %s OR post_content LIKE %s OR post_excerpt LIKE %s)
        ORDER BY relevance_score DESC, post_date DESC
        LIMIT 8";

    $phrase_params = array_merge(
        array(
            $like_prefix,
            '% ' . $like_prefix,
            $like_exact,
            $like_exact,
        ),
        $post_types,
        array(
            $like_exact,
            $like_exact,
            $like_exact,
        )
    );

    $exact_rows = $wpdb->get_results($wpdb->prepare($phrase_sql, $phrase_params));

    if (!empty($exact_rows)) {
        foreach ($exact_rows as $row) {
            $results[] = $format_item($row);
            $seen_ids[$row->ID] = true;
        }
    }

    // 5. STEP B: Multi-word strict AND search (if fewer than 8 results and multiple significant words)
    if (count($results) < 8 && count($significant_words) > 1) {
        $remaining = 8 - count($results);
        $where_clauses = array();
        $params_words = array();

        foreach ($significant_words as $word) {
            $word_like = '%' . $wpdb->esc_like($word) . '%';
            $where_clauses[] = "(post_title LIKE %s OR post_content LIKE %s OR post_excerpt LIKE %s)";
            $params_words[] = $word_like;
            $params_words[] = $word_like;
            $params_words[] = $word_like;
        }

        $all_words_where = implode(' AND ', $where_clauses);
        $not_in_clause = !empty($seen_ids) ? "AND ID NOT IN (" . implode(',', array_map('intval', array_keys($seen_ids))) . ")" : "";

        $words_sql = "SELECT ID, post_title, post_content, post_excerpt, post_type, post_date
            FROM {$wpdb->posts}
            WHERE post_status = 'publish'
              AND post_type IN ($post_types_placeholders)
              $not_in_clause
              AND ($all_words_where)
            ORDER BY post_date DESC
            LIMIT %d";

        $final_params = array_merge($post_types, $params_words, array($remaining));
        $word_rows = $wpdb->get_results($wpdb->prepare($words_sql, $final_params));

        if (!empty($word_rows)) {
            foreach ($word_rows as $row) {
                if (empty($seen_ids[$row->ID])) {
                    $results[] = $format_item($row);
                    $seen_ids[$row->ID] = true;
                }
            }
        }
    }

    // 6. GHOST AUTOCOMPLETE SUGGESTION DISCOVERY
    // Priority 1: Check prefix of post_title directly
    $sql_prefix = "SELECT post_title FROM {$wpdb->posts}
        WHERE post_status = 'publish'
          AND post_type IN ($post_types_placeholders)
          AND post_title LIKE %s
        ORDER BY CHAR_LENGTH(post_title) ASC, post_date DESC
        LIMIT 1";
    $p_row = $wpdb->get_row($wpdb->prepare($sql_prefix, array_merge($post_types, array($like_prefix))));

    if ($p_row) {
        $clean = html_entity_decode(wp_strip_all_tags($p_row->post_title), ENT_QUOTES, 'UTF-8');
        $suggestion_text = trim($clean, "«»\"' ");
    }

    // Priority 2: Russian quotes at beginning of title (« or ")
    if (empty($suggestion_text)) {
        $sql_quote = "SELECT post_title FROM {$wpdb->posts}
            WHERE post_status = 'publish'
              AND post_type IN ($post_types_placeholders)
              AND (post_title LIKE %s OR post_title LIKE %s)
            ORDER BY CHAR_LENGTH(post_title) ASC, post_date DESC
            LIMIT 1";
        $q_row = $wpdb->get_row($wpdb->prepare($sql_quote, array_merge($post_types, array('«' . $like_prefix, '"' . $like_prefix))));
        if ($q_row) {
            $clean = html_entity_decode(wp_strip_all_tags($q_row->post_title), ENT_QUOTES, 'UTF-8');
            $suggestion_text = trim($clean, "«»\"' ");
        }
    }

    // Priority 3: Word located INSIDE title (e.g., "А.С. Пушкин в воспоминаниях" -> "Пушкин в воспоминаниях")
    if (empty($suggestion_text)) {
        foreach ($results as $item) {
            $t = $item['title'];
            $pos = mb_stripos($t, $query);
            if ($pos !== false) {
                // Slice from matched word to the end
                $sliced = mb_substr($t, $pos);
                // Clean punctuation at end
                $sliced = trim($sliced, "«»\"' .,;:-");
                if (mb_strlen($sliced) > $query_len) {
                    $suggestion_text = $sliced;
                    break;
                }
            }
        }
    }

    // Priority 4: Look for word in content/snippet of results
    if (empty($suggestion_text)) {
        foreach ($results as $item) {
            if (!empty($item['snippet'])) {
                $pos = mb_stripos($item['snippet'], $query);
                if ($pos !== false) {
                    $sliced = mb_substr($item['snippet'], $pos);
                    // Take up to 6 words
                    $words_in_slice = preg_split('/\s+/u', $sliced, 7);
                    if (count($words_in_slice) > 1) {
                        array_pop($words_in_slice);
                        $candidate = implode(' ', $words_in_slice);
                        $candidate = trim($candidate, "«»\"' .,;:-…");
                        if (mb_strlen($candidate) > $query_len) {
                            $suggestion_text = $candidate;
                            break;
                        }
                    }
                }
            }
        }
    }

    wp_send_json_success(
        array(
            'query'           => $query,
            'results'         => $results,
            'suggestion_text' => $suggestion_text,
            'best_match'      => $suggestion_text,
            'total'           => count($results),
        )
    );
}
add_action('wp_ajax_city_library_live_search', 'city_library_live_search_handler');
add_action('wp_ajax_nopriv_city_library_live_search', 'city_library_live_search_handler');
