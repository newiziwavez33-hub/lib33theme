document.addEventListener('DOMContentLoaded', function() {
    let newsSwiper = null;
    let partnersSwiper = null;
    let importantLinksSwiper = null;
    let featuredCardsSwiper = null;

    const breakpoint = 1024;

    function initMobileSliders() {
        const isMobile = window.innerWidth < breakpoint;

        if (isMobile) {
            // Featured Cards Slider
            if (!featuredCardsSwiper && document.querySelector('.featured-cards-slider')) {
                featuredCardsSwiper = new Swiper('.featured-cards-slider', {
                    slidesPerView: 'auto',
                    centeredSlides: true,
                    spaceBetween: 16,
                    pagination: {
                        el: '.featured-cards-slider .swiper-pagination',
                        clickable: true,
                    },
                    on: {
                        destroy: function() {
                            const wrapper = document.querySelector('.featured-cards-slider .swiper-wrapper');
                            if (wrapper) wrapper.removeAttribute('style');
                            const slides = document.querySelectorAll('.featured-cards-slider .swiper-slide');
                            slides.forEach(slide => slide.removeAttribute('style'));
                        }
                    }
                });
            }

            // News Slider
            if (!newsSwiper && document.querySelector('.news-slider')) {
                newsSwiper = new Swiper('.news-slider', {
                    slidesPerView: 'auto',
                    centeredSlides: true,
                    spaceBetween: 16,
                    pagination: {
                        el: '.news-slider .swiper-pagination',
                        clickable: true,
                    },
                    on: {
                        destroy: function() {
                            // Clean up styles manually
                            const wrapper = document.querySelector('.news-slider .swiper-wrapper');
                            if (wrapper) wrapper.removeAttribute('style');
                            const slides = document.querySelectorAll('.news-slider .swiper-slide');
                            slides.forEach(slide => slide.removeAttribute('style'));
                        }
                    }
                });
            }

            // Partners Slider
            if (!partnersSwiper && document.querySelector('.partners-slider')) {
                partnersSwiper = new Swiper('.partners-slider', {
                    slidesPerView: 2,
                    spaceBetween: 20,
                    breakpoints: {
                        480: {
                            slidesPerView: 3,
                            spaceBetween: 24,
                        },
                        768: {
                            slidesPerView: 4,
                            spaceBetween: 32,
                        }
                    },
                    pagination: {
                        el: '.partners-slider .swiper-pagination',
                        clickable: true,
                    },
                     on: {
                        destroy: function() {
                            const wrapper = document.querySelector('.partners-slider .swiper-wrapper');
                            if (wrapper) wrapper.removeAttribute('style');
                            const slides = document.querySelectorAll('.partners-slider .swiper-slide');
                            slides.forEach(slide => slide.removeAttribute('style'));
                        }
                    }
                });
            }

            // Important Links Slider
            if (!importantLinksSwiper && document.querySelector('.important-links-slider')) {
                importantLinksSwiper = new Swiper('.important-links-slider', {
                    slidesPerView: 1.3,
                    spaceBetween: 20,
                    breakpoints: {
                        480: {
                            slidesPerView: 2.1,
                            spaceBetween: 20,
                        },
                        640: {
                            slidesPerView: 2.5,
                            spaceBetween: 24,
                        },
                        768: {
                            slidesPerView: 3.1,
                            spaceBetween: 24,
                        }
                    },
                    pagination: {
                        el: '.important-links-slider .swiper-pagination',
                        clickable: true,
                    },
                    on: {
                        destroy: function() {
                            const wrapper = document.querySelector('.important-links-slider .swiper-wrapper');
                            if (wrapper) wrapper.removeAttribute('style');
                            const slides = document.querySelectorAll('.important-links-slider .swiper-slide');
                            slides.forEach(slide => slide.removeAttribute('style'));
                        }
                    }
                });
            }
        } else {
            // Destroy if exists
            if (featuredCardsSwiper) {
                featuredCardsSwiper.destroy(true, true);
                featuredCardsSwiper = null;
            }
            if (newsSwiper) {
                newsSwiper.destroy(true, true);
                newsSwiper = null;
            }
            if (partnersSwiper) {
                partnersSwiper.destroy(true, true);
                partnersSwiper = null;
            }
            if (importantLinksSwiper) {
                importantLinksSwiper.destroy(true, true);
                importantLinksSwiper = null;
            }
        }
    }

    // Initialize on load
    initMobileSliders();

    // Re-initialize/Destroy on resize (debounced to avoid rapid cycles)
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(initMobileSliders, 150);
    });
});
