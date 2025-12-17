<?php
// Ensure base_path is defined before any usage
if (!isset($base_path)) { $base_path = ''; }
// Incluir sistema de idiomas
require_once(__DIR__ . '/lang.php');

// Incluir configurações de seções (backgrounds, logos)
require_once(__DIR__ . '/get_section_config.php');

// Incluir helpers de SEO
require_once(__DIR__ . '/seo_helpers.php');

// Buscar logo customizado do header (definido após calcular $base_path)
// Placeholder; redefinido após $base_url
$headerLogo = '';
$headerTextColor = '';

// Configurações da página atual
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Detecta se está na pasta pages/
$is_subpage = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;

// BASE PATH - Detecta automaticamente a pasta atual
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/v2/') !== false) {
    $base_path = '/v2';
} elseif (strpos($request_uri, '/stg/') !== false) {
    $base_path = '/stg';
} else {
    $base_path = '';
}
$base_url = 'https://lovelylondonbycarol.com' . $base_path;
// Agora podemos definir o logo/texto do header
$headerLogo = getCustomLogo('header', $base_path . '/assets/images/logo2.png');
$headerTextColor = getSectionTextColor('header');

// Buscar configuração de SEO (do JSON ou fallback para padrão)
$seo_config = getSEOConfig($current_page, $base_url);

// Extrair variáveis de SEO
$title = $seo_config['title'];
$description = $seo_config['description'];
$keywords = $seo_config['keywords'];
$canonical_url = $seo_config['url'];
$og_image = $seo_config['image'];
$og_type = $seo_config['type'];
$og_title = $seo_config['og_title'] ?? $title;
$og_description = $seo_config['og_description'] ?? $description;
$twitter_card = $seo_config['twitter_card'] ?? 'summary_large_image';
$twitter_title = $seo_config['twitter_title'] ?? $og_title;
$twitter_description = $seo_config['twitter_description'] ?? $og_description;
$robots = $seo_config['robots'] ?? 'index, follow';
$schema_json = $seo_config['schema_json'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- Meta Tags SEO -->
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    <meta name="author" content="Carol, Lovely London by Carol">
    <meta name="robots" content="<?php echo htmlspecialchars($robots); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Hreflang Tags (Bilingual) -->
    <link rel="alternate" hreflang="pt-BR" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <link rel="alternate" hreflang="en" href="<?php echo htmlspecialchars(str_replace($base_path . '/', $base_path . '/en/', $canonical_url)); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($canonical_url); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $base_path; ?>/assets/images/art/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo $base_path; ?>/assets/images/art/favicon.png">
    <meta name="theme-color" content="#700420">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo htmlspecialchars($og_type); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($og_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Lovely London by Carol">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="<?php echo htmlspecialchars($twitter_card); ?>">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($twitter_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($twitter_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <meta name="twitter:creator" content="@lovelylondoncar">

    <?php if (!empty($schema_json)): ?>
    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    <?php echo $schema_json; ?>
    </script>
    <?php endif; ?>

    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Preload Critical Fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@400;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@400;700;800&display=swap"></noscript>

    <!-- CSS Principal -->
    <link rel="preload" href="<?php echo $base_path; ?>/css/main.css" as="style">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/css/main.css">

    <!-- Enhancements (Visual + UX + Accessibility) -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/css/enhancements.css">

    <!-- Mobile Fixes - Correções específicas para dispositivos móveis -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/css/mobile-fixes.css">

    <!-- Modal de Tours/Serviços -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/css/tour-modal.css">

    <!-- CSS Específico por Página - Consolidado -->
    <?php
    $pages_with_custom_css = ['tours', 'blog', 'blog-post', 'services'];

    if (in_array($current_page, $pages_with_custom_css)) {
        echo '<link rel="stylesheet" href="' . $base_path . '/css/pages.css">';
    }

    // CSS específico para página Experience
    if ($current_page === 'experience') {
        echo '<link rel="stylesheet" href="' . $base_path . '/css/experience.css">';
    }
    ?>
    
    <!-- Backgrounds das Seções (Gerado pelo Admin) - DEVE SER O ÚLTIMO CSS! -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/section-backgrounds.css">

    <?php if ($current_page === 'index'): ?>
    <!-- Structured Data - LocalBusiness & TouristInformationCenter -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": ["LocalBusiness", "TouristInformationCenter"],
      "name": "Lovely London by Carol",
      "description": "Guia brasileira certificada em Londres oferecendo tours privados personalizados e experiências autênticas",
      "url": "<?php echo $base_url; ?>",
      "logo": "<?php echo $base_url; ?>/assets/images/logo2.png",
      "image": "<?php echo $base_url; ?>/assets/images/og-image.jpg",
      "telephone": "+44-7950-400919",
      "email": "carol@lovelylondon.com",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "London",
        "addressCountry": "GB"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "51.5074",
        "longitude": "-0.1278"
      },
      "priceRange": "£££",
      "sameAs": [
        "https://www.instagram.com/lovelylondonbycarol/",
        "https://www.facebook.com/lovelylondonbycarol",
        "https://www.tiktok.com/@lovelylondonbycarol",
        "https://twitter.com/lovelylondoncar"
      ],
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        "opens": "09:00",
        "closes": "18:00"
      }
    }
    </script>
    <?php endif; ?>

    <?php if ($current_page === 'tours'): ?>
    <!-- Structured Data - ItemList for Tours -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ItemList",
      "itemListElement": [
        {
          "@type": "TouristAttraction",
          "position": 1,
          "name": "Londres Clássica",
          "description": "Tour pelos pontos turísticos mais icônicos: Big Ben, London Eye, Palácio de Buckingham",
          "image": "https://images.unsplash.com/photo-1526129318478-62ed807ebdf9",
          "offers": {
            "@type": "Offer",
            "price": "89",
            "priceCurrency": "GBP"
          }
        },
        {
          "@type": "TouristAttraction",
          "position": 2,
          "name": "Notting Hill & Mercados",
          "description": "Explore Portobello Road, Camden Market e Borough Market",
          "image": "https://images.unsplash.com/photo-1529139574466-a303027c1d8b",
          "offers": {
            "@type": "Offer",
            "price": "75",
            "priceCurrency": "GBP"
          }
        },
        {
          "@type": "TouristAttraction",
          "position": 3,
          "name": "Londres Histórica",
          "description": "Torre de Londres, Westminster Abbey e St. Paul's Cathedral",
          "image": "https://images.unsplash.com/photo-1513028179155-324cfec2566c",
          "offers": {
            "@type": "Offer",
            "price": "95",
            "priceCurrency": "GBP"
          }
        }
      ]
    }
    </script>
    <?php endif; ?>

    <?php if ($current_page === 'index'): ?>
    <!-- FAQ Schema Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Como funciona a reserva de um tour?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "É muito simples! Entre em contato através do WhatsApp ou pelo formulário de contato, informe suas datas e interesses, e criaremos um roteiro personalizado para você. Após a confirmação, você receberá todos os detalhes por e-mail."
          }
        },
        {
          "@type": "Question",
          "name": "Qual é o tamanho dos grupos?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Trabalhamos principalmente com tours privados e em pequenos grupos (máximo 6 pessoas) para garantir uma experiência mais personalizada e intimista. Isso permite que você aproveite ao máximo cada momento sem pressa."
          }
        },
        {
          "@type": "Question",
          "name": "Os tours incluem ingressos para atrações?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "O valor do tour cobre o serviço de guia e planejamento. Ingressos para museus, monumentos e transporte são pagos separadamente. Posso ajudar na compra antecipada e organização de tudo que você precisar!"
          }
        },
        {
          "@type": "Question",
          "name": "Qual é a política de cancelamento?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Cancelamentos com 48 horas de antecedência têm reembolso total. Entre 24-48h, reembolso de 50%. Menos de 24h não há reembolso. Em caso de emergência ou problemas de saúde, avaliamos caso a caso com flexibilidade."
          }
        },
        {
          "@type": "Question",
          "name": "Posso personalizar completamente meu tour?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Absolutamente! Essa é nossa especialidade. Me conte seus interesses (história, gastronomia, arquitetura, fotografia, compras...) e criarei um roteiro único baseado no que você ama. Cada detalhe é pensado para você."
          }
        },
        {
          "@type": "Question",
          "name": "Os tours são adequados para crianças?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Sim! Adapto o ritmo e o conteúdo para famílias com crianças. Podemos incluir parques, museus interativos e paradas para lanches. A ideia é que todos se divirtam e aprendam juntos!"
          }
        }
      ]
    }
    </script>
    <?php endif; ?>
</head>
<body class="<?php echo $is_subpage ? 'page-inner' : 'page-home'; ?>">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    <header class="header section-header" id="header-section">
        <div class="container header-merged-container">
            <a href="<?php echo $base_path; ?>/index.php" class="logo-link">
                <img src="<?php echo $headerLogo; ?>" alt="Logo Lovely London by Carol" class="logo-img">
            </a>
            <nav class="nav" id="mainNav">
                <a href="<?php echo $base_path; ?>/index.php" class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>"><?php echo t('menu_home'); ?></a>
                <a href="<?php echo $base_path; ?>/pages/sobre.php" class="nav-link <?php echo $current_page === 'sobre' ? 'active' : ''; ?>"><?php echo t('menu_about'); ?></a>
                <a href="<?php echo $base_path; ?>/pages/tours.php" class="nav-link <?php echo $current_page === 'tours' ? 'active' : ''; ?>"><?php echo t('menu_tours'); ?></a>
                <a href="<?php echo $base_path; ?>/pages/services.php" class="nav-link <?php echo $current_page === 'services' ? 'active' : ''; ?>"><?php echo t('menu_services'); ?></a>
                <a href="<?php echo $base_path; ?>/pages/experience.php" class="nav-link <?php echo $current_page === 'experience' ? 'active' : ''; ?>"><?php echo t('menu_experience'); ?></a>
            </nav>
            <div class="header-right-controls">
                <button class="menu-toggle" id="mobileMenuToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="mainNav">
                    <span class="menu-toggle-bar"></span>
                    <span class="menu-toggle-bar"></span>
                    <span class="menu-toggle-bar"></span>
                </button>
                <a href="<?php echo getLangSwitchUrl(); ?>" class="language-toggle desktop-only-lang" aria-label="<?php echo t('change_language'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <span class="language-label"><?php echo getOtherLangLabel(); ?></span>
                </a>
            </div>
        </div>
    </header>

    <!-- Overlay de transição entre páginas -->
    <div class="page-transition-overlay" id="pageTransitionOverlay"></div>


