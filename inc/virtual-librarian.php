<?php
/**
 * Virtual Librarian (AI Chatbot) functionality using OpenRouter.
 */

// 1. Register Customizer Settings
function city_library_ai_customizer($wp_customize) {
    // --- Section: Virtual Librarian (Chat UI) ---
    $wp_customize->add_section('virtual_librarian_section', array(
        'title' => __('Виртуальный библиотекарь (Чат ИИ)', 'city-library'),
        'priority' => 160,
    ));

    $wp_customize->add_setting('enable_ai_librarian', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('enable_ai_librarian', array(
        'label' => __('Включить текстовый виджет чата ИИ', 'city-library'),
        'section' => 'virtual_librarian_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('ai_librarian_test_mode', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('ai_librarian_test_mode', array(
        'label' => __('Режим тестирования (Только для авторизованных)', 'city-library'),
        'section' => 'virtual_librarian_section',
        'type' => 'checkbox',
    ));

    // --- Section: Voice Assistant & AI Engine (Core AI Settings) ---
    $wp_customize->add_section('voice_assistant_section', array(
        'title' => __('Голосовой Ассистент и Ядро ИИ', 'city-library'),
        'priority' => 161,
        'description' => __('Тонкие настройки ядра Искусственного Интеллекта и голосового ассистента.', 'city-library'),
    ));

    $wp_customize->add_setting('enable_voice_control', array('default' => false, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('enable_voice_control', array(
        'label' => __('Включить Голосового Ассистента', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('voice_control_test_mode', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('voice_control_test_mode', array(
        'label' => __('Тестовый режим (только авторизованные)', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('openrouter_api_key', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('openrouter_api_key', array(
        'label' => __('OpenRouter API Key', 'city-library'),
        'description' => __('Обязателен для работы ИИ', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('ai_librarian_model', array('default' => 'google/gemini-2.0-flash-001', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ai_librarian_model', array(
        'label' => __('Основная Модель (LLM)', 'city-library'),
        'description' => __('Например: google/gemini-2.0-flash-001', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('ai_librarian_model_fallback', array('default' => 'openai/gpt-4o-mini', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ai_librarian_model_fallback', array(
        'label' => __('Запасная Модель (Fallback)', 'city-library'),
        'description' => __('Используется при сбоях. Например: openai/gpt-4o-mini', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('ai_librarian_image_model', array('default' => 'black-forest-labs/flux-schnell', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ai_librarian_image_model', array(
        'label' => __('Модель для генерации Изображений', 'city-library'),
        'description' => __('Выберите модель для команды /aimg. Модели Flux более бюджетные и качественные.', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'select',
        'choices' => array(
            'black-forest-labs/flux-schnell' => 'Flux Schnell (Высокое качество, бюджетно)',
            'black-forest-labs/flux-dev' => 'Flux Dev (Максимальное качество)',
            'openai/dall-e-3' => 'DALL-E 3 (OpenAI)',
            'stabilityai/stable-diffusion-xl-base-1.0' => 'SDXL 1.0 (Очень дешево)',
            'custom' => 'Указать вручную (Custom)'
        )
    ));

    $wp_customize->add_setting('ai_librarian_image_model_custom', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ai_librarian_image_model_custom', array(
        'label' => __('Пользовательская модель изображений', 'city-library'),
        'description' => __('Если выше выбрано "Указать вручную", впишите модель OpenRouter здесь.', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    // Chat Theme Selection
    $wp_customize->add_setting('ai_chat_theme', array(
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('ai_chat_theme', array(
        'label'    => __('Стиль чата', 'city-library'),
        'section'  => 'virtual_librarian_section',
        'type'     => 'select',
        'choices'  => array(
            'default' => 'Библиотека (по умолчанию)',
            'vk'      => 'ВКонтакте (VK Style)',
            'tg'      => 'Telegram (Светлый)',
            'wa'      => 'WhatsApp (Классика)',
            'mac'     => 'macOS (iMessage)'
        ),
    ));

    // AI Avatar Preset Selection
    $wp_customize->add_setting('ai_librarian_avatar_preset', array(
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('ai_librarian_avatar_preset', array(
        'label'    => __('Готовые Аватары Виртуального Библиотекаря', 'city-library'),
        'section'  => 'virtual_librarian_section',
        'type'     => 'select',
        'choices'  => array(
            'default' => 'Женщина-Библиотекарь 1 (По умолчанию)',
            'preset2' => 'Мужчина-Библиотекарь 1',
            'preset3' => 'Робот-Библиотекарь',
            'preset4' => 'Сова-Библиотекарь',
            'preset5' => 'Абстрактный ИИ',
            'custom'  => 'Своя картинка (ниже)'
        ),
    ));

    // AI Avatar URL
    $wp_customize->add_setting('ai_librarian_avatar', array(
        'default'           => get_template_directory_uri() . '/assets/images/ai-avatar.png',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'ai_librarian_avatar', array(
        'label'    => __('Аватар Виртуального Библиотекаря', 'city-library'),
        'section'  => 'virtual_librarian_section',
        'settings' => 'ai_librarian_avatar',
    )));

    $wp_customize->add_setting('ai_librarian_kb_ids', array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('ai_librarian_kb_ids', array(
        'label' => __('База знаний (ID файлов)', 'city-library'),
        'description' => __('Введите через запятую ID файлов (TXT, DOCX, ODT) из Медиабиблиотеки.', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('ai_persona_prompt', array('default' => 'Ты Виртуальный библиотекарь - библиограф (женщина) с 30 летним стажем. Обращайся к пользователю на "Вы", как профессиональный библиотекарь. Не выходи за рамки библиотечной этики и работы, всю информацию по литературе и книгам предоставляй только правдивую. Твое имя - Виртуальный библиотекарь. Пиши только на безупречном русском языке. СТРОГО ЗАПРЕЩЕНО использовать иероглифы, китайские или японские символы в любых ответах. При генерации изображений весь текст должен быть на идеальном русском языке, без ошибок, опечаток, со всеми правилами русского языка!', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('ai_persona_prompt', array(
        'label' => __('Системный промпт (Persona)', 'city-library'),
        'description' => __('Инструкция для ИИ, определяющая его характер. Не рекомендуется полностью удалять.', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'textarea',
    ));

    $wp_customize->add_setting('ai_persona_prompt_extra', array('default' => '', 'sanitize_callback' => 'sanitize_textarea_field'));
    $wp_customize->add_control('ai_persona_prompt_extra', array(
        'label' => __('Дополнительный промпт (Расширение)', 'city-library'),
        'description' => __('Добавьте свои инструкции, правила и специализации к базовой персоне. Библиотекарь будет неукоснительно соблюдать их.', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'textarea',
    ));

    $wp_customize->add_setting('voice_pitch', array('default' => '1.0', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('voice_pitch', array(
        'label' => __('Тон голоса (Pitch)', 'city-library'),
        'description' => __('Обычно 1.0. Можно сделать выше (1.2) или ниже (0.8).', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    $wp_customize->add_setting('voice_rate', array('default' => '1.05', 'sanitize_callback' => 'sanitize_text_field'));
    $wp_customize->add_control('voice_rate', array(
        'label' => __('Скорость речи (Rate)', 'city-library'),
        'description' => __('Обычно 1.05.', 'city-library'),
        'section' => 'voice_assistant_section',
        'type' => 'text',
    ));

    // --- Section: Geolocation for Library Branches (Maps) ---
    $wp_customize->add_section('voice_geolocation_section', array(
        'title' => __('Геолокация филиалов (Карты)', 'city-library'),
        'priority' => 162,
        'description' => __('Укажите точные адреса для каждого филиала (для Яндекс.Карт).', 'city-library'),
    ));

    $branches = array(
        'cgb' => 'Центральная городская библиотека',
        'cdb' => 'Центральная детская библиотека',
        '1' => 'Библиотека-филиал № 1',
        '2' => 'Библиотека-филиал № 2',
        '3' => 'Библиотека-филиал № 3',
        '4' => 'Библиотека-филиал № 4',
        '5' => 'Библиотека-филиал № 5',
        '6' => 'Библиотека-филиал № 6',
        '7' => 'Библиотека-филиал № 7 (Музей)',
        '8' => 'Библиотека-филиал № 8 (Экологическая)',
        '9' => 'Библиотека-филиал № 9',
        '10' => 'Библиотека-филиал № 10 (Досуговый центр)',
        '11' => 'Библиотека-филиал № 11',
        '13' => 'Библиотека-филиал № 13',
        '14' => 'Библиотека-филиал № 14',
        '15' => 'Библиотека-филиал № 15 (Семейного чтения)',
        '16' => 'Библиотека-филиал № 16 (Историко-духовного)'
    );

    foreach ($branches as $key => $label) {
        $default_address = '';
        if ($key === 'cgb') $default_address = 'г. Владимир, Суздальский пр-кт, д. 2';
        elseif ($key === 'cdb') $default_address = 'г. Владимир, ул. Белоконской, д. 10-а';
        elseif ($key === '1') $default_address = 'г. Владимир, пр-кт Строителей, д. 23';
        elseif ($key === '2') $default_address = 'г. Владимир, пр-кт Ленина, д. 12';
        elseif ($key === '3') $default_address = 'г. Владимир, ул. Добросельская, д. 2-в';
        elseif ($key === '4') $default_address = 'г. Владимир, ул. Комиссарова, д. 69';
        elseif ($key === '5') $default_address = 'г. Владимир, пр-кт Суздальский, д. 2';
        elseif ($key === '6') $default_address = 'г. Владимир, ул. Мира, д. 37';
        elseif ($key === '7') $default_address = 'г. Владимир, ул. Добросельская, д. 189-б';
        elseif ($key === '8') $default_address = 'г. Владимир, ул. Диктора Левитана, д. 36';
        elseif ($key === '9') $default_address = 'г. Владимир, ул. Горького, д. 85';
        elseif ($key === '10') $default_address = 'г. Владимир, ул. Егорова, д. 10';
        elseif ($key === '11') $default_address = 'г. Владимир, мкр. Юрьевец, ул. Институтский городок, д. 4';
        elseif ($key === '13') $default_address = 'г. Владимир, мкр. Юрьевец, ул. Ноябрьская, д. 2-а';
        elseif ($key === '14') $default_address = 'г. Владимир, мкр. Энергетик, ул. Энергетиков, д. 12';
        elseif ($key === '15') $default_address = 'г. Владимир, мкр. Энергетик, ул. Совхозная, д. 11';
        elseif ($key === '16') $default_address = 'г. Владимир, мкр. Коммунар, ул. Песочная, д. 2-а';

        $wp_customize->add_setting("branch_address_$key", array('default' => $default_address, 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("branch_address_$key", array(
            'label' => $label,
            'section' => 'voice_geolocation_section',
            'type' => 'text',
        ));
    }

    // --- Section: Custom Voice Commands ---
    $wp_customize->add_section('custom_voice_commands_section', array(
        'title' => __('Пользовательские голосовые команды', 'city-library'),
        'priority' => 163,
        'description' => __('Настройте до 20 собственных голосовых команд. В первом поле укажите фразы через запятую (например: библиотека 2, филиал 2). Во втором поле — ссылку, куда перейдет ассистент.', 'city-library'),
    ));

    for ($i = 1; $i <= 20; $i++) {
        $wp_customize->add_setting("voice_cmd_phrases_$i", array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("voice_cmd_phrases_$i", array(
            'label' => sprintf(__('Команда %d (фразы через запятую)', 'city-library'), $i),
            'section' => 'custom_voice_commands_section',
            'type' => 'text',
        ));

        $wp_customize->add_setting("voice_cmd_url_$i", array('default' => '', 'sanitize_callback' => 'esc_url_raw'));
        $wp_customize->add_control("voice_cmd_url_$i", array(
            'label' => sprintf(__('Ссылка для команды %d', 'city-library'), $i),
            'section' => 'custom_voice_commands_section',
            'type' => 'url',
        ));
    }
}
add_action('customize_register', 'city_library_ai_customizer');

// Clear KB cache when customizer is saved
function city_library_clear_ai_kb_cache() {
    delete_transient('city_library_ai_kb_text');
}
add_action('customize_save_after', 'city_library_clear_ai_kb_cache');

// Helper function to extract text from files
function city_library_extract_text_from_files($ids_string) {
    if (empty($ids_string)) return '';

    $cached_text = get_transient('city_library_ai_kb_text');
    if ($cached_text !== false) {
        return $cached_text;
    }

    $ids = array_map('intval', explode(',', $ids_string));
    $extracted_text = "";

    foreach ($ids as $id) {
        if (!$id) continue;

        $filepath = get_attached_file($id);
        if (!$filepath || !file_exists($filepath)) continue;

        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if ($ext === 'txt') {
            $text = file_get_contents($filepath);
            $extracted_text .= wp_strip_all_tags($text) . "\n\n";
        } elseif ($ext === 'docx' || $ext === 'odt') {
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($filepath) === true) {
                    $xml_content = '';
                    if ($ext === 'docx' && ($index = $zip->locateName('word/document.xml')) !== false) {
                        $xml_content = $zip->getFromIndex($index);
                    } elseif ($ext === 'odt' && ($index = $zip->locateName('content.xml')) !== false) {
                        $xml_content = $zip->getFromIndex($index);
                    }
                    $zip->close();

                    if (!empty($xml_content)) {
                        // Replace XML tags with spaces to avoid word merging, then strip
                        $clean_text = strip_tags(str_replace(['<', '>'], [' <', '> '], $xml_content));
                        // Clean up excess whitespace
                        $clean_text = preg_replace('/\s+/', ' ', $clean_text);
                        $extracted_text .= trim($clean_text) . "\n\n";
                    }
                }
            }
        }
    }

    // Cache the extracted text for 12 hours (to avoid unzipping on every chat message)
    // Limit to ~20,000 characters to fit within most free model context windows safely
    $extracted_text = mb_substr($extracted_text, 0, 20000);
    set_transient('city_library_ai_kb_text', $extracted_text, 12 * HOUR_IN_SECONDS);

    return $extracted_text;
}

// 1.5. Deep Knowledge Base Scraping (WP Cron)
function sync_library_knowledge_base() {
    $knowledge_data = array(
        'last_updated' => current_time('mysql'),
        'branches_data' => '',
        'extracted_addresses' => array()
    );

    $branches_text = "";
    $addresses_list = array();

    // Check direct biblioteka33 page first as requested
    $remote_url = "https://biblioteka33.ru/?p=19379";
    $response = wp_remote_get($remote_url, array('timeout' => 15));
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $html = wp_remote_retrieve_body($response);
        // Clean up formatting
        $html = str_replace(['<br>', '<br/>', '<br />', '<li>'], "\n", $html);
        $clean_text_for_regex = strip_tags($html);

        // This regex aggressively searches for blocks starting with a library name, and looks for its details
        preg_match_all('/(Библиотека(?:-филиал)?\s*(?:№\s*\d+|Центральная[^:\n]*)).*?(?:(?:адрес|ул\.|пр-т)[^\n]+).*?(?:(?:телефон|тел\.)[^\n]+)/uis', $clean_text_for_regex, $library_blocks);

        if (!empty($library_blocks[0])) {
            $branches_text .= "ДАННЫЕ СПИСКА БИБЛИОТЕК ИЗ ОСНОВНОГО ИСТОЧНИКА:\n";
            foreach ($library_blocks[0] as $block) {
                // Parse inner details from each block
                $lib_name = '';
                $address = '';
                $phone = '';

                if (preg_match('/(Библиотека(?:-филиал)?\s*(?:№\s*\d+|Центральная[^:\n]*))/ui', $block, $m)) $lib_name = trim($m[1]);
                if (preg_match('/(?:адрес|находимся)(?:[:\s]+)?([^\n]+)/ui', $block, $m)) $address = trim($m[1]);
                elseif (preg_match('/(?:г\.\s*Владимир,\s*)?(ул\.|пр-т|мкр\.|пр\.|Школьный пр\.)\s*([^.,\n]+),\s*(?:д\.\s*)?(\d+[а-яА-Я\-]*)/ui', $block, $m)) $address = trim($m[0]);

                if (preg_match('/(?:телефон|тел\.)(?:[:\s]+)?([+0-9\-\(\)\s]{7,20})/ui', $block, $m)) $phone = trim($m[1]);

                if ($lib_name) {
                    $branches_text .= "### {$lib_name}\n";
                    if ($address) {
                        $branches_text .= "- Адрес: {$address}\n";
                        $addresses_list[] = mb_strtolower($address);
                    }
                    if ($phone) $branches_text .= "- Телефон: {$phone}\n";
                    // Force the link to the actual site section as requested by the user
                    $branches_text .= "- Страница филиала на сайте: https://biblioteka33.ru/?p=19379\n\n";
                }
            }
        }
    }

    $menu_locations = get_nav_menu_locations();

    if (isset($menu_locations['primary'])) {
        $menu = wp_get_nav_menu_object($menu_locations['primary']);
        if ($menu) {
            $menu_items = wp_get_nav_menu_items($menu->term_id);
            if ($menu_items) {
                $libraries_parent_id = 0;
                $about_parent_id = 0;

                foreach ($menu_items as $item) {
                    if (mb_stripos($item->title, 'библиотеки') !== false || mb_stripos($item->title, 'филиалы') !== false) {
                        $libraries_parent_id = $item->ID;
                    }
                    if (mb_stripos($item->title, 'о нас') !== false || mb_stripos($item->title, 'о библиотеке') !== false) {
                        $about_parent_id = $item->ID;
                    }
                }

                if ($libraries_parent_id > 0) {
                    foreach ($menu_items as $item) {
                        if ($item->menu_item_parent == $libraries_parent_id) {
                            $branch_page_id = url_to_postid($item->url);
                            if ($branch_page_id) {
                                $branch_page = get_post($branch_page_id);
                                if ($branch_page) {
                                    $raw_content = strip_shortcodes($branch_page->post_content);

                                    $address = '';
                                    $phone = '';
                                    $vk_link = '';
                                    $hours = '';

                                    // Deep Regex extraction
                                    $clean_text_for_regex = wp_strip_all_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $raw_content));

                                    if (preg_match('/(?:адрес|находимся)(?:[:\s]+)?([^\n]+)/ui', $clean_text_for_regex, $m)) {
                                        $address = trim($m[1]);
                                    } elseif (preg_match('/(?:г\.\s*Владимир,\s*)?(ул\.|пр-т|мкр\.|пр\.|Школьный пр\.)\s*([^.,\n]+),\s*(?:д\.\s*)?(\d+[а-яА-Я\-]*)/ui', $clean_text_for_regex, $m)) {
                                        $address = trim($m[0]); // Fallback: look for actual street format anywhere in text
                                    }

                                    if (preg_match('/(?:телефон|тел\.)(?:[:\s]+)?([+0-9\-\(\)\s]{7,20})/ui', $clean_text_for_regex, $m)) {
                                        $phone = trim($m[1]);
                                    }

                                    if (preg_match('/(?:режим работы|график|часы работы)(?:[:\s]+)?([^\n]+)/ui', $clean_text_for_regex, $m)) {
                                        $hours = trim($m[1]);
                                    }

                                    if (preg_match('/href=["\'](https?:\/\/vk\.com\/[^"\']+)["\']/i', $raw_content, $m)) {
                                        $vk_link = $m[1];
                                    }

                                    // Also extract District/Street if explicit
                                    $district = '';
                                    if (preg_match('/(?:район|мкр\.|микрорайон)(?:[:\s]+)?([^\n]+)/ui', $clean_text_for_regex, $m)) {
                                        $district = trim($m[1]);
                                    }

                                    $clean_content = wp_strip_all_tags($raw_content);
                                    $summary = mb_substr(preg_replace('/\s+/', ' ', $clean_content), 0, 800);

                                    $branches_text .= "### {$item->title}\n";
                                    if ($district) $branches_text .= "- Район: {$district}\n";
                                    if ($address) {
                                        $branches_text .= "- Адрес: {$address}\n";
                                        $addresses_list[] = mb_strtolower($address);
                                    }
                                    if ($phone) $branches_text .= "- Телефон: {$phone}\n";
                                    if ($hours) $branches_text .= "- Режим работы: {$hours}\n";
                                    if ($vk_link) $branches_text .= "- Группа ВК: {$vk_link}\n";
                                    if (!$address && !$phone) $branches_text .= "- Описание: {$summary}...\n";
                                    $branches_text .= "- Ссылка на страницу: {$item->url}\n\n";
                                }
                            } else {
                                $branches_text .= "### {$item->title}\n- Ссылка на страницу: {$item->url}\n\n";
                            }
                        }
                    }
                }

                if ($about_parent_id > 0) {
                    foreach ($menu_items as $item) {
                        if ($item->menu_item_parent == $about_parent_id && mb_stripos($item->title, 'контакт') !== false) {
                            $contact_page_id = url_to_postid($item->url);
                            if ($contact_page_id) {
                                $contact_page = get_post($contact_page_id);
                                if ($contact_page) {
                                    $content = wp_strip_all_tags(strip_shortcodes($contact_page->post_content));
                                    $summary = mb_substr(preg_replace('/\s+/', ' ', $content), 0, 1500);
                                    $branches_text .= "РУКОВОДСТВО И ОБЩИЕ КОНТАКТЫ МБУК ЦГБ:\n{$summary}\n";
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $knowledge_data['branches_data'] = $branches_text;
    $knowledge_data['extracted_addresses'] = $addresses_list;
    update_option('city_library_ai_knowledge', $knowledge_data);
}
add_action('city_library_daily_cron', 'sync_library_knowledge_base');

// Schedule the cron event on theme setup if not already scheduled
if (!wp_next_scheduled('city_library_daily_cron')) {
    wp_schedule_event(time(), 'daily', 'city_library_daily_cron');
}

// 2. Render Frontend Chat Widget
function city_library_render_ai_librarian() {
    // Force strict authorized only check - ONLY visible to logged in users
    if (!is_user_logged_in()) {
        return;
    }

    // If enabled in customizer. Default to true.
    // We use a more robust check to ensure it's not blocked by missing settings.
    $is_enabled = get_theme_mod('enable_ai_librarian', true);
    if ($is_enabled === false || $is_enabled === '0' || $is_enabled === 0) {
        return;
    }

    ?>
    <div id="ai-librarian-widget" class="hidden lg:landscape:flex fixed bottom-24 lg:landscape:bottom-8 right-4 sm:right-6 lg:landscape:right-8 z-[80] flex-col items-end w-auto" style="visibility: visible !important; opacity: 1 !important; pointer-events: none;">
        <!-- Chat Window -->
        <?php $chat_theme = get_theme_mod('ai_chat_theme', 'default'); ?>
        <div id="ai-chat-window" data-theme="<?php echo esc_attr($chat_theme); ?>" class="hidden w-[96vw] sm:w-[90vw] md:w-[680px] max-w-full bg-white/95 backdrop-blur-2xl rounded-[2.5rem] shadow-[0_30px_70px_-15px_rgba(0,0,0,0.3)] border border-white/40 mb-6 overflow-hidden flex-col h-[70vh] max-h-[750px] sm:max-h-none sm:h-[650px] transition-all duration-500 transform origin-bottom-right theme-<?php echo esc_attr($chat_theme); ?> pointer-events-auto">
            <!-- Header -->
            <div class="bg-gradient-to-br from-primary via-primary to-secondary text-white p-5 flex justify-between items-center shadow-lg z-20 shrink-0 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md shadow-xl overflow-hidden border border-white/30 rotate-3 hover:rotate-0 transition-transform duration-300">
                        <img src="<?php echo esc_url(get_city_library_ai_avatar_url()); ?>" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-black text-base leading-tight tracking-wide">Виртуальный библиотекарь</h4>
                        <span class="text-[11px] text-white/80 flex items-center gap-2 uppercase tracking-widest font-bold mt-1">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            В сети
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="fullscreen-ai-chat" class="text-white/70 hover:text-white hover:bg-white/20 rounded-xl transition-all flex items-center justify-center w-10 h-10 border border-white/10 shadow-sm focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:outline-none" aria-label="Полный экран">
                        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">fullscreen</span>
                    </button>
                    <button id="close-ai-chat" class="text-white/70 hover:text-white hover:bg-white/20 rounded-xl transition-all flex items-center justify-center w-10 h-10 border border-white/10 shadow-sm focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:outline-none" aria-label="Закрыть чат">
                        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">close</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-grow overflow-hidden relative">
                <!-- Sidebar (Desktop only or Drawer style) -->
                <div id="ai-chat-sidebar" class="hidden sm:flex flex-col w-[200px] bg-slate-900 text-slate-300 border-r border-slate-800 shrink-0 overflow-y-auto custom-scrollbar p-2 py-4 gap-1 z-10 shadow-xl">
                    <div class="px-3 mb-4 flex flex-col gap-1">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Библиотекарь</span>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary/80 transition-all text-left shadow-lg shadow-primary/20 group" data-command="/help">
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">help</span> Справка / Гид
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-slate-900 bg-yellow-400 hover:bg-yellow-500 transition-all text-left shadow-lg shadow-yellow-500/10" data-command="/aimg">
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">palette</span> Генератор фото
                        </button>
                    </div>

                    <div class="px-3 flex flex-col gap-0.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2 mt-2">Инструменты</span>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/anniversaries">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">cake</span> Юбиляры
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/work_plan">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">calendar_today</span> План работы
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/social_post">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">share</span> Пост ВК
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/script">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">movie_edit</span> Сценарий
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/bib_list">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">format_list_bulleted</span> Библ. список
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/inventory">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">inventory</span> Фонд
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/gost">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">gavel</span> ГОСТ 7.0.100
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/exhibitions">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">stars</span> Выставки
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/vladimir_history">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">castle</span> Краеведение
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] hover:bg-white/5 hover:text-white transition-all text-left" data-command="/prof_resources">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">school</span> Ресурсы
                        </button>
                    </div>

                    <div class="mt-auto px-3 pt-4 border-t border-slate-800 flex flex-col gap-0.5">
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] text-slate-500 hover:text-primary transition-all text-left" data-command="/emoji">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">sentiment_satisfied</span> Смайлики
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] text-slate-500 hover:text-primary transition-all text-left" data-command="/stat">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">bar_chart</span> Статистика
                        </button>
                        <button class="ai-quick-action-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-[11px] text-red-400 hover:bg-red-500/10 transition-all text-left" data-command="/clear">
                            <span class="material-symbols-outlined text-[16px]" aria-hidden="true">delete</span> Очистить чат
                        </button>
                    </div>

                    <div class="px-3 mt-4">
                        <button id="ai-collect-draft-btn" class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-[11px] font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all text-left shadow-lg shadow-indigo-600/20 group">
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">library_add_check</span> Собрать черновик
                        </button>
                    </div>
                </div>

                <!-- Main Chat Area -->
                <div class="flex-grow flex flex-col min-w-0 bg-slate-50 relative">
                    <!-- Mobile Tools Toggle (Horizontal Scroll on Mobile) -->
                    <div class="sm:hidden px-3 py-2 bg-white border-b border-slate-100 flex gap-2 overflow-x-auto whitespace-nowrap scrollbar-hide shrink-0 text-xs shadow-sm">
                        <button class="ai-quick-action-btn flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white rounded-full transition-all shadow-sm font-bold" data-command="/help">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">help</span> Инфо
                        </button>
                        <button class="ai-quick-action-btn flex items-center gap-1.5 px-3 py-1.5 bg-yellow-400 text-slate-900 rounded-full transition-all shadow-sm font-bold" data-command="/aimg">
                            <span class="material-symbols-outlined text-[14px]" aria-hidden="true">palette</span> Фото
                        </button>
                        <button class="ai-quick-action-btn flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-full text-slate-600 transition-all" data-command="/anniversaries">Юбиляры</button>
                        <button class="ai-quick-action-btn flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-full text-slate-600 transition-all" data-command="/social_post">Пост ВК</button>
                        <button id="ai-collect-draft-btn-mobile" class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 border border-indigo-200 rounded-full text-indigo-600 transition-all font-bold">
                            <span class="material-symbols-outlined text-[14px]">library_add_check</span> Собрать
                        </button>
                        <button class="ai-quick-action-btn flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-full text-slate-600 transition-all" data-command="/clear">Очистить</button>
                    </div>

                    <!-- Selection Mode Toolbar (Moved outside to prevent deletion on clear) -->
                    <div id="ai-selection-toolbar" class="hidden bg-indigo-600 text-white p-3 shadow-lg flex justify-between items-center z-30 animate-in slide-in-from-top duration-300 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">fact_check</span>
                            <span class="text-xs font-bold"><span id="ai-selected-count">0</span> выбрано</span>
                        </div>
                        <div class="flex gap-2">
                            <button id="ai-compile-pdf" class="bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg text-[10px] font-bold flex items-center gap-1 transition-all">
                                <span class="material-symbols-outlined text-[14px]">picture_as_pdf</span> PDF
                            </button>
                            <button id="ai-compile-docx" class="bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg text-[10px] font-bold flex items-center gap-1 transition-all">
                                <span class="material-symbols-outlined text-[14px]">description</span> DOCX
                            </button>
                            <button id="ai-cancel-selection" class="text-white/70 hover:text-white p-1">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>
                    </div>

                    <div id="ai-chat-messages" class="flex-grow p-4 sm:p-6 overflow-y-auto flex flex-col gap-6 text-sm custom-scrollbar scroll-smooth relative">
                        <!-- Welcome Message -->
                        <div class="flex gap-2">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center shrink-0 mt-1 shadow-sm border border-slate-300 overflow-hidden relative">
                                <img src="<?php echo esc_url(get_city_library_ai_avatar_url()); ?>" alt="Avatar" class="w-full h-full object-cover">
                            </div>
                            <div class="bg-white border border-slate-200/80 p-4 rounded-[1.25rem] rounded-tl-sm shadow-sm hover:shadow-md transition-shadow text-slate-800 text-[14.5px] leading-relaxed">
                                Здравствуйте! Я ваш виртуальный библиотекарь. 📚 Слева расположены быстрые инструменты для работы. Вы можете прикрепить документ или фото для анализа. Чем могу помочь?
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
            <div class="p-4 bg-white border-t border-slate-100 flex gap-3 shadow-[0_-10px_25px_rgba(0,0,0,0.03)] shrink-0 z-10 relative items-end">
                <input type="file" id="ai-chat-file-input" class="hidden" accept=".txt,.docx,.pdf,.jpg,.jpeg,.png,.webp" style="display: none !important;">
                <button id="ai-chat-attachment" class="w-11 h-11 text-slate-400 hover:text-primary hover:bg-primary/5 rounded-2xl flex items-center justify-center transition-all shrink-0 border border-slate-100 shadow-sm" title="Прикрепить файл или фото (до 50МБ)" aria-label="Прикрепить файл">
                    <span class="material-symbols-outlined text-[22px]" aria-hidden="true">attach_file</span>
                </button>
                <div class="relative flex-grow">
                    <textarea id="ai-chat-input" rows="1" class="w-full bg-slate-50 hover:bg-slate-100/50 focus:bg-white border border-slate-200 focus:border-primary/50 focus:ring-4 focus:ring-primary/10 rounded-2xl text-[14px] px-5 py-3.5 transition-all duration-300 resize-none min-h-[50px] max-h-[180px] leading-relaxed custom-scrollbar shadow-inner" placeholder="Спросите что-нибудь..." aria-label="Текст запроса"></textarea>
                </div>
                <button id="ai-chat-send" class="w-12 h-12 bg-primary text-white rounded-2xl flex items-center justify-center hover:bg-secondary hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/20 transition-all duration-300 shrink-0 shadow-lg group active:scale-95 border-b-4 border-primary/20" aria-label="Отправить сообщение">
                    <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform duration-300" aria-hidden="true">keyboard_arrow_up</span>
                </button>
            </div>

            <!-- Disclaimer -->
            <div class="px-4 pb-3 bg-white text-[10px] text-slate-400 text-center leading-tight">
                ИИ может ошибаться! Всегда проверяйте полученные от ИИ данные!
            </div>
                </div> <!-- Close Main Chat Area (DIV 6) -->
            </div> <!-- Close flex flex-grow container (DIV 4) -->
        </div> <!-- Close ai-chat-window (DIV 2) -->

        <!-- Toggle Button -->
        <button id="ai-chat-toggle" class="w-16 h-16 bg-primary text-white rounded-full shadow-[0_8px_30px_rgba(11,121,48,0.4)] hover:-translate-y-1 hover:shadow-[0_12px_40px_rgba(11,121,48,0.5)] transition-all duration-300 flex items-center justify-center relative group overflow-hidden shrink-0 pointer-events-auto focus-visible:ring-4 focus-visible:ring-primary/40 focus-visible:outline-none" aria-label="Чат с Виртуальным библиотекарем">
            <span class="absolute inset-0 bg-gradient-to-tr from-white/0 to-white/20"></span>
            <span class="material-symbols-outlined text-[32px] group-hover:hidden relative z-10" aria-hidden="true">support_agent</span>
            <span class="material-symbols-outlined text-[32px] hidden group-hover:block relative z-10" aria-hidden="true">chat</span>
            <!-- Notification Dot -->
            <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full animate-pulse shadow-sm z-20"></span>
        </button>
    </div>
    <?php
}
add_action('wp_footer', 'city_library_render_ai_librarian');

// 3. Enqueue Script
function city_library_enqueue_ai_script() {
    // Force strict authorized only check
    if (!is_user_logged_in()) return;

    // If disabled in customizer. Default to true.
    $is_enabled = get_theme_mod('enable_ai_librarian', true);
    if ($is_enabled === false || $is_enabled === '0' || $is_enabled === 0) {
        return;
    }

    // Enqueue marked.js and pdf.js
    wp_enqueue_script('marked-js', 'https://cdn.jsdelivr.net/npm/marked/marked.min.js', array(), null, true);
    wp_enqueue_script('pdf-js', 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js', array(), null, true);

    wp_enqueue_script('city-library-ai-chat', get_template_directory_uri() . '/js/ai-chat.js', array('jquery', 'marked-js', 'pdf-js'), wp_get_theme()->get('Version'), true);
    wp_localize_script('city-library-ai-chat', 'cl_ai_ajax', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ai_chat_nonce'),
        'avatar_url' => get_city_library_ai_avatar_url(),
        'user_name' => is_user_logged_in() ? wp_get_current_user()->display_name : 'Гость',
        'is_logged_in' => is_user_logged_in() ? true : false
    ));
}
add_action('wp_enqueue_scripts', 'city_library_enqueue_ai_script');

// 4. AJAX Handler for OpenRouter
function city_library_handle_ai_chat() {
    check_ajax_referer('ai_chat_nonce', 'nonce');

    $api_key = get_theme_mod('openrouter_api_key', '');
    $model = get_theme_mod('ai_librarian_model', 'google/gemini-2.0-flash-001');
    $fallback_model = get_theme_mod('ai_librarian_model_fallback', 'openai/gpt-4o-mini');
    $user_message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
    $user_name = isset($_POST['user_name']) ? sanitize_text_field($_POST['user_name']) : 'Пользователь';
    $is_logged_in = isset($_POST['is_logged_in']) && $_POST['is_logged_in'] === 'true';
    $image_data = isset($_POST['image_data']) ? $_POST['image_data'] : '';
    $pdf_images = isset($_POST['pdf_images']) ? json_decode(stripslashes($_POST['pdf_images']), true) : array();

    if (empty($user_message) && empty($image_data) && empty($pdf_images)) {
        wp_send_json_error(array('reply' => 'Пожалуйста, введите сообщение.'));
    }

    if (empty($api_key)) {
        wp_send_json_error(array('reply' => 'Извините, библиотекарь временно недоступен (API ключ не настроен).'));
    }

    $clean_msg = trim(mb_strtolower($user_message));

    // Command: /emoji
    if ($clean_msg === '/emoji') {
        $emoji_list = "📚 📖 📗 📘 📙 📓 📔 📒 📕 🕮 📜 📄 📃 📑 🔖 🏷️ ✍️ 🖋️ 🖊️ 🖌️ 🖍️ 📝 ✏️ 📏 📐 🧮 🎓 🏫 🏛️ 🏢 🧑‍🎓 👩‍🎓 👨‍🎓 👨‍🏫 👩‍🏫 🧑‍🏫 💡 🧠 👁️ 🤓 🥸 🧐 🤯 🗂️ 📁 📂 🗄️ 📇 📋 📆 📅 ⌚ ⏳ ⌛ 🕰️ 🏆 🏅 🎖️ 🥇 🥈 🥉 🎭 🎨 🖼️ 🧵 🧶 🎼 🎵 🎶 🎤 🎧 📻 📺 📼 📸 📷 📹 📽️ 🎞️ 🎬 🧩 🎲 ♟️ 🎮 🧸 🪀 🪁 🎈 🪄 🔮 💻 🖥️ 🖨️ 🖱️ 🖲️ 💾 💽 💿 📀 📱 ☎️ 📞 📟 📠 ✉️ 📧 📨 📩 📤 📥 📦 📪 📭 📬 📮 📰 🗞️ 📢 📣 📯 🔔 🔕 🔍 🔎 🔬 🔭 📡 💡 🔦 🏮 🕯️";
        wp_send_json_success(array(
            'reply' => "### 📚 Библиотечные и канцелярские эмодзи\n\nСкопируйте нужные для ваших постов и афиш:\n\n<div class=\"text-2xl mt-4 leading-loose tracking-widest break-words bg-slate-50 p-4 rounded-xl border border-slate-200\">" . $emoji_list . "</div>"
        ));
    }

    // Command: /help (Enhanced User Guide)
    if (strpos($clean_msg, '/help') === 0 || strpos($clean_msg, 'справка') === 0) {
        $guide = "📖 **Гид пользователя: Как работать с Виртуальным библиотекарем**\n\n";

        $guide .= "### 🚀 Как пользоваться кнопками?\n";
        $guide .= "Кнопки в боковой панели — это ваши «горячие клавиши». \n";
        $guide .= "1. **Просто нажать:** Нажмите на «Юбиляры», чтобы сразу получить список писателей.\n";
        $guide .= "2. **Написать + Нажать:** Введите тему (например, *«Пушкин»*) в поле ввода и нажмите кнопку «Пост ВК». Я напишу пост именно про Пушкина.\n\n";

        $guide .= "### 🖼️ Генерация изображений (`/aimg`)\n";
        $guide .= "Вы можете создавать плакаты и афиши для мероприятий.\n";
        $guide .= "**Пример:** Нажмите кнопку «Фото» или введите: `/aimg Кот в библиотеке, акварель`. \n";
        $guide .= "_Совет: Описывайте стиль (фото, рисунок, 3D) для лучшего результата._\n\n";

        $guide .= "### 🛠️ Команды и возможности:\n";
        $guide .= "- **Библ. список:** Помогу оформить литературу. Введите тему и жмите кнопку.\n";
        $guide .= "- **Сценарий:** Составлю план праздника или вечера.\n";
        $guide .= "- **ГОСТ:** Составлю описание книги по ГОСТ Р 7.0.100–2018. Вы можете прикрепить файл с текстом книги, и я сама извлеку данные для описания!\n";
        $guide .= "- **Статистика:** Узнаете, что нового появилось на нашем сайте.\n";
        $guide .= "- **Файлы:** Скрепка слева позволяет прикрепить ваш текст (.txt, .docx), чтобы я его проанализировала.\n\n";

        $guide .= "### 📂 Экспорт ответа\n";
        $guide .= "Под моим ответом вы увидите кнопки: **PDF, DOCX, Почта**. Вы можете сохранить результат работы в файл или отправить себе на Email.\n\n";

        $guide .= "--- \n_Я всегда готова помочь! Просто напишите свой вопрос в свободной форме._";

        wp_send_json_success(array('reply' => $guide));
        return;
    }

    // Command: /anniversaries
    if (strpos($clean_msg, '/anniversaries') === 0) {
        $current_month = date_i18n('F');
        $next_month = date_i18n('F', strtotime('+1 month'));

        $user_message = "Ты — библиотекарь. Перечисли список известных русских и зарубежных писателей-юбиляров на {$current_month} и {$next_month} 2025 года.
        ОБЯЗАТЕЛЬНОЕ УСЛОВИЕ: Оформи каждое имя писателя в ответе как интерактивную кнопку HTML: <button class=\"ai-draft-btn bg-primary/10 text-primary px-2 py-1 rounded-md hover:bg-primary/20 transition-all font-bold my-1 block w-full text-left\" data-author=\"ИМЯ ПИСАТЕЛЯ\">ИМЯ ПИСАТЕЛЯ</button>.
        После списка добавь текст: 'Нажмите на имя писателя, чтобы я подготовила черновик статьи или сценария мероприятия о нём.'";
    }

    // Ensure history is initialized before command checks to allow contextual awareness for tools
    $history = isset($_POST['history']) ? json_decode(stripslashes($_POST['history']), true) : array();

    // Store original full history for context-heavy tasks like prompt refinement
    $full_history = $history;

    // Librarian Tools Shortcuts with Subject Support and CONTEXTUAL AWARENESS
    if (strpos($clean_msg, '/work_plan') === 0) {
        $subject = trim(mb_substr($user_message, 10));
        $user_message = "Помоги мне составить план работы библиотеки на следующий месяц" . ($subject ? " на тему: '{$subject}'" : "") . ". Предложи интересные темы выставок, мероприятий и онлайн-активностей. Если тема не указана выше, используй контекст последних сообщений в чате.";
    }
    if (strpos($clean_msg, '/social_post') === 0 || strpos($clean_msg, 'создай пост вк') === 0 || strpos($clean_msg, 'напиши пост для вк') === 0) {
        $subject = trim(mb_substr($user_message, 12));
        $user_message = "Напиши интересный и вовлекающий пост для группы библиотеки ВКонтакте" . ($subject ? " про: '{$subject}'" : "") . ". Используй эмодзи и хештеги. Если тема не указана выше, обязательно используй контекст последних сообщений в чате (например, создай пост о мероприятии или афише, которую мы обсуждали только что).";
    }
    if (strpos($clean_msg, '/script') === 0) {
        $subject = trim(mb_substr($user_message, 7));
        $user_message = "Составь подробный сценарий литературного вечера" . ($subject ? ", посвященного теме: '{$subject}'" : "") . ". Включи тайминг, список оборудования и идеи для интерактива. Если тема не указана выше, используй контекст последних сообщений в чате.";
    }
    if (strpos($clean_msg, '/bib_list') === 0) {
        $subject = trim(mb_substr($user_message, 9));
        $user_message = "Помоги составить библиографический список литературы" . ($subject ? " по теме: '{$subject}'" : "") . ". Укажи 5-7 основных источников с правильным оформлением. Если тема не указана выше, используй контекст последних сообщений в чате.";
    }
    if (strpos($clean_msg, '/inventory') === 0) {
        $user_message = "Дай методические рекомендации по проведению плановой проверки библиотечного фонда. На что обратить внимание и какие документы подготовить?";
    }
    if (strpos($clean_msg, '/gost') === 0) {
        $subject = trim(mb_substr($user_message, 5));
        // Force the AI to look at the attached file content
        $user_message = "ПРОАНАЛИЗИРУЙ ПРИКРЕПЛЕННЫЙ ФАЙЛ (если он есть) И СОСТАВЬ библиографическое описание" . ($subject ? " для: '{$subject}'" : " книги") . " согласно ГОСТ Р 7.0.100–2018. Ты ДОЛЖЕН использовать данные из текста прикрепленного файла (автор, название, город, издательство, год) как приоритетный источник информации.";
    }
    if (strpos($clean_msg, '/exhibitions') === 0) {
        $subject = trim(mb_substr($user_message, 12));
        $user_message = "Предложи 5-10 креативных названий и идей для книжных выставок" . ($subject ? " на тему: '{$subject}'" : "") . ". Укажи целевую аудиторию и формы работы с выставкой. ОБЯЗАТЕЛЬНО: Для каждой выставки приведи список из 3-5 рекомендуемых книг в формате: 'Автор - Название'.";
    }
    if (strpos($clean_msg, '/vladimir_history') === 0) {
        $subject = trim(mb_substr($user_message, 17));
        $user_message = "Предоставь интересную краеведческую справку" . ($subject ? " о: '{$subject}'" : " об истории города Владимира") . ". Используй только проверенные исторические факты.";
    }
    if (strpos($clean_msg, '/prof_resources') === 0) {
        $user_message = "Подготовь список полезных профессиональных интернет-ресурсов для сотрудников библиотек (методические порталы, профессиональные СМИ, базы данных).";
    }
    if (strpos($clean_msg, '/method_recs') === 0) {
        $subject = trim(mb_substr($user_message, 12));
        $user_message = "Подготовь краткие методические рекомендации" . ($subject ? " по теме: '{$subject}'" : " по организации библиотечного обслуживания") . ". Структурируй ответ по пунктам.";
    }

    // Handle interactive Author Draft command
    if (strpos($clean_msg, '/author') === 0) {
        $author = trim(mb_substr($user_message, 7));
        $user_message = "Подготовь набросок статьи и краткий сценарий мероприятия, посвященного жизни и творчеству писателя: {$author}. Структурируй ответ.";
    }

    // Direct Stat Command
    if (strpos($clean_msg, '/stat') === 0) {
        $stats = "📊 **Анализ обновлений сайта (Статистика)**\n\n";

        $knowledge = get_option('city_library_ai_knowledge');
        if ($knowledge && isset($knowledge['last_updated'])) {
             $stats .= "*Последний раз данные синхронизировались: " . date_i18n('d F Y H:i', strtotime($knowledge['last_updated'])) . "*\n\n";
        } else {
             $stats .= "*База знаний еще не была синхронизирована. Это произойдет автоматически.*\n\n";
        }

        // Get latest 5 posts efficiently
        global $wpdb;
        $latest_posts = $wpdb->get_results("SELECT ID, post_title, post_date FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 5");

        $stats .= "**Новые записи (Новости / Статьи):**\n";
        if (empty($latest_posts)) {
            $stats .= "- *Нет новых записей*\n";
        } else {
            foreach ($latest_posts as $p) {
                $stats .= "- [" . esc_html($p->post_title) . "](" . get_permalink($p->ID) . ") (" . date('d.m.Y', strtotime($p->post_date)) . ")\n";
            }
        }
        $stats .= "\n";

        // Get latest 3 updated pages
        $latest_pages = $wpdb->get_results("SELECT ID, post_title, post_modified FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' ORDER BY post_modified DESC LIMIT 3");
        $stats .= "**Недавно обновленные страницы:**\n";
        if (empty($latest_pages)) {
            $stats .= "- *Нет данных*\n";
        } else {
            foreach ($latest_pages as $p) {
                $stats .= "- [" . esc_html($p->post_title) . "](" . get_permalink($p->ID) . ") (обн. " . date('d.m.Y', strtotime($p->post_modified)) . ")\n";
            }
        }
        $stats .= "\n_Для более подробной информации Вы можете задать мне конкретный вопрос!_";

        wp_send_json_success(array('reply' => $stats));
        return;
    }

    // Direct OPAC Search Simulation
    $original_clean = mb_strtolower(trim(isset($_POST['message']) ? sanitize_text_field($_POST['message']) : ''));
    $is_opac_command = false;
    $opac_query = '';

    if (strpos($original_clean, '/opac') === 0 || strpos($original_clean, 'найди книгу') === 0 || strpos($original_clean, 'поиск в каталоге') === 0) {
        $is_opac_command = true;
        // Extract query
        if (strpos($clean_msg, '/opac') === 0) {
            $opac_query = trim(mb_substr(trim($user_message), 5));
        } else {
            // Remove trigger words
            $opac_query = trim(preg_replace('/^(найди книгу|поиск в каталоге)/ui', '', trim($user_message)));
        }
    }

    if ($is_opac_command) {
        if (empty($opac_query)) {
            wp_send_json_error(array('reply' => 'Пожалуйста, укажите автора или название книги для поиска в каталоге. Пример: "Найди книгу Пушкин Евгений Онегин" или "/opac Достоевский".'));
        }

        // OPAC systems using opacg/opac.exe heavily rely on JavaScript and Session states (frames, cookies, dynamic forms).
        // Since a pure server-side cURL cannot easily execute JS, wait 3-4 seconds, and traverse the frame logic without a headless browser,
        // we provide a constructed smart link and instructions that direct the user to the precise OPAC gateway with their query pre-filled/guided.

        $gateway_url = "http://library.vladimir.ru/rguest_vlad_cgb.htm";
        $encoded_query = urlencode($opac_query);

        $reply = "📚 **Поиск в Электронном каталоге (OPAC)**\n\n";
        $reply .= "Вы искали: *{$opac_query}*\n\n";
        $reply .= "Поскольку наш электронный каталог работает через защищенный шлюз с динамическими сессиями, я подготовила для Вас прямую ссылку для входа в систему:\n\n";
        $reply .= "[**Открыть каталог МБУК «ЦГБ»**]({$gateway_url})\n\n";
        $reply .= "1. Перейдите по ссылке (она откроется в новой вкладке).\n";
        $reply .= "2. Подождите 3-4 секунды (система автоматически создаст сессию и загрузит форму поиска).\n";
        $reply .= "3. Введите ваш запрос (*{$opac_query}*) в поле «Ключевые слова» или «Автор/Заглавие» и нажмите «Искать».\n\n";
        $reply .= "_Если Вам нужна помощь с составлением библиографического списка, просто попросите меня об этом!_";

        wp_send_json_success(array('reply' => $reply));
        return;
    }

    // Direct Image Generation Logic
    $is_draw_command = false;
    $draw_prompt = '';

    if (strpos($original_clean, '/aimg') === 0) {
        $is_draw_command = true;
        $draw_prompt = trim(mb_substr(trim($original_clean), 5));
    } else if (preg_match('/^(нарисуй|сгенерируй|создай картинку|нарисуй мне|сделай картинку)\s+(.+)/u', $original_clean, $matches)) {
        $is_draw_command = true;
        $draw_prompt = trim($matches[2]);
    }

    if ($is_draw_command) {
        if (empty($draw_prompt)) {
            wp_send_json_error(array('reply' => 'Пожалуйста, опишите, что нужно нарисовать. Пример: Нарисуй уютную библиотеку с камином.'));
        }

        // Get user preferred image model from customizer
        $selected_img_model = get_theme_mod('ai_librarian_image_model', 'black-forest-labs/flux-schnell');
        if ($selected_img_model === 'custom') {
            $selected_img_model = get_theme_mod('ai_librarian_image_model_custom', '');
        }

        $image_models = [];
        if (!empty($selected_img_model)) {
            $image_models[] = $selected_img_model;
        }
        // Always add a reliable fallback chain
        $image_models = array_unique(array_merge($image_models, [
            'black-forest-labs/flux-schnell',
            'black-forest-labs/flux-dev',
            'openai/dall-e-3',
            'stabilityai/stable-diffusion-xl-base-1.0'
        ]));

        $image_url = '';
        $used_model = '';

        // Improved Prompt Refinement via LLM
        $refine_system = 'You are a professional prompt engineer for AI image generators (like Flux). Your task is to transform Russian requests into high-quality English prompts.
        CRITICAL: If the user is asking to CHANGE or UPDATE an image we discussed previously, analyze the conversation history and incorporate those changes into a NEW complete prompt.
        MANDATORY:
        1. Explicitly instruct the model to write any text in RUSSIAN language (Cyrillic), using quotes.
        2. EXTREMELY IMPORTANT: Any text on the image MUST be in perfect Russian, without any errors, typos or mixed languages.
        3. If the user request is too vague (less than 3 words or just a generic object like "cat") and no context is available, start your response with "CLARIFY:" and ask the user for details in Russian.
        4. If a reference image description is provided, ensure the new prompt maintains its style/objects.
        5. Return ONLY the refined English prompt (or CLARIFY question), no extra meta-text.';

        $refine_messages = array(
            array('role' => 'system', 'content' => $refine_system),
        );

        // Handle Image-as-Reference using Gemini 2.0 Flash
        if (!empty($image_data)) {
            $vision_body = array(
                'model' => 'google/gemini-2.0-flash-001',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => array(
                            array("type" => "text", "text" => "Describe this image in detail for an AI image generator prompt. Focus on style, colors, objects, and composition. Return only the description in English."),
                            array("type" => "image_url", "image_url" => array("url" => $image_data))
                        )
                    )
                )
            );
            $vision_args = array(
                'headers' => array('Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json'),
                'body' => wp_json_encode($vision_body),
                'timeout' => 30
            );
            $vision_response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $vision_args);
            if (!is_wp_error($vision_response) && wp_remote_retrieve_response_code($vision_response) === 200) {
                $vision_data = json_decode(wp_remote_retrieve_body($vision_response), true);
                $reference_desc = $vision_data['choices'][0]['message']['content'] ?? '';
                if ($reference_desc) {
                    $refine_messages[] = array('role' => 'system', 'content' => "REFERENCE IMAGE CONTEXT (Maintain this style): " . $reference_desc);
                }
            }
        }

        // For refinement, we use the FULL history so the LLM perfectly understands what to change in the previous image
        if (!empty($full_history) && is_array($full_history)) {
            $history_tail = array_slice($full_history, -20); // Last 10 interactions for better precision
            foreach ($history_tail as $h_msg) {
                // Only include text content in prompt refinement context
                if (isset($h_msg['role']) && isset($h_msg['content']) && is_string($h_msg['content'])) {
                    $refine_messages[] = array('role' => $h_msg['role'], 'content' => $h_msg['content']);
                }
            }
        }

        $refine_messages[] = array('role' => 'user', 'content' => "Refine this request into an image prompt, considering previous context if this is a modification request: " . $draw_prompt);

        $refine_body = array(
            'model' => $model, // Use the primary LLM to refine the prompt
            'messages' => $refine_messages
        );

        $refine_args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'HTTP-Referer'  => home_url(),
                'Content-Type'  => 'application/json',
            ),
            'body' => wp_json_encode($refine_body),
            'timeout' => 15
        );

        $refine_response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $refine_args);
        $final_prompt = $draw_prompt; // Fallback

        if (!is_wp_error($refine_response) && wp_remote_retrieve_response_code($refine_response) === 200) {
            $refine_data = json_decode(wp_remote_retrieve_body($refine_response), true);
            if (!empty($refine_data['choices'][0]['message']['content'])) {
                $final_prompt = trim($refine_data['choices'][0]['message']['content']);

                // CLARIFY: Logic if user request is too vague
                if (strpos($final_prompt, 'CLARIFY:') === 0) {
                    $clarify_text = trim(substr($final_prompt, 8));
                    wp_send_json_success(array('reply' => "🤔 {$clarify_text}"));
                    return;
                }

                // Ensure common library quality keywords are present
                $final_prompt .= ", professional library poster style, highly detailed, 4k, educational context";
            }
        }

        foreach ($image_models as $img_model) {
            $request_body = array(
                'model' => $img_model,
                'messages' => array(
                    array('role' => 'user', 'content' => $final_prompt)
                ),
                'modalities' => array('image', 'text'),
                'response_format' => array('type' => 'url') // Explicitly request URL if supported by the provider
            );

            $api_args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'HTTP-Referer'  => home_url(),
                    'X-Title'       => 'City Library Theme',
                    'Content-Type'  => 'application/json',
                ),
                'body' => wp_json_encode($request_body),
                'timeout' => 60 // Images take longer to generate
            );

            $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $api_args);

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                // OpenRouter returns images differently based on the provider.
                // 1. Newer native multimodal response (images array)
                if (isset($data['choices'][0]['message']['images'][0]['image_url']['url'])) {
                    $image_url = $data['choices'][0]['message']['images'][0]['image_url']['url'];
                }
                // 2. Fallback to parsing 'content' (Legacy/Markdown format)
                elseif (isset($data['choices'][0]['message']['content'])) {
                    $content = $data['choices'][0]['message']['content'];

                    if (preg_match('/!\[.*?\]\((.*?)\)/', $content, $matches)) {
                        $image_url = $matches[1];
                    } elseif (filter_var($content, FILTER_VALIDATE_URL)) {
                        $image_url = $content;
                    } elseif (preg_match('/https?:\/\/[^\s"\'<>]+/', $content, $matches)) {
                        $image_url = $matches[0];
                    } elseif (strpos($content, 'data:image') === 0 || strpos($content, 'iVBORw0KGgo') === 0) {
                        // It returned a base64 image directly
                        $image_url = (strpos($content, 'data:image') === 0) ? $content : 'data:image/png;base64,' . $content;
                    }
                }

                if (!empty($image_url)) {
                    $used_model = $img_model;
                    break; // Success! Exit the loop.
                }
            }
        }

        if (!empty($image_url)) {
            $reply = "🎨 Вот ваше изображение по запросу: *{$draw_prompt}*\n\n![Сгенерированное изображение]({$image_url})\n\n_Вы можете уточнить детали (например, «сделай это в стиле аниме» или «добавь больше книг»), и я обновлю картинку._";
            wp_send_json_success(array('reply' => $reply));
        } else {
            $reply = "⚠️ К сожалению, в данный момент все нейросети для генерации изображений перегружены или недоступны. Пожалуйста, попробуйте повторить запрос через несколько минут.";
            wp_send_json_error(array('reply' => $reply));
        }
        return;
    }

    // Security Check: Enforce test mode strictly on the server-side
    // Allow request if EITHER the chat widget test mode is disabled OR the voice assistant test mode is disabled (and thus accessible to guests).
    // Also allow if the special voice test cookie is set and its 24h timestamp hasn't expired.
    $chat_test_mode = get_theme_mod('ai_librarian_test_mode', true);
    $voice_test_mode = get_theme_mod('voice_control_test_mode', true);
    $has_valid_test_cookie = false;

    if (isset($_COOKIE['cl_voice_test_active'])) {
        $cookie_timestamp = (int)$_COOKIE['cl_voice_test_active'];
        $current_timestamp_ms = time() * 1000;
        if ($cookie_timestamp > $current_timestamp_ms) {
            $has_valid_test_cookie = true;
        }
    }

    if ($chat_test_mode && $voice_test_mode && !is_user_logged_in() && !$has_valid_test_cookie) {
        wp_send_json_error(array('reply' => 'Виртуальная помощница в данный момент доступна только для авторизованных пользователей.'));
    }

    // Build Context (Simulated RAG)
    $base_persona = get_theme_mod('ai_persona_prompt', 'Ты Виртуальный библиотекарь - библиограф (женщина) с 30 летним стажем. Обращайся к пользователю на "Вы", как профессиональный библиотекарь. Не выходи за рамки библиотечной этики и работы, всю информацию по литературе и книгам предоставляй только правдивую. Твое имя - Виртуальный библиотекарь. Пиши только на безупречном русском языке. СТРОГО ЗАПРЕЩЕНО использовать иероглифы, китайские или японские символы в любых ответах. При генерации изображений весь текст должен быть на идеальном русском языке, без ошибок, опечаток, со всеми правилами русского языка!');
    $extra_prompt = get_theme_mod('ai_persona_prompt_extra', '');
    if (!empty($extra_prompt)) {
        $base_persona .= "\nДОПОЛНИТЕЛЬНЫЕ ИНСТРУКЦИИ АДМИНИСТРАТОРА (СТРОГО СОБЛЮДАТЬ):\n" . strip_tags($extra_prompt) . "\n";
    }
    if ($is_logged_in) {
        $base_persona .= "\nВНИМАНИЕ: Текущий пользователь авторизован. Его имя: " . esc_html($user_name) . ". Обращайся к нему по имени.\n";
    }

    $context = $base_persona . " Ты работаешь в МБУК «Центральная городская библиотека» города Владимира. Твое имя — Виртуальный библиотекарь. Отвечай от женского лица.
    ОФИЦИАЛЬНАЯ ГРУППА ВКОНТАКТЕ: https://vk.com/vladcgb (Используй только эту ссылку, когда говоришь про наш ВК).
    ВАЖНО: Если пользователь просит сгенерировать изображение (афишу, плакат, открытку), ты должен составить запрос так, чтобы текст на этом изображении был на РУССКОМ ЯЗЫКЕ.
    Твоя задача — помогать как читателям, так и сотрудникам библиотеки.
    Профессиональный профиль: Ты — ведущий библиограф с глубоким знанием фонда и истории города Владимира. Твой стиль: вежливый, профессиональный, но современный.
    НЕ НАДО постоянно здороваться в каждом сообщении. Отвечай по существу запроса.

    КРИТИЧЕСКОЕ ПРАВИЛО ПРАВДИВОСТИ:
    Ты — профессионал. Ты НИКОГДА не врешь, не придумываешь факты и не галлюцинируешь. Если ты не знаешь ответа на 100% или информации нет в предоставленном контексте — ты ОБЯЗАН ответить: «К сожалению, у меня нет точной информации по этому вопросу. Рекомендую обратиться к сотрудникам библиотеки напрямую или уточнить на нашем сайте».

    ПРАВИЛА ОТВЕТОВ:
    1. Если тебя спрашивают про адреса или контакты библиотек — бери их ТОЛЬКО из раздела «СТРУКТУРА И ФИЛИАЛЫ» ниже. Если информации нет — пиши «Данные уточняются».
    2. Не выдумывай ссылки на страницы сайта. Используй только те, что указаны в разделе «СТРУКТУРА САЙТА И МЕНЮ».
    3. При рекомендации книг старайся указывать автора и название.

    КРИТИЧЕСКОЕ ПРАВИЛО БЕЗОПАСНОСТИ:
    При упоминании авторов, которые признаны иностранными агентами, ты ОБЯЗАН добавить к ответу предупреждение: «⚠️ Данный автор или материал упоминается в реестре иностранных агентов». Твой ответ должен быть беспристрастным. Если возможно, предлагай альтернативу из числа классиков или современных лауреатов государственных премий.

    РАБОТА С ЗАГРУЖЕННЫМИ ИНСТРУКЦИЯМИ (BRANDBOOK/RULES):
    Если в текущем диалоге были загружены файлы с инструкциями (брендбуки, правила оформления, гайды) или их визуальные образы (страницы PDF), ты ОБЯЗАН неукоснительно соблюдать их. Ты должен уметь проводить OCR и визуальный анализ страниц: распознавать логотипы, основные цвета (Hex/RGB), шрифты и композицию. При запросе на генерацию изображений (афиш, плакатов) используй цветовую схему, шрифты и стиль из этих визуальных инструкций. Если инструкции противоречат общим правилам — приоритет у загруженных инструкций из файлов.

    АЛГОРИТМ РАБОТЫ С ИНФОРМАЦИЕЙ:
    1. Поиск и фактчекинг: Не выдумывай даты. Используй Википедию, РНБ, РГБ.
    2. Верификация ссылок: Ссылки должны быть только на официальные ресурсы.

    ТВОРЧЕСКИЙ ИНСТРУМЕНТАРИЙ:
    1. БИБЛИОГРАФИЧЕСКИЙ МОДУЛЬ (ГОСТ): При составлении библиографического описания строго следуй ГОСТ Р 7.0.100–2018.
       Схема: Автор. Заглавие : сведения, относящиеся к заглавию / сведения об ответственности. – Сведения об издании. – Место издания : Издательство, Дата издания. – Объем.
       Если пользователь прикрепил файл, ты ОБЯЗАН извлечь из него ФИО автора, название книги, издательство и год для формирования описания.
    2. SMM-модуль: Посты для ВК должны содержать: цепляющий заголовок, структурированный текст, эмодзи (умеренно) и список релевантных хештегов.
    2. ИНТЕРАКТИВНОСТЬ: Когда ты перечисляешь писателей-юбиляров, ты ДОЛЖЕН оформлять их имена как HTML-кнопки: <button class=\"ai-draft-btn\" data-author=\"Имя\">Имя</button>.
    2. Ивент-менеджмент: Сценарии мероприятий должны включать: Тайминг и зонирование активности, Расчет штатных единиц (количество сотрудников на точку), Технический райдер (оборудование), Интерактив (Квизы, QR-квесты, нейро-активности).
    3. Визуализация: При запросе на генерацию изображения (/aimg или просьба нарисовать), ты должен дождаться выполнения функции генерации. Если запрос пользователя слишком короткий или непонятный (например, просто слово 'Библиотека'), ты ДОЛЖЕН ответить строкой, начинающейся с 'CLARIFY:', и вежливо попросить уточнить детали (стиль, объекты, освещение).

    ФОРМАТ ОТВЕТА:
    Никакой «воды». Только таблицы, списки и четкие блоки данных. Если информации нет в сети — честно сообщай об этом, а не имитируй знание.\n\n";

    // Detect if we should use search model
    if (preg_match('/(?:поиск|найди|информация о|напиши|сценарий|план|пост|википедия|ргб|рнб|кто такой|расскажи о|факт|дата)/ui', $clean_msg)) {
        // If the user wants research or content generation, we upgrade the model to a stable research-capable endpoint
        $model = 'openai/gpt-4o-mini';
    }

    // Dynamic KB for MBUK CGB Vladimir (Extracts from WP Cron Cached DB Option)
    $context .= "СТРУКТУРА И ФИЛИАЛЫ МБУК ЦГБ г. ВЛАДИМИРА (Бери адреса строго отсюда!):\n";
    $context .= "Используй ТОЛЬКО данные из предоставленного списка. Если информации нет в базе — отвечай 'Данные уточняются', не выдумывай адреса. Когда пользователь спрашивает об адресах, контактах, телефонах, режимах работы библиотек или о том, какие библиотеки есть на конкретной улице или в районе, ТЫ ОБЯЗАН брать данные ТОЛЬКО из этого списка ниже.\n\n";

    $knowledge_base = get_option('city_library_ai_knowledge');

    // Auto-sync if option is completely empty (e.g. first run before cron triggers)
    if (empty($knowledge_base) || !isset($knowledge_base['branches_data']) || empty($knowledge_base['branches_data'])) {
        sync_library_knowledge_base();
        $knowledge_base = get_option('city_library_ai_knowledge');
    }

    if ($knowledge_base && isset($knowledge_base['branches_data'])) {
        $context .= $knowledge_base['branches_data'];
    } else {
        $context .= "ВНИМАНИЕ: База данных библиотек в данный момент недоступна. Пожалуйста, обратитесь к разделу 'Контакты' на сайте.\n";
    }

    $context .= "\n";

    // Add File Knowledge Base
    $kb_ids = get_theme_mod('ai_librarian_kb_ids', '');
    if (!empty($kb_ids)) {
        $file_text = city_library_extract_text_from_files($kb_ids);
        if (!empty($file_text)) {
            $context .= "ВСТРОЕННАЯ БАЗА ЗНАНИЙ (Используй эти факты в первую очередь):\n" . $file_text . "\n\n";
        }
    } else {
        $context .= "ВНИМАНИЕ: Пользовательская база знаний не предоставлена. Отвечай на вопросы, опираясь исключительно на свою собственную встроенную нейросетевую эрудицию (LLM).\n\n";
    }

    // Add library branches info (if configured in customizer)
    $branches_text = get_theme_mod('branches_map_description', '');
    if (!empty($branches_text)) {
        $context .= "ДРУГАЯ ИНФОРМАЦИЯ О БИБЛИОТЕКЕ:\n" . strip_tags($branches_text) . "\n\n";
    }

    // Dynamically Add Real WordPress Menu Structure
    $context .= "СТРУКТУРА САЙТА И МЕНЮ (Используй эти реальные ссылки, если пользователь спрашивает, где найти информацию):\n";
    $context .= "- Главная страница: " . home_url('/') . "\n";
    $context .= "- Новости: " . home_url('/?news_archive=true') . "\n";
    $context .= "- Афиша / Мероприятия: " . home_url('/#afisha') . "\n";
    $context .= "- Карта филиалов: " . home_url('/#branches') . "\n";
    $context .= "- Важная информация: " . home_url('/#important') . "\n";

    // Fetch primary menu items to teach AI the actual site structure (hierarchy)
    $menu_locations = get_nav_menu_locations();
    if (isset($menu_locations['primary'])) {
        $menu = wp_get_nav_menu_object($menu_locations['primary']);
        if ($menu) {
            $menu_items = wp_get_nav_menu_items($menu->term_id);
            if ($menu_items) {
                $menu_tree = array();
                foreach ($menu_items as $item) {
                    if (empty($item->menu_item_parent)) {
                        $menu_tree[$item->ID] = array('title' => $item->title, 'url' => $item->url, 'children' => array());
                    } else {
                        if (isset($menu_tree[$item->menu_item_parent])) {
                            $menu_tree[$item->menu_item_parent]['children'][] = array('title' => $item->title, 'url' => $item->url);
                        }
                    }
                }
                foreach ($menu_tree as $parent) {
                    $context .= "- Меню '" . esc_html($parent['title']) . "': " . esc_url($parent['url']) . "\n";
                    foreach ($parent['children'] as $child) {
                        $context .= "  -- Подменю '" . esc_html($child['title']) . "': " . esc_url($child['url']) . "\n";
                    }
                }
            }
        }
    }
    $context .= "\n";

    // Add ALL pages content to context (with safe limits)
    $context .= "КАТАЛОГ ОСНОВНЫХ СТРАНИЦ САЙТА:\n";
    $all_pages = get_pages(array('number' => 20, 'post_status' => 'publish', 'sort_column' => 'post_title'));
    foreach ($all_pages as $page) {
        $context .= "- [" . esc_html($page->post_title) . "](" . get_permalink($page->ID) . ")\n";
    }
    $context .= "\n";

    // Add recent news (extended list)
    $context .= "ПОСЛЕДНИЕ НОВОСТИ:\n";
    $recent_posts = wp_get_recent_posts(array('numberposts' => 10, 'post_status' => 'publish'));
    foreach ($recent_posts as $post) {
        $context .= "- [" . esc_html($post['post_title']) . "](" . get_permalink($post['ID']) . ")\n";
    }
    $context .= "\n";

    // Explicit Instruction on Site Knowledge
    $context .= "ОБЯЗАТЕЛЬНОЕ ПРАВИЛО: Ты обладаешь идеальным знанием структуры этого сайта. Если пользователь спрашивает, где найти какую-то информацию, услугу или раздел, ты должен выдать ТОЧНУЮ ссылку из предоставленного выше списка меню или каталога страниц. Никогда не придумывай URL-адреса, которых нет в списке.\n";

    // Safety character limit for context to avoid token issues (approx 60k chars ~ 15k tokens)
    $context = mb_substr($context, 0, 60000);

    $system_prompt = array(
        "role" => "system",
        "content" => $context
    );

    $messages = array($system_prompt);

    // Helper: Compress and truncate history messages
    $compress_history = function($text) {
        $text = preg_replace('/\s+/', ' ', $text); // Strip excessive whitespace
        return mb_substr(trim($text), 0, 1000); // Truncate to 1000 chars
    };

    // Support only the last 3 messages as explicitly requested by the user
    if (!empty($history) && is_array($history)) {
        $history = array_slice($history, -3);
        foreach ($history as $msg) {
            if (isset($msg['role']) && isset($msg['content'])) {
                 $compressed_content = $compress_history($msg['content']);
                 $messages[] = array('role' => $msg['role'], 'content' => $compressed_content);
            }
        }
    }

    // Add current user message
    $messages[] = array('role' => 'user', 'content' => $user_message);

    // Check if request is from voice assistant
    $is_voice = isset($_POST['is_voice']) && $_POST['is_voice'] === 'true';

    // Vision support: If image data or PDF images are provided, use Gemini 2.0 Flash
    if (!empty($image_data) || !empty($pdf_images)) {
        $model = 'google/gemini-2.0-flash-001';
        $content_array = array(
            array("type" => "text", "text" => $user_message)
        );

        if (!empty($image_data)) {
            $content_array[] = array("type" => "image_url", "image_url" => array("url" => $image_data));
        }

        if (!empty($pdf_images) && is_array($pdf_images)) {
            foreach ($pdf_images as $p_img) {
                $content_array[] = array("type" => "image_url", "image_url" => array("url" => $p_img));
            }
        }

        // Replace the last message content with the array (multimodal)
        $messages[count($messages) - 1]['content'] = $content_array;
    }

    // Call OpenRouter API with Fallback Logic
    $request_body = array(
        'model' => $model,
        'messages' => $messages
    );

    // Inject persistent context from uploaded files (brandbooks/instructions)
    $persistent_context = isset($_POST['persistent_context']) ? sanitize_textarea_field($_POST['persistent_context']) : '';
    if (!empty($persistent_context)) {
        // Find system message and append instructions
        foreach ($request_body['messages'] as &$msg) {
            if ($msg['role'] === 'system') {
                $msg['content'] .= "\n\nИНСТРУКЦИИ ИЗ ЗАГРУЖЕННЫХ ФАЙЛОВ (БРЕНДБУКИ/ПРАВИЛА):\n" . $persistent_context . "\nИспользуй эти инструкции как приоритетные при генерации контента и изображений!\n";
                break;
            }
        }
    }

    // If voice, try to use openai audio model
    if ($is_voice) {
        $request_body['model'] = 'openai/gpt-4o-mini-audio-preview'; // Reverting to the correct OpenRouter endpoint for audio, since 'gpt-audio-mini' causes routing errors.
        $request_body['modalities'] = array("text", "audio");
        $request_body['audio'] = array("voice" => "nova", "format" => "wav"); // 'nova' is a softer, warmer feminine voice than 'shimmer'.
    }

    $api_args = array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'HTTP-Referer'  => home_url(),
            'X-Title'       => 'City Library Theme',
            'Content-Type'  => 'application/json',
        ),
        'body' => wp_json_encode($request_body),
        'timeout' => 20 // Shorter timeout for primary attempt to trigger fallback faster if slow
    );

    // Helper to check for OpenRouter-specific errors in content or empty responses
    $is_invalid_response = function($response) {
        if (is_wp_error($response)) return true;
        $code = wp_remote_retrieve_response_code($response);

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Catch specific context length error or other API errors
        if ($code >= 400 || (isset($data['error']['message']) && mb_stripos($data['error']['message'], 'context length') !== false)) {
            return true;
        }

        if (!isset($data['choices'][0]['message']['content'])) return true;
        $content = $data['choices'][0]['message']['content'];

        // OpenRouter often returns "Provider returned error" or similar in the text even on 200 OK
        if (empty(trim($content)) ||
            mb_stripos($content, 'Provider returned error') !== false ||
            mb_stripos($content, 'upstream error') !== false ||
            mb_stripos($content, 'networks are overloaded') !== false ||
            mb_stripos($content, 'all providers failed') !== false ||
            mb_stripos($content, 'rate limit') !== false) {
            return true;
        }

        return false;
    };

    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $api_args);

    if ($is_invalid_response($response)) {
        // Handle context length overflow by truncating history further
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (isset($data['error']['message']) && mb_stripos($data['error']['message'], 'context length') !== false) {
            // Cut history by half and try again
            $reduced_history = array_slice($messages, 0, 1); // Keep system prompt
            $history_only = array_slice($messages, 1, -1); // Middle part
            $last_user_msg = end($messages);

            $truncated_history = array_slice($history_only, -(count($history_only) / 2));
            $new_messages = array_merge($reduced_history, $truncated_history, [$last_user_msg]);
            $request_body['messages'] = $new_messages;
        }

        // Attempt Fallback 1: Preferred Fallback Model
        $request_body['model'] = $fallback_model;
        unset($request_body['modalities']);
        unset($request_body['audio']);

        $api_args['body'] = wp_json_encode($request_body);
        $api_args['timeout'] = 25; // Slightly longer for fallback
        $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $api_args);

        if ($is_invalid_response($response)) {
            // Fallback 2: Stable free models requested by user
            $stable_fallbacks = [
                'nvidia/nemotron-3-super-120b-a12b:free',
                'qwen/qwen3-next-80b-a3b-instruct:free',
                'openai/gpt-4o-mini' // Ultimate reliability
            ];

            foreach ($stable_fallbacks as $f_model) {
                $request_body['model'] = $f_model;
                // Final attempt at truncation if still failing on context
                $final_messages = array_slice($request_body['messages'], 0, 1); // System only
                $final_messages[] = end($request_body['messages']); // Plus current message
                $request_body['messages'] = $final_messages;

                $api_args['body'] = wp_json_encode($request_body);
                $api_args['timeout'] = 25;
                $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $api_args);
                if (!$is_invalid_response($response)) break;
            }
        }
    }

    if (is_wp_error($response)) {
        wp_send_json_error(array('reply' => 'Произошла ошибка связи с сервером (' . $response->get_error_message() . '). Пожалуйста, попробуйте позже.'));
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    $http_code = wp_remote_retrieve_response_code($response);

    // If decoding failed completely (e.g. Cloudflare HTML instead of JSON)
    if ($data === null) {
        wp_send_json_error(array('reply' => 'Сервер ИИ недоступен (HTTP ' . $http_code . '). Техническая информация: ' . esc_html(substr(strip_tags($body), 0, 150))));
    }

    if (isset($data['choices'][0]['message']['content'])) {
        $reply = $data['choices'][0]['message']['content'];

        // Server-side Logging for AI Misses (Data not found in KB)
        if (mb_stripos($reply, 'Данные уточняются') !== false) {
            $log_entry = "[" . date('Y-m-d H:i:s') . "] MISS: user asked: '" . $user_message . "'\n";
            file_put_contents(get_template_directory() . '/city-library-ai-misses.log', $log_entry, FILE_APPEND);
        }

        $response_data = array('reply' => $reply);

        // Extract audio if requested and available
        if ($is_voice && isset($data['choices'][0]['message']['audio']['data'])) {
            $response_data['audio_base64'] = $data['choices'][0]['message']['audio']['data'];
        }

        wp_send_json_success($response_data);
    } else {
        // Fallback for API errors (e.g., rate limits, invalid keys, context length)
        $error_msg = 'Извините, я затрудняюсь ответить на этот вопрос.';
        if (isset($data['error']['message'])) {
            $raw_err = $data['error']['message'];
            if (strpos(strtolower($raw_err), 'rate') !== false || strpos(strtolower($raw_err), 'limit') !== false) {
                 $error_msg = 'К сожалению, сервис ИИ перегружен (исчерпан лимит запросов бесплатной модели). Пожалуйста, повторите попытку позже.';
            } else if (strpos(strtolower($raw_err), 'key') !== false || strpos(strtolower($raw_err), 'auth') !== false || strpos(strtolower($raw_err), 'unauthorized') !== false) {
                 $error_msg = 'Ошибка авторизации. Пожалуйста, проверьте API ключ OpenRouter в настройках сайта.';
            } else {
                 // Expose the raw error from OpenRouter so the admin knows exactly what's failing
                 $error_msg = 'Ответ от сервера ИИ: ' . esc_html($raw_err);
            }
        } else {
             // Fallback for generic JSON response without 'choices' and without 'error' string (e.g., partial outputs, server errors)
             $error_msg = 'Ответ от сервера ИИ не распознан (Код: ' . $http_code . '). Подробности: ' . esc_html(substr(json_encode($data), 0, 150));
        }
        wp_send_json_error(array('reply' => $error_msg));
    }
}
add_action('wp_ajax_city_library_ai_chat', 'city_library_handle_ai_chat');
add_action('wp_ajax_nopriv_city_library_ai_chat', 'city_library_handle_ai_chat');

/**
 * Handle DOCX Generation (Simulated as base64 HTML)
 */
function city_library_ai_docx() {
    check_ajax_referer('ai_chat_nonce', 'nonce');
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if (empty($content)) wp_send_json_error('Empty content');

    // For a real theme, we'd use a library like PHPWord,
    // but for this implementation we return a base64 encoded HTML that browser can download as .doc
    $html = "<html><head><meta charset='utf-8'></head><body>" . wpautop($content) . "</body></html>";
    wp_send_json_success(array('html' => base64_encode($html)));
}
add_action('wp_ajax_city_library_ai_docx', 'city_library_ai_docx');

/**
 * Handle AI Email Sending
 */
function city_library_ai_email() {
    check_ajax_referer('ai_chat_nonce', 'nonce');
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if (empty($email) || !is_email($email)) wp_send_json_error('Неверный Email');
    if (empty($content)) wp_send_json_error('Пустое содержание');

    $subject = 'Ответ от Виртуального Библиотекаря';

    // Extract images from content
    preg_match_all('/!\[.*?\]\((https?:\/\/.*?)\)/', $content, $matches);
    $attachments = array();
    $temp_files = array();

    if (!empty($matches[1])) {
        $upload_dir = wp_upload_dir();
        foreach ($matches[1] as $img_url) {
            // Some URLs might have random seeds or be encoded
            $clean_url = str_replace(' ', '%20', $img_url);
            $response = wp_remote_get($clean_url, array('timeout' => 10));

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $file_content = wp_remote_retrieve_body($response);
                $ext = pathinfo(parse_url($clean_url, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (empty($ext) || strlen($ext) > 4) $ext = 'png';

                $temp_filename = 'ai_attachment_' . uniqid() . '.' . $ext;
                $temp_path = $upload_dir['path'] . '/' . $temp_filename;

                if (file_put_contents($temp_path, $file_content)) {
                    $attachments[] = $temp_path;
                    $temp_files[] = $temp_path;
                }
            }
        }
    }

    $message = "Здравствуйте!\n\nВы просили отправить Вам ответ от Виртуального Библиотекаря:\n\n" . $content . "\n\n---\nС уважением, Центральная городская библиотека г. Владимира.";

    $sent = wp_mail($email, $subject, $message, array('Content-Type: text/plain; charset=UTF-8'), $attachments);

    // Cleanup
    foreach ($temp_files as $file) {
        @unlink($file);
    }

    if ($sent) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Ошибка отправки почты');
    }
}
add_action('wp_ajax_city_library_ai_email', 'city_library_ai_email');

/**
 * AJAX Handler for compiling selected messages into a single draft
 */
function city_library_ai_compile_draft() {
    check_ajax_referer('ai_chat_nonce', 'nonce');
    $selected_content = isset($_POST['content']) ? (array)$_POST['content'] : array();
    $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : 'pdf';

    if (empty($selected_content)) {
        wp_send_json_error('Ничего не выбрано для сборки.');
    }

    $combined_html = "
    <div class='ai-compiled-report' style='font-family: \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; max-width: 800px; margin: 0 auto; color: #334155;'>
        <header style='text-align: center; margin-bottom: 40px; border-bottom: 3px solid #0b7930; padding-bottom: 20px;'>
            <h1 style='color: #0b7930; margin: 0; font-size: 28px;'>Черновик материалов ИИ</h1>
            <p style='color: #64748b; font-size: 14px; margin-top: 5px;'>Подготовлено Виртуальным Библиотекарем МБУК «ЦГБ» г. Владимира</p>
            <p style='font-style: italic; font-size: 12px;'>" . date_i18n('d F Y, H:i') . "</p>
        </header>
        <main>";

    foreach ($selected_content as $index => $html) {
        $combined_html .= "
        <article style='margin-bottom: 40px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>
            <div style='background: #f8fafc; padding: 10px 20px; border-bottom: 1px solid #e2e8f0; font-weight: bold; color: #0b7930; display: flex; justify-content: space-between;'>
                <span>Материал №" . ($index + 1) . "</span>
            </div>
            <div style='padding: 25px; line-height: 1.7;'>
                " . $html . "
            </div>
        </article>";
    }

    $combined_html .= "
        </main>
        <footer style='text-align: center; margin-top: 60px; padding-top: 20px; border-top: 1px solid #e2e8f0; color: #94a3b8; font-size: 12px;'>
            <p>Данный документ сгенерирован автоматически с помощью технологий искусственного интеллекта.</p>
            <p>© " . date('Y') . " МБУК «Центральная городская библиотека» г. Владимира. Все права защищены.</p>
        </footer>
    </div>";

    if ($format === 'docx') {
        $full_html = "<html><head><meta charset='utf-8'></head><body style='background-color: #f1f5f9; padding: 20px;'>{$combined_html}</body></html>";
        wp_send_json_success(array(
            'base64' => base64_encode($full_html),
            'filename' => 'Library_Draft_' . date('Y-m-d') . '.doc'
        ));
    } else {
        // Return HTML for PDF printing
        wp_send_json_success(array(
            'html' => $combined_html
        ));
    }
}
add_action('wp_ajax_city_library_ai_compile_draft', 'city_library_ai_compile_draft');

/**
 * Handle Save as WordPress Draft
 */
function city_library_ai_draft() {
    check_ajax_referer('ai_chat_nonce', 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Нет прав доступа');

    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : 'Черновик ИИ';
    $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';

    $post_id = wp_insert_post(array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'draft',
        'post_type'    => 'post'
    ));

    if ($post_id) {
        wp_send_json_success(array('edit_link' => get_edit_post_link($post_id, '')));
    } else {
        wp_send_json_error('Не удалось создать пост');
    }
}
add_action('wp_ajax_city_library_ai_draft', 'city_library_ai_draft');

/**
 * Handle Temporary File Analysis (Text Extraction)
 */
function city_library_ai_upload() {
    check_ajax_referer('ai_chat_nonce', 'nonce');

    if (empty($_FILES['file'])) {
        wp_send_json_error('Файл не получен');
    }

    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extracted_text = "";

    if ($ext === 'txt') {
        $extracted_text = file_get_contents($file['tmp_name']);
    } elseif ($ext === 'docx') {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            if ($zip->open($file['tmp_name']) === true) {
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $xml_content = $zip->getFromIndex($index);
                    $zip->close();
                    $clean_text = strip_tags(str_replace(['<', '>'], [' <', '> '], $xml_content));
                    $extracted_text = preg_replace('/\s+/', ' ', $clean_text);
                }
            }
        }
    } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        $data = file_get_contents($file['tmp_name']);
        $base64 = 'data:image/' . $ext . ';base64,' . base64_encode($data);
        wp_send_json_success(array(
            'data_url' => $base64,
            'filename' => $file['name']
        ));
    } elseif ($ext === 'pdf') {
        // PHP fallback for PDF text extraction if client-side failed
        // For production, this requires Smalot/PdfParser or shell_exec('pdftotext')
        // Here we simulate successful upload as client-side is the primary method
        wp_send_json_success(array(
            'text' => '[PDF_CONTENT_EXTRACTED_CLIENT_SIDE]',
            'filename' => $file['name']
        ));
    } else {
        wp_send_json_error('Неподдерживаемый формат файла. Пожалуйста, используйте .txt, .docx, .pdf или изображения.');
    }

    if (empty($extracted_text)) {
        wp_send_json_error('Не удалось извлечь текст из файла или файл пуст.');
    }

    // Limit text size for AI context safety
    $extracted_text = mb_substr(trim($extracted_text), 0, 15000);

    wp_send_json_success(array(
        'text' => $extracted_text,
        'filename' => $file['name']
    ));
}
add_action('wp_ajax_city_library_ai_upload', 'city_library_ai_upload');

// Helper function to get AI avatar URL based on customizer presets
function get_city_library_ai_avatar_url() {
    $preset = get_theme_mod('ai_librarian_avatar_preset', 'default');
    $custom = get_theme_mod('ai_librarian_avatar', '');

    if ($preset === 'custom' && !empty($custom)) {
        return esc_url($custom);
    }

    switch ($preset) {
        case 'preset2':
            return 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jasper&backgroundColor=0b7930'; // Male Librarian
        case 'preset3':
            return 'https://api.dicebear.com/7.x/bottts/svg?seed=LibraryBot&backgroundColor=0b7930'; // Robot
        case 'preset4':
            return 'https://api.dicebear.com/7.x/thumbs/svg?seed=Owl&backgroundColor=0b7930'; // Owl/Mascot (fallback to thumbs if missing)
        case 'preset5':
            return 'https://api.dicebear.com/7.x/identicon/svg?seed=AI&backgroundColor=0b7930'; // Abstract
        case 'default':
        default:
            // Use Avery as specified in memory for the female librarian persona
            return 'https://api.dicebear.com/7.x/avataaars/svg?seed=Avery&backgroundColor=0b7930';
    }
}
