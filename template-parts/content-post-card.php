<?php
$bg_color = get_theme_mod('news_card_grid_bg_color', '#FFFFFF');
$title_color = get_theme_mod('news_card_grid_title_color', '#0f172a');
$text_color = get_theme_mod('news_card_grid_text_color', '#1e293b');
$link_color = get_theme_mod('news_card_grid_link_color', '#1c77d9');
?>
<article class="group relative flex flex-col h-full bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl hover:shadow-slate-900/5 border border-slate-100/80 transition-all duration-300 hover:-translate-y-1 w-full lg:w-auto max-w-full sm:max-w-[350px] lg:max-w-none mx-auto min-h-[360px] lg:aspect-[3/4.1]" style="background-color: <?php echo esc_attr($bg_color); ?>;">

  <!-- Image Container -->
  <div class="relative h-44 sm:h-48 lg:h-[50%] w-full shrink-0 overflow-hidden news-card-image-container pointer-events-auto">
    <a href="<?php the_permalink(); ?>" class="block w-full h-full pointer-events-auto">
      <?php if (has_post_thumbnail()) : ?>
        <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>" alt="<?php the_title_attribute(); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" aria-hidden="true">
      <?php else : ?>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
          <span class="material-symbols-outlined text-5xl text-slate-300" aria-hidden="true">image</span>
        </div>
      <?php endif; ?>
    </a>

    <?php
    $categories = get_the_category();
    if (!empty($categories)) : ?>
      <div class="absolute top-4 left-4 z-10">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/95 text-slate-700 shadow-sm border border-slate-100/80">
          <?php echo esc_html($categories[0]->name); ?>
        </span>
      </div>
    <?php endif; ?>
  </div>

  <!-- Content -->
  <div class="relative z-20 flex flex-col flex-1 lg:h-[50%] p-[20px] justify-between pointer-events-auto news-card-content-wrapper bg-white">
    <div class="min-h-0 flex-grow flex flex-col justify-start">
      <div class="flex items-center text-slate-400 text-xs font-semibold tracking-widest uppercase mb-1.5 shrink-0">
        <span class="material-symbols-outlined text-sm mr-1.5 text-slate-400" aria-hidden="true">calendar_month</span>
        <?php echo get_the_date('d.m.Y'); ?>
      </div>

      <h3 class="text-sm lg:text-[14.5px] font-bold font-display leading-tight mb-2 pointer-events-auto relative shrink-0 line-clamp-2">
        <a href="<?php the_permalink(); ?>" class="transition-colors duration-300 hover:text-primary text-slate-900 focus:outline-none news-card-title-link" style="--title-color: <?php echo esc_attr($title_color); ?>;">
          <?php the_title(); ?>
        </a>
      </h3>

      <div class="block text-xs leading-relaxed text-slate-500 pointer-events-auto news-card-excerpt-text line-clamp-2" style="--text-color: <?php echo esc_attr($text_color); ?>;">
        <?php echo wp_trim_words(get_the_excerpt(), 10, '...'); ?>
      </div>
    </div>

    <div class="pt-2 shrink-0 items-center justify-between pointer-events-auto relative z-20 border-0">
      <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-xs font-bold uppercase tracking-widest transition-all duration-300 relative z-10 group/link news-card-link" style="--link-color: <?php echo esc_attr($link_color); ?>;">
        <span><?php _e('Читать полностью', 'city-library'); ?></span>
        <span class="material-symbols-outlined ml-1.5 text-base transform transition-transform duration-300 group-hover/link:translate-x-1" aria-hidden="true">arrow_forward</span>
      </a>
    </div>
  </div>
</article>
