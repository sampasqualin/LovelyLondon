<?php
/**
 * =========================================================================
 * IMAGE HELPER - Funções auxiliares para gerenciamento de imagens
 * =========================================================================
 * Sistema centralizado para garantir caminhos corretos independente da
 * pasta onde o site está instalado (/v2/, /stg/, raiz, etc.)
 */

/**
 * Detecta automaticamente o base path do projeto
 *
 * @return string Base path (ex: '/v2', '/stg', ou '')
 */
function detectBasePath() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Detecta /v2/
    if (strpos($request_uri, '/v2/') !== false) {
        return '/v2';
    }

    // Detecta /stg/
    if (strpos($request_uri, '/stg/') !== false) {
        return '/stg';
    }

    // Detecta /staging/
    if (strpos($request_uri, '/staging/') !== false) {
        return '/staging';
    }

    // Detecta /dev/
    if (strpos($request_uri, '/dev/') !== false) {
        return '/dev';
    }

    // Raiz (produção)
    return '';
}

/**
 * Retorna o caminho completo da imagem com base_path automático
 *
 * @param string $relativePath Caminho relativo (ex: 'assets/images/logo.png')
 * @param bool $absolute Se true, retorna URL absoluto com domínio
 * @return string Caminho completo
 */
function getImagePath($relativePath, $absolute = false) {
    global $base_path;

    // Se $base_path não estiver definido, detecta automaticamente
    if (!isset($base_path)) {
        $base_path = detectBasePath();
    }

    // Remove barra inicial se existir (para evitar duplicação)
    $relativePath = ltrim($relativePath, '/');

    // Remove base_path se já estiver no caminho (evita duplicação)
    if ($base_path && strpos($relativePath, ltrim($base_path, '/') . '/') === 0) {
        $relativePath = substr($relativePath, strlen(ltrim($base_path, '/')) + 1);
    }

    // Constrói caminho
    $path = $base_path . '/' . $relativePath;

    // Se solicitado URL absoluto
    if ($absolute) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'lovelylondonbycarol.com';
        return $protocol . '://' . $host . $path;
    }

    return $path;
}

/**
 * Retorna URL da imagem (alias para getImagePath)
 *
 * @param string $relativePath Caminho relativo
 * @param bool $absolute Se true, retorna URL absoluto
 * @return string URL da imagem
 */
function getImageUrl($relativePath, $absolute = false) {
    return getImagePath($relativePath, $absolute);
}

/**
 * Retorna caminho para imagem de upload do admin
 *
 * @param string $category Categoria do upload (tours, services, blog, etc)
 * @param string $filename Nome do arquivo
 * @param bool $absolute Se true, retorna URL absoluto
 * @return string Caminho completo
 */
function getUploadPath($category, $filename, $absolute = false) {
    return getImagePath("assets/uploads/{$category}/{$filename}", $absolute);
}

/**
 * Processa caminho de imagem do banco de dados
 * Aceita diferentes formatos e normaliza para o formato correto
 *
 * @param string $imagePath Caminho da imagem do banco
 * @param bool $absolute Se true, retorna URL absoluto
 * @return string Caminho completo normalizado
 */
function processImagePath($imagePath, $absolute = false) {
    if (empty($imagePath)) {
        return '';
    }

    global $base_path;

    // Se não tiver $base_path, detecta
    if (!isset($base_path)) {
        $base_path = detectBasePath();
    }

    // Casos possíveis:
    // 1. "tours/arquivo.jpg" (novo formato - apenas categoria/arquivo)
    // 2. "/assets/uploads/tours/arquivo.jpg" (formato antigo - absoluto)
    // 3. "assets/uploads/tours/arquivo.jpg" (formato antigo - relativo)

    // Remove barra inicial se tiver
    $cleanPath = ltrim($imagePath, '/');

    // Se já começa com "assets/uploads/", usa direto
    if (strpos($cleanPath, 'assets/uploads/') === 0) {
        return getImagePath($cleanPath, $absolute);
    }

    // Se é formato novo (categoria/arquivo), adiciona assets/uploads/
    if (preg_match('#^[a-z_]+/[^/]+\.(jpg|jpeg|png|webp|gif)$#i', $cleanPath)) {
        return getImagePath("assets/uploads/{$cleanPath}", $absolute);
    }

    // Fallback: tenta usar como está
    return getImagePath($cleanPath, $absolute);
}

/**
 * Retorna caminho para imagem estática
 *
 * @param string $category Categoria (hero, art, flags, etc)
 * @param string $filename Nome do arquivo
 * @param bool $absolute Se true, retorna URL absoluto
 * @return string Caminho completo
 */
function getStaticImagePath($category, $filename, $absolute = false) {
    return getImagePath("assets/images/{$category}/{$filename}", $absolute);
}

/**
 * Verifica se uma imagem existe
 *
 * @param string $relativePath Caminho relativo da imagem
 * @return bool True se existe
 */
function imageExists($relativePath) {
    $basePath = detectBasePath();
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $fullPath = $documentRoot . $basePath . '/' . ltrim($relativePath, '/');

    return file_exists($fullPath);
}

/**
 * Retorna imagem placeholder se a imagem não existir
 *
 * @param string $relativePath Caminho relativo da imagem
 * @param string $placeholder Caminho do placeholder (opcional)
 * @return string Caminho da imagem ou placeholder
 */
function getImageOrPlaceholder($relativePath, $placeholder = 'assets/images/placeholder.jpg') {
    if (imageExists($relativePath)) {
        return getImagePath($relativePath);
    }
    return getImagePath($placeholder);
}

/**
 * Converte caminho antigo (hardcoded) para caminho dinâmico
 *
 * @param string $oldPath Caminho antigo (ex: '/v2/assets/images/logo.png')
 * @return string Caminho correto
 */
function convertOldPath($oldPath) {
    // Remove /v2/, /stg/, etc do início
    $cleaned = preg_replace('#^/(v2|stg|staging|dev)/#', '', $oldPath);

    // Remove barra inicial se não tiver base_path
    $cleaned = ltrim($cleaned, '/');

    return getImagePath($cleaned);
}

/**
 * Sanitiza nome de arquivo para upload
 *
 * @param string $filename Nome original do arquivo
 * @return string Nome sanitizado
 */
function sanitizeFilename($filename) {
    // Remove caracteres especiais
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

    // Remove múltiplos underscores
    $filename = preg_replace('/_+/', '_', $filename);

    // Converte para lowercase
    $filename = strtolower($filename);

    return $filename;
}

/**
 * Gera nome único para arquivo
 *
 * @param string $originalFilename Nome original
 * @param string $prefix Prefixo opcional
 * @return string Nome único
 */
function generateUniqueFilename($originalFilename, $prefix = 'img') {
    $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    $sanitized = sanitizeFilename(pathinfo($originalFilename, PATHINFO_FILENAME));

    return $prefix . '_' . $sanitized . '_' . uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Retorna dimensões da imagem
 *
 * @param string $relativePath Caminho relativo
 * @return array|false Array com [width, height, type] ou false
 */
function getImageDimensions($relativePath) {
    $basePath = detectBasePath();
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $fullPath = $documentRoot . $basePath . '/' . ltrim($relativePath, '/');

    if (!file_exists($fullPath)) {
        return false;
    }

    $info = getimagesize($fullPath);

    if ($info === false) {
        return false;
    }

    return [
        'width' => $info[0],
        'height' => $info[1],
        'type' => $info[2],
        'mime' => $info['mime']
    ];
}

/**
 * Retorna URL absoluto do site
 *
 * @return string URL base (ex: 'https://lovelylondonbycarol.com/v2')
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'lovelylondonbycarol.com';
    $basePath = detectBasePath();

    return $protocol . '://' . $host . $basePath;
}

?>
