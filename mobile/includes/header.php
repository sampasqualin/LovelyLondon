<?php
// mobile/includes/header.php
require_once __DIR__ . '/bootstrap.php';

// Configuração básica do app mobile
$app_title = 'Lovely London - Mobile';

// Logo mobile reutilizando o mesmo helper de logo do site principal
$mobileLogo = getCustomLogo('header', $mobile_base_path . '/../assets/images/logo2.png');

?><!DOCTYPE html>
<html lang="<?php echo $lang === 'en' ? 'en' : 'pt-BR'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($app_title); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $mobile_base_path; ?>/../assets/images/art/favicon.png">

    <!-- CSS mobile principal -->
    <link rel="stylesheet" href="<?php echo $mobile_base_path; ?>/css/app.css">
</head>
<body class="mobile-app">
    <div class="app-shell">
        <header class="app-header">
            <div class="app-header-center">
                <img src="<?php echo htmlspecialchars($mobileLogo); ?>" 
                     alt="Lovely London by Carol" 
                     class="app-header-logo-img">
            </div>
        </header>

        <main class="app-content" id="main-content">
