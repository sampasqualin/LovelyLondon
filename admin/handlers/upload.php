<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    http_response_code(401);
    exit;
}

header('Content-Type: application/json');

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
    echo json_encode(['success' => false, 'message' => 'Erro no upload']);
    exit;
}

$file = $_FILES['image'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
    echo json_encode(['success' => false, 'message' => 'Tipo não permitido']);
    exit;
}

$type = $_POST['type'] ?? 'general';
$uploadDir = __DIR__ . "/../../assets/uploads/{$type}/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$filename = time() . '_' . uniqid() . '.' . $ext;
$path = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $path)) {
    echo json_encode([
        'success' => true,
        'message' => 'Upload realizado',
        'path' => "/assets/uploads/{$type}/{$filename}"
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
}
