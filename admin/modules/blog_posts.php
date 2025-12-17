<?php
/**
 * =========================================================================
 * MÓDULO BLOG POSTS - Gerenciar Blog Posts
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
    $result = $db->query("SELECT * FROM blog_posts");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
        usort($records, function($a, $b) {
            return strtotime(($b['published_at'] ?? $b['created_at'] ?? '')) - strtotime(($a['published_at'] ?? $a['created_at'] ?? ''));
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
    <h2>📝 Gerenciar Blog Posts</h2>
    <?php if ($op !== 'new' && $op !== 'edit'): ?>
        <a href="?action=blog_posts&op=new" class="btn btn-primary">+ Novo Post</a>
    <?php endif; ?>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Título (PT)</th>
                    <th>Categoria</th>
                    <th>Status</th>
                    <th>Data Publicação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post['title_pt'] ?? '') ?></td>
                    <td><?= htmlspecialchars($post['category_pt'] ?? '') ?></td>
                    <td>
                        <?php 
                        $status = $post['status'] ?? 'draft';
                        if ($status === 'published'): ?>
                            <span class="badge badge-success">Publicado</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Rascunho</span>
                        <?php endif; ?>
                    </td>
                    <td><?= isset($post['published_at']) ? date('d/m/Y H:i', strtotime($post['published_at'])) : '-' ?></td>
                    <td>
                        <a href="?action=blog_posts&op=edit&id=<?= urlencode($post['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="deleteRecord('blog_posts', '<?= htmlspecialchars($post['id']) ?>'); return false;">Deletar</a>
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
            <input type="hidden" name="module" value="blog_posts">
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
                        <label for="category_pt">Categoria (PT)</label>
                        <input type="text" id="category_pt" name="category_pt" value="<?= htmlspecialchars($record['category_pt'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">ID Categoria</label>
                        <input type="number" id="category_id" name="category_id" value="<?= $record['category_id'] ?? 1 ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="reading_time">Tempo Leitura (min)</label>
                        <input type="number" id="reading_time" name="reading_time" value="<?= $record['reading_time'] ?? 5 ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Conteúdo</h3>
                
                <div class="form-group">
                    <label for="excerpt_pt">Resumo (PT) *</label>
                    <textarea id="excerpt_pt" name="excerpt_pt" rows="3" required><?= htmlspecialchars($record['excerpt_pt'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="excerpt_en">Resumo (EN) *</label>
                    <textarea id="excerpt_en" name="excerpt_en" rows="3" required><?= htmlspecialchars($record['excerpt_en'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="content_pt">Conteúdo (PT) *</label>
                    <div id="editor_pt" style="height: 400px; background: white;"></div>
                    <textarea id="content_pt" name="content_pt" style="display:none;" required><?= htmlspecialchars($record['content_pt'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="content_en">Conteúdo (EN) *</label>
                    <div id="editor_en" style="height: 400px; background: white;"></div>
                    <textarea id="content_en" name="content_en" style="display:none;" required><?= htmlspecialchars($record['content_en'] ?? '') ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Imagem Destaque</h3>
                
                <?php if ($record && !empty($record['featured_image'])): ?>
                <div class="image-preview">
                    <img src="<?= htmlspecialchars($record['featured_image']) ?>" alt="Featured Image" style="max-width: 200px;">
                    <input type="hidden" name="featured_image_old" value="<?= htmlspecialchars($record['featured_image']) ?>">
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="featured_image">Upload Imagem (JPG, PNG, WebP, GIF - máx. 5MB)</label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*">
                </div>
            </div>
            
            <div class="form-section">
                <h3>Publicação</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="draft" <?= ($record['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                            <option value="published" <?= ($record['status'] ?? 'draft') === 'published' ? 'selected' : '' ?>>Publicado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="published_at">Data Publicação</label>
                        <input type="datetime-local" id="published_at" name="published_at" value="<?= isset($record['published_at']) ? date('Y-m-d\TH:i', strtotime($record['published_at'])) : '' ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags (separadas por vírgula)</label>
                    <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($record['tags'] ?? '') ?>" placeholder="Londres, Dicas, Viagem">
                </div>
                
                <div class="form-group checkbox">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= ($record['is_featured'] ?? 0) ? 'checked' : '' ?>>
                    <label for="is_featured">Destaque na Homepage</label>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?action=blog_posts" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= $record ? 'Atualizar Post' : 'Criar Post' ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="<?= BASE_URL ?>/admin/quill.min.js"></script>
    <script>
        // Inicializar editor PT
        var quillPt = new Quill('#editor_pt', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        // Inicializar editor EN
        var quillEn = new Quill('#editor_en', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        // Carregar conteúdo existente
        var contentPt = document.querySelector('#content_pt').value;
        var contentEn = document.querySelector('#content_en').value;
        if (contentPt) quillPt.root.innerHTML = contentPt;
        if (contentEn) quillEn.root.innerHTML = contentEn;

        // Sincronizar com textarea ao submeter
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('#content_pt').value = quillPt.root.innerHTML;
            document.querySelector('#content_en').value = quillEn.root.innerHTML;
        });
    </script>

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
.form-group input[type="datetime-local"],
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
    if (confirm('Tem certeza que deseja deletar este post?')) {
        window.location.href = '<?= BASE_URL ?>/admin/admin_actions.php?module=' + module + '&action=delete&id=' + encodeURIComponent(id);
    }
}
</script>
