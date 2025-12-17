<?php
// Sistema de Internacionalização PT/EN
// Detecta idioma atual (padrão: PT)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detecta mudança de idioma via GET
if (isset($_GET['lang']) && in_array($_GET['lang'], ['pt', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Define idioma padrão
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'pt';
}

$lang = $_SESSION['lang'];

// Array de traduções
$translations = [
    'pt' => [
        // Header
        'reserve_now' => 'Reserve Agora',
        'change_language' => 'Mudar idioma',
        'open_menu' => 'Abrir menu',
        'privacy_policy' => 'Política de Privacidade',
        'terms_of_service' => 'Termos de Serviço',

        // Menu
        'menu_home' => 'Início',
        'menu_about' => 'Sobre',
        'menu_tours' => 'Tours',
        'menu_services' => 'Serviços',
        'menu_experience' => 'Experience',

        // Hero Slide 1
        'hero1_title' => 'Descubra a Londres dos Seus Sonhos',
        'hero1_subtitle' => 'Experiências autênticas e inesquecíveis na cidade mais fascinante do mundo, guiadas por uma especialista certificada.',
        'hero1_cta' => 'Conheça os Tours',

        // Features
        'feature1_title' => 'Roteiros Exclusivos',
        'feature1_desc' => 'Itinerários sob medida para seus interesses',
        'feature2_title' => 'Guia Certificada',
        'feature2_desc' => 'Profissional reconhecida oficialmente',
        'feature3_title' => 'Grupos Pequenos',
        'feature3_desc' => 'Máximo 6 pessoas para experiência intimista',
        'feature4_title' => 'Suporte Total',
        'feature4_desc' => 'Assistência em português durante toda viagem',

        // Hero Slide 2
        'hero2_title' => 'Os Mais Populares do Momento',
        'hero2_subtitle' => 'Experiências cuidadosamente selecionadas que encantam nossos visitantes. Escolha seu tour favorito e comece sua aventura em Londres!',
        'hero2_cta' => 'Ver Todos os Tours',

        // Tours
        'tour_classic' => 'Londres Clássica',
        'tour_pubs' => 'Sabores & Pubs',
        'tour_royalty' => 'Londres Real',
        'tour_harry' => 'Mundo de Harry Potter',

        // Hero Slide 3
        'hero3_title' => 'Viva a Cidade Como um Local',
        'hero3_subtitle' => 'Explore pubs históricos, mercados vibrantes e segredos que só os londrinos conhecem.',
        'hero3_cta' => 'Nossos Serviços',

        // Services Section
        'services_title' => 'Nossos Serviços',
        'services_subtitle' => 'Experiências completas e personalizadas para sua viagem a Londres',

        // Footer
        'footer_tagline' => 'Transformando sua visita a Londres em uma experiência inesquecível',
        'footer_contact' => 'Contato',
        'footer_quick_links' => 'Links Rápidos',
        'footer_follow' => 'Siga-nos',
        'footer_rights' => 'Todos os direitos reservados.',

        // Breadcrumbs
        'breadcrumb_home' => 'Início',
    ],

    'en' => [
        // Header
        'reserve_now' => 'Book Now',
        'change_language' => 'Change language',
        'open_menu' => 'Open menu',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',

        // Menu
        'menu_home' => 'Home',
        'menu_about' => 'About',
        'menu_tours' => 'Tours',
        'menu_services' => 'Services',
        'menu_experience' => 'Experience',

        // Hero Slide 1
        'hero1_title' => 'Discover Your Dream London',
        'hero1_subtitle' => 'Authentic and unforgettable experiences in the world\'s most fascinating city, guided by a certified specialist.',
        'hero1_cta' => 'Explore Tours',

        // Features
        'feature1_title' => 'Exclusive Itineraries',
        'feature1_desc' => 'Tailored routes for your interests',
        'feature2_title' => 'Certified Guide',
        'feature2_desc' => 'Officially recognized professional',
        'feature3_title' => 'Small Groups',
        'feature3_desc' => 'Maximum 6 people for intimate experience',
        'feature4_title' => 'Full Support',
        'feature4_desc' => 'Portuguese assistance throughout your trip',

        // Hero Slide 2
        'hero2_title' => 'Most Popular Right Now',
        'hero2_subtitle' => 'Carefully selected experiences that delight our visitors. Choose your favorite tour and start your London adventure!',
        'hero2_cta' => 'View All Tours',

        // Tours
        'tour_classic' => 'Classic London',
        'tour_pubs' => 'Flavors & Pubs',
        'tour_royalty' => 'Royal London',
        'tour_harry' => 'Harry Potter World',

        // Hero Slide 3
        'hero3_title' => 'Live the City Like a Local',
        'hero3_subtitle' => 'Explore historic pubs, vibrant markets and secrets only Londoners know.',
        'hero3_cta' => 'Our Services',

        // Services Section
        'services_title' => 'Our Services',
        'services_subtitle' => 'Complete and personalized experiences for your London trip',

        // Footer
        'footer_tagline' => 'Turning your London visit into an unforgettable experience',
        'footer_contact' => 'Contact',
        'footer_quick_links' => 'Quick Links',
        'footer_follow' => 'Follow Us',
        'footer_rights' => 'All rights reserved.',

        // Breadcrumbs
        'breadcrumb_home' => 'Home',
    ]
];

// Função helper para traduzir
function t($key) {
    global $translations, $lang;
    return $translations[$lang][$key] ?? $key;
}

// Função para obter idioma oposto (para o botão de troca)
function getOtherLang() {
    global $lang;
    return $lang === 'pt' ? 'en' : 'pt';
}

// Função para obter label do idioma oposto
function getOtherLangLabel() {
    global $lang;
    return $lang === 'pt' ? 'EN' : 'PT';
}

// Função para obter URL de troca de idioma mantendo a página atual
function getLangSwitchUrl() {
    $current_url = $_SERVER['REQUEST_URI'];
    $other_lang = getOtherLang();

    // Parse URL
    $url_parts = parse_url($current_url);
    $path = $url_parts['path'] ?? '';
    $query = $url_parts['query'] ?? '';

    // Parse query string existente
    parse_str($query, $params);

    // Atualizar/adicionar parâmetro lang
    $params['lang'] = $other_lang;

    // Reconstruir URL
    $new_query = http_build_query($params);
    return $path . ($new_query ? '?' . $new_query : '');
}
