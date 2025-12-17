<?php
/**
 * =========================================================================
 * UPLOAD HANDLER - Gerenciamento de uploads de imagens
 * =========================================================================
 */

require_once __DIR__ . '/config.php';

// Verificar autenticação
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json');

// Validar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

// Validar arquivo
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'Arquivo muito grande (exceeds upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'Arquivo muito grande (exceeds MAX_FILE_SIZE)',
        UPLOAD_ERR_PARTIAL => 'Upload interrompido',
        UPLOAD_ERR_NO_FILE => 'Nenhum arquivo enviado',
        UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário ausente',
        UPLOAD_ERR_CANT_WRITE => 'Erro ao escrever arquivo',
        UPLOAD_ERR_EXTENSION => 'Upload bloqueado pela extensão',
    ];
    
    $error = isset($_FILES['image']['error']) ? $errors[$_FILES['image']['error']] : 'Erro desconhecido';
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

$file = $_FILES['image'];

// Validar tipo de arquivo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG, WebP ou GIF']);
    exit;
}

// Validar extensão
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
    echo json_encode(['success' => false, 'message' => 'Extensão não permitida']);
    exit;
}

// Validar tamanho
if ($file['size'] > MAX_UPLOAD_SIZE) {
    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Tamanho máximo: 5MB']);
    exit;
}

// Obter categoria
$category = $_POST['category'] ?? 'general';
if (!isset(UPLOAD_CATEGORIES[$category])) {
    $category = 'general';
}

// Criar diretório se não existir
$uploadDir = UPLOADS_PATH . '/' . $category . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Gerar nome único
$filename = 'img_' . uniqid() . '_' . time() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Mover arquivo
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar arquivo']);
    exit;
}

// Redimensionar imagem se necessário
resizeImage($filepath, 1920, 1080);

// Gerar caminho relativo (sem base_path, apenas categoria/arquivo)
// O base_path será adicionado automaticamente pelo helper no frontend
$relativePath = $category . '/' . $filename;

echo json_encode([
    'success' => true,
    'message' => 'Upload realizado com sucesso',
    'filename' => $filename,
    'path' => $relativePath,
    'url' => $relativePath
]);
exit;

/**
 * Redimensionar imagem
 */
function resizeImage($filepath, $maxWidth, $maxHeight) {
    try {
        // Obter dimensões
        list($width, $height, $type) = getimagesize($filepath);
        
        // Verificar se precisa redimensionar
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }
        
        // Calcular novas dimensões
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        
        // Criar imagem redimensionada
        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        
        // Carregar imagem original
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filepath);
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($filepath);
                } else {
                    return true; // WebP não suportado, manter original
                }
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($filepath);
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
                break;
            default:
                return true;
        }
        
        if (!$image) {
            return true;
        }
        
        // Copiar e redimensionar
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Salvar imagem redimensionada
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumb, $filepath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumb, $filepath, 8);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    imagewebp($thumb, $filepath, 85);
                }
                break;
            case IMAGETYPE_GIF:
                imagegif($thumb, $filepath);
                break;
        }
        
        imagedestroy($thumb);
        imagedestroy($image);
        
        return true;
        
    } catch (Exception $e) {
        // Erro ao redimensionar, manter original
        return true;
    }
}

?>
