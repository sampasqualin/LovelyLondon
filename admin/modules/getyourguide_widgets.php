<?php
/**
 * =========================================================================
 * MÓDULO GETYOURGUIDE WIDGETS - Gerenciar Widgets de Afiliação
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
    $result = $db->query("SELECT * FROM getyourguide_widgets ORDER BY created_at DESC");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
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
            // Decodificar JSON fields
            if (isset($record['widget_config']) && is_string($record['widget_config'])) {
                $record['widget_config'] = json_decode($record['widget_config'], true);
            }
            if (isset($record['tags']) && is_string($record['tags'])) {
                $record['tags'] = json_decode($record['tags'], true);
            }
            // Se não tem widget_code mas tem widget_config, reconstruir o código
            if (empty($record['widget_code']) && !empty($record['widget_config'])) {
                $config = $record['widget_config'];
                $record['widget_code'] = '<div data-gyg-href="' . ($config['href'] ?? '') . '" ' .
                    'data-gyg-locale-code="' . ($config['locale'] ?? 'pt-BR') . '" ' .
                    'data-gyg-widget="' . ($config['widget_type'] ?? 'activities') . '" ' .
                    'data-gyg-number-of-items="' . ($config['number_of_items'] ?? '1') . '" ' .
                    'data-gyg-partner-id="' . ($config['partner_id'] ?? 'MJKDHZZ') . '" ' .
                    'data-gyg-tour-ids="' . ($config['tour_ids'] ?? '') . '"></div>';
            }
            break;
        }
    }
}

// Estatísticas
$total_widgets = count($records);
$active_widgets = count(array_filter($records, fn($r) => $r['is_active'] == 1));

?>

<div class="module-header">
    <h2>🎫 Gerenciar Widgets GetYourGuide</h2>
    <?php if ($op !== 'new' && $op !== 'edit'): ?>
        <a href="?action=getyourguide_widgets&op=new" class="btn btn-primary">+ Novo Widget</a>
    <?php endif; ?>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- ESTATÍSTICAS -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <span class="stat-value"><?= $total_widgets ?></span>
                <span class="stat-label">Total de Widgets</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <span class="stat-value"><?= $active_widgets ?></span>
                <span class="stat-label">Widgets Ativos</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔗</div>
            <div class="stat-info">
                <span class="stat-value">MJKDHZZ</span>
                <span class="stat-label">Partner ID</span>
            </div>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Preço</th>
                    <th>Tour ID</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem;">
                        Nenhum widget cadastrado. Clique em "+ Novo Widget" para começar.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($records as $widget): ?>
                    <?php
                    $config = is_string($widget['widget_config']) ? json_decode($widget['widget_config'], true) : $widget['widget_config'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($widget['id']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($widget['title']) ?></strong>
                            <br>
                            <small style="color: #666;"><?= htmlspecialchars(substr($widget['description'], 0, 60)) ?>...</small>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--lovely);">
                                <?= htmlspecialchars($widget['category_pt']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge" style="background: var(--thames);">
                                <?= htmlspecialchars($widget['type_pt']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($widget['price_from'] ?? '-') ?></td>
                        <td><code><?= htmlspecialchars($config['tour_ids'] ?? '-') ?></code></td>
                        <td>
                            <?php if ($widget['is_active']): ?>
                                <span class="badge badge-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?action=getyourguide_widgets&op=edit&id=<?= urlencode($widget['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                            <a href="#" class="btn btn-sm btn-danger" onclick="deleteRecord('getyourguide_widgets', '<?= htmlspecialchars($widget['id']) ?>'); return false;">Deletar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- INSTRUÇÕES -->
    <div class="alert alert-info" style="margin-top: 2rem;">
        <h4>📘 Como obter e usar o código do widget GetYourGuide:</h4>
        <ol style="margin: 1rem 0;">
            <li>Acesse seu painel de afiliado em <a href="https://partner.getyourguide.com/" target="_blank">partner.getyourguide.com</a></li>
            <li>Navegue até a atividade/tour que deseja promover</li>
            <li>Clique em "Get Widget" ou "Obter Widget"</li>
            <li><strong>Copie o código HTML completo</strong> do widget</li>
            <li>Cole o código no campo "Código HTML do Widget" do formulário acima</li>
            <li>Adicione um título, categoria e tags</li>
            <li>Salve! O sistema extrairá automaticamente todas as configurações</li>
        </ol>
        <p><strong>Exemplo de código que você deve colar:</strong></p>
        <pre style="background: #f5f5f5; padding: 1rem; border-radius: 4px; overflow-x: auto;"><code>&lt;div data-gyg-href="https://widget.getyourguide.com/default/activities.frame"
     data-gyg-locale-code="pt-BR"
     data-gyg-widget="activities"
     data-gyg-number-of-items="1"
     data-gyg-partner-id="MJKDHZZ"
     data-gyg-tour-ids="505308"&gt;&lt;/div&gt;</code></pre>
        <p>✨ <strong>Tudo isso é extraído automaticamente</strong> - você só precisa colar o código completo!</p>
    </div>

<?php elseif ($op === 'new' || $op === 'edit'): ?>
    <!-- FORMULÁRIO -->
    <div class="form-container">
        <form method="POST" action="<?= BASE_URL ?>/admin/admin_actions.php" class="form">
            <input type="hidden" name="module" value="getyourguide_widgets">
            <input type="hidden" name="_action" value="save">
            <?php if ($record): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
            <?php endif; ?>

            <!-- CÓDIGO DO WIDGET -->
            <div class="form-section">
                <h3>🔗 Código do Widget GetYourGuide</h3>
                <p class="form-help">Cole o código completo do widget que você copiou do GetYourGuide</p>

                <div class="form-group">
                    <label for="widget_code">Código HTML do Widget * <small>(Obrigatório)</small></label>
                    <textarea id="widget_code"
                              name="widget_code"
                              rows="8"
                              required
                              placeholder='Cole aqui o código completo, exemplo:
<div data-gyg-href="https://widget.getyourguide.com/default/activities.frame" data-gyg-locale-code="pt-BR" data-gyg-widget="activities" data-gyg-number-of-items="1" data-gyg-partner-id="MJKDHZZ" data-gyg-tour-ids="505308"></div>'
                              style="font-family: monospace; font-size: 0.9rem;"><?= htmlspecialchars($record['widget_code'] ?? '') ?></textarea>
                    <small class="form-help">O sistema extrairá automaticamente as configurações necessárias</small>

                    <button type="button"
                            class="btn btn-info"
                            onclick="autoFillFromWidget()"
                            style="margin-top: 1rem;">
                        🔍 Extrair Informações do Código
                    </button>
                    <div id="extraction-result" style="margin-top: 1rem; padding: 1rem; background: #e3f2fd; border-radius: 8px; display: none;">
                        <strong>✅ Informações extraídas:</strong>
                        <ul id="extracted-info" style="margin: 0.5rem 0 0 1.5rem;"></ul>
                    </div>
                </div>
            </div>

            <!-- INFORMAÇÕES BÁSICAS -->
            <div class="form-section">
                <h3>📝 Título para Identificação</h3>

                <div class="form-group">
                    <label for="title">Título do Widget *</label>
                    <input type="text"
                           id="title"
                           name="title"
                           required
                           value="<?= htmlspecialchars($record['title'] ?? '') ?>"
                           placeholder="ex: Torre de Londres - Ingresso com as Joias da Coroa">
                    <small class="form-help">Nome para identificar este widget no admin e na busca</small>
                </div>
            </div>

            <!-- CATEGORIZAÇÃO -->
            <div class="form-section">
                <h3>🏷️ Categorização</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Categoria *</label>
                        <select id="category" name="category" required>
                            <option value="">Selecione...</option>
                            <option value="attractions" <?= ($record['category'] ?? '') === 'attractions' ? 'selected' : '' ?>>Atrações e Monumentos</option>
                            <option value="tours" <?= ($record['category'] ?? '') === 'tours' ? 'selected' : '' ?>>Tours e Passeios</option>
                            <option value="transport" <?= ($record['category'] ?? '') === 'transport' ? 'selected' : '' ?>>Transporte</option>
                            <option value="day_trips" <?= ($record['category'] ?? '') === 'day_trips' ? 'selected' : '' ?>>Excursões de 1 Dia</option>
                            <option value="food_drink" <?= ($record['category'] ?? '') === 'food_drink' ? 'selected' : '' ?>>Comida e Bebida</option>
                            <option value="entertainment" <?= ($record['category'] ?? '') === 'entertainment' ? 'selected' : '' ?>>Entretenimento</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type">Tipo *</label>
                        <select id="type" name="type" required>
                            <option value="">Selecione...</option>
                            <option value="ticket" <?= ($record['type'] ?? '') === 'ticket' ? 'selected' : '' ?>>Ingresso</option>
                            <option value="tour" <?= ($record['type'] ?? '') === 'tour' ? 'selected' : '' ?>>Tour</option>
                            <option value="bus" <?= ($record['type'] ?? '') === 'bus' ? 'selected' : '' ?>>Ônibus Turístico</option>
                            <option value="train" <?= ($record['type'] ?? '') === 'train' ? 'selected' : '' ?>>Trem</option>
                            <option value="cruise" <?= ($record['type'] ?? '') === 'cruise' ? 'selected' : '' ?>>Cruzeiro</option>
                            <option value="pass" <?= ($record['type'] ?? '') === 'pass' ? 'selected' : '' ?>>Passe/Cartão</option>
                            <option value="experience" <?= ($record['type'] ?? '') === 'experience' ? 'selected' : '' ?>>Experiência</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category_pt">Nome da Categoria (PT)</label>
                    <input type="text"
                           id="category_pt"
                           name="category_pt"
                           value="<?= htmlspecialchars($record['category_pt'] ?? '') ?>"
                           placeholder="ex: Atrações e Monumentos">
                    <small class="form-help">Preenchimento automático ao usar "Extrair Informações"</small>
                </div>

                <div class="form-group">
                    <label for="category_en">Nome da Categoria (EN)</label>
                    <input type="text"
                           id="category_en"
                           name="category_en"
                           value="<?= htmlspecialchars($record['category_en'] ?? '') ?>"
                           placeholder="ex: Attractions & Monuments">
                    <small class="form-help">Preenchimento automático ao usar "Extrair Informações"</small>
                </div>

                <div class="form-group">
                    <label for="type_pt">Nome do Tipo (PT)</label>
                    <input type="text"
                           id="type_pt"
                           name="type_pt"
                           value="<?= htmlspecialchars($record['type_pt'] ?? '') ?>"
                           placeholder="ex: Ingresso">
                    <small class="form-help">Preenchimento automático ao usar "Extrair Informações"</small>
                </div>

                <div class="form-group">
                    <label for="type_en">Nome do Tipo (EN)</label>
                    <input type="text"
                           id="type_en"
                           name="type_en"
                           value="<?= htmlspecialchars($record['type_en'] ?? '') ?>"
                           placeholder="ex: Ticket">
                    <small class="form-help">Preenchimento automático ao usar "Extrair Informações"</small>
                </div>
            </div>

            <!-- TAGS PARA BUSCA -->
            <div class="form-section">
                <h3>🔍 Tags para Busca</h3>
                <p class="form-help">Adicione palavras-chave separadas por vírgula para facilitar a busca</p>

                <div class="form-group">
                    <label for="tags">Tags</label>
                    <input type="text"
                           id="tags"
                           name="tags"
                           value="<?= htmlspecialchars(is_array($record['tags'] ?? null) ? implode(', ', $record['tags']) : '') ?>"
                           placeholder="ex: torre de londres, joias da coroa, história, monumento">
                    <small class="form-help">Separe as tags com vírgula. Exemplo: torre de londres, história, ingresso</small>
                </div>
            </div>

            <!-- STATUS -->
            <div class="form-section">
                <h3>⚙️ Configurações</h3>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               <?= ($record['is_active'] ?? 1) ? 'checked' : '' ?>>
                        Widget Ativo (será exibido na página Experience)
                    </label>
                </div>
            </div>

            <!-- BOTÕES -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Salvar Widget</button>
                <a href="?action=getyourguide_widgets" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- PREVIEW DO WIDGET -->
    <?php if ($record): ?>
    <div class="form-section" style="margin-top: 2rem; background: #f5f5f5; padding: 2rem; border-radius: 8px;">
        <h3>👁️ Preview do Widget</h3>
        <p style="color: #666; margin-bottom: 1rem;">Este é o widget que será exibido na página Experience:</p>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div data-gyg-href="<?= htmlspecialchars($record['widget_config']['href'] ?? 'https://widget.getyourguide.com/default/activities.frame') ?>"
                 data-gyg-locale-code="<?= htmlspecialchars($record['widget_config']['locale'] ?? 'pt-BR') ?>"
                 data-gyg-widget="<?= htmlspecialchars($record['widget_config']['widget_type'] ?? 'activities') ?>"
                 data-gyg-number-of-items="<?= htmlspecialchars($record['widget_config']['number_of_items'] ?? '1') ?>"
                 data-gyg-partner-id="<?= htmlspecialchars($record['widget_config']['partner_id'] ?? 'MJKDHZZ') ?>"
                 data-gyg-tour-ids="<?= htmlspecialchars($record['widget_config']['tour_ids'] ?? '') ?>">
            </div>
            <script src="https://widget.getyourguide.com/dist/pa.umd.production.min.js" data-gyg-partner-id="MJKDHZZ"></script>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

<style>
/* ========== ESTATÍSTICAS ========== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #700420 0%, #955425 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    box-shadow: 0 8px 24px rgba(112, 4, 32, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(112, 4, 32, 0.3);
}

.stat-icon {
    font-size: 3rem;
    opacity: 0.9;
    min-width: 60px;
    text-align: center;
}

.stat-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-value {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.95rem;
    opacity: 0.85;
    font-weight: 400;
}

/* ========== TABELA ========== */
.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: linear-gradient(135deg, #700420 0%, #955425 100%);
    color: white;
}

.table thead th {
    padding: 1.25rem 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s ease;
}

.table tbody tr:hover {
    background: #fafafa;
}

.table tbody td {
    padding: 1.25rem 1rem;
    font-size: 0.95rem;
}

.table .badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table code {
    background: #f5f5f5;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
    color: #700420;
    font-family: 'Courier New', monospace;
}

/* ========== FORMULÁRIO ========== */
.form-container {
    background: white;
    border-radius: 12px;
    padding: 2.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.form-section {
    margin-bottom: 2.5rem;
    padding-bottom: 2.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 2rem;
}

.form-section h3 {
    font-size: 1.5rem;
    color: #700420;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section p {
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-group label small {
    color: #dc3545;
    font-weight: 400;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: inherit;
    transition: all 0.3s ease;
    background: white;
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
    min-height: 100px;
    line-height: 1.6;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.form-help {
    display: block;
    color: #666;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    font-style: italic;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: 500;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
}

.checkbox-label:hover {
    background: #fff;
    border-color: #700420;
}

.checkbox-label input[type="checkbox"] {
    width: 24px;
    height: 24px;
    cursor: pointer;
    accent-color: #700420;
}

/* ========== BOTÕES ========== */
.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 2px solid #f0f0f0;
}

.btn {
    padding: 0.875rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #700420 0%, #955425 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(112, 4, 32, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(112, 4, 32, 0.4);
}

.btn-secondary {
    background: #f5f5f5;
    color: #333;
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    background: #fff;
    border-color: #700420;
    color: #700420;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-info:hover,
.btn-danger:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* ========== ALERT BOX ========== */
.alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-left: 5px solid #2196f3;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(33, 150, 243, 0.15);
    margin-top: 2rem;
}

.alert-info h4 {
    color: #1976d2;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-info ol {
    color: #424242;
    margin-left: 1.5rem;
    line-height: 1.8;
}

.alert-info ol li {
    margin-bottom: 0.5rem;
}

.alert-info a {
    color: #1976d2;
    font-weight: 600;
    text-decoration: underline;
}

.alert-info a:hover {
    color: #0d47a1;
}

.alert-info pre {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    font-size: 0.85rem;
    line-height: 1.6;
    overflow-x: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin: 1rem 0;
}

.alert-info code {
    color: #700420;
    background: #fff3e0;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}

/* ========== PREVIEW WIDGET ========== */
.form-section[style*="background"] {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 2px dashed #dee2e6;
}

.form-section[style*="background"] h3 {
    color: #495057;
}

/* ========== RESPONSIVO ========== */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .table-container {
        overflow-x: auto;
    }
}

/* ========== ANIMAÇÕES ========== */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-section,
.stat-card {
    animation: fadeIn 0.5s ease-out;
}
</style>

<script>
function deleteRecord(module, id) {
    if (confirm('Tem certeza que deseja deletar este widget?\n\nEsta ação não pode ser desfeita.')) {
        window.location.href = '<?= BASE_URL ?>/admin/admin_actions.php?module=' + module + '&_action=delete&id=' + encodeURIComponent(id);
    }
}

// Função para extrair informações do widget e preencher campos
function autoFillFromWidget() {
    const widgetCode = document.getElementById('widget_code').value;
    const resultDiv = document.getElementById('extraction-result');
    const infoList = document.getElementById('extracted-info');

    if (!widgetCode.trim()) {
        alert('Por favor, cole o código do widget primeiro!');
        return;
    }

    // Extrair tour ID
    const tourIdMatch = widgetCode.match(/data-gyg-tour-ids="([^"]+)"/);
    const tourId = tourIdMatch ? tourIdMatch[1] : null;

    if (!tourId) {
        alert('Não foi possível encontrar o Tour ID no código. Verifique se colou o código completo.');
        return;
    }

    // Mostrar loading
    resultDiv.style.display = 'block';
    infoList.innerHTML = '<li>🔄 Buscando informações do tour ' + tourId + '...</li>';

    // Fazer chamada para buscar informações do GetYourGuide
    fetch('https://www.getyourguide.com/api/1/tours/' + tourId + '?currency=GBP&locale=pt-BR')
        .then(response => {
            if (!response.ok) throw new Error('Erro ao buscar informações');
            return response.json();
        })
        .then(data => {
            // Extrair informações relevantes
            const title = data.title || 'Tour ID: ' + tourId;
            const category = suggestCategory(title, data.categories || []);
            const type = suggestType(title);
            const tags = generateTags(title, data.categories || []);

            // Preencher campos
            document.getElementById('title').value = title;

            // Selecionar categoria
            const categorySelect = document.getElementById('category');
            if (categorySelect) {
                categorySelect.value = category;
            }

            // Selecionar tipo
            const typeSelect = document.getElementById('type');
            if (typeSelect) {
                typeSelect.value = type;
            }

            // Preencher tags
            const tagsInput = document.getElementById('tags');
            if (tagsInput) {
                tagsInput.value = tags.join(', ');
            }

            // Preencher nomes de categoria e tipo
            fillCategoryNames(category);
            fillTypeNames(type);

            // Mostrar resultados
            infoList.innerHTML = `
                <li><strong>Tour ID:</strong> ${tourId}</li>
                <li><strong>Título:</strong> ${title}</li>
                <li><strong>Categoria sugerida:</strong> ${category}</li>
                <li><strong>Tipo sugerido:</strong> ${type}</li>
                <li><strong>Tags geradas:</strong> ${tags.join(', ')}</li>
            `;

            // Scroll suave para o resultado
            resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .catch(error => {
            // Se falhar na API, usar método offline
            console.log('Erro ao buscar da API, usando método offline:', error);
            autoFillOffline(tourId);
        });
}

// Método offline caso a API não funcione
function autoFillOffline(tourId) {
    const resultDiv = document.getElementById('extraction-result');
    const infoList = document.getElementById('extracted-info');

    // Gerar título sugerido
    const suggestedTitle = 'Tour/Atividade - ID: ' + tourId;

    // Preencher campos com valores padrão
    document.getElementById('title').value = suggestedTitle;

    // Mostrar resultados
    infoList.innerHTML = `
        <li><strong>Tour ID:</strong> ${tourId} ✅</li>
        <li><strong>Título sugerido:</strong> ${suggestedTitle}</li>
        <li>⚠️ Não foi possível buscar mais informações. Por favor, preencha manualmente:</li>
        <li>- Edite o título acima</li>
        <li>- Selecione a categoria apropriada</li>
        <li>- Selecione o tipo de serviço</li>
        <li>- Adicione tags para facilitar a busca</li>
    `;
}

// Sugerir categoria baseada no título
function suggestCategory(title, categories) {
    const titleLower = title.toLowerCase();

    if (titleLower.includes('museum') || titleLower.includes('museu') || titleLower.includes('tower') || titleLower.includes('palace') || titleLower.includes('palácio')) {
        return 'attractions';
    }
    if (titleLower.includes('tour') || titleLower.includes('walking') || titleLower.includes('guided')) {
        return 'tours';
    }
    if (titleLower.includes('bus') || titleLower.includes('ônibus') || titleLower.includes('train') || titleLower.includes('transfer')) {
        return 'transport';
    }
    if (titleLower.includes('food') || titleLower.includes('comida') || titleLower.includes('dinner') || titleLower.includes('lunch')) {
        return 'food_drink';
    }
    if (titleLower.includes('day trip') || titleLower.includes('excursão') || titleLower.includes('stonehenge') || titleLower.includes('oxford')) {
        return 'day_trips';
    }

    return 'attractions'; // Padrão
}

// Sugerir tipo baseado no título
function suggestType(title) {
    const titleLower = title.toLowerCase();

    if (titleLower.includes('ticket') || titleLower.includes('ingresso') || titleLower.includes('entrada') || titleLower.includes('skip-the-line')) {
        return 'ticket';
    }
    if (titleLower.includes('bus') || titleLower.includes('hop-on')) {
        return 'bus';
    }
    if (titleLower.includes('cruise') || titleLower.includes('cruzeiro') || titleLower.includes('boat')) {
        return 'cruise';
    }
    if (titleLower.includes('train') || titleLower.includes('trem')) {
        return 'train';
    }
    if (titleLower.includes('pass') || titleLower.includes('card')) {
        return 'pass';
    }
    if (titleLower.includes('tour') || titleLower.includes('walking') || titleLower.includes('guided')) {
        return 'tour';
    }

    return 'ticket'; // Padrão
}

// Gerar tags baseadas no título
function generateTags(title, categories) {
    const words = title.toLowerCase()
        .replace(/[^\w\s]/g, ' ')
        .split(/\s+/)
        .filter(word => word.length > 3);

    // Remover palavras comuns
    const stopWords = ['with', 'from', 'tour', 'ticket', 'london', 'the', 'and', 'for'];
    const tags = words.filter(word => !stopWords.includes(word));

    return tags.slice(0, 5); // Máximo 5 tags
}

// Preencher nomes de categoria
function fillCategoryNames(category) {
    const categoryNames = {
        'attractions': { pt: 'Atrações e Monumentos', en: 'Attractions & Monuments' },
        'tours': { pt: 'Tours e Passeios', en: 'Tours & Experiences' },
        'transport': { pt: 'Transporte', en: 'Transport' },
        'day_trips': { pt: 'Excursões de 1 Dia', en: 'Day Trips' },
        'food_drink': { pt: 'Comida e Bebida', en: 'Food & Drink' },
        'entertainment': { pt: 'Entretenimento', en: 'Entertainment' }
    };

    if (categoryNames[category]) {
        document.getElementById('category_pt').value = categoryNames[category].pt;
        document.getElementById('category_en').value = categoryNames[category].en;
    }
}

// Preencher nomes de tipo
function fillTypeNames(type) {
    const typeNames = {
        'ticket': { pt: 'Ingresso', en: 'Ticket' },
        'tour': { pt: 'Tour', en: 'Tour' },
        'bus': { pt: 'Ônibus Turístico', en: 'Tour Bus' },
        'train': { pt: 'Trem', en: 'Train' },
        'cruise': { pt: 'Cruzeiro', en: 'Cruise' },
        'pass': { pt: 'Passe/Cartão', en: 'Pass/Card' },
        'experience': { pt: 'Experiência', en: 'Experience' }
    };

    if (typeNames[type]) {
        document.getElementById('type_pt').value = typeNames[type].pt;
        document.getElementById('type_en').value = typeNames[type].en;
    }
}
</script>
