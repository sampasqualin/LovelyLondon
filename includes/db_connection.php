<?php

// =============================================================================
// LOVELY LONDON - CONEXÃO COM A BASE DE DADOS (JSON)
// =============================================================================
// Agora usando JSONDatabase em vez de MySQL

require_once __DIR__ . '/pdo_connection.php';

// Para compatibilidade com código antigo, defina $conn como alias para $pdo
$conn = $pdo;

?>
