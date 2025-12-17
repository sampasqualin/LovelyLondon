// =============================================================================
// INTERACTIVE TOUR MAP - Google Maps Integration
// =============================================================================

class TourMap {
    constructor(containerId, apiKey) {
        this.container = document.getElementById(containerId);
        this.apiKey = apiKey;
        this.map = null;
        this.markers = [];
        this.infoWindows = [];

        // Tour locations with coordinates
        this.tourLocations = [
            {
                id: 'big-ben',
                name: 'Big Ben & Parlamento',
                tour: 'Londres Clássica',
                lat: 51.5007,
                lng: -0.1246,
                icon: '🏰',
                description: 'Ícone mais famoso de Londres, construído em 1859',
                price: '£89'
            },
            {
                id: 'tower-bridge',
                name: 'Tower Bridge',
                tour: 'Londres Histórica',
                lat: 51.5055,
                lng: -0.0754,
                icon: '🌉',
                description: 'Ponte icônica vitoriana sobre o Rio Tâmisa',
                price: '£95'
            },
            {
                id: 'buckingham',
                name: 'Buckingham Palace',
                tour: 'Londres Clássica',
                lat: 51.5014,
                lng: -0.1419,
                icon: '👑',
                description: 'Residência oficial da monarquia britânica',
                price: '£89'
            },
            {
                id: 'notting-hill',
                name: 'Notting Hill',
                tour: 'Notting Hill',
                lat: 51.5158,
                lng: -0.2058,
                icon: '🎨',
                description: 'Bairro charmoso com casas coloridas',
                price: '£95'
            },
            {
                id: 'camden',
                name: 'Camden Market',
                tour: 'Mercados de Londres',
                lat: 51.5413,
                lng: -0.1464,
                icon: '🛍️',
                description: 'Mercado vibrante com cultura alternativa',
                price: '£85'
            },
            {
                id: 'tower-london',
                name: 'Torre de Londres',
                tour: 'Londres Histórica',
                lat: 51.5081,
                lng: -0.0759,
                icon: '🏛️',
                description: 'Fortaleza histórica de 1000 anos',
                price: '£95'
            }
        ];
    }

    async init() {
        if (!window.google) {
            await this.loadGoogleMapsAPI();
        }
        this.initializeMap();
    }

    loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (window.google && window.google.maps) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}&callback=initMap`;
            script.async = true;
            script.defer = true;

            window.initMap = () => {
                resolve();
            };

            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    initializeMap() {
        if (!this.container) return;

        // Center on London
        const londonCenter = { lat: 51.5074, lng: -0.1278 };

        this.map = new google.maps.Map(this.container, {
            zoom: 12,
            center: londonCenter,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: this.getMapStyles()
        });

        this.addMarkers();
        this.addLegend();
    }

    addMarkers() {
        this.tourLocations.forEach(location => {
            // Create custom marker with emoji icon
            const marker = new google.maps.Marker({
                position: { lat: location.lat, lng: location.lng },
                map: this.map,
                title: location.name,
                label: {
                    text: location.icon,
                    fontSize: '24px'
                },
                animation: google.maps.Animation.DROP
            });

            // Create info window
            const infoWindow = new google.maps.InfoWindow({
                content: this.createInfoWindowContent(location)
            });

            // Add click listener
            marker.addListener('click', () => {
                // Close all other info windows
                this.infoWindows.forEach(iw => iw.close());
                infoWindow.open(this.map, marker);

                // Track map interaction
                if (typeof trackEvent === 'function') {
                    trackEvent('click', 'engagement', `map_location_${location.id}`, 1);
                }
            });

            this.markers.push(marker);
            this.infoWindows.push(infoWindow);
        });
    }

    createInfoWindowContent(location) {
        return `
            <div class="map-info-window">
                <div class="map-info-icon">${location.icon}</div>
                <h3 class="map-info-title">${location.name}</h3>
                <p class="map-info-tour">${location.tour}</p>
                <p class="map-info-description">${location.description}</p>
                <div class="map-info-footer">
                    <span class="map-info-price">${location.price}/pessoa</span>
                    <a href="#contact" class="map-info-cta">Reservar →</a>
                </div>
            </div>
        `;
    }

    addLegend() {
        const legend = document.createElement('div');
        legend.className = 'map-legend';
        legend.innerHTML = `
            <h4>🗺️ Tours Disponíveis</h4>
            <ul>
                ${[...new Set(this.tourLocations.map(l => l.tour))].map(tour => `
                    <li>
                        <span class="legend-marker">${this.tourLocations.find(l => l.tour === tour).icon}</span>
                        ${tour}
                    </li>
                `).join('')}
            </ul>
        `;
        this.map.controls[google.maps.ControlPosition.LEFT_TOP].push(legend);
    }

    getMapStyles() {
        // Custom map styling for brand consistency
        return [
            {
                "featureType": "all",
                "elementType": "geometry",
                "stylers": [{ "color": "#f5f5f5" }]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{ "color": "#c9e7f7" }]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [{ "color": "#ffffff" }]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{ "color": "#eeeeee" }]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{ "color": "#e5e8e5" }]
            }
        ];
    }

    // Fallback static map for when Google Maps API is not available
    renderStaticFallback() {
        if (!this.container) return;

        this.container.innerHTML = `
            <div class="static-map-fallback">
                <div class="map-locations-list">
                    <h3>📍 Localização dos Tours</h3>
                    ${this.tourLocations.map(location => `
                        <div class="location-item">
                            <span class="location-icon">${location.icon}</span>
                            <div class="location-details">
                                <strong>${location.name}</strong>
                                <p>${location.tour} - ${location.price}/pessoa</p>
                            </div>
                            <a href="https://maps.google.com/?q=${location.lat},${location.lng}"
                               target="_blank"
                               rel="noopener"
                               class="location-link">
                                Ver no Maps →
                            </a>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
}

// Initialize map when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const mapContainer = document.getElementById('tour-map');
    if (!mapContainer) return;

    // Replace with your Google Maps API key
    const API_KEY = 'YOUR_GOOGLE_MAPS_API_KEY';

    const tourMap = new TourMap('tour-map', API_KEY);

    // Use static fallback until API key is configured
    tourMap.renderStaticFallback();

    // Uncomment when API key is ready:
    // tourMap.init();
});
