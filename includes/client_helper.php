<?php
/**
 * CLIENT HELPER
 * Funções para gerenciar clientes via JSON
 */

require_once __DIR__ . '/image_helper.php';

/**
 * Get all clients from JSON
 */
function getClients() {
    $file = __DIR__ . '/../data/clients.json';
    
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
    
    return [];
}

/**
 * Get client by ID
 */
function getClient($id) {
    $clients = getClients();
    foreach ($clients as $client) {
        if ($client['id'] == $id) {
            return $client;
        }
    }
    return null;
}

/**
 * Save clients to JSON
 */
function saveClients($clients) {
    $file = __DIR__ . '/../data/clients.json';
    $json = json_encode($clients, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($file, $json) !== false;
}

/**
 * Add new client
 */
function addClient($name, $logo_url, $website_url = null, $display_order = 0, $is_active = 1) {
    $clients = getClients();
    
    // Get next ID
    $maxId = 0;
    foreach ($clients as $client) {
        if ($client['id'] > $maxId) {
            $maxId = $client['id'];
        }
    }
    
    $newClient = [
        'id' => $maxId + 1,
        'name' => $name,
        'logo_url' => $logo_url,
        'website_url' => $website_url,
        'display_order' => $display_order,
        'is_active' => $is_active
    ];
    
    $clients[] = $newClient;
    
    if (saveClients($clients)) {
        return $newClient;
    }
    
    return false;
}

/**
 * Update client
 */
function updateClient($id, $name, $logo_url = null, $website_url = null, $display_order = 0, $is_active = 1) {
    $clients = getClients();
    
    foreach ($clients as &$client) {
        if ($client['id'] == $id) {
            $client['name'] = $name;
            if ($logo_url) {
                $client['logo_url'] = $logo_url;
            }
            $client['website_url'] = $website_url;
            $client['display_order'] = $display_order;
            $client['is_active'] = $is_active;
            break;
        }
    }
    
    return saveClients($clients);
}

/**
 * Delete client
 */
function deleteClient($id) {
    $clients = getClients();
    $clients = array_filter($clients, function($client) use ($id) {
        return $client['id'] != $id;
    });
    $clients = array_values($clients); // Re-index
    return saveClients($clients);
}

/**
 * Handle file upload
 */
function uploadClientLogo($file) {
    if (!isset($file['name']) || empty($file['name'])) {
        return null;
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        throw new Exception('Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WebP');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Arquivo muito grande (máximo 5MB)');
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro ao fazer upload do arquivo');
    }
    
    $uploadDir = dirname(__DIR__) . '/assets/uploads/clients/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Falha ao salvar arquivo');
    }

    // Retornar caminho relativo (categoria/arquivo)
    return 'clients/' . $filename;
}
?>
