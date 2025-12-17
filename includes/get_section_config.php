<?php
/**
 * =========================================================================
 * BUSCAR CONFIGURAÇÕES DE SEÇÕES (BACKGROUNDS, LOGOS, TEXT COLORS)
 * =========================================================================
 * Retorna configurações dinâmicas do banco de dados
 */

// Incluir conexão PDO se não estiver disponível
if (!isset($pdo)) {
    require_once(__DIR__ . '/pdo_connection.php');
}

/**
 * Buscar configuração de uma seção específica
 */
function getSectionConfig($sectionName) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM section_backgrounds WHERE section_name = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$sectionName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao buscar configuração da seção {$sectionName}: " . $e->getMessage());
        return null;
    }
}

/**
 * Obter logo customizado de uma seção (header ou footer)
 */
function getCustomLogo($sectionName, $default = null) {
    $config = getSectionConfig($sectionName);
    
    if ($config && !empty($config['custom_logo'])) {
        return $config['custom_logo'];
    }
    
    return $default;
}

/**
 * Obter cor do texto de uma seção
 */
function getSectionTextColor($sectionName, $default = null) {
    $config = getSectionConfig($sectionName);
    
    if ($config && !empty($config['text_color'])) {
        return $config['text_color'];
    }
    
    return $default;
}
