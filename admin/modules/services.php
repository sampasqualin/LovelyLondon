<?php
/**
 * =========================================================================
 * MÓDULO SERVICES - Gerenciar Serviços
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
    $result = $db->query("SELECT * FROM services");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
        usort($records, function($a, $b) {
            return ($a['display_order'] ?? 0) - ($b['display_order'] ?? 0);
        });
    }
} catch (Exception $e) {
    $records = [];
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

?>

<div class="module-header">
    <h2>🔧 Gerenciar Serviços</h2>
    <?php if ($op !== 'new' && $op !== 'edit'): ?>
        <a href="?action=services&op=new" class="btn btn-primary">+ Novo Serviço</a>
    <?php endif; ?>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Ordem</th>
                    <th>Título (PT)</th>
                    <th>Preço de</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $service): ?>
                <tr>
                    <td><?= $service['display_order'] ?? 0 ?></td>
                    <td><?= htmlspecialchars($service['title_pt'] ?? '') ?></td>
                    <td>£<?= number_format($service['price_from'] ?? 0, 2) ?></td>
                    <td>
                        <?php if ($service['is_active']): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?action=services&op=edit&id=<?= urlencode($service['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="deleteRecord('services', '<?= htmlspecialchars($service['id']) ?>'); return false;">Deletar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($op === 'new' || $op === 'edit'): ?>
    <!-- FORMULÁRIO -->
    <div class="form-container">
        <form method="POST" action="<?= BASE_URL ?>/admin/admin_actions.php" enctype="multipart/form-data" class="form">
            <input type="hidden" name="module" value="services">
            <input type="hidden" name="_action" value="save">
            <?php if ($record): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
            <?php endif; ?>
            
            <div class="form-section">
                <h3>Informações Básicas</h3>
                
                <div class="form-group">
                    <label for="title_pt">Título (Português) *</label>
                    <input type="text" id="title_pt" name="title_pt" required value="<?= htmlspecialchars($record['title_pt'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="title_en">Título (Inglês) *</label>
                    <input type="text" id="title_en" name="title_en" required value="<?= htmlspecialchars($record['title_en'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="slug">URL Slug</label>
                    <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($record['slug'] ?? '') ?>" placeholder="Auto-gerado">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price_from">Preço de (GBP)</label>
                        <input type="number" id="price_from" name="price_from" step="0.01" value="<?= $record['price_from'] ?? 0 ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Ícone (ex: star, heart, zap)</label>
                        <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($record['icon'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Descrição</h3>
                
                <div class="form-group">
                    <label for="description_pt">Descrição (PT)</label>
                    <textarea id="description_pt" name="description_pt" rows="4"><?= htmlspecialchars($record['description_pt'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="description_en">Descrição (EN)</label>
                    <textarea id="description_en" name="description_en" rows="4"><?= htmlspecialchars($record['description_en'] ?? '') ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Imagem</h3>
                
                <?php if ($record && !empty($record['image_url'])): ?>
                <div class="image-preview">
                    <img src="<?= htmlspecialchars($record['image_url']) ?>" alt="Service Image" style="max-width: 200px;">
                    <input type="hidden" name="image_url_old" value="<?= htmlspecialchars($record['image_url']) ?>">
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="image_url">Upload Imagem (JPG, PNG, WebP, GIF - máx. 5MB)</label>
                    <input type="file" id="image_url" name="image_url" accept="image/*">
                </div>
            </div>
            
            <div class="form-section">
                <h3>Configurações</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="display_order">Ordem de Exibição</label>
                        <input type="number" id="display_order" name="display_order" value="<?= $record['display_order'] ?? 0 ?>">
                    </div>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= ($record['is_featured'] ?? 0) ? 'checked' : '' ?>>
                    <label for="is_featured">Destaque na Homepage</label>
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?= ($record['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <label for="is_active">Ativo</label>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?action=services" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= $record ? 'Atualizar Serviço' : 'Criar Serviço' ?>
                </button>
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
.form-group input[type="email"],
.form-group input[type="number"],
.form-group input[type="file"],
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #700420;
    box-shadow: 0 0 0 3px rgba(112, 4, 32, 0.1);
}

.form-group textarea {
    resize: vertical;
}

.form-group.checkbox {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.form-group.checkbox input[type="checkbox"] {
    width: auto;
}

.form-group.checkbox label {
    margin-bottom: 0;
}

.image-preview {
    margin-bottom: 1rem;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 4px;
}

.image-preview img {
    border-radius: 4px;
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

.btn-danger {
    background: #cc0000;
    color: white;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 12px;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #d4edda;
    color: #155724;
}

.badge-secondary {
    background: #e2e3e5;
    color: #383d41;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow-x: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: #f5f5f5;
}

.table th,
.table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.table th {
    font-weight: 600;
    color: #333;
}

.table tbody tr:hover {
    background: #f9f9f9;
}
</style>

<script>
function deleteRecord(module, id) {
    if (confirm('Tem certeza que deseja deletar este registro?')) {
        window.location.href = '<?= BASE_URL ?>/admin/admin_actions.php?module=' + module + '&action=delete&id=' + encodeURIComponent(id);
    }
}
</script>
