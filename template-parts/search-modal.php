<?php
/**
 * Search Modal Template Part
 * 
 * Lead UI/UX Design Implementation:
 * - 100% exact match to design reference (soft grey-blue #ccd8e6, large 24px rounded card, shadow-2xl)
 * - Strictly left-aligned title and subtitle
 * - Minimalist top-right close cross icon
 * - Custom input with vibrant blue #066ffb bottom underline
 * - Separate saturated blue #004fc5 submit button with white right arrow
 * - Inline ghost autocomplete (Yandex/Google style)
 * - Live suggestions dropdown with categories, highlight, and keyboard navigation
 *
 * @package CityLibrary
 */
?>
<div id="search-modal" class="fixed inset-0 z-[100] hidden bg-slate-950/70 backdrop-blur-md transition-opacity duration-300 opacity-0 flex items-center justify-center p-3 sm:p-5" role="dialog" aria-modal="true" aria-labelledby="search-modal-title" aria-hidden="true">
    <div class="search-modal-card relative transform scale-95 transition-all duration-300" id="search-modal-content">
        
        <!-- Close Button (Top-Right) -->
        <button id="search-modal-close" class="search-modal-close-btn" type="button" aria-label="<?php esc_attr_e('Закрыть поиск', 'city-library'); ?>">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- Header: Strictly Left-Aligned -->
        <div class="search-modal-header">
            <h2 id="search-modal-title" class="search-modal-title">
                <?php _e('Поиск по сайту', 'city-library'); ?>
            </h2>
            <p class="search-modal-subtitle">
                <?php _e('Введите ключевое слово или фразу', 'city-library'); ?>
            </p>
        </div>

        <!-- Search Form Row -->
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-modal-form" id="search-modal-form" autocomplete="off">
            <div class="search-input-group">
                <!-- Search Icon (Left) -->
                <span class="search-input-icon" aria-hidden="true">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7.5"></circle>
                        <line x1="21" y1="21" x2="16.5" y2="16.5"></line>
                    </svg>
                </span>

                <!-- Ghost Autocomplete Overlay (Yandex/Google style) -->
                <div class="search-ghost-overlay !pl-[64px] !pr-[48px] !py-0" id="search-ghost-overlay" aria-hidden="true">
                    <span class="search-ghost-typed" id="search-ghost-typed"></span><span class="search-ghost-rest" id="search-ghost-rest"></span>
                </div>

                <!-- Real Search Input Field -->
                <input type="search"
                       id="search-modal-input"
                       name="s"
                       class="search-modal-input !pl-[64px] !pr-[48px] !py-0 !border-0 !bg-transparent !shadow-none focus:!ring-0 focus:!outline-none"
                       placeholder="<?php echo esc_attr_x('Поиск книг, статей, событий...', 'placeholder', 'city-library'); ?>"
                       value="<?php echo get_search_query(); ?>"
                       autocomplete="off"
                       autocorrect="off"
                       autocapitalize="off"
                       spellcheck="false"
                       aria-label="<?php echo esc_attr_x('Поиск по сайту', 'label', 'city-library'); ?>"
                       aria-autocomplete="both"
                       aria-controls="search-suggestions-dropdown"
                       aria-expanded="false" />

                <!-- Clear input button -->
                <button type="button" id="search-clear-btn" class="search-clear-btn" aria-label="<?php esc_attr_e('Очистить поле', 'city-library'); ?>">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Submit Button (Separate Right Button with White Arrow) -->
            <button type="submit" class="search-modal-submit-btn" aria-label="<?php echo esc_attr_x('Найти', 'submit button', 'city-library'); ?>">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </button>
        </form>

        <!-- Dropdown Live Search Suggestions -->
        <div id="search-suggestions-dropdown" class="search-suggestions-dropdown" role="region" aria-label="<?php esc_attr_e('Результаты живого поиска', 'city-library'); ?>">
            <!-- List of matches -->
            <ul id="search-suggestions-list" class="search-suggestions-list" role="listbox" aria-label="<?php esc_attr_e('Подсказки поиска', 'city-library'); ?>"></ul>

            <!-- Loading indicator -->
            <div id="search-suggestions-loading" class="search-state-message" style="display: none;">
                <div class="search-spinner"></div>
                <span class="text-xs text-slate-500 font-medium"><?php _e('Поиск совпадений...', 'city-library'); ?></span>
            </div>

            <!-- Empty state -->
            <div id="search-suggestions-empty" class="search-state-message" style="display: none;">
                <svg class="w-8 h-8 text-slate-400 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    <line x1="8" y1="11" x2="14" y2="11"></line>
                </svg>
                <strong class="text-sm text-slate-700"><?php _e('Ничего не найдено', 'city-library'); ?></strong>
                <span class="text-xs text-slate-500 mt-1"><?php _e('Попробуйте изменить формулировку запроса', 'city-library'); ?></span>
            </div>

            <!-- Footer: View all + keyboard guide -->
            <div class="search-suggestions-footer">
                <a href="#" id="search-view-all-link" class="search-view-all-link">
                    <span><?php _e('Показать все результаты для', 'city-library'); ?> «<strong id="search-view-all-query"></strong>»</span>
                    <svg class="w-4 h-4 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
                <div class="search-kbd-hints">
                    <span><kbd>Tab</kbd> <?php _e('дополнить', 'city-library'); ?></span>
                    <span><kbd>↑</kbd><kbd>↓</kbd> <?php _e('выбор', 'city-library'); ?></span>
                    <span><kbd>Enter</kbd> <?php _e('перейти', 'city-library'); ?></span>
                    <span><kbd>Esc</kbd> <?php _e('закрыть', 'city-library'); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>
