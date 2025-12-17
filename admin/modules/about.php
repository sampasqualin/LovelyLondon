<?php
/**
 * =========================================================================
 * MÓDULO ABOUT - Editar Textos da Seção Sobre
 * =========================================================================
 */

$base_path = BASE_URL;

// Carregar dados do about
$aboutData = [];
try {
    $jsonFile = DATA_PATH . '/about_content.json';
    if (file_exists($jsonFile)) {
        $json = file_get_contents($jsonFile);
        $aboutData = json_decode($json, true) ?? [];
    }
} catch (Exception $e) {
    $aboutData = [];
}

$op = $_GET['op'] ?? 'list';

?>

<link rel="stylesheet" href="<?= BASE_URL ?>/admin/css/admin.css">

<div class="page-header">
    <h2 class="page-title">📝 Editar Seção Sobre</h2>
</div>

<?php displayFlash(); ?>

<?php if ($op === 'list'): ?>
    <!-- LISTAGEM -->
    <div class="about-sections-grid">
        
        <!-- Seção: Sobre Carol -->
        <div class="about-section-card">
            <div class="about-section-header">
                <h3>👩 Sobre Carol</h3>
                <p class="about-section-subtitle">Texto de apresentação pessoal</p>
            </div>
            <div class="about-section-preview">
                <strong><?= htmlspecialchars($aboutData['about_carol']['title_pt'] ?? 'Não definido') ?></strong>
                <p><?= htmlspecialchars(substr($aboutData['about_carol']['subtitle_pt'] ?? '', 0, 100)) ?>...</p>
            </div>
            <a href="?action=about&op=edit&section=about_carol" class="btn btn-primary">Editar</a>
        </div>

        <!-- Seção: Sobre Lovely London -->
        <div class="about-section-card">
            <div class="about-section-header">
                <h3>🌍 Sobre Lovely London</h3>
                <p class="about-section-subtitle">Texto sobre a empresa</p>
            </div>
            <div class="about-section-preview">
                <strong><?= htmlspecialchars($aboutData['about_lovely_london']['title_pt'] ?? 'Não definido') ?></strong>
                <p><?= htmlspecialchars(substr($aboutData['about_lovely_london']['subtitle_pt'] ?? '', 0, 100)) ?>...</p>
            </div>
            <a href="?action=about&op=edit&section=about_lovely_london" class="btn btn-primary">Editar</a>
        </div>

        <!-- Seção: Links Sociais -->
        <div class="about-section-card">
            <div class="about-section-header">
                <h3>🔗 Links Sociais</h3>
                <p class="about-section-subtitle">Instagram, Facebook, TikTok</p>
            </div>
            <div class="about-section-preview">
                <p>Instagram | Facebook | TikTok</p>
            </div>
            <a href="?action=about&op=edit&section=social_links" class="btn btn-primary">Editar</a>
        </div>

    </div>

<?php elseif ($op === 'edit'): ?>
    <?php
    $section = $_GET['section'] ?? 'about_carol';
    $data = $aboutData[$section] ?? [];
    $jsonFile = DATA_PATH . '/about_content.json';
    ?>
    
    <!-- FORMULÁRIO DE EDIÇÃO -->
    <div class="form-container">
        
        <?php if ($section === 'about_carol'): ?>
            <h2>Editar: Sobre Carol</h2>
            <form method="POST" action="<?= BASE_URL ?>/admin/admin_about_actions.php" enctype="multipart/form-data" class="form">
                <input type="hidden" name="section" value="about_carol">
                
                <div class="form-section">
                    <h3>Imagem de Perfil</h3>
                    
                    <?php if (!empty($data['image'])): ?>
                    <div class="image-preview">
                        <img src="<?= htmlspecialchars($base_path . $data['image']) ?>" alt="Foto Carol">
                        <input type="hidden" name="image_old" value="<?= htmlspecialchars($data['image']) ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="image">Atualizar Foto (JPG, PNG, WebP - máx. 5MB)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Português</h3>
                    
                    <div class="form-group">
                        <label for="title_pt">Título (PT)</label>
                        <input type="text" id="title_pt" name="title_pt" value="<?= htmlspecialchars($data['title_pt'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subtitle_pt">Subtítulo (PT)</label>
                        <textarea id="subtitle_pt" name="subtitle_pt" rows="2" required><?= htmlspecialchars($data['subtitle_pt'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content_pt">Conteúdo (PT)</label>
                        <textarea id="content_pt" name="content_pt" rows="4" required><?= htmlspecialchars($data['content_pt'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>English</h3>
                    
                    <div class="form-group">
                        <label for="title_en">Title (EN)</label>
                        <input type="text" id="title_en" name="title_en" value="<?= htmlspecialchars($data['title_en'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subtitle_en">Subtitle (EN)</label>
                        <textarea id="subtitle_en" name="subtitle_en" rows="2" required><?= htmlspecialchars($data['subtitle_en'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content_en">Content (EN)</label>
                        <textarea id="content_en" name="content_en" rows="4" required><?= htmlspecialchars($data['content_en'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="?action=about" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>

        <?php elseif ($section === 'about_lovely_london'): ?>
            <h2>Editar: Sobre Lovely London</h2>
            <form method="POST" action="<?= BASE_URL ?>/admin/admin_about_actions.php" enctype="multipart/form-data" class="form">
                <input type="hidden" name="section" value="about_lovely_london">
                
                <div class="form-section">
                    <h3>Imagem de Destaque</h3>
                    
                    <?php if (!empty($data['image'])): ?>
                    <div class="image-preview">
                        <img src="<?= htmlspecialchars($base_path . $data['image']) ?>" alt="Lovely London">
                        <input type="hidden" name="image_old" value="<?= htmlspecialchars($data['image']) ?>">
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="image">Atualizar Foto (JPG, PNG, WebP - máx. 5MB)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>Português</h3>
                    
                    <div class="form-group">
                        <label for="title_pt">Título (PT)</label>
                        <input type="text" id="title_pt" name="title_pt" value="<?= htmlspecialchars($data['title_pt'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subtitle_pt">Subtítulo (PT)</label>
                        <textarea id="subtitle_pt" name="subtitle_pt" rows="2" required><?= htmlspecialchars($data['subtitle_pt'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content_pt">Conteúdo (PT)</label>
                        <textarea id="content_pt" name="content_pt" rows="6" required><?= htmlspecialchars($data['content_pt'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3>English</h3>
                    
                    <div class="form-group">
                        <label for="title_en">Title (EN)</label>
                        <input type="text" id="title_en" name="title_en" value="<?= htmlspecialchars($data['title_en'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subtitle_en">Subtitle (EN)</label>
                        <textarea id="subtitle_en" name="subtitle_en" rows="2" required><?= htmlspecialchars($data['subtitle_en'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content_en">Content (EN)</label>
                        <textarea id="content_en" name="content_en" rows="6" required><?= htmlspecialchars($data['content_en'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="?action=about" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>

        <?php elseif ($section === 'social_links'): ?>
            <h2>Editar: Links Sociais</h2>
            <form method="POST" action="<?= BASE_URL ?>/admin/admin_about_actions.php" class="form">
                <input type="hidden" name="section" value="social_links">
                
                <div class="form-section">
                    <h3>Redes Sociais</h3>
                    
                    <div class="form-group">
                        <label for="instagram">Instagram URL</label>
                        <input type="url" id="instagram" name="instagram" value="<?= htmlspecialchars($data['instagram'] ?? '') ?>" placeholder="https://www.instagram.com/..." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="facebook">Facebook URL</label>
                        <input type="url" id="facebook" name="facebook" value="<?= htmlspecialchars($data['facebook'] ?? '') ?>" placeholder="https://www.facebook.com/..." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tiktok">TikTok URL</label>
                        <input type="url" id="tiktok" name="tiktok" value="<?= htmlspecialchars($data['tiktok'] ?? '') ?>" placeholder="https://www.tiktok.com/@..." required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="?action=about" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        <?php endif; ?>
        
    </div>

<?php endif; ?>
