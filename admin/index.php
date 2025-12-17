<?php
require_once __DIR__ . '/config.php';
requireAuth();

// Módulos disponíveis
$modules = [
    ['name' => 'Sobre', 'icon' => '📖', 'action' => 'about'],
    ['name' => 'Serviços', 'icon' => '🔧', 'action' => 'services'],
    ['name' => 'Tours', 'icon' => '🗺️', 'action' => 'tours'],
    ['name' => 'Widgets GetYourGuide', 'icon' => '🎫', 'action' => 'getyourguide_widgets'],
    ['name' => 'Blog', 'icon' => '📝', 'action' => 'blog_posts'],
    ['name' => 'Depoimentos', 'icon' => '💬', 'action' => 'testimonials'],
    ['name' => 'FAQ', 'icon' => '❓', 'action' => 'faqs'],
    ['name' => 'Clientes', 'icon' => '👥', 'action' => 'clients'],
    ['name' => 'Galeria', 'icon' => '🖼️', 'action' => 'gallery_photos'],
    ['name' => 'Hero Slides', 'icon' => '🎬', 'action' => 'hero_slides'],
    ['name' => 'Backgrounds', 'icon' => '🎨', 'action' => 'backgrounds'],
    ['name' => 'SEO & Meta Tags', 'icon' => '🔍', 'action' => 'seo_metadata'],
    ['name' => 'Configurações Gerais', 'icon' => '⚙️', 'action' => 'site_settings'],
];

$action = $_GET['action'] ?? null;
$op = $_GET['op'] ?? 'list';
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Lovely London</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="index.php" class="navbar-brand">✨ Lovely London Admin</a>
            <div class="navbar-menu">
                <a href="../index.php" target="_blank">Ver Site</a>
                <a href="logout.php" class="navbar-logout">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (!$action): ?>
            <!-- Dashboard Principal -->
            <div class="header fade-in">
                <h1>🔒 Painel de Administração</h1>
                <div class="header-links">
                    <span class="text-muted">Bem-vindo!</span>
                </div>
            </div>

            <div class="modules-grid fade-in">
                <?php foreach ($modules as $module): ?>
                    <a href="?action=<?= $module['action'] ?>" class="module-card">
                        <div class="module-card-icon"><?= $module['icon'] ?></div>
                        <div class="module-card-name"><?= $module['name'] ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Conteúdo do módulo -->
            <div class="fade-in">
                <a href="?" class="btn btn-secondary mb-3">← Voltar ao Dashboard</a>

                <div class="content slide-down">
                    <?php
                        $allowed_modules = ['about', 'services', 'tours', 'getyourguide_widgets', 'blog_posts', 'testimonials', 'faqs', 'clients', 'gallery_photos', 'hero_slides', 'backgrounds', 'seo_metadata', 'site_settings'];
                        if (in_array($action, $allowed_modules)) {
                            $module_file = "modules/{$action}.php";
                            if (file_exists($module_file)) {
                                include $module_file;
                            } else {
                                echo '<div class="alert alert-error">Arquivo do módulo não encontrado: ' . htmlspecialchars($module_file) . '</div>';
                            }
                        } else {
                            echo '<div class="alert alert-error">Módulo inválido: ' . htmlspecialchars($action) . '</div>';
                        }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Lovely London by Carol - Admin Panel</p>
    </footer>
</body>
</html>
