/**
 * WEATHER WIDGET - Previsão do Tempo em Londres
 * Usa Open-Meteo API (gratuita, sem API key necessária)
 * Versão Simplificada e Robusta
 */

(function() {
    'use strict';

    // Esperar DOM carregar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWeatherWidget);
    } else {
        initWeatherWidget();
    }

    function initWeatherWidget() {
        const tempEl = document.getElementById('weatherTemp');
        const metaEl = document.getElementById('weatherMeta');
        const iconEl = document.getElementById('weatherIcon');

        // Se os elementos não existem, não fazer nada
        if (!tempEl || !metaEl) {
            console.log('Weather widget elements not found');
            return;
        }

        console.log('Initializing weather widget...');
        loadWeatherData(tempEl, metaEl, iconEl);
    }

    function loadWeatherData(tempEl, metaEl, iconEl) {
        // Coordenadas de Londres
        const lat = 51.5074;
        const lon = -0.1278;
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;

        console.log('Fetching weather from:', url);

        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Weather data received:', data);
                if (!data.current_weather) {
                    throw new Error('Invalid weather data structure');
                }
                updateWeatherUI(data.current_weather, tempEl, metaEl, iconEl);
            })
            .catch(error => {
                console.error('Weather widget error:', error);
                showError(tempEl, metaEl);
            });
    }

    function updateWeatherUI(weather, tempEl, metaEl, iconEl) {
        const temp = Math.round(weather.temperature);
        const wind = Math.round(weather.windspeed);
        const code = weather.weathercode;

        console.log('Updating UI - Temp:', temp, 'Wind:', wind, 'Code:', code);

        // Descrições das condições climáticas
        const weatherDescriptions = {
            0: 'Céu limpo',
            1: 'Principalmente limpo',
            2: 'Parcialmente nublado',
            3: 'Nublado',
            45: 'Nevoeiro',
            48: 'Nevoeiro com gelo',
            51: 'Garoa fraca',
            53: 'Garoa moderada',
            55: 'Garoa intensa',
            61: 'Chuva fraca',
            63: 'Chuva moderada',
            65: 'Chuva forte',
            71: 'Neve fraca',
            73: 'Neve moderada',
            75: 'Neve intensa',
            80: 'Pancadas fracas',
            81: 'Pancadas moderadas',
            82: 'Pancadas fortes',
            95: 'Tempestade',
            96: 'Tempestade com granizo'
        };

        const description = weatherDescriptions[code] || 'Variável';

        // Atualizar temperatura
        tempEl.innerHTML = `${temp}°C`;

        // Atualizar meta info
        metaEl.innerHTML = `
            <span class="weather-desc">${description}</span>
            <span class="weather-wind">Vento ${wind} km/h</span>
        `;

        // Atualizar ícone
        if (iconEl) {
            iconEl.innerHTML = getWeatherIcon(code);
        }

        console.log('Weather widget updated successfully');
    }

    function getWeatherIcon(code) {
        // Ícones SVG simplificados

        // Céu limpo (0-1)
        if (code <= 1) {
            return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>`;
        }

        // Nublado (2-3)
        if (code <= 3) {
            return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
            </svg>`;
        }

        // Chuva (51-67, 80-82)
        if ((code >= 51 && code <= 67) || (code >= 80 && code <= 82)) {
            return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="16" y1="13" x2="16" y2="21"></line>
                <line x1="8" y1="13" x2="8" y2="21"></line>
                <line x1="12" y1="15" x2="12" y2="23"></line>
                <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
            </svg>`;
        }

        // Neve (71-77)
        if (code >= 71 && code <= 77) {
            return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 17.58A5 5 0 0 0 18 8h-1.26A8 8 0 1 0 4 16.25"></path>
                <line x1="8" y1="16" x2="8.01" y2="16"></line>
                <line x1="8" y1="20" x2="8.01" y2="20"></line>
                <line x1="12" y1="18" x2="12.01" y2="18"></line>
                <line x1="12" y1="22" x2="12.01" y2="22"></line>
                <line x1="16" y1="16" x2="16.01" y2="16"></line>
                <line x1="16" y1="20" x2="16.01" y2="20"></line>
            </svg>`;
        }

        // Tempestade (95+)
        if (code >= 95) {
            return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
                <polyline points="13 11 9 17 15 17 11 23"></polyline>
            </svg>`;
        }

        // Default: nublado
        return `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
        </svg>`;
    }

    function showError(tempEl, metaEl) {
        console.log('Showing error state');
        tempEl.innerHTML = '--°';
        metaEl.innerHTML = '<span class="weather-error">Indisponível</span>';
    }
})();
