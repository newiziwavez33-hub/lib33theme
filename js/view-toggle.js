jQuery(document).ready(function($) {
    const gridButton = $('#view-grid');
    const listButton = $('#view-list');
    const container = $('#posts-container');

    if (!gridButton.length || !listButton.length || !container.length || container.hasClass('swiper')) {
        return;
    }

    function setView(view) {
        if (view === 'list') {
            listButton.addClass('bg-white shadow-sm').removeClass('text-slate-400');
            gridButton.removeClass('bg-white shadow-sm').addClass('text-slate-400');
            container.removeClass('md:grid-cols-2').addClass('flex flex-col');
            localStorage.setItem('post_view', 'list');
        } else {
            gridButton.addClass('bg-white shadow-sm').removeClass('text-slate-400');
            listButton.removeClass('bg-white shadow-sm').addClass('text-slate-400');
            container.addClass('md:grid-cols-2').removeClass('flex flex-col');
            localStorage.setItem('post_view', 'grid');
        }
    }

    function loadPosts(view) {
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'load_posts_by_view',
                view: view,
                nonce: ajax_params.nonce
            },
            success: function(response) {
                container.html(response);
                setView(view);
            },
            error: function(error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    gridButton.on('click', function() {
        if (localStorage.getItem('post_view') !== 'grid') {
            loadPosts('grid');
        }
    });

    listButton.on('click', function() {
        if (localStorage.getItem('post_view') !== 'list') {
           loadPosts('list');
        }
    });

    // Apply view from localStorage on page load
    const currentView = localStorage.getItem('post_view') || 'grid';
    setView(currentView);
});
