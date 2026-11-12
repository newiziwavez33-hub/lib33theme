<?php
if (!get_theme_mod('show_featured_cards', false)) {
    return;
}

$section_title = get_theme_mod('featured_cards_title', 'Наши направления');
$design_style = get_theme_mod('featured_cards_design', 'design-1');
?>

<section class="featured-cards-section py-16 bg-transparent w-full <?php echo city_library_get_animation_class(); ?>">
    <div class="w-full lg:max-w-[80%] lg:mx-auto px-4 lg:px-8">
        <?php if ($section_title) : ?>
            <div class="flex flex-col items-center justify-center mb-12 gap-6 text-center">
                <div class="space-y-4 max-w-2xl mx-auto">
                    <div class="h-1 w-20 bg-primary mx-auto"></div>
                    <h2 class="text-[32px] md:text-[50px] font-display font-bold leading-tight"><?php echo esc_html($section_title); ?></h2>
                </div>
            </div>
        <?php endif; ?>

        <!-- Responsive Layout: Swiper for Mobile (< lg), Grid for Kiosk/Desktop (>= lg) -->
        <div class="swiper featured-cards-slider w-full">
            <div class="swiper-wrapper lg:!grid lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 lg:gap-6">
                <?php for ($i = 1; $i <= 4; $i++) :
                    $image = get_theme_mod("fc_image_$i", '');
                    $title = get_theme_mod("fc_title_$i", sprintf('Карточка %d', $i));
                    $desc = get_theme_mod("fc_desc_$i", 'Краткое описание карточки.');
                    $link = get_theme_mod("fc_link_$i", '#');
                    ?>

                    <div class="swiper-slide h-auto shrink-0 w-[80vw] sm:w-[310px] lg:w-auto snap-center">
                        <a href="<?php echo esc_url($link); ?>" class="block h-full group focus:outline-none">
                            <?php
                            // Render based on design choice
                            switch ($design_style) {
                                case 'design-2': // Text over image
                                    ?>
                                    <div class="relative w-full aspect-[3/4] lg:aspect-[4/5] rounded-2xl overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-300">
                                        <?php if ($image) : ?>
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        <?php else : ?>
                                            <div class="absolute inset-0 bg-slate-200"></div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                                        <div class="absolute inset-0 p-6 flex flex-col justify-end text-white">
                                            <h3 class="text-xl font-bold font-display mb-2 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                            <p class="text-sm text-slate-200 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-3': // Minimalist without background
                                    ?>
                                    <div class="flex flex-col h-full bg-transparent group-hover:-translate-y-2 transition-transform duration-300">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-video rounded-xl overflow-hidden mb-4">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="text-xl font-bold font-display mb-2 text-slate-900 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                        <p class="text-sm text-slate-600 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                    </div>
                                    <?php
                                    break;

                                case 'design-4': // Shadow and border radius
                                    ?>
                                    <div class="flex flex-col h-full bg-white rounded-[2rem] overflow-hidden shadow-xl group-hover:shadow-2xl transition-all duration-300 group-hover:-translate-y-1">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-video overflow-hidden shrink-0">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-6 flex flex-col flex-grow">
                                            <h3 class="text-xl font-bold font-display mb-2 text-slate-900 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                            <p class="text-sm text-slate-600 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-5': // Gradient background
                                    ?>
                                    <div class="flex flex-col h-full bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl overflow-hidden shadow-md group-hover:shadow-lg border border-slate-200 transition-all duration-300 group-hover:-translate-y-1">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-[4/3] overflow-hidden shrink-0 relative">
                                                <div class="absolute inset-0 bg-primary/10 mix-blend-multiply z-10 group-hover:opacity-0 transition-opacity duration-300"></div>
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-6 flex flex-col flex-grow text-center">
                                            <h3 class="text-lg font-bold font-display mb-2 text-slate-900 group-hover:text-primary transition-colors uppercase tracking-wider"><?php echo esc_html($title); ?></h3>
                                            <p class="text-sm text-slate-600 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-6': // Glassmorphism
                                    ?>
                                    <div class="relative flex flex-col w-full aspect-[3/4] lg:aspect-[4/5] rounded-2xl overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300">
                                        <?php if ($image) : ?>
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        <?php else : ?>
                                            <div class="absolute inset-0 bg-slate-800"></div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors duration-300 z-0"></div>
                                        <div class="relative z-10 flex flex-col justify-end flex-grow p-4 pointer-events-none">
                                            <div class="bg-white/20 backdrop-blur-md border border-white/30 rounded-xl p-5 text-white pointer-events-auto">
                                                <h3 class="text-lg font-bold font-display mb-2 text-white shadow-sm"><?php echo esc_html($title); ?></h3>
                                                <p class="text-sm text-slate-100 line-clamp-2"><?php echo esc_html($desc); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-7': // Thin top line
                                    ?>
                                    <div class="flex flex-col h-full bg-white rounded-lg shadow-sm group-hover:shadow-md border border-slate-100 border-t-4 border-t-primary transition-all duration-300 group-hover:-translate-y-1 p-6">
                                        <?php if ($image) : ?>
                                            <div class="w-16 h-16 rounded-full overflow-hidden mb-4 border border-slate-200 shrink-0 shadow-inner">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover">
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="text-lg font-bold font-display mb-2 text-slate-900 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                        <p class="text-sm text-slate-600 line-clamp-4"><?php echo esc_html($desc); ?></p>
                                    </div>
                                    <?php
                                    break;

                                case 'design-8': // Solid fill, icon (using image as icon)
                                    ?>
                                    <div class="flex flex-col items-center text-center h-full bg-slate-800 rounded-3xl p-8 shadow-xl group-hover:bg-primary transition-colors duration-500">
                                        <?php if ($image) : ?>
                                            <div class="w-20 h-20 mb-6 shrink-0 flex items-center justify-center bg-white/10 rounded-full p-4">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-contain filter brightness-0 invert">
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="text-xl font-bold font-display mb-3 text-white"><?php echo esc_html($title); ?></h3>
                                        <p class="text-sm text-slate-300 line-clamp-4"><?php echo esc_html($desc); ?></p>
                                    </div>
                                    <?php
                                    break;

                                case 'design-9': // Large title, no photo
                                    ?>
                                    <div class="flex flex-col justify-between h-full bg-white rounded-2xl p-8 border border-slate-200 shadow-sm group-hover:shadow-xl group-hover:border-primary transition-all duration-300 group-hover:-translate-y-2">
                                        <div>
                                            <h3 class="text-3xl font-bold font-display mb-4 text-slate-900 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                            <p class="text-base text-slate-600 line-clamp-4"><?php echo esc_html($desc); ?></p>
                                        </div>
                                        <div class="mt-6 flex items-center text-primary font-bold text-sm uppercase tracking-wider">
                                            <?php _e('Подробнее', 'city-library'); ?>
                                            <span class="material-symbols-outlined ml-2 transform group-hover:translate-x-2 transition-transform" aria-hidden="true">arrow_forward</span>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-10': // Polaroid style
                                    ?>
                                    <div class="flex flex-col h-full bg-white p-4 shadow-md group-hover:shadow-2xl transition-all duration-300 transform group-hover:rotate-1">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-[4/5] bg-slate-100 overflow-hidden shrink-0 mb-4 border border-slate-200">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                            </div>
                                        <?php else : ?>
                                             <div class="w-full aspect-[4/5] bg-slate-200 shrink-0 mb-4 border border-slate-300"></div>
                                        <?php endif; ?>
                                        <div class="text-center px-2 pb-4 pt-2">
                                            <h3 class="text-lg font-bold font-handwriting mb-1 text-slate-800"><?php echo esc_html($title); ?></h3>
                                            <p class="text-xs text-slate-500 line-clamp-2"><?php echo esc_html($desc); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-11': // Only Photo, completely adaptive
                                    ?>
                                    <div class="relative w-full h-full aspect-square lg:aspect-[3/4] rounded-3xl overflow-hidden shadow-md group-hover:shadow-2xl transition-all duration-500 border-4 border-white flex-grow featured-cards-image-only">
                                        <?php if ($image) : ?>
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" aria-hidden="true">
                                        <?php else : ?>
                                            <div class="absolute inset-0 bg-slate-300 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white text-4xl" aria-hidden="true">image</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-500 pointer-events-none"></div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-12': // Duotone overlay
                                    ?>
                                    <div class="relative flex flex-col w-full h-full aspect-square md:aspect-[4/3] rounded-2xl overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300 group-hover:-translate-y-2">
                                        <div class="absolute inset-0 bg-indigo-500 mix-blend-multiply z-10 transition-colors duration-500 group-hover:bg-primary"></div>
                                        <div class="absolute inset-0 bg-rose-500 mix-blend-screen z-10 opacity-70"></div>
                                        <?php if ($image) : ?>
                                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover grayscale transition-transform duration-700 group-hover:scale-105 z-0">
                                        <?php else : ?>
                                            <div class="absolute inset-0 bg-slate-200 z-0"></div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 p-6 flex flex-col justify-end text-white z-20">
                                            <h3 class="text-2xl font-bold font-display leading-tight mb-2 uppercase tracking-widest text-white drop-shadow-md"><?php echo esc_html($title); ?></h3>
                                            <p class="text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-4 group-hover:translate-y-0 text-white drop-shadow"><?php echo esc_html($desc); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-13': // Neumorphism
                                    ?>
                                    <div class="flex flex-col h-full bg-slate-100 rounded-3xl p-6 transition-all duration-300 shadow-[8px_8px_16px_#cbd5e1,-8px_-8px_16px_#ffffff] group-hover:shadow-[inset_4px_4px_8px_#cbd5e1,inset_-4px_-4px_8px_#ffffff]">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-video rounded-xl overflow-hidden mb-6 shadow-inner shrink-0">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-300">
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="text-xl font-bold font-display mb-3 text-slate-800 text-center"><?php echo esc_html($title); ?></h3>
                                        <p class="text-sm text-slate-500 line-clamp-3 text-center flex-grow"><?php echo esc_html($desc); ?></p>
                                    </div>
                                    <?php
                                    break;

                                case 'design-14': // Brutalism
                                    ?>
                                    <div class="flex flex-col h-full bg-[#f4f4f0] border-4 border-black p-0 group-hover:-translate-y-1 transition-transform duration-200 shadow-[8px_8px_0px_rgba(0,0,0,1)] hover:shadow-[12px_12px_0px_rgba(0,0,0,1)]">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-video border-b-4 border-black overflow-hidden shrink-0 filter contrast-125 saturate-0">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover">
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-6 flex flex-col flex-grow bg-white">
                                            <h3 class="text-2xl font-black uppercase tracking-tighter mb-3 text-black leading-none group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                            <p class="text-sm text-black font-mono leading-tight flex-grow line-clamp-4"><?php echo esc_html($desc); ?></p>
                                            <div class="mt-4 inline-block bg-primary text-white font-bold uppercase text-xs px-4 py-2 border-2 border-black w-max">Смотреть</div>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-15': // Circular Block
                                    ?>
                                    <div class="flex flex-col items-center text-center h-full group-hover:-translate-y-2 transition-transform duration-300">
                                        <div class="w-32 h-32 md:w-48 md:h-48 rounded-full overflow-hidden shadow-xl border-4 border-white mb-6 group-hover:border-primary transition-colors duration-300 shrink-0">
                                            <?php if ($image) : ?>
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110">
                                            <?php else : ?>
                                                <div class="w-full h-full bg-slate-200"></div>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="text-xl font-bold font-display mb-2 text-slate-900"><?php echo esc_html($title); ?></h3>
                                        <p class="text-sm text-slate-500 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                    </div>
                                    <?php
                                    break;

                                case 'design-16': // B&W Contrast
                                    ?>
                                    <div class="flex flex-col h-full bg-black text-white p-8 group-hover:bg-white group-hover:text-black transition-colors duration-500 border border-slate-800 group-hover:border-slate-200">
                                        <h3 class="text-2xl font-bold font-serif italic mb-4 border-b border-white/20 group-hover:border-black/20 pb-4 transition-colors"><?php echo esc_html($title); ?></h3>
                                        <p class="text-sm text-slate-400 group-hover:text-slate-600 line-clamp-5 transition-colors leading-relaxed flex-grow"><?php echo esc_html($desc); ?></p>
                                        <div class="mt-6 font-bold uppercase tracking-widest text-xs flex items-center justify-end group-hover:text-primary">
                                            <span class="material-symbols-outlined transition-transform transform group-hover:translate-x-2" aria-hidden="true">arrow_right_alt</span>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-17': // Abstract Shapes
                                    ?>
                                    <div class="relative flex flex-col h-full w-full aspect-[4/5] lg:aspect-auto rounded-[3rem] rounded-tr-none overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-500 bg-gradient-to-br from-primary to-secondary p-1">
                                        <div class="absolute inset-1 bg-white rounded-[2.8rem] rounded-tr-none z-0"></div>
                                        <div class="relative z-10 flex flex-col h-full bg-white rounded-[2.8rem] rounded-tr-none overflow-hidden">
                                            <?php if ($image) : ?>
                                                <div class="w-full h-[50%] shrink-0">
                                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover rounded-bl-[3rem] transition-transform duration-700 group-hover:scale-105">
                                                </div>
                                            <?php endif; ?>
                                            <div class="p-6 flex flex-col justify-center flex-grow">
                                                <h3 class="text-xl font-bold font-display mb-2 text-slate-800"><?php echo esc_html($title); ?></h3>
                                                <p class="text-sm text-slate-500 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-18': // Elegant Typography
                                    ?>
                                    <div class="flex flex-col h-full bg-[#fdfbf7] p-8 border border-[#e5e0d8] shadow-sm group-hover:shadow-xl transition-all duration-500 relative overflow-hidden">
                                        <div class="absolute -right-4 -top-4 text-9xl font-serif text-[#f0ebe1] opacity-50 z-0 pointer-events-none"><?php echo $i; ?></div>
                                        <div class="relative z-10 flex flex-col h-full">
                                            <h3 class="text-3xl font-serif text-[#2c3e50] mb-6 leading-tight group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                            <p class="text-[15px] font-sans text-[#7f8c8d] line-clamp-4 leading-loose flex-grow mb-6 border-l-2 border-[#e5e0d8] pl-4"><?php echo esc_html($desc); ?></p>
                                            <div class="mt-auto text-xs uppercase tracking-[0.2em] font-bold text-[#2c3e50] border-b border-[#2c3e50] inline-block pb-1 w-max group-hover:border-primary group-hover:text-primary transition-colors cursor-pointer">Читать</div>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-19': // Cyberpunk
                                    ?>
                                    <div class="flex flex-col h-full bg-slate-900 border border-purple-500/30 p-1 group-hover:shadow-[0_0_20px_rgba(168,85,247,0.4)] transition-all duration-300 relative overflow-hidden group">
                                        <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-cyan-400 to-transparent opacity-50 z-0"></div>
                                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-fuchsia-500 to-transparent opacity-50 z-0"></div>
                                        <div class="relative z-10 flex flex-col h-full bg-slate-900/90 backdrop-blur-sm p-6 border border-slate-800">
                                            <?php if ($image) : ?>
                                                <div class="w-full h-32 overflow-hidden shrink-0 mb-4 border border-cyan-500/50 mix-blend-luminosity opacity-80 group-hover:mix-blend-normal group-hover:opacity-100 transition-all duration-500">
                                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover">
                                                </div>
                                            <?php endif; ?>
                                            <h3 class="text-xl font-bold font-mono uppercase tracking-widest text-cyan-400 mb-3 drop-shadow-[0_0_5px_rgba(34,211,238,0.8)]"><?php echo esc_html($title); ?></h3>
                                            <p class="text-xs text-slate-300 font-mono line-clamp-4 flex-grow"><?php echo esc_html($desc); ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-20': // Offset Blocks
                                    ?>
                                    <div class="relative flex flex-col w-full h-full aspect-[4/5] lg:aspect-auto pt-4 pr-4 transition-transform duration-300 group-hover:-translate-y-2 group-hover:-translate-x-2">
                                        <div class="absolute inset-0 bg-primary/20 rounded-2xl border border-primary/30 mt-4 ml-4 z-0 transition-transform duration-300 group-hover:translate-x-2 group-hover:translate-y-2"></div>
                                        <div class="relative z-10 flex flex-col h-full bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                                            <?php if ($image) : ?>
                                                <div class="w-full h-[55%] shrink-0">
                                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                                </div>
                                            <?php endif; ?>
                                            <div class="p-6 flex flex-col justify-center flex-grow bg-slate-50">
                                                <h3 class="text-lg font-bold font-display mb-2 text-slate-900 leading-snug"><?php echo esc_html($title); ?></h3>
                                                <p class="text-xs text-slate-500 line-clamp-3"><?php echo esc_html($desc); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    break;

                                case 'design-1': // Default standard with border
                                default:
                                    ?>
                                    <div class="flex flex-col h-full bg-white rounded-2xl overflow-hidden shadow-md group-hover:shadow-xl border border-slate-200 transition-all duration-300 group-hover:-translate-y-1">
                                        <?php if ($image) : ?>
                                            <div class="w-full aspect-[4/3] overflow-hidden shrink-0 border-b border-slate-100">
                                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" aria-hidden="true">
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-6 flex flex-col flex-grow">
                                            <h3 class="text-xl font-bold font-display mb-3 text-slate-900 group-hover:text-primary transition-colors"><?php echo esc_html($title); ?></h3>
                                            <p class="text-sm text-slate-600 line-clamp-3 mb-4"><?php echo esc_html($desc); ?></p>
                                            <div class="mt-auto flex items-center text-primary font-bold text-sm">
                                                <?php _e('Узнать больше', 'city-library'); ?>
                                                <span class="material-symbols-outlined ml-1 text-lg transform transition-transform group-hover:translate-x-1" aria-hidden="true">arrow_right_alt</span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    break;
                            }
                            ?>
                        </a>
                    </div>
                <?php endfor; ?>
            </div>
            <!-- Pagination for mobile swiper -->
            <div class="swiper-pagination lg:hidden !bottom-0 !relative mt-8"></div>
        </div>
    </div>
</section>
