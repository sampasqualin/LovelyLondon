<?php
/**
 * ABOUT PAGE HELPER
 * Funções para carregar e gerenciar conteúdo da página Sobre
 */

require_once __DIR__ . '/pdo_connection.php';

/**
 * Carregar conteúdo do About do banco de dados JSON
 */
function getAboutContent($section = null) {
    // Carregar APENAS do about_content.json
    $aboutFile = __DIR__ . '/../data/about_content.json';
    
    if (file_exists($aboutFile)) {
        $json = file_get_contents($aboutFile);
        $content = json_decode($json, true);
        
        if ($content && is_array($content)) {
            if ($section && isset($content[$section])) {
                return $content[$section];
            }
            return $content;
        }
    }
    
    // Se arquivo não existe, retornar estrutura vazia
    // NÃO fazer fallback para page_content
    
    // Return empty structure if no data found
    return [
        'about_carol' => [
            'title_pt' => '',
            'title_en' => '',
            'subtitle_pt' => '',
            'subtitle_en' => '',
            'content_pt' => '',
            'content_en' => '',
            'image' => '/assets/images/sobre/carol-profile.png'
        ],
        'about_lovely_london' => [
            'title_pt' => '',
            'title_en' => '',
            'subtitle_pt' => '',
            'subtitle_en' => '',
            'content_pt' => '',
            'content_en' => '',
            'image' => '/assets/images/sobre/galeria1.jpg'
        ],
        'social_links' => [
            'instagram' => '',
            'facebook' => '',
            'tiktok' => ''
        ]
    ];
}

/**
 * Obter conteúdo do Carol
 */
function getAboutCarol() {
    return getAboutContent('about_carol');
}

/**
 * Obter conteúdo da Lovely London
 */
function getAboutLovelyLondon() {
    return getAboutContent('about_lovely_london');
}

/**
 * Obter links de redes sociais
 */
function getSocialLinks() {
    return getAboutContent('social_links');
}

/**
 * Salvar conteúdo do About (para o admin)
 */
function saveAboutContent($data) {
    // Salvar no about_content.json
    $aboutFile = __DIR__ . '/../data/about_content.json';
    
    try {
        // Preparar dados para salvar
        $contentToSave = [
            'about_carol' => $data['about_carol'] ?? [],
            'about_lovely_london' => $data['about_lovely_london'] ?? [],
            'social_links' => $data['social_links'] ?? []
        ];
        
        // Salvar no arquivo JSON
        $json = json_encode($contentToSave, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        if (file_put_contents($aboutFile, $json)) {
            return [
                'success' => true,
                'message' => 'Conteúdo do About atualizado com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao salvar o arquivo JSON'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Erro ao salvar: ' . $e->getMessage()
        ];
    }
}
?>
