<?php
/**
 * =========================================================================
 * ADMIN ABOUT ACTIONS - Processar edições de textos do About
 * =========================================================================
 */

require_once __DIR__ . '/config.php';
requireAuth();

/**
 * Função para processar upload de imagem
 */
function handleImageUpload($file, $subfolder) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Tipo de arquivo não permitido'];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'Arquivo muito grande (máximo 5MB)'];
    }
    
    // Criar diretório
    $uploadDir = UPLOADS_PATH . '/' . $subfolder . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Gerar nome único
    $filename = 'img_' . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Mover arquivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Erro ao salvar arquivo'];
    }
    
    return [
        'success' => true,
        'path' => '/assets/uploads/' . $subfolder . '/' . $filename
    ];
}

try {
    $section = $_POST['section'] ?? null;
    
    if (!$section) {
        throw new Exception('Seção não especificada');
    }
    
    // Carregar dados atuais
    $jsonFile = DATA_PATH . '/about_content.json';
    $aboutData = [];
    
    if (file_exists($jsonFile)) {
        $json = file_get_contents($jsonFile);
        $aboutData = json_decode($json, true) ?? [];
    }
    
    // Atualizar dados conforme a seção
    if ($section === 'about_carol' || $section === 'about_lovely_london') {
        // Processar upload de imagem se houver
        $imagePath = $aboutData[$section]['image'] ?? '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handleImageUpload($_FILES['image'], 'sobre');
            if ($uploadResult['success']) {
                // Deletar imagem antiga se houver
                if (!empty($_POST['image_old']) && $_POST['image_old'] !== $imagePath) {
                    $oldImagePath = PROJECT_ROOT . $_POST['image_old'];
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
                $imagePath = $uploadResult['path'];
            }
        } elseif (!empty($_POST['image_old'])) {
            $imagePath = $_POST['image_old'];
        }
        
        $aboutData[$section] = [
            'title_pt' => $_POST['title_pt'] ?? '',
            'title_en' => $_POST['title_en'] ?? '',
            'subtitle_pt' => $_POST['subtitle_pt'] ?? '',
            'subtitle_en' => $_POST['subtitle_en'] ?? '',
            'content_pt' => $_POST['content_pt'] ?? '',
            'content_en' => $_POST['content_en'] ?? '',
            'image' => $imagePath
        ];
    } elseif ($section === 'social_links') {
        $aboutData[$section] = [
            'instagram' => $_POST['instagram'] ?? '',
            'facebook' => $_POST['facebook'] ?? '',
            'tiktok' => $_POST['tiktok'] ?? ''
        ];
    }
    
    // Salvar no JSON
    $json = json_encode($aboutData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($jsonFile, $json) === false) {
        throw new Exception('Erro ao salvar arquivo JSON');
    }
    
    // Mensagem de sucesso
    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Conteúdo "' . ucfirst($section) . '" atualizado com sucesso!'
    ];
    
    header('Location: ' . BASE_URL . '/admin/index.php?action=about');
    exit;
    
} catch (Exception $e) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => $e->getMessage()
    ];
    header('Location: ' . BASE_URL . '/admin/index.php?action=about');
    exit;
}
?>
