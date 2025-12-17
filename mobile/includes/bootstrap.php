<?php
// mobile/includes/bootstrap.php
// Bootstrap compartilhado para a versão mobile

// Caminhos básicos
// __DIR__ = .../mobile/includes -> subimos dois níveis até a raiz v2
$MOBILE_ROOT = dirname(__DIR__);      // .../mobile
$PROJECT_ROOT = dirname($MOBILE_ROOT); // .../v2

// Reaproveita includes e dados da versão principal
require_once $PROJECT_ROOT . '/includes/lang.php';
require_once $PROJECT_ROOT . '/includes/get_section_config.php';
require_once $PROJECT_ROOT . '/includes/content_helpers.php';

// Definições específicas da versão mobile
$mobile_base_path = '/v2/mobile';

// Base path geral (mesmo da versão desktop)
if (!isset($base_path)) {
    $base_path = '/v2';
}

// Página atual (para destacar item do menu/bottom-nav)
$mobile_current_page = basename($_SERVER['PHP_SELF'], '.php');

// Idioma atual (reutiliza mesma lógica se já existir variável $lang)
if (!isset($lang)) {
    $lang = isset($_GET['lang']) && in_array($_GET['lang'], ['pt', 'en']) ? $_GET['lang'] : 'pt';
}

?>