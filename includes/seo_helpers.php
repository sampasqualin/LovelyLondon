<?php
/**
 * SEO Helpers - Funções para buscar dados de SEO
 */

/**
 * Buscar configuração de SEO para uma página específica
 *
 * @param string $page_slug Slug da página (ex: 'index', 'blog', 'tours')
 * @param string $base_url URL base do site
 * @return array Configuração de SEO
 */
function getSEOConfig($page_slug, $base_url = '') {
    // Configurações padrão por página (fallback se não houver no JSON)
    $default_configs = [
        'index' => [
            'title' => 'Lovely London by Carol | Guia Brasileira Certificada em Londres',
            'description' => 'Guia brasileira certificada em Londres. Tours privados personalizados, roteiros exclusivos e experiências autênticas. Descubra Londres com Carol!',
            'keywords' => 'guia brasileira Londres, tour privado Londres, guia turística Londres, passeios personalizados Londres, roteiro Londres',
            'url' => $base_url . '/index.php',
            'image' => $base_url . '/assets/images/og-image.jpg',
            'type' => 'website'
        ],
        'blog' => [
            'title' => 'Blog - Dicas e Segredos de Londres | Lovely London by Carol',
            'description' => 'Dicas exclusivas, histórias fascinantes e segredos de Londres compartilhados por uma guia local certificada. Planeje sua viagem perfeita!',
            'keywords' => 'blog Londres, dicas Londres, guia Londres, o que fazer em Londres, turismo Londres',
            'url' => $base_url . '/pages/blog.php',
            'image' => $base_url . '/assets/images/og-blog.jpg',
            'type' => 'website'
        ],
        'blog-post' => [
            'title' => 'Blog | Lovely London by Carol',
            'description' => 'Dicas exclusivas e histórias fascinantes sobre Londres',
            'keywords' => 'Londres, dicas de viagem, turismo Londres',
            'url' => $base_url . '/pages/blog-post.php',
            'image' => $base_url . '/assets/images/og-blog.jpg',
            'type' => 'article'
        ],
        'tours' => [
            'title' => 'Tours Personalizados em Londres | Lovely London by Carol',
            'description' => 'Tours privados e personalizados em Londres com guia brasileira certificada. Londres Clássica, Histórica, Gastronômica e muito mais. Reserve agora!',
            'keywords' => 'tours Londres, passeios Londres, tour guiado Londres, tour privado Londres, guia Londres',
            'url' => $base_url . '/pages/tours.php',
            'image' => $base_url . '/assets/images/og-tours.jpg',
            'type' => 'website'
        ],
        'services' => [
            'title' => 'Serviços - Planejamento e Consultoria | Lovely London by Carol',
            'description' => 'Serviços completos para sua viagem a Londres: planejamento, consultoria, transfers, assistência 24h e muito mais. Experiência premium garantida!',
            'keywords' => 'serviços turísticos Londres, consultoria viagem Londres, planejamento Londres, transfer Londres',
            'url' => $base_url . '/pages/services.php',
            'image' => $base_url . '/assets/images/og-services.jpg',
            'type' => 'website'
        ],
        'sobre' => [
            'title' => 'Sobre Carol - Guia Brasileira Certificada | Lovely London',
            'description' => 'Conheça Carol, guia turística brasileira certificada em Londres. Experiência, paixão e dedicação para tornar sua visita inesquecível.',
            'keywords' => 'Carol guia Londres, guia brasileira certificada, sobre Carol Londres',
            'url' => $base_url . '/pages/sobre.php',
            'image' => $base_url . '/assets/images/og-image.jpg',
            'type' => 'profile'
        ],
        'experience' => [
            'title' => 'Experience London - Experiências Exclusivas | Lovely London',
            'description' => 'Experiências exclusivas e memoráveis em Londres com guia brasileira certificada.',
            'keywords' => 'experiências Londres, tours exclusivos Londres, passeios especiais Londres',
            'url' => $base_url . '/pages/experience.php',
            'image' => $base_url . '/assets/images/og-image.jpg',
            'type' => 'website'
        ],
    ];

    // Tentar buscar do JSON
    $seo_data_path = __DIR__ . '/../data/seo_metadata.json';
    if (file_exists($seo_data_path)) {
        $json = file_get_contents($seo_data_path);
        $seo_records = json_decode($json, true);

        if ($seo_records && is_array($seo_records)) {
            foreach ($seo_records as $record) {
                if ($record['page_slug'] === $page_slug) {
                    // Encontrou configuração personalizada
                    return [
                        'title' => $record['meta_title'] ?? $default_configs[$page_slug]['title'] ?? '',
                        'description' => $record['meta_description'] ?? $default_configs[$page_slug]['description'] ?? '',
                        'keywords' => $record['meta_keywords'] ?? $default_configs[$page_slug]['keywords'] ?? '',
                        'url' => $record['canonical_url'] ?: ($default_configs[$page_slug]['url'] ?? $base_url),
                        'image' => $record['og_image'] ?: ($default_configs[$page_slug]['image'] ?? $base_url . '/assets/images/og-image.jpg'),
                        'type' => $record['og_type'] ?? ($default_configs[$page_slug]['type'] ?? 'website'),
                        'og_title' => $record['og_title'] ?: $record['meta_title'],
                        'og_description' => $record['og_description'] ?: $record['meta_description'],
                        'twitter_card' => $record['twitter_card'] ?? 'summary_large_image',
                        'twitter_title' => $record['twitter_title'] ?: $record['og_title'] ?: $record['meta_title'],
                        'twitter_description' => $record['twitter_description'] ?: $record['og_description'] ?: $record['meta_description'],
                        'robots' => $record['robots'] ?? 'index, follow',
                        'schema_json' => $record['schema_json'] ?? '',
                    ];
                }
            }
        }
    }

    // Retorna configuração padrão se não encontrou no JSON
    $default = $default_configs[$page_slug] ?? $default_configs['index'];

    return [
        'title' => $default['title'],
        'description' => $default['description'],
        'keywords' => $default['keywords'],
        'url' => $default['url'],
        'image' => $default['image'],
        'type' => $default['type'],
        'og_title' => $default['title'],
        'og_description' => $default['description'],
        'twitter_card' => 'summary_large_image',
        'twitter_title' => $default['title'],
        'twitter_description' => $default['description'],
        'robots' => 'index, follow',
        'schema_json' => '',
    ];
}
