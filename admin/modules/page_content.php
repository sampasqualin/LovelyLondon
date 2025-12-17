<?php
/**
 * =========================================================================
 * MÓDULO PAGE_CONTENT - Gerenciar Conteúdo de Páginas
 * =========================================================================
 */

// Verificar que a config foi carregada
if (!defined('DATA_PATH')) {
    require_once __DIR__ . '/../config.php';
}

$op = $_GET['op'] ?? 'list';
$id = $_GET['id'] ?? null;

// Carregar registros
$records = [];
try {
    $result = $db->query("SELECT * FROM page_content");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
        usort($records, function($a, $b) {
            $pageA = $a['page_slug'] ?? '';
            $pageB = $b['page_slug'] ?? '';
            if ($pageA === $pageB) {
                return strnatcmp($a['section_slug'] ?? '', $b['section_slug'] ?? '');
            }
            return strnatcmp($pageA, $pageB);
        });
    }
} catch (Exception $e) {
    $records = [];
}

// Agrupar por página
$pagesBySlug = [];
foreach ($records as $r) {
    $page = $r['page_slug'] ?? 'default';
    if (!isset($pagesBySlug[$page])) {
        $pagesBySlug[$page] = [];
    }
    $pagesBySlug[$page][] = $r;
}

// Carregar registro para edição
$record = null;
if ($op === 'edit' && $id) {
    foreach ($records as $r) {
        if ($r['id'] == $id) {
            $record = $r;
            break;
        }
    }
}

// Páginas disponíveis
$availablePages = [
    'home' => 'Página Inicial',
    'sobre' => 'Página Sobre',
    'services' => 'Página de Serviços',
    'tours' => 'Página de Tours',
    'blog' => 'Página de Blog',
    'galeria' => 'Página de Galeria',
    'contato' => 'Página de Contato',
];

?>

<div class="module-header">
    <h2>📄 Gerenciar Conteúdo de Páginas</h2>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM POR PÁGINAS -->
    <div class="pages-grid">
        <?php foreach ($availablePages as $slug => $label): ?>
            <div class="page-card">
                <h3><?= htmlspecialchars($label) ?></h3>
                <div class="section-list">
                    <?php if (isset($pagesBySlug[$slug])): ?>
                        <?php foreach ($pagesBySlug[$slug] as $item): ?>
                            <div class="section-item">
                                <span class="section-name"><?= htmlspecialchars($item['section_slug'] ?? '') ?></span>
                                <a href="?action=page_content&op=edit&id=<?= urlencode($item['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty">Nenhum conteúdo configurado</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php elseif ($op === 'edit'): ?>
    <!-- FORMULÁRIO DE EDIÇÃO -->
    <div class="form-container">
        <form method="POST" action="<?= BASE_URL ?>/admin/admin_actions.php" enctype="multipart/form-data" class="form">
            <input type="hidden" name="module" value="page_content">
            <input type="hidden" name="_action" value="save">
            <?php if ($record): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
            <?php endif; ?>
            
            <div class="form-section">
                <h3>Localização do Conteúdo</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="page_slug">Página</label>
                        <input type="text" id="page_slug" name="page_slug" readonly value="<?= htmlspecialchars($record['page_slug'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="section_slug">Seção</label>
                        <input type="text" id="section_slug" name="section_slug" readonly value="<?= htmlspecialchars($record['section_slug'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Conteúdo</h3>
                
                <div class="form-group">
                    <label for="content_pt">Conteúdo (Português) *</label>
                    <textarea id="content_pt" name="content_pt" rows="8" required><?= htmlspecialchars($record['content_pt'] ?? '') ?></textarea>
                    <small>Suporta HTML: &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;h1-h6&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;a&gt;, etc.</small>
                </div>
                
                <div class="form-group">
                    <label for="content_en">Conteúdo (Inglês)</label>
                    <textarea id="content_en" name="content_en" rows="8"><?= htmlspecialchars($record['content_en'] ?? '') ?></textarea>
                    <small>Suporta HTML: &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;h1-h6&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;a&gt;, etc.</small>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?action=page_content" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Conteúdo</button>
            </div>
        </form>
    </div>

<?php endif; ?>

<style>
.module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.pages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.page-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.page-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.page-card h3 {
    margin-top: 0;
    color: #700420;
    margin-bottom: 1rem;
    border-bottom: 2px solid #700420;
    padding-bottom: 0.5rem;
}

.section-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.section-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f9f9f9;
    border-radius: 4px;
    border-left: 3px solid #700420;
}

.section-name {
    font-weight: 600;
    color: #555;
    flex: 1;
}

.page-card .btn {
    margin-left: 1rem;
}

.empty {
    color: #999;
    font-style: italic;
    margin: 0;
}

.form-container {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    margin-bottom: 1rem;
    color: #333;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #555;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #700420;
    box-shadow: 0 0 0 3px rgba(112, 4, 32, 0.1);
}

.form-group textarea {
    resize: vertical;
}

.form-group small {
    display: block;
    margin-top: 0.5rem;
    color: #999;
    font-size: 12px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #700420;
    color: white;
}

.btn-primary:hover {
    background: #5a0319;
}

.btn-secondary {
    background: #999;
    color: white;
}

.btn-info {
    background: #0066cc;
    color: white;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 12px;
}
</style>
