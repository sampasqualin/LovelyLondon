<?php
/**
 * Site Settings Helpers - Funções para buscar configurações do site
 */

/**
 * Buscar uma configuração específica do site
 *
 * @param string $key Chave da configuração
 * @param string $default Valor padrão se não encontrar
 * @return string Valor da configuração
 */
function getSiteSetting($key, $default = '') {
    static $settings_cache = null;

    // Carregar cache uma única vez
    if ($settings_cache === null) {
        $settings_cache = [];
        $settings_path = __DIR__ . '/../data/site_settings.json';

        if (file_exists($settings_path)) {
            $json = file_get_contents($settings_path);
            $settings_array = json_decode($json, true);

            if ($settings_array && is_array($settings_array)) {
                foreach ($settings_array as $setting) {
                    $settings_cache[$setting['setting_key']] = $setting['setting_value'];
                }
            }
        }
    }

    return $settings_cache[$key] ?? $default;
}

/**
 * Buscar todas as redes sociais configuradas
 *
 * @return array Array de redes sociais com [network => url]
 */
function getSocialMedia() {
    $social_networks = [
        'instagram' => getSiteSetting('social_instagram'),
        'facebook' => getSiteSetting('social_facebook'),
        'x' => getSiteSetting('social_x'),
        'tiktok' => getSiteSetting('social_tiktok'),
        'linkedin' => getSiteSetting('social_linkedin'),
        'youtube' => getSiteSetting('social_youtube'),
        'spotify' => getSiteSetting('social_spotify'),
        'pinterest' => getSiteSetting('social_pinterest'),
    ];

    // Filtrar apenas os que têm URL válida configurada (não vazio e começa com http)
    return array_filter($social_networks, function($url) {
        return !empty($url) && (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0);
    });
}

/**
 * Retornar SVG icon para uma rede social
 *
 * @param string $network Nome da rede social
 * @param int $size Tamanho do ícone
 * @return string HTML do SVG
 */
function getSocialIcon($network, $size = 20) {
    $icons = [
        'instagram' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="m16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
        'facebook' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
        'x' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>',
        'twitter' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>',
        'linkedin' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
        'youtube' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
        'tiktok' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
        'spotify' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14.5c2-1 4.5-1.5 7-1"></path><path d="M9 12c1.5-.5 3.5-1 6-.5"></path><path d="M9.5 9.5c2-.5 4.5-.5 6.5 0"></path></svg>',
        'pinterest' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="21" x2="12" y2="13"></line><line x1="15" y1="9.34" x2="15" y2="9.35"></line><circle cx="12" cy="12" r="10"></circle><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.25-1.42-1.33-2.67-2.58-3.59-4.16-1.02-1.75-1.76-3.67-2.44-5.84"></path></svg>',
        'whatsapp' => '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"></path><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"></path></svg>',
    ];

    return $icons[$network] ?? '';
}

/**
 * Retornar label amigável para uma rede social
 *
 * @param string $network Nome da rede social
 * @return string Label
 */
function getSocialLabel($network) {
    $labels = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'x' => 'X (Twitter)',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'spotify' => 'Spotify',
        'pinterest' => 'Pinterest',
        'whatsapp' => 'WhatsApp',
    ];

    return $labels[$network] ?? ucfirst($network);
}
