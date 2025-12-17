<?php
/**
 * CONTENT HELPERS - COMPATÍVEL COM JSONDatabase
 * Funções helper para buscar conteúdo do database JSON
 */

if (file_exists(__DIR__ . '/db_connection.php')) {
    require_once __DIR__ . '/db_connection.php';
}
require_once __DIR__ . '/lang.php';

/**
 * Helper para verificar se database está disponível
 */
function isDatabaseAvailable() {
    global $pdo;
    return isset($pdo);
}

/**
 * Permite usar tags <span> com formatação em textos do hero
 * Exemplo: "Come with me! [skyline]London[/skyline] is Lovely"
 * Converte para: "Come with me! <span style=\"color: var(--skyline);\">London</span> is Lovely"
 */
function formatHeroText($text) {
    if (!$text) return '';
    
    // Mapeamento de tags para cores da paleta
    $colors = [
        'skyline' => '#DAB59A',
        'lovely' => '#700420',
        'blackfriars' => '#292828',
        'notting-hill' => '#955425',
        'thames' => '#7FA1C3'
    ];
    
    // Substituir [cor]texto[/cor] por <span style="color: var(--cor);">texto</span>
    foreach ($colors as $color => $hex) {
        $text = preg_replace(
            '/\[' . $color . '\](.*?)\[\/' . $color . '\]/i',
            '<span style="color: var(--' . $color . ');">$1</span>',
            $text
        );
    }
    
    return $text;
}

/**
 * Busca tours publicados do database
 */
function getTours($limit = null, $featured_only = false) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM tours WHERE is_active = 1";
        
        if ($featured_only) {
            $sql .= " AND is_featured = 1";
        }
        
        $sql .= " ORDER BY display_order ASC, created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log("Erro ao buscar tours: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca um tour específico por slug
 */
function getTourBySlug($slug) {
    if (!isDatabaseAvailable()) {
        return null;
    }

    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Erro ao buscar tour: " . $e->getMessage());
        return null;
    }
}

/**
 * Busca services ativos do database (JSON ou MySQL)
 */
function getServices($limit = null) {
    // Tentar carregar do JSON primeiro
    $json_file = __DIR__ . '/../data/services.json';
    if (file_exists($json_file)) {
        $json = file_get_contents($json_file);
        $services = json_decode($json, true);
        if (is_array($services)) {
            // Filtrar por ativos
            $services = array_filter($services, function($s) {
                return $s['is_active'] == 1;
            });
            // Ordenar por display_order
            uasort($services, function($a, $b) {
                return $a['display_order'] - $b['display_order'];
            });
            // Limitar se necessário
            if ($limit) {
                $services = array_slice($services, 0, $limit, true);
            }
            return array_values($services);
        }
    }
    
    // Fallback para MySQL se JSON não existir
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erro ao buscar services: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca posts publicados do blog
 */
function getBlogPosts($limit = null, $featured_only = false) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM blog_posts WHERE status = 'published'";

        if ($featured_only) {
            $sql .= " AND is_featured = 1";
        }

        $sql .= " ORDER BY published_at DESC, created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erro ao buscar posts: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca um post específico por slug
 */
function getPostBySlug($slug) {
    if (!isDatabaseAvailable()) {
        return null;
    }

    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Erro ao buscar post: " . $e->getMessage());
        return null;
    }
}

/**
 * Busca testimonials publicados
 */
function getTestimonials($limit = null, $featured_only = false) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM testimonials WHERE status = 'approved' AND (is_active IS NULL OR is_active = 1)";

        if ($featured_only) {
            $sql .= " AND is_featured = 1";
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->query($sql);
        $stmt->execute();
        $testimonials = $stmt->fetchAll();
        
        // Enriquecer com dados do tour se existir
        foreach ($testimonials as &$testimonial) {
            if (!empty($testimonial['tour_id'])) {
                $tour = getTourById($testimonial['tour_id']);
                if ($tour) {
                    $testimonial['tour_name_pt'] = $tour['title_pt'] ?? '';
                    $testimonial['tour_name_en'] = $tour['title_en'] ?? '';
                }
            }
        }
        
        return $testimonials;
    } catch (Exception $e) {
        error_log("Erro ao buscar testimonials: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca um tour por ID
 */
function getTourById($id) {
    if (!isDatabaseAvailable()) {
        return null;
    }

    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Busca FAQs ativos
 */
function getFAQs($limit = null) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erro ao buscar FAQs: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca fotos da galeria publicadas
 */
function getGalleryPhotos($limit = null, $category = null) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM gallery_photos WHERE is_published = 1";

        if ($category) {
            $sql .= " AND category = ?";
        }

        $sql .= " ORDER BY display_order ASC, created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        if ($category) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category]);
            return $stmt->fetchAll();
        } else {
            $stmt = $pdo->query($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    } catch (Exception $e) {
        error_log("Erro ao buscar galeria: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca hero slides ativos com items
 */
function getHeroSlides() {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY display_order ASC";
        $stmt = $pdo->query($sql);
        $stmt->execute();
        $slides = $stmt->fetchAll();

        // Buscar items de cada slide
        foreach ($slides as &$slide) {
            $items_sql = "SELECT * FROM hero_slide_items WHERE slide_id = ? ORDER BY display_order ASC";
            $items_stmt = $pdo->prepare($items_sql);
            $items_stmt->execute([$slide['id']]);
            $slide['items'] = $items_stmt->fetchAll();
        }

        return $slides;
    } catch (Exception $e) {
        error_log("Erro ao buscar hero slides: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca page sections de uma página específica
 */
function getPageSections($page_name) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM page_sections WHERE page_name = ? AND is_active = 1 ORDER BY section_order ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$page_name]);
        $sections = $stmt->fetchAll();

        // Buscar items de cada section
        foreach ($sections as &$section) {
            $items_sql = "SELECT * FROM page_section_items WHERE section_id = ? ORDER BY item_order ASC";
            $items_stmt = $pdo->prepare($items_sql);
            $items_stmt->execute([$section['id']]);
            $section['items'] = $items_stmt->fetchAll();
        }

        return $sections;
    } catch (Exception $e) {
        error_log("Erro ao buscar page sections: " . $e->getMessage());
        return [];
    }
}

/**
 * Retorna conteúdo multilíngue baseado no idioma atual
 */
function getContent($item, $field) {
    global $lang;

    if (!is_array($item)) {
        return '';
    }

    $field_with_lang = $field . '_' . $lang;

    // Retorna no idioma atual, ou fallback para PT se EN estiver vazio
    if (isset($item[$field_with_lang]) && !empty(trim($item[$field_with_lang]))) {
        return $item[$field_with_lang];
    } elseif (isset($item[$field . '_pt'])) {
        return $item[$field . '_pt'];
    }

    return '';
}

/**
 * Formata texto de post em HTML sem depender do editor
 * - Converte quebras de linha duplas em parágrafos
 * - Converte listas com prefixo "- ", "* " e "1. " em <ul>/<ol>
 * - Suporte simples a headings estilo markdown: "# ", "## ", "### "
 * - Auto-link para URLs http/https
 * Se já contiver tags HTML de bloco, retorna como está.
 */
function formatPostContent($text)
{
    if (empty($text)) return '';

    // Se já parece HTML de bloco, não reformatar
    if (preg_match('/<\s*(p|h[1-6]|ul|ol|li|br|blockquote|figure|img)\b/i', $text)) {
        return $text;
    }

    // Normalizar quebras de linha
    $text = str_replace(["\r\n", "\r"], "\n", $text);

    // Escapar para evitar XSS
    $safe = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    // Auto-link de URLs
    $safe = preg_replace('~(https?://[^\s<]+)~i', '<a href="$1" target="_blank" rel="noopener">$1</a>', $safe);

    $lines = explode("\n", $safe);
    $html = '';
    $in_ul = false; $in_ol = false; $p_buf = [];

    $flush_paragraph = function() use (&$p_buf, &$html) {
        if (!empty($p_buf)) {
            $html .= '<p>' . implode('<br>', $p_buf) . '</p>' . "\n";
            $p_buf = [];
        }
    };

    foreach ($lines as $line) {
        $trim = trim($line);

        if ($trim === '') {
            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
            $flush_paragraph();
            continue;
        }

        // Headings markdown simples
        if (preg_match('/^(#{1,3})\s+(.*)$/', $trim, $m)) {
            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
            $flush_paragraph();
            $level = strlen($m[1]);
            $tag = $level === 1 ? 'h2' : ($level === 2 ? 'h3' : 'h4');
            $html .= '<' . $tag . '>' . $m[2] . '</' . $tag . '>' . "\n";
            continue;
        }

        // Lista não ordenada
        if (preg_match('/^[-*]\s+(.*)$/', $trim, $m)) {
            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
            if (!$in_ul) { $flush_paragraph(); $html .= "<ul>\n"; $in_ul = true; }
            $html .= '<li>' . $m[1] . '</li>' . "\n";
            continue;
        }

        // Lista ordenada
        if (preg_match('/^\d+\.\s+(.*)$/', $trim, $m)) {
            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if (!$in_ol) { $flush_paragraph(); $html .= "<ol>\n"; $in_ol = true; }
            $html .= '<li>' . $m[1] . '</li>' . "\n";
            continue;
        }

        // Linha de parágrafo
        $p_buf[] = $trim;
    }

    // Fechar blocos abertos
    if ($in_ul) { $html .= "</ul>\n"; }
    if ($in_ol) { $html .= "</ol>\n"; }
    if (!empty($p_buf)) { $html .= '<p>' . implode('<br>', $p_buf) . '</p>' . "\n"; }

    return $html;
}

/**
 * Decodifica campo JSON do database
 */
function decodeJSON($json) {
    if (empty($json)) {
        return [];
    }

    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Formata data para exibição
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return '';
    }

    try {
        $timestamp = strtotime($date);
        return date($format, $timestamp);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Renderiza estrelas de rating
 */
function renderStars($rating) {
    $html = '<div class="rating-stars">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<span class="star filled">★</span>';
        } else {
            $html .= '<span class="star">☆</span>';
        }
    }
    $html .= '</div>';
    return $html;
}

/**
 * Busca clientes ativos
 */
function getClients($limit = null) {
    // Tentar carregar do JSON primeiro
    $json_file = __DIR__ . '/../data/clients.json';
    if (file_exists($json_file)) {
        $json = file_get_contents($json_file);
        $clients = json_decode($json, true);
        if (is_array($clients)) {
            // Filtrar por ativos
            $clients = array_filter($clients, function($c) {
                return $c['is_active'] == 1;
            });
            // Ordenar por display_order
            uasort($clients, function($a, $b) {
                return $a['display_order'] - $b['display_order'];
            });
            // Limitar se necessário
            if ($limit) {
                $clients = array_slice($clients, 0, $limit, true);
            }
            return array_values($clients);
        }
    }
    
    // Fallback para MySQL se JSON não existir
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM clients WHERE is_active = 1 ORDER BY display_order ASC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $pdo->query($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erro ao buscar clientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Trunca texto com ellipsis
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . $suffix;
}

/**
 * =========================================================================
 * GETYOURGUIDE WIDGETS - Funções para gerenciar widgets de afiliação
 * =========================================================================
 */

/**
 * Busca widgets do GetYourGuide
 * @param array $filters Filtros: category, type, search
 * @param int $limit Limite de resultados
 * @return array Lista de widgets
 */
function getGetYourGuideWidgets($filters = [], $limit = null) {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $sql = "SELECT * FROM getyourguide_widgets WHERE is_active = 1";
        $params = [];

        // Filtro por categoria
        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }

        // Filtro por tipo
        if (!empty($filters['type'])) {
            $sql .= " AND type = ?";
            $params[] = $filters['type'];
        }

        // Busca por texto
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $sql .= " AND (title LIKE ? OR description LIKE ? OR tags LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $widgets = $stmt->fetchAll();

        // Decodificar widget_config JSON
        foreach ($widgets as &$widget) {
            if (isset($widget['widget_config']) && is_string($widget['widget_config'])) {
                $widget['widget_config'] = json_decode($widget['widget_config'], true);
            }
            if (isset($widget['tags']) && is_string($widget['tags'])) {
                $widget['tags'] = json_decode($widget['tags'], true);
            }
        }

        return $widgets;
    } catch (Exception $e) {
        error_log("Erro ao buscar widgets GetYourGuide: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca categorias únicas de widgets
 * @return array Lista de categorias
 */
function getWidgetCategories() {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT DISTINCT category, category_pt, category_en FROM getyourguide_widgets WHERE is_active = 1 ORDER BY category_pt");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erro ao buscar categorias: " . $e->getMessage());
        return [];
    }
}

/**
 * Busca tipos únicos de widgets
 * @return array Lista de tipos
 */
function getWidgetTypes() {
    if (!isDatabaseAvailable()) {
        return [];
    }

    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT DISTINCT type, type_pt, type_en FROM getyourguide_widgets WHERE is_active = 1 ORDER BY type_pt");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erro ao buscar tipos: " . $e->getMessage());
        return [];
    }
}

/**
 * Renderiza widget do GetYourGuide
 * @param array $widget Dados do widget
 * @return string HTML do widget
 */
function renderGetYourGuideWidget($widget) {
    $config = $widget['widget_config'];

    $html = '<div class="gyg-widget-wrapper"
                  data-gyg-href="' . htmlspecialchars($config['href']) . '"
                  data-gyg-locale-code="' . htmlspecialchars($config['locale']) . '"
                  data-gyg-widget="' . htmlspecialchars($config['widget_type']) . '"
                  data-gyg-number-of-items="' . htmlspecialchars($config['number_of_items']) . '"
                  data-gyg-partner-id="' . htmlspecialchars($config['partner_id']) . '"
                  data-gyg-tour-ids="' . htmlspecialchars($config['tour_ids']) . '">';
    $html .= '<span>Powered by <a target="_blank" rel="sponsored" href="https://www.getyourguide.com/london-l57/">GetYourGuide</a></span>';
    $html .= '</div>';

    return $html;
}
?>
