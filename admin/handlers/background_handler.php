<?php
/**
 * =========================================================================
 * BACKGROUND HANDLER - Gerenciamento de backgrounds de seções
 * =========================================================================
 */

require_once __DIR__ . '/../config.php';
requireAuth();

header('Content-Type: application/json');

// Validar ação
$action = $_POST['action'] ?? null;

if ($action !== 'update') {
    echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    exit;
}

try {
    // Obter dados
    $section = sanitize($_POST['section'] ?? '');
    $backgroundType = sanitize($_POST['background_type'] ?? 'color');
    $backgroundColor = sanitize($_POST['background_color'] ?? '#ffffff');
    $textColor = sanitize($_POST['text_color'] ?? '#000000');
    $imageOpacity = (float)($_POST['image_opacity'] ?? 1);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($section)) {
        throw new Exception('Seção não identificada');
    }
    
    // Processar upload de imagem
    $backgroundImage = null;
    if ($backgroundType === 'image' || $backgroundType === 'both') {
        if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['background_image'], 'backgrounds', $section . '-bg');
            if ($uploadResult['success']) {
                $backgroundImage = $uploadResult['path'];
            } else {
                throw new Exception($uploadResult['message']);
            }
        } elseif ($backgroundType === 'image' && empty($_POST['current_background_image'])) {
            throw new Exception('É necessário fazer upload de uma imagem para este tipo de fundo');
        } else {
            $backgroundImage = sanitize($_POST['current_background_image'] ?? '');
        }
    }
    
    // Preparar dados
    $sectionData = [
        'section' => $section,
        'background_type' => $backgroundType,
        'background_color' => $backgroundColor,
        'background_image' => $backgroundImage,
        'image_opacity' => $imageOpacity,
        'text_color' => $textColor,
        'is_active' => $isActive,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Salvar configuração
    $sections = $db->query("SELECT * FROM section_backgrounds")->fetchAll(PDO::FETCH_ASSOC);
    $found = false;
    
    foreach ($sections as $key => $sec) {
        if ($sec['section'] === $section) {
            // Deletar imagem antiga se uma nova for enviada
            if ($backgroundImage && !empty($sec['background_image']) && $sec['background_image'] !== $backgroundImage) {
                deleteImage($sec['background_image']);
            }
            $sections[$key] = array_merge($sec, $sectionData);
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $sectionData['id'] = generateId();
        $sectionData['created_at'] = date('Y-m-d H:i:s');
        $sections[] = $sectionData;
    }
    
    // Gerar CSS
    generateBackgroundCSS($sections);
    
    echo json_encode([
        'success' => true,
        'message' => 'Background da seção atualizado com sucesso'
    ]);
    exit;
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

/**
 * Processar upload de imagem com nome fixo
 */
function handleImageUpload($file, $category, $fixedName) {
    // Validar
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'Arquivo muito grande'];
    }
    
    // Criar diretório
    $uploadDir = UPLOADS_PATH . '/' . $category . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Usar nome fixo
    $filename = $fixedName . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Deletar arquivo antigo se existir
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    // Mover arquivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Erro ao salvar arquivo'];
    }
    
    // Retornar caminho relativo (categoria/arquivo)
    $relativePath = $category . '/' . $filename;

    return [
        'success' => true,
        'path' => $relativePath,
        'url' => $relativePath
    ];
}

/**
 * Gerar CSS para backgrounds
 */
function generateBackgroundCSS($sections) {
    $css = "/* Auto-generated CSS for Section Backgrounds */\n\n";
    
    foreach ($sections as $section) {
        if (!$section['is_active']) {
            continue;
        }
        
        $sectionName = $section['section'];
        $selector = '.section-' . $sectionName . ', #' . $sectionName . '-section';
        
        $css .= "/* Section: {$sectionName} */\n";
        $css .= "{$selector} {\n";
        
        // Background
        if ($section['background_type'] === 'color') {
            $css .= "    background-color: {$section['background_color']};\n";
        } elseif ($section['background_type'] === 'image') {
            if ($section['background_image']) {
                $css .= "    background-image: url('{$section['background_image']}');\n";
                $css .= "    background-size: cover;\n";
                $css .= "    background-position: center;\n";
                $css .= "    opacity: {$section['image_opacity']};\n";
            }
        } elseif ($section['background_type'] === 'both') {
            if ($section['background_image']) {
                $css .= "    background-image: url('{$section['background_image']}');\n";
                $css .= "    background-size: cover;\n";
                $css .= "    background-position: center;\n";
            }
            $css .= "    background-color: {$section['background_color']};\n";
        }
        
        // Text color
        if (!empty($section['text_color'])) {
            $css .= "    color: {$section['text_color']};\n";
        }
        
        $css .= "}\n\n";
    }
    
    // Salvar CSS em arquivo
    $cssPath = PROJECT_ROOT . '/assets/css/section-backgrounds.css';
    file_put_contents($cssPath, $css);
}

?>
