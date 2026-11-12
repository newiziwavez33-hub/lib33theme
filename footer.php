<?php
/**
 * Footer Template
 *
 * @package City_Library
 */
?>
</main>

<?php
// Partners Section
if (get_theme_mod('show_partners_section', true)) {
    get_template_part('template-parts/content-partners');
}
?>

<footer class="site-footer pb-28 lg:landscape:pb-16" style="background-color: <?php echo esc_attr(get_theme_mod('footer_bg_color', '#F1F5F9')); ?>; color: <?php echo esc_attr(get_theme_mod('footer_text_color', '#0F172A')); ?>;" role="contentinfo">
    <div class="footer-grid">
        <!-- Brand Column -->
        <?php if (get_theme_mod('footer_show_brand', true)) : ?>
        <div class="footer-brand">
            <?php if (has_custom_logo()) : ?>
                <div class="footer-logo footer-logo-custom">
                    <?php the_custom_logo(); ?>
                </div>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo footer-logo-fallback">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" fill="none" width="32" height="32" class="shrink-0">
                        <rect x="4" y="4" width="32" height="32" rx="8" fill="#1c77d9" opacity="0.15"/>
                        <path d="M14 10v20M20 10v20M10 14h16M10 18h16M10 22h16" stroke="#1c77d9" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M26 8c0 0 4 2 4 6s-4 6-4 6" stroke="#1c77d9" stroke-width="2" stroke-linecap="round" fill="none"/>
                    </svg>
                    <span class="logo-title"><?php echo esc_html(get_theme_mod('footer_logo_text', 'DIANA')); ?></span>
                </a>
            <?php endif; ?>
            <?php $footer_desc = get_theme_mod('footer_desc', 'Центральная городская библиотека — пространство для открытий, творчества и вдохновения.'); ?>
            <?php if ($footer_desc) : ?>
                <p class="footer-desc"><?php echo esc_html($footer_desc); ?></p>
            <?php endif; ?>
            <div class="footer-social">
                <?php
                $social_icons = [
                    'telegram' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
                    'vk' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M15.684 0H8.316C1.592 0 0 1.592 0 8.316v7.368C0 22.408 1.592 24 8.316 24h7.368C22.408 24 24 22.408 24 15.684V8.316C24 1.592 22.391 0 15.684 0zm3.692 17.123h-1.744c-.66 0-.864-.525-2.05-1.727-1.033-1-1.49-1.135-1.744-1.135-.356 0-.458.102-.458.593v1.575c0 .424-.135.678-1.253.678-1.846 0-3.896-1.118-5.335-3.202C4.624 10.857 4 8.57 4 8.096c0-.254.102-.491.593-.491h1.744c.44 0 .61.203.78.678.848 2.5 2.289 4.686 2.88 4.686.22 0 .322-.102.322-.66V9.721c-.068-1.186-.695-1.287-.695-1.71 0-.204.17-.407.44-.407h2.744c.373 0 .508.203.508.643v3.473c0 .372.17.508.271.508.22 0 .407-.136.813-.542 1.254-1.406 2.15-3.574 2.15-3.574.119-.254.322-.491.763-.491h1.744c.525 0 .644.27.525.643-.22 1.017-2.354 4.031-2.354 4.031-.186.305-.254.44 0 .78.186.254.796.779 1.203 1.253.745.847 1.32 1.558 1.473 2.05.17.49-.085.744-.576.744z"/></svg>',
                    'youtube' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                    'ok' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.001 0a12 12 0 1 0-.001 24 12 12 0 0 0 .001-24zm3.872 7.502c.451 0 .817.366.817.817v.001a.818.818 0 0 1-.817.817 3.873 3.873 0 1 1-3.873-3.872 3.87 3.87 0 0 1 3.873 2.237zm-3.873 6.983c2.513 0 4.743-1.219 6.137-3.097.163-.22.115-.533-.105-.697a.496.496 0 0 0-.697.106 6.74 6.74 0 0 0-5.334 2.688 6.74 6.74 0 0 0-5.335-2.688.496.496 0 0 0-.697-.106.496.496 0 0 0-.105.697 7.915 7.915 0 0 1 6.136 3.097zm3.179 2.261c.217.217.217.569 0 .786L12.786 20a1.112 1.112 0 0 1-1.572 0l-2.321-2.321a.556.556 0 0 1 .786-.786l2.321 2.321 2.321-2.261a.556.556 0 0 1 .786 0z"/></svg>',
                ];
                foreach ($social_icons as $social => $icon) :
                    $url = get_theme_mod("footer_social_{$social}", '#');
                    if ($url && $url !== '#') : ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($social)); ?>" class="footer-social-link footer-social-<?php echo esc_attr($social); ?>">
                            <?php echo $icon; ?>
                        </a>
                    <?php endif;
                endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Navigation Column -->
        <?php if (get_theme_mod('footer_show_nav', true)) : ?>
        <div class="footer-col">
            <h4><?php echo esc_html(get_theme_mod('footer_nav_title', 'Навигация')); ?></h4>
            <?php wp_nav_menu(['theme_location' => 'footer', 'container' => false, 'items_wrap' => '<ul class="footer-links">%3$s</ul>', 'fallback_cb' => false, 'depth' => 1]); ?>
            <?php if (!has_nav_menu('footer')) : ?>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Главная', 'city-library'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('О библиотеке', 'city-library'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Читателям', 'city-library'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Афиша', 'city-library'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Филиалы', 'city-library'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Контакты', 'city-library'); ?></a></li>
                </ul>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Contacts Column -->
        <?php if (get_theme_mod('footer_show_contacts', true)) : ?>
        <div class="footer-col footer-col-contacts min-w-0">
            <h4><?php echo esc_html(get_theme_mod('footer_contacts_title', 'Контакты')); ?></h4>
            <ul class="footer-links footer-contacts-list min-w-0">
                <?php $phone = get_theme_mod('footer_phone', '+7 (4922) 123-456'); if ($phone) : ?>
                    <li class="footer-contact-item min-w-0">
                        <span class="footer-contact-icon shrink-0 inline-flex items-center justify-center" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </span>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" class="footer-contact-link footer-contact-phone break-words"><?php echo esc_html($phone); ?></a>
                    </li>
                <?php endif; ?>
                <?php $email = get_theme_mod('footer_email', 'info@diana-library.ru'); if ($email) : ?>
                    <li class="footer-contact-item min-w-0">
                        <span class="footer-contact-icon shrink-0 inline-flex items-center justify-center" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </span>
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="footer-contact-link break-all sm:break-words min-w-0"><?php echo esc_html($email); ?></a>
                    </li>
                <?php endif; ?>
                <?php $address = get_theme_mod('footer_address', 'г. Владимир, ул. Библиотечная, 1'); if ($address) : ?>
                    <li class="footer-contact-item min-w-0">
                        <span class="footer-contact-icon shrink-0 inline-flex items-center justify-center" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <span class="footer-contact-text break-words"><?php echo esc_html($address); ?></span>
                    </li>
                <?php endif; ?>
                <?php $work_hours = get_theme_mod('footer_work_hours', "Пн-Пт: 9:00 - 20:00\nСб-Вс: 10:00 - 18:00"); if ($work_hours) : ?>
                    <li class="footer-contact-item footer-contact-hours min-w-0">
                        <span class="footer-contact-icon shrink-0 inline-flex items-center justify-center" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <span class="footer-contact-text break-words"><?php echo nl2br(esc_html($work_hours)); ?></span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Widget Columns (Footer 1, 2, 3, 4) -->
        <?php for ($i = 1; $i <= 4; $i++) : ?>
            <?php if (is_active_sidebar('footer-' . $i)) : ?>
                <div class="footer-col footer-col-widget footer-col-<?php echo esc_attr($i); ?>">
                    <?php dynamic_sidebar('footer-' . $i); ?>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    <div class="footer-bottom">
        <div class="footer-bottom-info flex flex-col sm:flex-row items-center sm:items-baseline gap-2 sm:gap-4">
            <p class="footer-copyright"><?php echo esc_html(get_theme_mod('footer_copyright', '© ' . date('Y') . ' ' . get_bloginfo('name') . '. Все права защищены.')); ?></p>
            <div class="flex flex-wrap items-center gap-3 footer-bottom-links">
                <?php if (get_theme_mod('footer_show_privacy', true)) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('footer_privacy_link', '#')); ?>" class="hover:text-slate-800 transition-colors underline underline-offset-2"><?php esc_html_e('Политика конфиденциальности', 'city-library'); ?></a>
                <?php endif; ?>
                <?php if (get_theme_mod('footer_show_sitemap', true)) : ?>
                    <a href="<?php echo esc_url(get_theme_mod('footer_sitemap_link', '#')); ?>" class="hover:text-slate-800 transition-colors underline underline-offset-2"><?php esc_html_e('Карта сайта', 'city-library'); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <?php
        $has_pro_culture_bottom = (get_theme_mod('pro_culture_enable', true) && get_theme_mod('pro_culture_position', 'footer-bottom') === 'footer-bottom');
        $has_custom_bottom_code = !empty(get_theme_mod('analytics_footer_bottom_code'));
        $has_counters_sidebar   = is_active_sidebar('footer-counters');

        if ($has_pro_culture_bottom || $has_custom_bottom_code || $has_counters_sidebar) : ?>
            <div class="footer-bottom-counters flex flex-wrap items-center justify-center sm:justify-end gap-3" role="region" aria-label="<?php esc_attr_e('Счетчики и аналитика', 'city-library'); ?>">
                <?php
                if (function_exists('city_library_render_pro_culture_counter')) {
                    city_library_render_pro_culture_counter('footer-bottom');
                }
                if (function_exists('city_library_render_footer_bottom_counters')) {
                    city_library_render_footer_bottom_counters();
                }
                if ($has_counters_sidebar) {
                    dynamic_sidebar('footer-counters');
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</footer>

<?php get_template_part('template-parts/mobile-bottom-nav'); ?>

<button id="back-to-top" class="fixed bottom-28 sm:bottom-24 right-4 sm:right-6 w-11 h-11 bg-white text-primary rounded-2xl shadow-lg border border-slate-200 hidden items-center justify-center transition-all duration-300 hover:bg-slate-50 hover:-translate-y-1 hover:shadow-xl z-[40]" aria-label="Наверх">
    <span class="material-symbols-outlined text-xl">arrow_upward</span>
</button>

<?php if (get_theme_mod('show_modal', false)) :
    $modal_image = get_theme_mod('modal_image');
    $modal_video = get_theme_mod('modal_video');
    $modal_title = get_theme_mod('modal_title', 'Специальное предложение!');
    $modal_text = get_theme_mod('modal_text', 'Подпишитесь на рассылку.');
    $modal_delay = get_theme_mod('modal_delay', 3000);
?>
<div id="city-library-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 hidden" data-delay="<?php echo esc_attr($modal_delay); ?>">
    <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 relative overflow-hidden animate-fade-in-up max-h-[90vh] overflow-y-auto">
        <button class="modal-close absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors z-20 bg-white/80 rounded-full p-1 shadow-sm" aria-label="<?php esc_attr_e('Закрыть', 'city-library'); ?>">
            <span class="material-symbols-outlined text-2xl">close</span>
        </button>
        <?php if ($modal_video) :
            $ext = pathinfo($modal_video, PATHINFO_EXTENSION);
            $mime = 'video/' . $ext;
            if ($ext === 'mov') $mime = 'video/quicktime';
        ?>
            <div class="w-full aspect-video">
                <video class="w-full h-full object-cover rounded-t-2xl" controls autoplay muted loop playsinline>
                    <source src="<?php echo esc_url($modal_video); ?>" type="<?php echo esc_attr($mime); ?>">
                </video>
            </div>
        <?php elseif ($modal_image) : ?>
            <div class="w-full">
                <img src="<?php echo esc_url($modal_image); ?>" alt="<?php echo esc_attr($modal_title); ?>" class="w-full h-auto object-contain max-h-[50vh]">
            </div>
        <?php endif; ?>
        <div class="p-8 text-center space-y-4">
            <?php if ($modal_title) : ?>
                <h3 class="text-2xl font-bold text-slate-900"><?php echo esc_html($modal_title); ?></h3>
            <?php endif; ?>
            <div class="prose prose-sm mx-auto text-slate-600 max-w-none">
                <?php echo city_library_sanitize_html(wpautop($modal_text)); ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php get_template_part('template-parts/search-modal'); ?>

<?php wp_footer(); ?>
</body>
</html>
