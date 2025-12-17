<?php
/**
 * =========================================================================
 * MÓDULO SEO_METADATA - Gerenciar SEO e Meta Tags
 * =========================================================================
 */

// Verificar que a config foi carregada
if (!defined('DATA_PATH')) {
    require_once __DIR__ . '/../config.php';
}

$op = $_GET['op'] ?? 'list';
$id = $_GET['id'] ?? null;

// Páginas disponíveis para configurar SEO
$available_pages = [
    'index' => 'Página Inicial',
    'blog' => 'Blog',
    'blog-post' => 'Post do Blog',
    'tours' => 'Tours',
    'services' => 'Serviços',
    'sobre' => 'Sobre',
    'experience' => 'Experience',
    'privacidade' => 'Privacidade',
    'termos' => 'Termos de Uso',
];

// Carregar registros
$records = [];
try {
    $result = $db->query("SELECT * FROM seo_metadata");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $records = [];
}

// Criar array indexado por page_slug
$seo_by_page = [];
foreach ($records as $record) {
    $seo_by_page[$record['page_slug']] = $record;
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
    <h2>🔍 Gerenciar SEO & Meta Tags</h2>
    <?php if ($op !== 'new' && $op !== 'edit'): ?>
        <a href="?action=seo_metadata&op=new" class="btn btn-primary">+ Adicionar Página</a>
    <?php endif; ?>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM -->
    <div class="alert alert-info mb-3">
        <strong>ℹ️ Sobre SEO:</strong> Configure os meta tags para cada página do site. Isso ajuda o Google e redes sociais a entender e exibir melhor seu conteúdo.
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Página</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($available_pages as $slug => $name): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($name) ?></strong><br>
                        <small class="text-muted"><?= $slug ?></small>
                    </td>
                    <td style="max-width: 300px;">
                        <?php if (isset($seo_by_page[$slug])): ?>
                            <?= htmlspecialchars(substr($seo_by_page[$slug]['meta_title'] ?? '', 0, 60)) ?>...
                        <?php else: ?>
                            <span class="text-muted">Não configurado</span>
                        <?php endif; ?>
                    </td>
                    <td style="max-width: 350px;">
                        <?php if (isset($seo_by_page[$slug])): ?>
                            <?= htmlspecialchars(substr($seo_by_page[$slug]['meta_description'] ?? '', 0, 80)) ?>...
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($seo_by_page[$slug])): ?>
                            <span class="badge badge-success">✓ Configurado</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Padrão</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($seo_by_page[$slug])): ?>
                            <a href="?action=seo_metadata&op=edit&id=<?= urlencode($seo_by_page[$slug]['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                            <a href="#" class="btn btn-sm btn-danger" onclick="deleteRecord('seo_metadata', '<?= htmlspecialchars($seo_by_page[$slug]['id']) ?>'); return false;">Deletar</a>
                        <?php else: ?>
                            <a href="?action=seo_metadata&op=new&page=<?= urlencode($slug) ?>" class="btn btn-sm btn-success">Configurar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($op === 'new' || $op === 'edit'): ?>
    <!-- FORMULÁRIO -->
    <div class="form">
        <form method="POST" action="<?= BASE_URL ?>/admin/admin_actions.php" enctype="multipart/form-data">
            <input type="hidden" name="module" value="seo_metadata">
            <input type="hidden" name="_action" value="save">
            <?php if ($record): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
            <?php endif; ?>

            <div class="form-section">
                <h3>📄 Página</h3>

                <div class="form-group">
                    <label for="page_slug">Página *</label>
                    <select id="page_slug" name="page_slug" required <?= $record ? 'disabled' : '' ?>>
                        <option value="">Selecione uma página</option>
                        <?php foreach ($available_pages as $slug => $name): ?>
                            <option value="<?= $slug ?>"
                                <?= (($record['page_slug'] ?? ($_GET['page'] ?? '')) === $slug) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($name) ?> (<?= $slug ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($record): ?>
                        <input type="hidden" name="page_slug" value="<?= htmlspecialchars($record['page_slug']) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-section">
                <h3>🏷️ Meta Tags Básicos</h3>

                <div class="form-group">
                    <label for="meta_title">Title (Título da Página) *</label>
                    <input type="text" id="meta_title" name="meta_title" required
                           value="<?= htmlspecialchars($record['meta_title'] ?? '') ?>"
                           maxlength="60"
                           oninput="updateCharCount('meta_title', 'title-count', 60)">
                    <small>
                        <span id="title-count">0</span>/60 caracteres.
                        Aparece na aba do navegador e nos resultados do Google.
                    </small>
                </div>

                <div class="form-group">
                    <label for="meta_description">Description (Descrição) *</label>
                    <textarea id="meta_description" name="meta_description" required rows="3"
                              maxlength="160"
                              oninput="updateCharCount('meta_description', 'desc-count', 160)"><?= htmlspecialchars($record['meta_description'] ?? '') ?></textarea>
                    <small>
                        <span id="desc-count">0</span>/160 caracteres.
                        Aparece nos resultados de busca do Google.
                    </small>
                </div>

                <div class="form-group">
                    <label for="meta_keywords">Keywords (Palavras-chave)</label>
                    <input type="text" id="meta_keywords" name="meta_keywords"
                           value="<?= htmlspecialchars($record['meta_keywords'] ?? '') ?>"
                           placeholder="guia Londres, tour privado, passeios Londres">
                    <small>Separe com vírgulas. Ex: guia Londres, tour privado, passeios personalizados</small>
                </div>

                <div class="form-group">
                    <label for="canonical_url">URL Canônica</label>
                    <input type="url" id="canonical_url" name="canonical_url"
                           value="<?= htmlspecialchars($record['canonical_url'] ?? '') ?>"
                           placeholder="https://lovelylondonbycarol.com/pages/tours.php">
                    <small>URL principal da página. Deixe em branco para usar a URL padrão.</small>
                </div>
            </div>

            <div class="form-section">
                <h3>📱 Open Graph (Facebook, WhatsApp, LinkedIn)</h3>

                <div class="form-group">
                    <label for="og_title">OG Title</label>
                    <input type="text" id="og_title" name="og_title"
                           value="<?= htmlspecialchars($record['og_title'] ?? '') ?>"
                           placeholder="Deixe vazio para usar o Title principal">
                    <small>Título quando compartilhado em redes sociais. Se vazio, usa o Title.</small>
                </div>

                <div class="form-group">
                    <label for="og_description">OG Description</label>
                    <textarea id="og_description" name="og_description" rows="2"><?= htmlspecialchars($record['og_description'] ?? '') ?></textarea>
                    <small>Descrição para redes sociais. Se vazio, usa a Description.</small>
                </div>

                <div class="form-group">
                    <label for="og_type">OG Type</label>
                    <select id="og_type" name="og_type">
                        <option value="website" <?= ($record['og_type'] ?? 'website') === 'website' ? 'selected' : '' ?>>Website</option>
                        <option value="article" <?= ($record['og_type'] ?? '') === 'article' ? 'selected' : '' ?>>Article (Artigo)</option>
                        <option value="profile" <?= ($record['og_type'] ?? '') === 'profile' ? 'selected' : '' ?>>Profile (Perfil)</option>
                    </select>
                </div>

                <?php if ($record && !empty($record['og_image'])): ?>
                <div class="image-preview">
                    <p><strong>Imagem OG Atual:</strong></p>
                    <img src="<?= htmlspecialchars($record['og_image']) ?>" alt="OG Image" style="max-width: 400px;">
                    <input type="hidden" name="og_image_old" value="<?= htmlspecialchars($record['og_image']) ?>">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="og_image">Imagem Open Graph</label>
                    <input type="file" id="og_image" name="og_image" accept="image/*">
                    <small>Recomendado: 1200x630px. Esta imagem aparece quando a página é compartilhada.</small>
                </div>
            </div>

            <div class="form-section">
                <h3>🐦 Twitter Card</h3>

                <div class="form-group">
                    <label for="twitter_card">Twitter Card Type</label>
                    <select id="twitter_card" name="twitter_card">
                        <option value="summary_large_image" <?= ($record['twitter_card'] ?? 'summary_large_image') === 'summary_large_image' ? 'selected' : '' ?>>Summary Large Image</option>
                        <option value="summary" <?= ($record['twitter_card'] ?? '') === 'summary' ? 'selected' : '' ?>>Summary</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="twitter_title">Twitter Title</label>
                    <input type="text" id="twitter_title" name="twitter_title"
                           value="<?= htmlspecialchars($record['twitter_title'] ?? '') ?>"
                           placeholder="Deixe vazio para usar o OG Title">
                </div>

                <div class="form-group">
                    <label for="twitter_description">Twitter Description</label>
                    <textarea id="twitter_description" name="twitter_description" rows="2"><?= htmlspecialchars($record['twitter_description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>🤖 Indexação</h3>

                <div class="form-group">
                    <label for="robots">Robots Meta Tag</label>
                    <select id="robots" name="robots">
                        <option value="index, follow" <?= ($record['robots'] ?? 'index, follow') === 'index, follow' ? 'selected' : '' ?>>Index, Follow (Padrão - Indexar e seguir links)</option>
                        <option value="noindex, follow" <?= ($record['robots'] ?? '') === 'noindex, follow' ? 'selected' : '' ?>>NoIndex, Follow (Não indexar, seguir links)</option>
                        <option value="index, nofollow" <?= ($record['robots'] ?? '') === 'index, nofollow' ? 'selected' : '' ?>>Index, NoFollow (Indexar, não seguir links)</option>
                        <option value="noindex, nofollow" <?= ($record['robots'] ?? '') === 'noindex, nofollow' ? 'selected' : '' ?>>NoIndex, NoFollow (Não indexar, não seguir)</option>
                    </select>
                    <small>Controla se o Google deve indexar esta página e seguir seus links.</small>
                </div>
            </div>

            <div class="form-section">
                <h3>📊 Schema.org (JSON-LD)</h3>

                <div class="form-group">
                    <label for="schema_json">Schema JSON-LD (Opcional)</label>
                    <textarea id="schema_json" name="schema_json" rows="8" style="font-family: monospace;"><?= htmlspecialchars($record['schema_json'] ?? '') ?></textarea>
                    <small>
                        Código JSON-LD para rich snippets. Ex: LocalBusiness, Article, FAQ, etc.<br>
                        <a href="https://schema.org/LocalBusiness" target="_blank">Ver exemplos →</a>
                    </small>
                </div>
            </div>

            <div class="form-actions">
                <a href="?action=seo_metadata" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= $record ? '💾 Atualizar SEO' : '✨ Criar Configuração SEO' ?>
                </button>
            </div>
        </form>
    </div>

<?php endif; ?>

<style>
.text-muted {
    color: #999;
    font-size: 0.9em;
}

#title-count, #desc-count {
    font-weight: bold;
    color: #700420;
}
</style>

<script>
function updateCharCount(inputId, counterId, maxLength) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const length = input.value.length;
    counter.textContent = length;

    if (length > maxLength * 0.9) {
        counter.style.color = '#dc3545';
    } else if (length > maxLength * 0.7) {
        counter.style.color = '#ffc107';
    } else {
        counter.style.color = '#700420';
    }
}

function deleteRecord(module, id) {
    if (confirm('Tem certeza que deseja deletar esta configuração de SEO?')) {
        window.location.href = '<?= BASE_URL ?>/admin/admin_actions.php?module=' + module + '&_action=delete&id=' + encodeURIComponent(id);
    }
}

// Initialize character counters
document.addEventListener('DOMContentLoaded', function() {
    updateCharCount('meta_title', 'title-count', 60);
    updateCharCount('meta_description', 'desc-count', 160);
});
</script>
