<?php get_header(); ?>

<div class="w-full max-w-[95%] xl:max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div id="primary" class="w-full flex items-center justify-center min-h-[60vh]">

        <div class="text-center max-w-2xl mx-auto space-y-8 p-8 sm:p-12 bg-white rounded-[2rem] shadow-xl border border-slate-100 relative overflow-hidden hover:shadow-2xl transition-shadow duration-500">

            <!-- Decorative Animated Blobs -->
            <div class="absolute -top-20 -left-20 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none float-animation"></div>
            <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none float-animation-delayed"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-40 h-40 bg-sky-400/5 rounded-full blur-2xl pointer-events-none float-animation"></div>

            <div class="relative z-10">
                <!-- Animated SVG Illustration -->
                <div class="mb-6 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="180" height="140" viewBox="0 0 180 140" fill="none" class="float-animation">
                        <!-- Open Book -->
                        <path d="M30 100 L90 85 L150 100 L150 40 L90 55 L30 40 Z" fill="#f1f5f9" stroke="#cbd5e1" stroke-width="2"/>
                        <path d="M90 85 L90 55" stroke="#cbd5e1" stroke-width="2"/>
                        <!-- Pages -->
                        <line x1="45" y1="55" x2="80" y2="62" stroke="#e2e8f0" stroke-width="1.5"/>
                        <line x1="45" y1="62" x2="80" y2="68" stroke="#e2e8f0" stroke-width="1.5"/>
                        <line x1="45" y1="69" x2="75" y2="74" stroke="#e2e8f0" stroke-width="1.5"/>
                        <line x1="100" y1="62" x2="140" y2="55" stroke="#e2e8f0" stroke-width="1.5"/>
                        <line x1="100" y1="68" x2="140" y2="62" stroke="#e2e8f0" stroke-width="1.5"/>
                        <line x1="100" y1="74" x2="135" y2="69" stroke="#e2e8f0" stroke-width="1.5"/>
                        <!-- Question Mark -->
                        <text x="90" y="38" font-size="36" font-weight="900" fill="url(#grad)" text-anchor="middle" font-family="sans-serif">?</text>
                        <defs>
                            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#0b7930"/>
                                <stop offset="100%" style="stop-color:#d4af37"/>
                            </linearGradient>
                        </defs>
                        <!-- Small decorative dots -->
                        <circle cx="40" cy="30" r="3" fill="#0b7930" opacity="0.2"/>
                        <circle cx="145" cy="28" r="2" fill="#d4af37" opacity="0.3"/>
                        <circle cx="60" cy="22" r="2" fill="#0ea5e9" opacity="0.2"/>
                    </svg>
                </div>

                <h1 class="text-8xl sm:text-9xl font-black font-display text-gradient-animated mb-4 select-none">404</h1>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4"><?php esc_html_e('Страница не найдена', 'city-library'); ?></h2>
                <p class="text-slate-500 text-lg mb-8"><?php esc_html_e('К сожалению, страница, которую вы ищете, не существует или была перемещена. Попробуйте воспользоваться поиском.', 'city-library'); ?></p>

                <div class="max-w-md mx-auto mb-8">
                    <?php get_search_form(); ?>
                </div>

                <!-- Quick Links -->
                <div class="flex flex-wrap justify-center gap-3 mb-8">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 transition-all duration-200 border border-slate-200 hover:border-slate-300">
                        <span class="material-symbols-outlined text-base" aria-hidden="true">home</span>
                        <?php esc_html_e('Главная', 'city-library'); ?>
                    </a>
                    <?php
                    $pages = array(
                        'news' => array('icon' => 'newspaper', 'label' => __('Новости', 'city-library')),
                        'events' => array('icon' => 'event', 'label' => __('Афиша', 'city-library')),
                        'contacts' => array('icon' => 'location_on', 'label' => __('Контакты', 'city-library')),
                    );
                    foreach ($pages as $slug => $data) :
                        $page = get_page_by_path($slug);
                        if ($page) : ?>
                            <a href="<?php echo esc_url(get_permalink($page)); ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-sm font-semibold text-slate-600 hover:text-slate-900 transition-all duration-200 border border-slate-200 hover:border-slate-300">
                                <span class="material-symbols-outlined text-base" aria-hidden="true"><?php echo esc_attr($data['icon']); ?></span>
                                <?php echo esc_html($data['label']); ?>
                            </a>
                        <?php endif;
                    endforeach; ?>
                </div>

                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-2 text-primary font-bold uppercase tracking-widest hover:text-primary/80 transition-colors group btn-glow px-6 py-3 rounded-full bg-primary/5">
                    <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform" aria-hidden="true">arrow_back</span>
                    <?php esc_html_e('Вернуться на главную', 'city-library'); ?>
                </a>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>
