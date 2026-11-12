// Global function for Accordion Toggle
window.toggleLibraryItem = function(header) {
    const body = header.nextElementSibling;
    const icon = header.querySelector('.material-symbols-outlined.transform');

    if (body.classList.contains('hidden')) {
        // Close others? Optional. Let's keep it simple (multiple open allowed).
        body.classList.remove('hidden');
        body.classList.add('block');
        if (icon) icon.classList.add('rotate-180');
        header.setAttribute('aria-expanded', 'true');
    } else {
        body.classList.add('hidden');
        body.classList.remove('block');
        if (icon) icon.classList.remove('rotate-180');
        header.setAttribute('aria-expanded', 'false');
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const mapContainer = document.getElementById('branches-yandex-map');
    if (!mapContainer || typeof ymaps === 'undefined') return;

    ymaps.ready(init);

    function init() {
        const data = window.branches_map_data || {};
        const branches = data.branches || [];
        // Default center if no data
        const center = (branches.length > 0 && branches[0].coords) ? branches[0].coords : [56.145, 40.405];
        const zoom = data.zoom || 12;

        const myMap = new ymaps.Map("branches-yandex-map", {
            center: center,
            zoom: zoom,
            controls: ['zoomControl', 'fullscreenControl']
        });

        // Create a clusterer
        const clusterer = new ymaps.Clusterer({
            preset: 'islands#invertedDarkGreenClusterIcons',
            groupByCoordinates: false,
            clusterDisableClickZoom: false,
            clusterHideIconOnBalloonOpen: false,
            geoObjectHideIconOnBalloonOpen: false
        });

        const placemarks = [];

        branches.forEach(branch => {
            const placemark = new ymaps.Placemark(branch.coords, {
                balloonContentHeader: `<h3 style="color: #0b7930; font-weight: bold;">${branch.name}</h3>`,
                balloonContentBody: `
                    <p><strong>Адрес:</strong> ${branch.address}</p>
                    <p><strong>Телефон:</strong> ${branch.phone}</p>
                `,
                balloonContentFooter: 'Центральная городская библиотека',
                hintContent: branch.name
            }, {
                preset: 'islands#darkGreenIcon'
            });

            // Optional: Scroll to list item on click
            placemark.events.add('click', function () {
                const listItem = document.getElementById('library-item-' + branch.id);
                if (listItem) {
                    listItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Optionally open it
                    const header = listItem.querySelector('.library-header');
                    const body = listItem.querySelector('.library-body');
                    if (header && body.classList.contains('hidden')) {
                        toggleLibraryItem(header);
                    }
                }
            });

            placemarks.push(placemark);
        });

        clusterer.add(placemarks);
        myMap.geoObjects.add(clusterer);

        myMap.behaviors.disable('scrollZoom');

        if (placemarks.length > 0) {
             myMap.setBounds(clusterer.getBounds(), { checkZoomRange: true });
        }
    }
});
