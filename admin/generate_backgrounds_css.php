<?php
/**
 * =========================================================================
 * GERADOR DE CSS - BACKGROUNDS DAS SEÇÕES
 * =========================================================================
 * Regenera o arquivo section-backgrounds.css a partir do JSON
 */

define('PROJECT_ROOT', dirname(__DIR__));
define('DATA_PATH', PROJECT_ROOT . '/data');

// Carregar JSON de section_backgrounds
$jsonFile = DATA_PATH . '/section_backgrounds.json';
$sections = [];

if (file_exists($jsonFile)) {
    $json = file_get_contents($jsonFile);
    $sections = json_decode($json, true) ?? [];
}

// Gerar CSS
$css = "/**\r\n";
$css .= " * =========================================================================\r\n";
$css .= " * LOVELY LONDON - BACKGROUNDS DAS SEÇÕES (GERADO AUTOMATICAMENTE)\r\n";
$css .= " * =========================================================================\r\n";
$css .= " * Arquivo gerado automaticamente pelo admin.\r\n";
$css .= " * Não edite manualmente - suas alterações serão sobrescritas!\r\n";
$css .= " * Última atualização: " . date('d/m/Y H:i:s') . "\r\n";
$css .= " * =========================================================================\r\n";
$css .= " */\r\n\r\n";

// Mapa de seções para seletores CSS
$selectorMap = [
    'header' => [
        'html body header.header.section-header',
        'html body header.section-header#header-section',
        'html body .header.section-header',
        'html body .header.section-header .header-main',
        'html body .header-main.bg-pattern'
    ],
    'hero' => [
        'html body section.section.section-hero',
        'html body section.section-hero#hero-section',
        'html body .section.section-hero',
        'html body #hero-section.section'
    ],
    'tours' => [
        'html body section.section.section-tours',
        'html body section.section-tours#tours-section',
        'html body .section.section-tours',
        'html body #tours-section.section'
    ],
    'about' => [
        'html body section.section.section-about',
        'html body section.section-about#about-section',
        'html body .section.section-about',
        'html body #about-section.section'
    ],
    'services' => [
        'html body section.section.section-services',
        'html body section.section-services#services-section',
        'html body .section.section-services',
        'html body #services-section.section'
    ],
    'testimonials' => [
        'html body section.section.section-testimonials',
        'html body section.section-testimonials#testimonials-section',
        'html body .section.section-testimonials',
        'html body #testimonials-section.section'
    ],
    'gallery' => [
        'html body section.section.section-gallery',
        'html body section.section-gallery#gallery-section',
        'html body .section.section-gallery',
        'html body #gallery-section.section'
    ],
    'blog' => [
        'html body section.section.section-blog',
        'html body section.section-blog#blog-section',
        'html body .section.section-blog',
        'html body #blog-section.section'
    ],
    'faq' => [
        'html body section.section.section-faq',
        'html body section.section-faq#faq-section',
        'html body .section.section-faq',
        'html body #faq-section.section'
    ],
    'contact' => [
        'html body section.section.section-contact',
        'html body section.section-contact#contact-section',
        'html body .section.section-contact',
        'html body #contact-section.section'
    ],
    'footer' => [
        'html body footer.footer.section-footer',
        'html body footer.section-footer#footer-section',
        'html body .footer.section-footer'
    ],
    'services_cta' => [
        'html body section.section.section-services-cta',
        'html body section.section-services-cta#services-cta-section',
        'html body .section.section-services-cta',
        'html body #services-cta-section.section',
        'html body section.contact-section'
    ]
];

// Processar cada seção
foreach ($sections as $section) {
    if (!$section['is_active']) {
        continue;
    }
    
    $sectionName = $section['section_name'];
    $sectionLabel = $section['section_label'];
    $selectors = $selectorMap[$sectionName] ?? [];
    
    if (empty($selectors)) {
        continue;
    }
    
    $css .= "/* Seção: " . $sectionLabel . " */\r\n";
    $css .= implode(",\r\n", $selectors) . " {\r\n";
    
    // Aplicar background
    $bgType = $section['background_type'] ?? 'color';
    
    if ($bgType === 'color') {
        $css .= "    background-color: " . $section['background_color'] . " !important;\r\n";
    } elseif ($bgType === 'image') {
        if (!empty($section['background_image'])) {
            $css .= "    background-image: url('" . $section['background_image'] . "') !important;\r\n";
            $css .= "    background-size: cover !important;\r\n";
            $css .= "    background-position: center !important;\r\n";
            $css .= "    background-repeat: no-repeat !important;\r\n";
        }
    } elseif ($bgType === 'gradient') {
        $css .= "    background: linear-gradient(" . $section['gradient_direction'] . ", " . $section['gradient_start'] . ", " . $section['gradient_end'] . ") !important;\r\n";
    } elseif ($bgType === 'both') {
        if (!empty($section['background_image'])) {
            $css .= "    background-image: url('" . $section['background_image'] . "') !important;\r\n";
            $css .= "    background-size: cover !important;\r\n";
            $css .= "    background-position: center !important;\r\n";
            $css .= "    background-repeat: no-repeat !important;\r\n";
            $css .= "    background-blend-mode: multiply !important;\r\n";
        }
        $css .= "    background-color: " . $section['background_color'] . " !important;\r\n";
    }
    
    // Text color
    if (!empty($section['text_color'])) {
        $css .= "    color: " . $section['text_color'] . " !important;\r\n";
    }
    
    $css .= "}\r\n";
    
    // Aplicar overlay no ::before para header
    if ($sectionName === 'header') {
        $css .= "html body .header-main::before {\r\n";
        $css .= "    display: none !important;\r\n";
        $css .= "    content: none !important;\r\n";
        $css .= "}\r\n";
    }
    
    $css .= "\r\n";
}

// Salvar CSS
$cssPath = PROJECT_ROOT . '/assets/css/section-backgrounds.css';
if (file_put_contents($cssPath, $css) !== false) {
    echo json_encode(['success' => true, 'message' => 'CSS regenerado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar CSS']);
}
?>
