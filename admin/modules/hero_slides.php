<?php
/**
 * =========================================================================
 * MÓDULO HERO_SLIDES - Gerenciar Slides da Homepage
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
    $result = $db->query("SELECT * FROM hero_slides");
    if ($result) {
        $records = $result->fetchAll(PDO::FETCH_ASSOC);
        usort($records, function($a, $b) {
            return ($a['slide_number'] ?? 0) - ($b['slide_number'] ?? 0);
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
    <h2>🎬 Gerenciar Hero Slides</h2>
    <?php if ($op !== 'new' && $op !== 'edit'): ?>
        <a href="?action=hero_slides&op=new" class="btn btn-primary">+ Novo Slide</a>
    <?php endif; ?>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM -->
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Alinhamento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $slide): ?>
                <tr>
                    <td><?= $slide['slide_number'] ?? 0 ?></td>
                    <td><?= htmlspecialchars(strip_tags($slide['title_pt'] ?? '')) ?></td>
                    <td>
                        <span class="badge badge-info"><?= htmlspecialchars($slide['slide_type'] ?? '') ?></span>
                    </td>
                    <td><?= htmlspecialchars($slide['content_alignment'] ?? 'left') ?></td>
                    <td>
                        <?php if ($slide['is_active']): ?>
                            <span class="badge badge-success">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inativo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?action=hero_slides&op=edit&id=<?= urlencode($slide['id']) ?>" class="btn btn-sm btn-info">Editar</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="deleteRecord('hero_slides', '<?= htmlspecialchars($slide['id']) ?>'); return false;">Deletar</a>
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
            <input type="hidden" name="module" value="hero_slides">
            <input type="hidden" name="_action" value="save">
            <?php if ($record): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
            <?php endif; ?>

            <div class="form-section">
                <h3>⚙️ Configurações Básicas</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="slide_number">Número do Slide *</label>
                        <input type="number" id="slide_number" name="slide_number" required value="<?= $record['slide_number'] ?? 0 ?>">
                    </div>

                    <div class="form-group">
                        <label for="slide_type">Tipo de Slide *</label>
                        <select id="slide_type" name="slide_type" required>
                            <option value="split" <?= ($record['slide_type'] ?? '') === 'split' ? 'selected' : '' ?>>Dividido (Split)</option>
                            <option value="full" <?= ($record['slide_type'] ?? '') === 'full' ? 'selected' : '' ?>>Completo (Full)</option>
                            <option value="text" <?= ($record['slide_type'] ?? '') === 'text' ? 'selected' : '' ?>>Apenas Texto</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="content_alignment">Alinhamento do Conteúdo</label>
                        <select id="content_alignment" name="content_alignment">
                            <option value="left" <?= ($record['content_alignment'] ?? 'left') === 'left' ? 'selected' : '' ?>>Esquerda</option>
                            <option value="center" <?= ($record['content_alignment'] ?? '') === 'center' ? 'selected' : '' ?>>Centro</option>
                            <option value="right" <?= ($record['content_alignment'] ?? '') === 'right' ? 'selected' : '' ?>>Direita</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="display_order">Ordem de Exibição</label>
                        <input type="number" id="display_order" name="display_order" value="<?= $record['display_order'] ?? 1 ?>">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>📝 Conteúdo</h3>

                <div class="form-group">
                    <label for="title_pt">Título (PT) *</label>
                    <textarea id="title_pt" name="title_pt" required rows="2"><?= htmlspecialchars($record['title_pt'] ?? '') ?></textarea>
                    <small>Você pode usar HTML e tags style para formatação especial</small>
                </div>

                <div class="form-group">
                    <label for="title_en">Título (EN)</label>
                    <textarea id="title_en" name="title_en" rows="2"><?= htmlspecialchars($record['title_en'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="subtitle_pt">Subtítulo (PT)</label>
                    <textarea id="subtitle_pt" name="subtitle_pt" rows="3"><?= htmlspecialchars($record['subtitle_pt'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="subtitle_en">Subtítulo (EN)</label>
                    <textarea id="subtitle_en" name="subtitle_en" rows="3"><?= htmlspecialchars($record['subtitle_en'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>🎨 Cores de Texto</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="title_color">Cor do Título</label>
                        <input type="color" id="title_color" name="title_color" value="<?= htmlspecialchars($record['title_color'] ?? '#ffffff') ?>">
                    </div>

                    <div class="form-group">
                        <label for="subtitle_color">Cor do Subtítulo</label>
                        <input type="color" id="subtitle_color" name="subtitle_color" value="<?= htmlspecialchars($record['subtitle_color'] ?? '#f0f0f0') ?>">
                    </div>

                    <div class="form-group">
                        <label for="text_shadow">Sombra do Texto (CSS)</label>
                        <input type="text" id="text_shadow" name="text_shadow" value="<?= htmlspecialchars($record['text_shadow'] ?? '0 2px 4px rgba(0,0,0,0.5)') ?>" placeholder="0 2px 4px rgba(0,0,0,0.5)">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>🔘 Botão de Ação (CTA)</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cta_text_pt">Texto do Botão (PT)</label>
                        <input type="text" id="cta_text_pt" name="cta_text_pt" value="<?= htmlspecialchars($record['cta_text_pt'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="cta_text_en">Texto do Botão (EN)</label>
                        <input type="text" id="cta_text_en" name="cta_text_en" value="<?= htmlspecialchars($record['cta_text_en'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="cta_url">URL do Botão</label>
                        <input type="text" id="cta_url" name="cta_url" value="<?= htmlspecialchars($record['cta_url'] ?? '') ?>" placeholder="/pages/tours.php">
                    </div>
                </div>

                <h4 class="mt-3">Cores do Botão</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="button_bg_color">Cor de Fundo</label>
                        <input type="color" id="button_bg_color" name="button_bg_color" value="<?= htmlspecialchars($record['button_bg_color'] ?? '#700420') ?>">
                    </div>

                    <div class="form-group">
                        <label for="button_text_color">Cor do Texto</label>
                        <input type="color" id="button_text_color" name="button_text_color" value="<?= htmlspecialchars($record['button_text_color'] ?? '#ffffff') ?>">
                    </div>

                    <div class="form-group">
                        <label for="button_hover_bg">Cor de Fundo (Hover)</label>
                        <input type="color" id="button_hover_bg" name="button_hover_bg" value="<?= htmlspecialchars($record['button_hover_bg'] ?? '#e3ab67') ?>">
                    </div>

                    <div class="form-group">
                        <label for="button_hover_text">Cor do Texto (Hover)</label>
                        <input type="color" id="button_hover_text" name="button_hover_text" value="<?= htmlspecialchars($record['button_hover_text'] ?? '#ffffff') ?>">
                    </div>
                </div>
            </div>

            <!-- SEÇÃO AVANÇADA - CARDS (para slide tipo split) -->
            <div class="form-section advanced-section" id="cards-section">
                <div class="advanced-header">
                    <h3>🃏 Cards (Slides Tipo "Split")</h3>
                    <button type="button" class="btn btn-sm btn-secondary toggle-advanced" onclick="toggleSection('cards-content')">
                        Mostrar/Ocultar
                    </button>
                </div>

                <div id="cards-content" class="advanced-content" style="display: none;">
                    <div class="form-group checkbox mb-3">
                        <input type="checkbox" id="show_cards" name="show_cards" value="1" <?= ($record['show_cards'] ?? 1) ? 'checked' : '' ?>>
                        <label for="show_cards">Exibir Cards</label>
                    </div>

                    <h4>Aparência dos Cards</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="card_bg_color">Cor de Fundo</label>
                            <input type="color" id="card_bg_color" name="card_bg_color" value="<?= htmlspecialchars($record['card_bg_color'] ?? '#f0f0f0') ?>">
                        </div>

                        <div class="form-group">
                            <label for="card_bg_opacity">Opacidade (%)</label>
                            <input type="number" id="card_bg_opacity" name="card_bg_opacity" min="0" max="100" value="<?= $record['card_bg_opacity'] ?? 84 ?>">
                        </div>

                        <div class="form-group">
                            <label for="card_text_color">Cor do Texto</label>
                            <input type="color" id="card_text_color" name="card_text_color" value="<?= htmlspecialchars($record['card_text_color'] ?? '#666060') ?>">
                        </div>
                    </div>

                    <h4 class="mt-3">Borda dos Cards</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="card_border_color">Cor da Borda</label>
                            <input type="color" id="card_border_color" name="card_border_color" value="<?= htmlspecialchars($record['card_border_color'] ?? '#5a0319') ?>">
                        </div>

                        <div class="form-group">
                            <label for="card_border_width">Largura da Borda (px)</label>
                            <input type="number" id="card_border_width" name="card_border_width" min="0" max="10" value="<?= $record['card_border_width'] ?? 1 ?>">
                        </div>

                        <div class="form-group">
                            <label for="card_border_radius">Raio da Borda (px)</label>
                            <input type="number" id="card_border_radius" name="card_border_radius" min="0" max="50" value="<?= $record['card_border_radius'] ?? 6 ?>">
                        </div>
                    </div>

                    <h4 class="mt-3">Efeitos Hover</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="card_hover_effect">Efeito Hover</label>
                            <select id="card_hover_effect" name="card_hover_effect">
                                <option value="none" <?= ($record['card_hover_effect'] ?? '') === 'none' ? 'selected' : '' ?>>Nenhum</option>
                                <option value="zoom" <?= ($record['card_hover_effect'] ?? 'zoom') === 'zoom' ? 'selected' : '' ?>>Zoom</option>
                                <option value="lift" <?= ($record['card_hover_effect'] ?? '') === 'lift' ? 'selected' : '' ?>>Levitar</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="card_hover_shadow">Sombra Hover (CSS)</label>
                            <input type="text" id="card_hover_shadow" name="card_hover_shadow" value="<?= htmlspecialchars($record['card_hover_shadow'] ?? '0 10px 30px rgba(0,0,0,0.2)') ?>" placeholder="0 10px 30px rgba(0,0,0,0.2)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO AVANÇADA - MÍDIA DE FUNDO -->
            <div class="form-section">
                <h3>🖼️ Mídia de Fundo</h3>

                <div class="form-group">
                    <label for="media_type">Tipo de Mídia</label>
                    <select id="media_type" name="media_type">
                        <option value="image" <?= ($record['media_type'] ?? 'image') === 'image' ? 'selected' : '' ?>>Imagem</option>
                        <option value="video" <?= ($record['media_type'] ?? '') === 'video' ? 'selected' : '' ?>>Vídeo</option>
                    </select>
                </div>

                <?php if ($record && !empty($record['background_image'])): ?>
                <div class="image-preview">
                    <p><strong>Imagem Atual:</strong></p>
                    <img src="<?= htmlspecialchars($record['background_image']) ?>" alt="Hero Background" style="max-width: 400px;">
                    <input type="hidden" name="background_image_old" value="<?= htmlspecialchars($record['background_image']) ?>">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="background_image">Upload Imagem de Fundo</label>
                    <input type="file" id="background_image" name="background_image" accept="image/*">
                    <small>JPG, PNG, WebP, GIF - máx. 5MB</small>
                </div>

                <?php if ($record && !empty($record['background_video'])): ?>
                <div class="image-preview">
                    <p><strong>Vídeo Atual:</strong> <?= htmlspecialchars(basename($record['background_video'])) ?></p>
                    <input type="hidden" name="background_video_old" value="<?= htmlspecialchars($record['background_video']) ?>">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="background_video">Upload Vídeo de Fundo (Opcional)</label>
                    <input type="file" id="background_video" name="background_video" accept="video/mp4,video/webm,video/ogg">
                    <small>MP4, WebM, OGG - máx. 50MB. O vídeo será exibido como fundo do hero.</small>
                </div>

                <h4 class="mt-3">Overlay (Camada Escura)</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="overlay_color">Cor do Overlay</label>
                        <input type="text" id="overlay_color" name="overlay_color" value="<?= htmlspecialchars($record['overlay_color'] ?? 'rgba(0,0,0,0.5)') ?>" placeholder="rgba(0,0,0,0.5)">
                    </div>

                    <div class="form-group">
                        <label for="overlay_opacity">Opacidade do Overlay (0-1)</label>
                        <input type="number" id="overlay_opacity" name="overlay_opacity" step="0.1" min="0" max="1" value="<?= $record['overlay_opacity'] ?? 0.5 ?>">
                    </div>
                </div>
            </div>

            <!-- SEÇÃO AVANÇADA - ANIMAÇÕES -->
            <div class="form-section advanced-section" id="animation-section">
                <div class="advanced-header">
                    <h3>✨ Animações</h3>
                    <button type="button" class="btn btn-sm btn-secondary toggle-advanced" onclick="toggleSection('animation-content')">
                        Mostrar/Ocultar
                    </button>
                </div>

                <div id="animation-content" class="advanced-content" style="display: none;">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="animation_type">Tipo de Animação</label>
                            <select id="animation_type" name="animation_type">
                                <option value="fade" <?= ($record['animation_type'] ?? 'slide') === 'fade' ? 'selected' : '' ?>>Fade</option>
                                <option value="slide" <?= ($record['animation_type'] ?? 'slide') === 'slide' ? 'selected' : '' ?>>Slide</option>
                                <option value="zoom" <?= ($record['animation_type'] ?? '') === 'zoom' ? 'selected' : '' ?>>Zoom</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="animation_duration">Duração (ms)</label>
                            <input type="number" id="animation_duration" name="animation_duration" value="<?= $record['animation_duration'] ?? 5000 ?>" step="100" min="1000">
                            <small>Tempo em milissegundos (1000 = 1 segundo)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>⚡ Status</h3>

                <div class="form-group checkbox">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?= ($record['is_active'] ?? 1) ? 'checked' : '' ?>>
                    <label for="is_active">Slide Ativo</label>
                </div>
            </div>

            <div class="form-actions">
                <a href="?action=hero_slides" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= $record ? '💾 Atualizar Slide' : '✨ Criar Slide' ?>
                </button>
            </div>
        </form>
    </div>

<?php endif; ?>

<style>
.advanced-section {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    background: #fafafa;
}

.advanced-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.advanced-header h3 {
    margin-bottom: 0;
}

.advanced-content {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #ddd;
}

.toggle-advanced {
    font-size: 12px;
    padding: 0.4rem 0.8rem;
}

h4 {
    margin-top: 1rem;
    margin-bottom: 0.75rem;
    color: #555;
    font-size: 16px;
}

.mt-3 {
    margin-top: 1.5rem !important;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
}
</style>

<script>
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section.style.display === 'none') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

function deleteRecord(module, id) {
    if (confirm('Tem certeza que deseja deletar este registro?')) {
        window.location.href = '<?= BASE_URL ?>/admin/admin_actions.php?module=' + module + '&_action=delete&id=' + encodeURIComponent(id);
    }
}

// Auto-toggle media sections based on media_type
document.addEventListener('DOMContentLoaded', function() {
    const mediaType = document.getElementById('media_type');
    if (mediaType) {
        mediaType.addEventListener('change', function() {
            // You can add logic here to show/hide image vs video upload sections
        });
    }
});
</script>
