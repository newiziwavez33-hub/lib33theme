document.addEventListener('DOMContentLoaded', function() {
    const mapContainer = document.getElementById('footer-yandex-map');
    if (!mapContainer) return;

    // Check if ymaps is loaded
    if (typeof ymaps === 'undefined') {
        console.error('Yandex Maps API not loaded');
        return;
    }

    const params = window.yandex_map_params || {};
    const lat = parseFloat(params.lat) || 56.162458;
    const lon = parseFloat(params.lon) || 40.470598;
    const zoom = parseInt(params.zoom) || 15;

    ymaps.ready(init);

    function init() {
        const myMap = new ymaps.Map("footer-yandex-map", {
            center: [lat, lon],
            zoom: zoom,
            controls: ['zoomControl', 'fullscreenControl']
        });

        const myPlacemark = new ymaps.Placemark([lat, lon], {
            hintContent: 'Центральная городская библиотека',
            balloonContent: 'Мы находимся здесь!'
        });

        myMap.geoObjects.add(myPlacemark);

        // Disable scroll zoom by default for better UX
        myMap.behaviors.disable('scrollZoom');
    }
});
