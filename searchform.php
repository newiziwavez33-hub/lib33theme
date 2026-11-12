<?php
/**
 * Custom Search Form Template
 *
 * Designed to match the modern library UI design system.
 *
 * @package CityLibrary
 */
?>
<form role="search" method="get" class="search-modal-form w-full" action="<?php echo esc_url(home_url('/')); ?>" autocomplete="off">
    <div class="search-input-group flex-1">
        <span class="search-input-icon" aria-hidden="true">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="7.5"></circle>
                <line x1="21" y1="21" x2="16.5" y2="16.5"></line>
            </svg>
        </span>
        <input type="search"
               class="search-modal-input !pl-[64px] !pr-[48px] !py-0 !border-0 !bg-transparent !shadow-none focus:!ring-0 focus:!outline-none"
               placeholder="<?php echo esc_attr_x('Поиск книг, статей, событий...', 'placeholder', 'city-library'); ?>"
               value="<?php echo get_search_query(); ?>"
               name="s"
               title="<?php echo esc_attr_x('Search for:', 'label', 'city-library'); ?>"
               aria-label="<?php echo esc_attr_x('Поиск', 'label', 'city-library'); ?>" />
    </div>
    <button type="submit" class="search-modal-submit-btn" aria-label="<?php echo esc_attr_x('Найти', 'submit button', 'city-library'); ?>">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
    </button>
</form>
