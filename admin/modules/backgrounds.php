<?php
/**
 * =========================================================================
 * MÓDULO BACKGROUNDS - Gerenciar Fundos e Estilos de Seções
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
    $result = $db->query("SELECT * FROM section_backgrounds");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
        usort($records, function($a, $b) {
            return ($a['id'] ?? 0) - ($b['id'] ?? 0);
        });
    }
} catch (Exception $e) {
    // Fallback: Carregar do JSON se o banco falhar
    $jsonFile = DATA_PATH . '/section_backgrounds.json';
    if (file_exists($jsonFile)) {
        $json = file_get_contents($jsonFile);
        $records = json_decode($json, true) ?? [];
        usort($records, function($a, $b) {
            return ($a['id'] ?? 0) - ($b['id'] ?? 0);
        });
    } else {
        $records = [];
    }
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
    <h2>🎨 Gerenciar Fundos & Estilos de Seções</h2>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Seção</th>
                    <th>Tipo de Fundo</th>
                    <th>Cor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $bg): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($bg['section_label'] ?? '') ?></strong></td>
                    <td><?= htmlspecialchars($bg['background_type'] ?? '') ?></td>
                    <td>
                        <span style="display: inline-block; width: 30px; height: 30px; background-color: <?= htmlspecialchars($bg['background_color'] ?? '#fff') ?>; border: 1px solid #ddd; border-radius: 4px;"></span>
                        <?= htmlspecialchars($bg['background_color'] ?? '') ?>
                    </td>
                    <td>
                        <?php if ($bg['is_active']): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?action=backgrounds&op=edit&id=<?= urlencode($bg['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($op === 'edit'): ?>
    <!-- FORMULÁRIO DE EDIÇÃO -->
    <div class="form-container">
        <form method="POST" action="<?= BASE_URL ?>/admin/admin_actions.php" enctype="multipart/form-data" class="form">
            <input type="hidden" name="module" value="section_backgrounds">
            <input type="hidden" name="_action" value="save">
            <?php if ($record): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
            <?php endif; ?>
            
            <div class="form-section">
                <h3>Informações da Seção</h3>
                
                <div class="form-group">
                    <label for="section_name">Nome da Seção</label>
                    <input type="text" id="section_name" name="section_name" readonly value="<?= htmlspecialchars($record['section_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="section_label">Rótulo da Seção</label>
                    <input type="text" id="section_label" name="section_label" value="<?= htmlspecialchars($record['section_label'] ?? '') ?>">
                </div>
            </div>
            
            <h2 class="section-title">🎨 <?= htmlspecialchars($record['section_label'] ?? '') ?></h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="background_type">Tipo de Fundo</label>
                    <select id="background_type" name="background_type" onchange="updateBackgroundPreview()">
                        <option value="color" <?= ($record['background_type'] ?? '') === 'color' ? 'selected' : '' ?>>Cor Sólida</option>
                        <option value="image" <?= ($record['background_type'] ?? '') === 'image' ? 'selected' : '' ?>>Imagem</option>
                        <option value="gradient" <?= ($record['background_type'] ?? '') === 'gradient' ? 'selected' : '' ?>>Gradiente</option>
                        <option value="both" <?= ($record['background_type'] ?? '') === 'both' ? 'selected' : '' ?>>Cor + Imagem</option>
                    </select>
                </div>
            </div>

            <div class="form-row two-columns">
                <div class="form-group">
                    <label>Cor de Fundo</label>
                    <div class="color-palette-group">
                        <div class="color-palette">
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #700420;" title="Lovely (Principal)" onclick="selectColor('background_color', '#700420'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #292828;" title="Blackfriars" onclick="selectColor('background_color', '#292828'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #955425;" title="Notting Hill" onclick="selectColor('background_color', '#955425'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #DAB59A;" title="Skyline" onclick="selectColor('background_color', '#DAB59A'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #7FA1C3;" title="Thames" onclick="selectColor('background_color', '#7FA1C3'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #F8F9FA; border: 2px solid #ddd;" title="Fog White" onclick="selectColor('background_color', '#F8F9FA'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #FFFFFF; border: 2px solid #ddd;" title="White" onclick="selectColor('background_color', '#FFFFFF'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #6B7280;" title="Gray" onclick="selectColor('background_color', '#6B7280'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="background_color" style="background-color: #E5E7EB; border: 2px solid #ddd;" title="Light Gray" onclick="selectColor('background_color', '#E5E7EB'); return false;"></button>
                        </div>
                        <div class="color-input-wrapper">
                            <input type="color" id="background_color" name="background_color" value="<?= htmlspecialchars($record['background_color'] ?? '#ffffff') ?>" onchange="updateBackgroundPreview(); updateColorSwatch('background_color');" class="color-picker">
                            <span class="color-value" id="background_color_value"><?= htmlspecialchars(strtoupper($record['background_color'] ?? '#ffffff')) ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Cor do Texto</label>
                    <div class="color-palette-group">
                        <div class="color-palette">
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #700420;" title="Lovely (Principal)" onclick="selectColor('text_color', '#700420'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #292828;" title="Blackfriars" onclick="selectColor('text_color', '#292828'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #955425;" title="Notting Hill" onclick="selectColor('text_color', '#955425'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #DAB59A;" title="Skyline" onclick="selectColor('text_color', '#DAB59A'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #7FA1C3;" title="Thames" onclick="selectColor('text_color', '#7FA1C3'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #F8F9FA; border: 2px solid #ddd;" title="Fog White" onclick="selectColor('text_color', '#F8F9FA'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #FFFFFF; border: 2px solid #ddd;" title="White" onclick="selectColor('text_color', '#FFFFFF'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #6B7280;" title="Gray" onclick="selectColor('text_color', '#6B7280'); return false;"></button>
                            <button type="button" class="color-swatch" data-target="text_color" style="background-color: #E5E7EB; border: 2px solid #ddd;" title="Light Gray" onclick="selectColor('text_color', '#E5E7EB'); return false;"></button>
                        </div>
                        <div class="color-input-wrapper">
                            <input type="color" id="text_color" name="text_color" value="<?= htmlspecialchars($record['text_color'] ?? '#000000') ?>" onchange="updateBackgroundPreview(); updateColorSwatch('text_color');" class="color-picker">
                            <span class="color-value" id="text_color_value"><?= htmlspecialchars(strtoupper($record['text_color'] ?? '#000000')) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="section-title">Gradiente (opcional)</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="gradient_start">Cor Inicial</label>
                    <input type="color" id="gradient_start" name="gradient_start" value="<?= htmlspecialchars($record['gradient_start'] ?? '#ffffff') ?>" onchange="updateBackgroundPreview()">
                </div>
                <div class="form-group">
                    <label for="gradient_end">Cor Final</label>
                    <input type="color" id="gradient_end" name="gradient_end" value="<?= htmlspecialchars($record['gradient_end'] ?? '#000000') ?>" onchange="updateBackgroundPreview()">
                </div>
                <div class="form-group">
                    <label for="gradient_direction">Direção</label>
                    <select id="gradient_direction" name="gradient_direction" onchange="updateBackgroundPreview()">
                        <option value="to bottom" <?= ($record['gradient_direction'] ?? '') === 'to bottom' ? 'selected' : '' ?>>Para Baixo</option>
                        <option value="to right" <?= ($record['gradient_direction'] ?? '') === 'to right' ? 'selected' : '' ?>>Para Direita</option>
                        <option value="to bottom right" <?= ($record['gradient_direction'] ?? '') === 'to bottom right' ? 'selected' : '' ?>>Diagonal</option>
                        <option value="to top" <?= ($record['gradient_direction'] ?? '') === 'to top' ? 'selected' : '' ?>>Para Cima</option>
                    </select>
                </div>
            </div>
            
            <h3 class="section-title">Imagem de Fundo (opcional)</h3>
            <?php if ($record && !empty($record['background_image'])): ?>
            <div class="image-preview">
                <img src="<?= htmlspecialchars($record['background_image']) ?>" alt="Background">
                <input type="hidden" name="background_image_old" value="<?= htmlspecialchars($record['background_image']) ?>">
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="background_image">Upload Imagem (JPG, PNG, WebP, GIF - máx. 5MB)</label>
                <input type="file" id="background_image" name="background_image" accept="image/*">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="image_opacity">Opacidade da Imagem</label>
                    <input type="number" id="image_opacity" name="image_opacity" step="0.01" min="0" max="1" value="<?= $record['image_opacity'] ?? 1 ?>" onchange="updateBackgroundPreview()">
                </div>
                <div class="form-group">
                    <label for="overlay_opacity">Opacidade do Overlay</label>
                    <input type="number" id="overlay_opacity" name="overlay_opacity" step="0.01" min="0" max="1" value="<?= $record['overlay_opacity'] ?? 0 ?>" onchange="updateBackgroundPreview()">
                </div>
                <div class="form-group">
                    <label for="overlay_color">Cor do Overlay</label>
                    <input type="color" id="overlay_color" name="overlay_color" value="<?= htmlspecialchars($record['overlay_color'] ?? '#000000') ?>" onchange="updateBackgroundPreview()">
                </div>
            </div>
            
            <h3 class="section-title">Logo Personalizado (Header/Footer)</h3>
            <?php if ($record && !empty($record['custom_logo'])): ?>
            <div class="image-preview">
                <img src="<?= htmlspecialchars($record['custom_logo']) ?>" alt="Logo" style="max-width: 200px;">
                <input type="hidden" name="custom_logo_old" value="<?= htmlspecialchars($record['custom_logo']) ?>">
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="custom_logo">Upload Logo (PNG, WebP - máx. 5MB)</label>
                <input type="file" id="custom_logo" name="custom_logo" accept="image/*">
            </div>
            
            <h3 class="section-title">📋 Configuração do Formulário de Contato</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="form_title">Título do Formulário</label>
                    <input type="text" id="form_title" name="form_title" value="<?= htmlspecialchars($record['form_title'] ?? 'Planeje Sua Viagem Perfeita') ?>" placeholder="Título principal">
                </div>
                <div class="form-group">
                    <label for="form_subtitle">Subtítulo do Formulário</label>
                    <input type="text" id="form_subtitle" name="form_subtitle" value="<?= htmlspecialchars($record['form_subtitle'] ?? 'Conte-me sobre seus sonhos para Londres...') ?>" placeholder="Subtítulo descritivo">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="form_button_text">Texto do Botão</label>
                    <input type="text" id="form_button_text" name="form_button_text" value="<?= htmlspecialchars($record['form_button_text'] ?? 'Enviar Mensagem') ?>" placeholder="Texto do botão de envio">
                </div>
                <div class="form-group">
                    <label for="form_show_message_field">
                        <input type="checkbox" id="form_show_message_field" name="form_show_message_field" value="1" <?= ($record['form_show_message_field'] ?? 1) ? 'checked' : '' ?>>
                        Mostrar Campo de Mensagem
                    </label>
                </div>
            </div>
            
            <div class="form-group checkbox">
                <input type="checkbox" id="is_active" name="is_active" value="1" <?= ($record['is_active'] ?? 1) ? 'checked' : '' ?>>
                <label for="is_active">Ativo</label>
            </div>
            
            <h3 class="section-title">Visualização em Tempo Real</h3>
            <div class="preview-container">
                <div class="preview-box" id="preview" style="background-color: <?= htmlspecialchars($record['background_color'] ?? '#fff') ?>; color: <?= htmlspecialchars($record['text_color'] ?? '#000') ?>;">
                    Visualização da Seção
                </div>
                <div style="display: flex; flex-direction: column; justify-content: center;">
                    <div style="font-weight: 600; color: #333; margin-bottom: 1rem;">Configuração Atual:</div>
                    <div style="font-size: 14px; color: #666; line-height: 1.8;">
                        <div>📊 Tipo: <strong><?= htmlspecialchars(ucfirst($record['background_type'] ?? 'color')) ?></strong></div>
                        <div>🎨 Cor: <strong><?= htmlspecialchars($record['background_color'] ?? '#ffffff') ?></strong></div>
                        <div>✏️ Texto: <strong><?= htmlspecialchars($record['text_color'] ?? '#000000') ?></strong></div>
                        <div>🖼️ Imagem: <strong><?= !empty($record['background_image']) ? 'Sim' : 'Não' ?></strong></div>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="?action=backgrounds" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Seção</button>
            </div>
        </form>
    </div>

<?php endif; ?>

<style>
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="color"],
.form-group input[type="file"],
.form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #700420;
    box-shadow: 0 0 0 3px rgba(112, 4, 32, 0.1);
}

.form-group.checkbox {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: 1rem;
}

.form-group.checkbox input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.form-group.checkbox label {
    margin-bottom: 0;
}

.image-preview {
    margin: 1rem 0;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 4px;
    text-align: center;
}

.image-preview img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #333;
    margin: 2rem 0 1.5rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #700420;
}

.section-title:first-of-type {
    margin-top: 0;
}

.preview-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin: 2rem 0;
}

.preview-box {
    padding: 2rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid #eee;
}

.preview-label {
    font-size: 12px;
    font-weight: 600;
    color: #999;
    margin-top: 0.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 3rem;
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
    font-size: 14px;
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

.btn-secondary:hover {
    background: #777;
}

.btn-info {
    background: #0066cc;
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
    border-radius: 8px;
    overflow-x: auto;
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

.color-palette-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form-row.two-columns {
    grid-template-columns: 1fr 1fr;
}

.color-palette {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
    gap: 0.5rem;
    padding: 1rem;
    background: #f9f9f9;
    border: 1px solid #eee;
    border-radius: 4px;
}

.color-swatch {
    width: 100%;
    aspect-ratio: 1;
    border: 2px solid transparent;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 0;
}

.color-swatch:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.color-swatch:active,
.color-swatch.selected {
    border-color: #333;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.color-input-wrapper {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.color-picker {
    width: 60px !important;
    height: 45px !important;
    padding: 4px !important;
    cursor: pointer;
    border: 1px solid #ddd !important;
}

.color-value {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    font-family: 'Courier New', monospace;
    flex: 1;
    padding: 0.75rem;
    background: #f5f5f5;
    border-radius: 4px;
    border: 1px solid #ddd;
}

@media (max-width: 768px) {
    .preview-container {
        grid-template-columns: 1fr;
    }
    .form-row {
        grid-template-columns: 1fr;
    }
    .form-row.two-columns {
        grid-template-columns: 1fr;
    }
    .color-palette {
        grid-template-columns: repeat(5, 1fr);
    }
}
</style>

<script>
function selectColor(fieldId, color) {
    const input = document.getElementById(fieldId);
    if (input) {
        input.value = color;
        input.dispatchEvent(new Event('change', { bubbles: true }));
        updateColorSwatch(fieldId);
        updateBackgroundPreview();
    }
}

function updateColorSwatch(fieldId) {
    const input = document.getElementById(fieldId);
    const valueDisplay = document.getElementById(fieldId + '_value');
    
    if (input && valueDisplay) {
        valueDisplay.textContent = input.value.toUpperCase();
    }
}

function updateBackgroundPreview() {
    const bgType = document.getElementById('background_type')?.value || 'color';
    const bgColor = document.getElementById('background_color')?.value || '#fff';
    const textColor = document.getElementById('text_color')?.value || '#000';
    const gradientStart = document.getElementById('gradient_start')?.value || '#fff';
    const gradientEnd = document.getElementById('gradient_end')?.value || '#000';
    const gradientDir = document.getElementById('gradient_direction')?.value || 'to bottom';
    const preview = document.getElementById('preview');
    
    if (preview) {
        preview.style.color = textColor;
        
        if (bgType === 'gradient') {
            preview.style.background = `linear-gradient(${gradientDir}, ${gradientStart}, ${gradientEnd})`;
        } else {
            preview.style.background = bgColor;
        }
    }
}

// Validar formulário antes de enviar
function validateBackgroundForm(form) {
    // Garantir que todos os campos visíveis foram enviados
    const requiredFields = [
        'background_type',
        'background_color',
        'text_color',
        'gradient_start',
        'gradient_end',
        'gradient_direction',
        'image_opacity',
        'overlay_opacity',
        'overlay_color'
    ];
    
    for (const field of requiredFields) {
        const input = document.getElementById(field);
        if (input && !input.value) {
            console.warn('Campo vazio:', field);
        }
    }
    
    return true;
}

// Atualizar preview ao carregar
document.addEventListener('DOMContentLoaded', function() {
    updateBackgroundPreview();
    updateColorSwatch('background_color');
    updateColorSwatch('text_color');
    
    // Adicionar validação ao formulário
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            validateBackgroundForm(form);
        });
    }
});
</script>
