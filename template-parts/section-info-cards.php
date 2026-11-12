<?php
/**
 * Info Cards Section Template Part
 *
 * @package City_Library
 */

$show_info_cards = get_theme_mod('show_info_cards', true);
if (!$show_info_cards) return;

$card_count = get_theme_mod('info_cards_count', 5);
$card_count = min(max(intval($card_count), 1), 5);

$card_bg = get_theme_mod('info_cards_bg_color', '#FFFFFF');
$card_border = get_theme_mod('info_cards_border_color', '#e2e8f0');
$card_icon_bg = get_theme_mod('info_cards_icon_bg', 'rgba(11, 121, 48, 0.1)');
$card_icon_color = get_theme_mod('info_cards_icon_color', '#0b7930');
$card_title_color = get_theme_mod('info_cards_title_color', '#0f172a');
$card_desc_color = get_theme_mod('info_cards_desc_color', '#64748b');
$card_font = get_theme_mod('info_cards_font_family', 'Inter');
$card_font_size_title = get_theme_mod('info_cards_font_size_title', 14);
$card_font_size_desc = get_theme_mod('info_cards_font_size_desc', 12);

$default_cards = [
    ['title' => 'Электронный каталог', 'desc' => 'Поиск книг в каталоге', 'icon' => 'search', 'link' => '#'],
    ['title' => 'AI-библиотекарь', 'desc' => 'Задайте вопрос', 'icon' => 'smart_toy', 'link' => '#'],
    ['title' => 'Читательский билет', 'desc' => 'Электронный билет', 'icon' => 'credit_card', 'link' => '#'],
    ['title' => 'Поддержка', 'desc' => 'Ответим на вопросы', 'icon' => 'support_agent', 'link' => '#'],
    ['title' => 'Режим чтения', 'desc' => 'Удобный просмотр', 'icon' => 'menu_book', 'link' => '#'],
];
?>

<section class="w-full section-info-cards" style="font-family: '<?php echo esc_attr($card_font); ?>', sans-serif;">
    <div class="w-full max-w-[95%] sm:max-w-[90%] xl:max-w-[85%] 2xl:max-w-[80%] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:portrait:grid-cols-3 lg:landscape:grid-cols-5 gap-4">
            <?php for ($i = 1; $i <= $card_count; $i++) :
                $title = get_theme_mod("info_card_{$i}_title", $default_cards[$i-1]['title'] ?? "Карточка {$i}");
                $desc = get_theme_mod("info_card_{$i}_desc", $default_cards[$i-1]['desc'] ?? '');
                $icon = get_theme_mod("info_card_{$i}_icon", $default_cards[$i-1]['icon'] ?? 'info');
                $link = get_theme_mod("info_card_{$i}_link", $default_cards[$i-1]['link'] ?? '#');
                $image = get_theme_mod("info_card_{$i}_image", '');
            ?>
            <a href="<?php echo esc_url($link); ?>" 
               class="group flex flex-col items-center text-center p-5 sm:p-6 rounded-2xl border transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
               style="background-color: <?php echo esc_attr($card_bg); ?>; border-color: <?php echo esc_attr($card_border); ?>;">
                <?php if (!empty($image)) : ?>
                    <div class="w-full max-w-[200px] aspect-square rounded-xl overflow-hidden mb-4 transition-transform duration-300 group-hover:scale-110 flex items-center justify-center">
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="max-w-full max-h-full object-contain rounded-xl">
                    </div>
                <?php else : ?>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl flex items-center justify-center mb-4 transition-transform duration-300 group-hover:scale-110 shrink-0"
                         style="background-color: <?php echo esc_attr($card_icon_bg); ?>;">
                        <span class="material-symbols-outlined text-2xl sm:text-3xl" style="color: <?php echo esc_attr($card_icon_color); ?>;">
                            <?php echo esc_html($icon); ?>
                        </span>
                    </div>
                <?php endif; ?>
                <h3 class="font-semibold mb-1" style="color: <?php echo esc_attr($card_title_color); ?>; font-size: <?php echo esc_attr($card_font_size_title); ?>px;">
                    <?php echo esc_html($title); ?>
                </h3>
                <p class="leading-relaxed" style="color: <?php echo esc_attr($card_desc_color); ?>; font-size: <?php echo esc_attr($card_font_size_desc); ?>px;">
                    <?php echo esc_html($desc); ?>
                </p>
            </a>
            <?php endfor; ?>
        </div>
    </div>
</section>
