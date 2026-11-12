<?php
/**
 * Afisha (Events) Section
 *
 * "AAA" Quality Animation & Visuals Update
 */

$mods = get_theme_mods() ?: [];

if (!($mods['show_afisha_section'] ?? true)) {
    return;
}

// Collect events data
$events = [];
for ($i = 1; $i <= 5; $i++) {
    $image = $mods["afisha_image_$i"] ?? '';
    $title = $mods["afisha_title_$i"] ?? '';
    $link = $mods["afisha_link_$i"] ?? '';
    $ribbon = $mods["afisha_ribbon_$i"] ?? '';
    $badge = $mods["afisha_badge_$i"] ?? '';
    $date = $mods["afisha_date_$i"] ?? ''; // Assuming this might exist or we use badge as date

    if ($image || $title) {
        $events[] = [
            'image' => $image,
            'title' => $title,
            'link' => $link,
            'ribbon' => $ribbon,
            'badge' => $badge,
            'date' => $date,
        ];
    }
}

if (empty($events)) {
    return;
}

$section_title = $mods['afisha_title'] ?? 'Афиша Мероприятий';
$bg_style = $mods['afisha_bg_style'] ?? 'default';
$card_style = $mods['afisha_card_style'] ?? 'default';

// Container Styles - Adaptive: Full width on mobile but with internal padding, rounded on desktop
$container_classes = "bg-white relative overflow-hidden transition-all duration-500";
$bg_overlay = "";

if ($bg_style === 'gradient') {
    $container_classes = "bg-gradient-to-br from-indigo-900 via-purple-900 to-slate-900 text-white relative overflow-hidden shadow-2xl border border-white/10";
    $bg_overlay = '<div class="absolute inset-0 bg-[url(\'https://www.transparenttextures.com/patterns/cubes.png\')] opacity-10 pointer-events-none mix-blend-overlay"></div>';
}

// Card Style Logic
$card_base_classes = 'relative w-full aspect-[2/3] overflow-hidden transition-all duration-500 bg-slate-900 select-none transform-gpu flex flex-col';
$card_extra_classes = 'rounded-2xl shadow-md hover:shadow-2xl hover:shadow-slate-900/20 border border-white/5'; // Default

if ($card_style === 'card') {
    $card_extra_classes = 'rounded-xl shadow-md hover:shadow-xl border-4 border-white';
} elseif ($card_style === 'clean') {
    $card_extra_classes = 'rounded-3xl border border-slate-100'; // No shadow
} elseif ($card_style === 'overlay') {
    $card_extra_classes = 'rounded-none shadow-none border-0'; // Flat
} elseif ($card_style === 'glass') {
    $card_extra_classes = 'rounded-[2rem] shadow-xl border border-white/30 backdrop-blur-sm bg-white/10';
} elseif ($card_style === 'gradient') {
    $card_extra_classes = 'rounded-2xl shadow-lg border-b-4 border-primary';
} elseif ($card_style === 'brutalism') {
    $card_extra_classes = 'rounded-none shadow-[8px_8px_0px_rgba(0,0,0,1)] hover:shadow-[12px_12px_0px_rgba(0,0,0,1)] border-4 border-black';
} elseif ($card_style === 'minimal-text') {
    $card_extra_classes = 'rounded-[3rem] border border-slate-200 shadow-sm hover:shadow-xl bg-white';
} elseif ($card_style === 'cyberpunk') {
    $card_extra_classes = 'rounded-lg border border-cyan-500/50 shadow-[0_0_15px_rgba(34,211,238,0.3)] hover:shadow-[0_0_25px_rgba(34,211,238,0.6)] bg-slate-900';
} elseif ($card_style === 'rounded-image') {
    $card_extra_classes = 'rounded-full shadow-lg border-8 border-white hover:border-primary transition-colors';
}

$card_classes = $card_base_classes . ' ' . $card_extra_classes;
?>

<section id="afisha" class="py-2 lg:py-3 bg-white overflow-hidden <?php echo city_library_get_animation_class(); ?>">
    <!-- Width Correction: 80% to match other blocks -->
    <div class="w-full lg:max-w-[80%] lg:mx-auto px-4 lg:px-8">

        <div class="<?php echo esc_attr($container_classes); ?> rounded-2xl shadow-md border border-slate-100 p-2 md:p-3">
            <?php echo $bg_overlay; ?>

            <!-- Background Decorative Elements -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary/5 rounded-full blur-[100px] pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-[80px] pointer-events-none"></div>

            <div class="flex flex-col gap-2 relative z-10">

                <!-- Header Row: Text Content & Navigation -->
                <div class="w-full flex flex-row justify-between items-center gap-2 relative z-20">

                    <div class="text-left space-y-0.5">
                        <div class="relative flex items-center gap-3">
                            <div class="h-1 w-10 bg-[#1c77d9] rounded-full shrink-0"></div>
                            <h2 class="text-xl md:text-2xl font-display font-extrabold tracking-tight leading-tight text-slate-900">
                                <?php echo esc_html($section_title); ?>
                            </h2>
                        </div>
                    </div>

                    <!-- Navigation Buttons (inline right) -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button class="afisha-prev slider-nav-btn group w-11 h-11 rounded-full border border-slate-200 transition-all duration-300 flex items-center justify-center cursor-pointer shadow-sm hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary/20" aria-label="<?php _e('Предыдущий слайд', 'city-library'); ?>">
                            <span class="material-symbols-outlined text-lg transition-colors duration-300" aria-hidden="true">arrow_back</span>
                        </button>
                        <button class="afisha-next slider-nav-btn group w-11 h-11 rounded-full border border-slate-200 transition-all duration-300 flex items-center justify-center cursor-pointer shadow-sm hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary/20" aria-label="<?php _e('Следующий слайд', 'city-library'); ?>">
                            <span class="material-symbols-outlined text-lg transition-colors duration-300" aria-hidden="true">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Full Width Slider -->
                <div class="w-full relative z-10">

                    <!-- Slider Container with overflow-visible for depth effect -->
                    <div class="swiper afisha-slider w-full !pb-4 !px-2 md:!px-0 overflow-visible">
                        <div class="swiper-wrapper items-stretch">
                        <?php foreach ($events as $index => $event) : ?>
                            <div class="swiper-slide h-auto group cursor-pointer perspective-1000 afisha-slide-item transition-all duration-500"
                                 data-afisha-image="<?php echo esc_url($event['image']); ?>"
                                 data-afisha-link="<?php echo esc_url($event['link']); ?>"
                                 data-afisha-title="<?php echo esc_attr($event['title']); ?>"
                                 role="button"
                                 tabindex="0"
                                 aria-label="<?php echo esc_attr($event['title']); ?>">

                                <div class="<?php echo esc_attr($card_classes); ?>">

                                    <?php if ($card_style === 'minimal-text') : ?>
                                        <!-- Minimal Text Design Layout -->
                                        <?php if ($event['image']) : ?>
                                            <div class="w-full h-3/5 md:h-2/3 shrink-0 overflow-hidden rounded-t-[3rem]">
                                                <img src="<?php echo esc_url($event['image']); ?>" alt="<?php echo esc_attr($event['title']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" loading="eager">
                                            </div>
                                        <?php else : ?>
                                            <div class="w-full h-3/5 md:h-2/3 bg-slate-200 flex items-center justify-center rounded-t-[3rem] shrink-0">
                                                <span class="material-symbols-outlined text-6xl text-slate-400" aria-hidden="true">event</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-6 md:p-8 flex flex-col justify-between flex-grow bg-white rounded-b-[3rem] z-10 pointer-events-none">
                                            <div class="flex justify-between items-start pointer-events-auto">
                                                <?php if (!empty($event['badge'])) : ?>
                                                    <span class="text-xs font-bold uppercase tracking-widest text-[#1c77d9] bg-[#1c77d9]/10 px-3 py-1 rounded-full"><?php echo esc_html($event['badge']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <h3 class="text-xl md:text-2xl font-display font-bold text-slate-900 leading-tight line-clamp-3 pointer-events-auto group-hover:text-primary transition-colors"><?php echo esc_html($event['title']); ?></h3>
                                        </div>
                                    <?php elseif ($card_style === 'rounded-image') : ?>
                                         <!-- Circular Image Design Layout -->
                                         <div class="flex flex-col h-full items-center p-8 bg-white/50 backdrop-blur-sm pointer-events-none">
                                            <div class="w-48 h-48 md:w-56 md:h-56 rounded-full overflow-hidden shadow-2xl border-4 border-white mb-6 shrink-0 group-hover:border-primary transition-colors duration-500 z-10">
                                                <?php if ($event['image']) : ?>
                                                    <img src="<?php echo esc_url($event['image']); ?>" alt="<?php echo esc_attr($event['title']); ?>" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110" loading="eager">
                                                <?php else : ?>
                                                    <div class="w-full h-full bg-slate-200"></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow flex flex-col items-center justify-center text-center w-full z-10 pointer-events-auto">
                                                 <?php if (!empty($event['badge'])) : ?>
                                                    <div class="bg-slate-900 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full mb-4"><?php echo esc_html($event['badge']); ?></div>
                                                <?php endif; ?>
                                                <h3 class="text-2xl font-serif font-bold text-slate-800 leading-tight line-clamp-3 group-hover:text-primary transition-colors"><?php echo esc_html($event['title']); ?></h3>
                                            </div>
                                         </div>
                                    <?php else : ?>
                                        <!-- Default / Gradient / Glass / Brutalism / Cyberpunk Layout -->
                                        <!-- AAA Image Block: blurred bg + object-contain poster -->
                                        <?php if ($event['image']) : ?>
                                            <!-- Blurred ambient background -->
                                            <div class="absolute inset-0 overflow-hidden z-0 <?php echo ($card_style === 'brutalism') ? 'grayscale contrast-125' : ''; ?>">
                                                <div class="absolute inset-[-8%] bg-cover bg-center" style="background-image: url('<?php echo esc_url($event['image']); ?>'); filter: blur(28px) brightness(0.55); transform: scale(1.08);"></div>
                                            </div>
                                            <!-- Main poster image — fully visible, object-contain -->
                                            <div class="relative z-10 flex-1 flex items-center justify-center min-h-0 p-1">
                                                <img src="<?php echo esc_url($event['image']); ?>"
                                                     alt="<?php echo esc_attr($event['title']); ?>"
                                                     class="w-full h-full object-contain rounded-lg shadow-xl ring-1 ring-white/10 transition-transform duration-700 group-hover:scale-[1.02] <?php echo ($card_style === 'cyberpunk') ? 'mix-blend-luminosity group-hover:mix-blend-normal' : ''; ?>"
                                                     loading="eager">
                                            </div>
                                        <?php else : ?>
                                            <div class="relative z-10 flex-1 flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900">
                                                <span class="material-symbols-outlined text-8xl text-slate-600" aria-hidden="true">event</span>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Cyberpunk Overlays -->
                                        <?php if ($card_style === 'cyberpunk') : ?>
                                            <div class="absolute inset-0 bg-slate-900/40 group-hover:bg-slate-900/20 transition-colors duration-500 z-10"></div>
                                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-cyan-500/40 to-transparent pointer-events-none z-10"></div>
                                        <?php endif; ?>

                                        <!-- Hover zoom icon overlay -->
                                        <div class="absolute inset-0 z-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 pointer-events-none">
                                            <div class="bg-white/15 backdrop-blur-md border border-white/30 text-white rounded-full p-3.5 shadow-2xl scale-75 group-hover:scale-100 transition-transform duration-500">
                                                <span class="material-symbols-outlined text-3xl drop-shadow-md" aria-hidden="true">visibility</span>
                                            </div>
                                        </div>

                                        <!-- Bottom text panel -->
                                        <div class="relative z-20 shrink-0 px-4 pb-4 pt-3 bg-gradient-to-t from-black/80 via-black/60 to-transparent pointer-events-none">
                                            <!-- Badges & Ribbons row -->
                                            <div class="flex justify-between items-center mb-2 pointer-events-auto">
                                                <?php if (!empty($event['badge'])) : ?>
                                                    <span class="bg-white/90 backdrop-blur-md text-slate-900 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-md border border-white/50 <?php echo ($card_style === 'cyberpunk') ? '!bg-slate-900 !border-cyan-500 !text-cyan-400' : ''; ?>">
                                                        <?php echo esc_html($event['badge']); ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span></span>
                                                <?php endif; ?>
                                                <?php if (!empty($event['ribbon'])) : ?>
                                                    <span class="bg-red-600 text-white font-bold text-[10px] uppercase py-1 px-2.5 rounded-md shadow-md">
                                                        <?php echo esc_html($event['ribbon']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Title -->
                                            <?php if (!empty($event['title'])) : ?>
                                            <div class="transform transition-transform duration-500 translate-y-1 group-hover:translate-y-0 pointer-events-auto">
                                                <h3 class="text-base md:text-lg font-display font-bold text-white leading-snug line-clamp-2 drop-shadow-md <?php echo ($card_style === 'cyberpunk') ? 'font-mono !text-cyan-400' : ''; ?>">
                                                    <?php echo esc_html($event['title']); ?>
                                                </h3>
                                                <div class="h-0.5 w-8 bg-primary rounded-full mt-2 transition-all duration-500 group-hover:w-full group-hover:bg-green-400 shadow-[0_0_8px_rgba(74,222,128,0.5)]"></div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>

                        <!-- Pagination (Dots) -->
                        <div class="swiper-pagination !bottom-0"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Full Screen Modal for Afisha -->
<div id="afisha-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/95 backdrop-blur-lg transition-all duration-500 opacity-0" aria-hidden="true" role="dialog" aria-modal="true">

    <!-- Background Blur Click Area -->
    <div class="absolute inset-0" id="afisha-modal-bg"></div>

    <!-- Close Button -->
    <button id="afisha-modal-close" class="absolute top-6 right-6 z-[110] p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white/50 cursor-pointer group rotate-0 hover:rotate-90">
        <span class="material-symbols-outlined text-4xl drop-shadow-md" aria-hidden="true">close</span>
        <span class="sr-only"><?php _e('Закрыть', 'city-library'); ?></span>
    </button>

    <!-- Modal Content -->
    <div class="relative w-full h-full max-w-7xl mx-auto flex flex-col items-center justify-center p-4 md:p-8 pointer-events-none">

        <!-- Image Container -->
        <div class="relative w-full flex-grow flex items-center justify-center mb-8 pointer-events-auto transform scale-95 opacity-0 transition-all duration-500 delay-100" id="afisha-modal-content-wrapper">
            <img id="afisha-modal-image" src="" alt="" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl ring-1 ring-white/10">
        </div>

        <!-- Action Button -->
        <div class="mt-auto mb-4 pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-500 delay-200" id="afisha-modal-action-wrapper">
            <a id="afisha-modal-link" href="#" class="button inline-flex items-center justify-center px-10 py-4 font-bold text-lg uppercase tracking-wider rounded-full transition-all shadow-lg hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-primary/50 group">
                <?php _e('Подробнее', 'city-library'); ?>
                <span class="material-symbols-outlined ml-2 text-2xl group-hover:translate-x-1 transition-transform" aria-hidden="true">arrow_forward</span>
            </a>
        </div>
    </div>
</div>

<style>
/* ===== Anti-FOUC: hide slider until Swiper initialises ===== */
.afisha-slider {
    opacity: 0;
    padding-bottom: 2.5rem;
    transition: opacity 0.35s ease;
}
/* Swiper adds this class automatically once ready */
.afisha-slider.swiper-initialized {
    opacity: 1;
}

/* Clamp slide height BEFORE Swiper init to prevent giant flash */
.afisha-slider:not(.swiper-initialized) .swiper-slide {
    max-height: 300px;
    overflow: hidden;
}

/* Slides take height from aspect-ratio of the card */
.afisha-slider .swiper-slide {
    height: auto !important;
    display: flex;
    flex-direction: column;
}

.afisha-slider .swiper-slide > div {
    height: auto !important;
    width: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Inactive slides — soft fade only, no scale (breaks flex layout) */
.afisha-slider .swiper-slide:not(.swiper-slide-active) {
    opacity: 0.72;
    filter: grayscale(10%) brightness(0.9);
    transition: opacity 0.4s ease, filter 0.4s ease;
}

.afisha-slider .swiper-slide-active {
    opacity: 1;
    z-index: 10;
    filter: grayscale(0%) brightness(1);
    box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.28);
}

/* Pagination Dots */
.afisha-slider .swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #cbd5e1;
    opacity: 1;
    transition: all 0.3s ease;
}
.afisha-slider .swiper-pagination-bullet-active {
    width: 32px;
    border-radius: 5px;
    background: var(--primary-color, #0b7930);
}

/* Modal Open State */
#afisha-modal.open {
    display: flex;
    opacity: 1;
}
#afisha-modal.open #afisha-modal-content-wrapper {
    transform: scale(1);
    opacity: 1;
}
#afisha-modal.open #afisha-modal-action-wrapper {
    transform: translateY(0);
    opacity: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper with optimized settings for all devices
    const swiper = new Swiper('.afisha-slider', {
        // Core
        loop: true,
        speed: 800,
        spaceBetween: 24,
        grabCursor: true,
        centeredSlides: false, // Default grid-like behavior on desktop

        // Autoplay
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },

        // Navigation
        navigation: {
            nextEl: '.afisha-next',
            prevEl: '.afisha-prev',
        },

        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        // Responsive Breakpoints (Mobile & 1080x1920 Kiosk Friendly)
        breakpoints: {
            320: {
                slidesPerView: 1.15,
                spaceBetween: 12,
            },
            480: {
                slidesPerView: 1.6,
                spaceBetween: 16,
            },
            640: {
                slidesPerView: 2.2,
                spaceBetween: 16,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            1280: {
                slidesPerView: 4.2,
                spaceBetween: 20,
            }
        },

        // Accessibility
        a11y: {
            prevSlideMessage: 'Предыдущий слайд',
            nextSlideMessage: 'Следующий слайд',
        },
    });

    // --- Modal Logic ---
    const modal = document.getElementById('afisha-modal');
    const modalImg = document.getElementById('afisha-modal-image');
    const modalLink = document.getElementById('afisha-modal-link');
    const closeBtn = document.getElementById('afisha-modal-close');
    const bgOverlay = document.getElementById('afisha-modal-bg');

    // Delegation for dynamic/cloned slides (loop mode)
    document.querySelector('.afisha-slider').addEventListener('click', function(e) {
        const slide = e.target.closest('.afisha-slide-item');
        if (slide) {
            e.preventDefault();
            const img = slide.dataset.afishaImage;
            const link = slide.dataset.afishaLink;
            const title = slide.dataset.afishaTitle;
            openModal(img, link, title);
        }
    });

    function openModal(imageSrc, linkUrl, title) {
        if (!modal || !modalImg || !modalLink) return;

        // Set Content
        modalImg.src = imageSrc;
        modalImg.alt = title || 'Афиша';

        // Set Link
        if (linkUrl && linkUrl !== '#' && linkUrl !== '') {
            modalLink.href = linkUrl;
            modalLink.classList.remove('hidden');
            modalLink.classList.add('inline-flex');
        } else {
            modalLink.classList.add('hidden');
            modalLink.classList.remove('inline-flex');
        }

        // Show Modal
        modal.classList.remove('hidden');

        // Trigger Animation Frame
        requestAnimationFrame(() => {
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            // Focus trap could be added here
            closeBtn.focus();
        });

        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!modal) return;

        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');

        // Wait for transition
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        }, 500);
    }

    // Close Triggers
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (bgOverlay) bgOverlay.addEventListener('click', closeModal);

    // Keyboard Access
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });
});
</script>
