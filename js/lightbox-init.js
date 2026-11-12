document.addEventListener('DOMContentLoaded', function() {
    /**
     * Robust Lightbox Initialization
     * Targets images in posts, pages, and news content.
     */
    const contentContainers = [
        '.entry-content',
        '.prose',
        '.post-content',
        '.page-content',
        '.news-content',
        '.wp-block-image',
        '.wp-block-gallery',
        '.promo-section-content',
        '.important-section-link',
        '.library-branch-image-wrapper'
    ];

    const selector = contentContainers.map(c => `${c} img:not(.emoji)`).join(', ');
    const contentImages = document.querySelectorAll(selector);

    if (contentImages.length > 0) {
        contentImages.forEach(img => {
            // Skip if already processed or already inside a glightbox link
            if (img.closest('.glightbox')) return;

            const parent = img.parentElement;

            // Extract alignment classes and other relevant WP classes to transfer them
            const classesToTransfer = Array.from(img.classList).filter(c =>
                c.startsWith('align') ||
                c.startsWith('wp-image-') ||
                c.startsWith('size-')
            );

            // 1. Handle already linked images
            if (parent.tagName === 'A') {
                const href = parent.getAttribute('href');
                // If it links to an image or seems like a media link, add glightbox
                if (href && (href.match(/\.(jpg|jpeg|png|webp|gif|svg)(\?.*)?$/i) || href.includes('wp-content/uploads'))) {
                    parent.classList.add('glightbox');
                    if (!parent.dataset.gallery) {
                        parent.dataset.gallery = 'post-gallery';
                    }
                    // Ensure parent has alignment classes for proper layout
                    classesToTransfer.forEach(c => parent.classList.add(c));
                }
                return;
            }

            // 2. Wrap unlinked images
            const imageUrl = img.getAttribute('data-full-url') || img.src;
            if (!imageUrl) return;

            const link = document.createElement('a');
            link.href = imageUrl;
            link.classList.add('glightbox');
            link.dataset.gallery = 'post-gallery';

            // For SEO and accessibility, copy alt to title if present
            if (img.alt) {
                link.setAttribute('data-title', img.alt);
            }

            // Transfer classes to link to maintain layout (floats etc)
            classesToTransfer.forEach(c => link.classList.add(c));

            // Wrap the image
            img.parentNode.insertBefore(link, img);
            link.appendChild(img);
        });
    }

    // Global Initialization for all .glightbox elements
    // This handles images wrapped above, plus hardcoded ones in templates
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        zoomable: true,
        autoplayVideos: true,
        moreLength: 0 // Show full caption
    });
});
