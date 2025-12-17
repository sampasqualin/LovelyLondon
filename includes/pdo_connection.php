<?php

// =============================================================================
// LOVELY LONDON - CONEXÃO JSON DATABASE
// =============================================================================
// Usando JSON em vez de MySQL - os dados são salvos em arquivos .json na pasta data/

require_once __DIR__ . '/json_database.php';

// --- Criar a Conexão JSONDatabase ---
$dataPath = dirname(__DIR__) . '/data';

try {
    $pdo = new JSONDatabase($dataPath);
} catch (Exception $e) {
    throw new Exception("Erro ao conectar com o banco de dados JSON: " . $e->getMessage());
}

// Define PDO constants para compatibilidade
if (!defined('PDO::FETCH_ASSOC')) {
    define('PDO::FETCH_ASSOC', 2);
}
if (!defined('PDO::FETCH_BOTH')) {
    define('PDO::FETCH_BOTH', 4);
}
if (!defined('PDO::FETCH_OBJ')) {
    define('PDO::FETCH_OBJ', 5);
}
if (!defined('PDO::ATTR_ERRMODE')) {
    define('PDO::ATTR_ERRMODE', 'errmode');
}
if (!defined('PDO::ERRMODE_EXCEPTION')) {
    define('PDO::ERRMODE_EXCEPTION', 'exception');
}

?>
