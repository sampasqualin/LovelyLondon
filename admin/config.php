<?php
/**
 * =========================================================================
 * LOVELY LONDON - CONFIGURAÇÃO CENTRALIZADA DO ADMIN
 * =========================================================================
 * Todas as configurações, constantes e definições do painel admin
 * Adaptada para funcionar com JSONDatabase
 */

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// PATHS
// =========================================================================
define('ADMIN_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(__DIR__));
define('INCLUDES_PATH', PROJECT_ROOT . '/includes');
define('ASSETS_PATH', PROJECT_ROOT . '/assets');
define('DATA_PATH', PROJECT_ROOT . '/data');
define('UPLOADS_PATH', ASSETS_PATH . '/uploads');

// Incluir image helper para detecção automática de base path
require_once INCLUDES_PATH . '/image_helper.php';

// Base URL do projeto (detectado automaticamente)
define('BASE_URL', detectBasePath());

// =========================================================================
// JSON DATABASE
// =========================================================================
require_once INCLUDES_PATH . '/json_database.php';
$db = new JSONDatabase(DATA_PATH);

// =========================================================================
// PALETA DE CORES LOVELY LONDON
// =========================================================================
if (!defined('LOVELY_COLORS')) {
    define('LOVELY_COLORS', [
        'lovely' => [
            'hex' => '#700420',
            'name' => 'Lovely (Vinho)',
            'rgb' => '112, 4, 32'
        ],
        'blackfriars' => [
            'hex' => '#292828',
            'name' => 'Blackfriars (Preto Suave)',
            'rgb' => '41, 40, 40'
        ],
        'notting-hill' => [
            'hex' => '#955425',
            'name' => 'Notting Hill (Marrom)',
            'rgb' => '149, 84, 37'
        ],
        'skyline' => [
            'hex' => '#DAB59A',
            'name' => 'Skyline (Bege)',
            'rgb' => '218, 181, 154'
        ],
        'thames' => [
            'hex' => '#7FA1C3',
            'name' => 'Thames (Azul)',
            'rgb' => '127, 161, 195'
        ],
        'fog-white' => [
            'hex' => '#F8F9FA',
            'name' => 'Fog White (Branco Névoa)',
            'rgb' => '248, 249, 250'
        ]
    ]);
}

// =========================================================================
// TABELAS DO SISTEMA
// =========================================================================
define('DB_TABLES', [
    'users' => 'users',
    'admin_users' => 'admin_users',
    'tours' => 'tours',
    'services' => 'services',
    'blog_posts' => 'blog_posts',
    'blog_categories' => 'blog_categories',
    'testimonials' => 'testimonials',
    'faqs' => 'faqs',
    'hero_slides' => 'hero_slides',
    'hero_slide_items' => 'hero_slide_items',
    'features' => 'features',
    'gallery_photos' => 'gallery_photos',
    'page_content' => 'page_content',
    'page_sections' => 'page_sections',
    'page_section_items' => 'page_section_items',
    'site_settings' => 'site_settings',
    'seo_metadata' => 'seo_metadata',
    'section_backgrounds' => 'section_backgrounds',
    'contact_submissions' => 'contact_submissions',
    'bookings' => 'bookings',
    'clients' => 'clients',
    'virtual_tour_locations' => 'virtual_tour_locations',
]);

// =========================================================================
// CATEGORIAS DE UPLOAD
// =========================================================================
define('UPLOAD_CATEGORIES', [
    'tours' => 'tours',
    'services' => 'services',
    'blog' => 'blog',
    'hero' => 'hero',
    'testimonials' => 'testimonials',
    'gallery' => 'gallery',
    'clients' => 'clients',
    'backgrounds' => 'backgrounds',
    'logos' => 'logos',
    'about' => 'about',
    'general' => 'general',
]);

// =========================================================================
// TAMANHO MÁXIMO DE UPLOAD
// =========================================================================
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// =========================================================================
// FUNÇÕES AUXILIARES
// =========================================================================

/**
 * Gerar slug a partir de título
 */
function generateSlug($text) {
    // Converter para minúsculas
    $text = strtolower($text);
    
    // Remover acentos
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    
    // Substituir espaços e caracteres especiais por hífen
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    
    // Remover hífens no início e fim
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Definir mensagem flash
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Obter e limpar mensagem flash
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Exibir mensagem flash em HTML
 */
function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $class = $flash['type'] === 'success' ? 'alert-success' : 'alert-' . $flash['type'];
        echo '<div class="alert ' . $class . '">' . htmlspecialchars($flash['message']) . '</div>';
    }
}

/**
 * Verificar autenticação
 */
function requireAuth() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Sanitizar entrada
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Gerar ID único baseado em timestamp e random
 */
function generateId() {
    return time() . '_' . uniqid();
}

/**
 * Deletar arquivo de imagem
 */
function deleteImage($imagePath) {
    if (empty($imagePath)) {
        return true;
    }
    
    // Remover /assets/ do início se existir
    $imagePath = str_replace('/assets/', '', $imagePath);
    
    // Construir caminho completo
    $fullPath = ASSETS_PATH . '/' . $imagePath;
    
    // Deletar arquivo
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    
    return true;
}

/**
 * Deletar arquivo de vídeo
 */
function deleteVideo($videoPath) {
    if (empty($videoPath)) {
        return true;
    }
    
    // Remover /assets/ do início se existir
    $videoPath = str_replace('/assets/', '', $videoPath);
    
    // Construir caminho completo
    $fullPath = ASSETS_PATH . '/' . $videoPath;
    
    // Deletar arquivo
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    
    return true;
}

/**
 * Gerar array de dados com timestamps
 */
function prepareData($data) {
    $now = date('Y-m-d H:i:s');
    
    if (isset($data['id']) && !empty($data['id'])) {
        // UPDATE
        $data['updated_at'] = $now;
    } else {
        // CREATE
        $data['id'] = generateId();
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
    }
    
    return $data;
}

/**
 * Gerar slug único para um registro
 */
function generateUniqueSlug($table, $slug, $excludeId = null) {
    global $db;
    
    $originalSlug = $slug;
    $counter = 1;
    
    // Verificar se slug existe
    $records = $db->query("SELECT * FROM " . $table)->fetchAll(PDO::FETCH_ASSOC);
    
    while (true) {
        $exists = false;
        foreach ($records as $record) {
            if ($record['slug'] === $slug && ($excludeId === null || $record['id'] !== $excludeId)) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            break;
        }
        
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

// =========================================================================
// FUNÇÃO PARA PEGAR CONFIGURAÇÕES DO SITE
// =========================================================================
function getSiteSettings() {
    global $db;
    
    $settings = [];
    
    try {
        $rows = $db->query("SELECT * FROM site_settings")->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) {
        // Retornar array vazio se erro
    }
    
    return $settings;
}

?>
