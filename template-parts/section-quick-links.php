<?php
$show_quick_links = get_theme_mod('show_quick_links', true);
if (!$show_quick_links) return;
?>
<section class="section bg-white dark:bg-slate-900 -mt-1">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6 stagger-grid">
            <?php
            $links = array(
                array(
                    'icon' => 'auto_stories',
                    'title' => __('Каталог книг', 'city-library'),
                    'desc' => __('Ищите книги в электронном каталоге', 'city-library'),
                    'link' => get_theme_mod('quick_link_catalog', '#'),
                    'color' => 'emerald',
                ),
                array(
                    'icon' => 'event',
                    'title' => __('Афиша', 'city-library'),
                    'desc' => __('Мероприятия и события', 'city-library'),
                    'link' => get_theme_mod('quick_link_afisha', '#events'),
                    'color' => 'amber',
                ),
                array(
                    'icon' => 'location_on',
                    'title' => __('Филиалы', 'city-library'),
                    'desc' => __('Адреса и контакты', 'city-library'),
                    'link' => get_theme_mod('quick_link_branches', '#'),
                    'color' => 'blue',
                ),
                array(
                    'icon' => 'support_agent',
                    'title' => __('Виртуальный библиотекарь', 'city-library'),
                    'desc' => __('Задайте вопрос онлайн', 'city-library'),
                    'link' => get_theme_mod('quick_link_ai', '#ai-chat'),
                    'color' => 'purple',
                ),
            );

            foreach ($links as $link) :
                $color_map = array(
                    'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white',
                    'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-600 group-hover:text-white',
                    'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white',
                    'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white',
                );
            ?>
            <a href="<?php echo esc_url($link['link']); ?>" class="group relative p-6 lg:p-8 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 text-center reveal">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl flex items-center justify-center transition-all duration-300 <?php echo $color_map[$link['color']]; ?>">
                    <span class="material-symbols-outlined text-3xl"><?php echo esc_attr($link['icon']); ?></span>
                </div>
                <h3 class="text-base lg:text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                    <?php echo esc_html($link['title']); ?>
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo esc_html($link['desc']); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
