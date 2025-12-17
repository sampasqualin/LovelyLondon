// mobile/js/app.js - comportamento mínimo para a versão mobile

document.addEventListener('DOMContentLoaded', () => {
    // Transição de entrada da tela (efeito app)
    const appContent = document.querySelector('.app-content');
    if (appContent) {
        requestAnimationFrame(() => {
            appContent.classList.add('page-enter');
        });
    }

    // Widgets: clima em Londres (Open-Meteo) e câmbio (exchangerate.host)

    // 1) Clima em Londres
    (function loadWeather() {
        const tempEl = document.getElementById('weatherTemp');
        const metaEl = document.getElementById('weatherMeta');
        if (!tempEl || !metaEl) return;

        // Coordenadas aproximadas de Londres
        const url = 'https://api.open-meteo.com/v1/forecast?latitude=51.5074&longitude=-0.1278&current_weather=true';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.current_weather) return;
                const temp = Math.round(data.current_weather.temperature);
                const wind = Math.round(data.current_weather.windspeed);
                const code = data.current_weather.weathercode;

                const descriptions = {
                    0: 'Céu limpo',
                    1: 'Principalmente limpo',
                    2: 'Parcialmente nublado',
                    3: 'Nublado',
                    45: 'Nevoeiro',
                    48: 'Nevoeiro com gelo',
                    51: 'Garoa fraca',
                    61: 'Chuva fraca',
                    63: 'Chuva moderada',
                    65: 'Chuva forte'
                };

                const desc = descriptions[code] || 'Condições variáveis';
                tempEl.textContent = temp + '°C';
                metaEl.textContent = desc + ' • Vento ' + wind + ' km/h';
            })
            .catch(() => {
                metaEl.textContent = 'Não foi possível carregar o clima agora.';
            });
    })();

    // 2) Câmbio simples: mostra quanto valem 1 EUR e 1 USD em BRL
    (function loadSimpleExchange() {
        const eurEl = document.getElementById('rateEUR');
        const usdEl = document.getElementById('rateUSD');
        if (!eurEl || !usdEl) return;

        const url = 'https://api.exchangerate.host/latest?base=BRL&symbols=EUR,USD';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.rates) return;
                const rates = data.rates;

                function formatToBRL(symbol) {
                    const rate = rates[symbol];
                    if (!rate) return '...';
                    const inv = 1 / rate; // quanto BRL vale 1 moeda
                    return 'R$ ' + inv.toFixed(2).replace('.', ',');
                }

                eurEl.textContent = formatToBRL('EUR');
                usdEl.textContent = formatToBRL('USD');
            })
            .catch(() => {
                eurEl.textContent = 'indisp.';
                usdEl.textContent = 'indisp.';
            });
    })();

});
