document.addEventListener('DOMContentLoaded', function() {
    if ('IntersectionObserver' in window === false) {
        document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
            el.classList.add('aos-animate');
        });
        return;
    }

    var observerOptions = {
        root: null,
        rootMargin: '0px 0px -80px 0px',
        threshold: 0.05
    };

    var observer = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-animate');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(function(el) {
        observer.observe(el);
    });
});
