<?php
/**
 * Weather Widget Component
 * Widget de previsão do tempo para Londres
 * Exibe temperatura e condições climáticas atuais
 *
 * @param bool $show_location - Mostrar nome da localização (default: true)
 * @param string $variant - Variante do widget (compact, full) (default: compact)
 *
 * @example
 * renderWeatherWidget(true, 'compact');
 */

if (!function_exists('renderWeatherWidget')) {
    function renderWeatherWidget(bool $show_location = true, string $variant = 'compact'): void {
    $widget_class = "weather-widget weather-widget-{$variant}";
    ?>

    <div class="<?= $widget_class ?>" id="weatherWidget" aria-label="Previsão do tempo em Londres">
        <?php if ($show_location): ?>
            <div class="weather-widget-location">
                <svg class="weather-widget-icon-location" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Londres</span>
            </div>
        <?php endif; ?>

        <div class="weather-widget-content">
            <div class="weather-widget-temp" id="weatherTemp">
                <span class="weather-loading">--°</span>
            </div>

            <div class="weather-widget-meta" id="weatherMeta">
                <span class="weather-loading">Carregando...</span>
            </div>
        </div>

        <div class="weather-widget-icon" id="weatherIcon" aria-hidden="true">
            <!-- Ícone será preenchido via JS baseado na condição climática -->
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line>
                <line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
        </div>
    </div>

    <?php
    }
}
