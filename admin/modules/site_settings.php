<?php
/**
 * =========================================================================
 * MÓDULO SITE_SETTINGS - Configurações Gerais do Site
 * =========================================================================
 */

// Função para salvar no JSON (igual ao admin_actions.php)
function saveToJSON($table, $records) {
    $filePath = DATA_PATH . '/' . $table . '.json';
    $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if (file_put_contents($filePath, $json) === false) {
        throw new Exception('Erro ao salvar arquivo JSON');
    }
}

// Processar POST antes de qualquer output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Iniciar buffer de output para permitir redirect
    ob_start();

    try {
        // Lista de campos esperados
        $expected_fields = [
            'site_title', 'site_description', 'contact_email', 'contact_phone',
            'whatsapp_number', 'whatsapp_message',
            'social_instagram', 'social_facebook', 'social_x', 'social_tiktok',
            'social_linkedin', 'social_youtube', 'social_spotify', 'social_pinterest',
            'google_analytics', 'booking_enabled'
        ];

        // Carregar todos os registros atuais
        $records = $db->query("SELECT * FROM site_settings")->fetchAll(PDO::FETCH_ASSOC);

        $success_count = 0;
        $updated_records = [];

        foreach ($expected_fields as $key) {
            // Checkbox precisa de tratamento especial
            if ($key === 'booking_enabled') {
                $value = isset($_POST[$key]) ? '1' : '0';
            } else {
                $value = $_POST[$key] ?? '';
            }

            // Trim em campos de texto
            $value = is_string($value) ? trim($value) : $value;

            // Procurar se o campo já existe
            $found = false;
            foreach ($records as $index => $record) {
                if ($record['setting_key'] === $key) {
                    // Atualizar existente
                    $records[$index]['setting_value'] = $value;
                    $records[$index]['updated_at'] = date('Y-m-d H:i:s');
                    $found = true;
                    $success_count++;
                    break;
                }
            }

            // Se não encontrou, criar novo
            if (!$found) {
                $max_id = 0;
                foreach ($records as $rec) {
                    if ($rec['id'] > $max_id) $max_id = $rec['id'];
                }

                $now = date('Y-m-d H:i:s');
                $records[] = [
                    'id' => $max_id + 1,
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_type' => 'text',
                    'description_pt' => '',
                    'description_en' => '',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                $success_count++;
            }
        }

        // SALVAR NO JSON (esta é a parte crucial!)
        saveToJSON('site_settings', $records);

        setFlash('success', "Configurações salvas com sucesso! ($success_count campos atualizados)");

        // Limpar buffer e redirecionar
        ob_end_clean();
        header('Location: ' . BASE_URL . '/admin/index.php?action=site_settings');
        exit;
    } catch (Exception $e) {
        ob_end_clean();
        setFlash('error', 'Erro ao salvar: ' . $e->getMessage());
        header('Location: ' . BASE_URL . '/admin/index.php?action=site_settings');
        exit;
    }
}

// Verificar que a config foi carregada
if (!defined('DATA_PATH')) {
    require_once __DIR__ . '/../config.php';
}

// Ícones SVG para redes sociais
$social_icons = [
    'instagram' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="m16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
    'facebook' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
    'twitter' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>',
    'x' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>',
    'linkedin' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
    'youtube' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
    'tiktok' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
    'spotify' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M8 14.5c2-1 4.5-1.5 7-1"></path><path d="M9 12c1.5-.5 3.5-1 6-.5"></path><path d="M9.5 9.5c2-.5 4.5-.5 6.5 0"></path></svg>',
    'whatsapp' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"></path><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"></path></svg>',
    'pinterest' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="21" x2="12" y2="13"></line><line x1="15" y1="9.34" x2="15" y2="9.35"></line><circle cx="12" cy="12" r="10"></circle><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.25-1.42-1.33-2.67-2.58-3.59-4.16-1.02-1.75-1.76-3.67-2.44-5.84"></path></svg>',
];

// Carregar configurações para exibir no formulário
$settings = [];
try {
    $result = $db->query("SELECT * FROM site_settings ORDER BY id ASC");
    if ($result) {
        $all_settings = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach ($all_settings as $setting) {
            $settings[$setting['setting_key']] = $setting;
        }
    }
} catch (Exception $e) {
    $settings = [];
}

?>

<div class="module-header">
    <h2>⚙️ Configurações Gerais do Site</h2>
</div>

<?php displayFlash(); ?>

<form method="POST" class="form">
    <input type="hidden" name="save_settings" value="1">

    <!-- INFORMAÇÕES DO SITE -->
    <div class="form-section">
        <h3>🏢 Informações do Site</h3>

        <div class="form-group">
            <label for="site_title">Nome do Site</label>
            <input type="text" id="site_title" name="site_title"
                   value="<?= htmlspecialchars($settings['site_title']['setting_value'] ?? 'Lovely London by Carol') ?>">
        </div>

        <div class="form-group">
            <label for="site_description">Descrição do Site</label>
            <textarea id="site_description" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description']['setting_value'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- CONTATO -->
    <div class="form-section">
        <h3>📞 Informações de Contato</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="contact_email">Email</label>
                <input type="email" id="contact_email" name="contact_email"
                       value="<?= htmlspecialchars($settings['contact_email']['setting_value'] ?? 'carol@lovelylondonbycarol.com') ?>"
                       placeholder="carol@lovelylondonbycarol.com">
            </div>

            <div class="form-group">
                <label for="contact_phone">Telefone</label>
                <input type="text" id="contact_phone" name="contact_phone"
                       value="<?= htmlspecialchars($settings['contact_phone']['setting_value'] ?? '') ?>"
                       placeholder="+44 7950 400919">
            </div>
        </div>

        <div class="form-group">
            <label for="whatsapp_number">WhatsApp (apenas números)</label>
            <input type="text" id="whatsapp_number" name="whatsapp_number"
                   value="<?= htmlspecialchars($settings['whatsapp_number']['setting_value'] ?? '447950400919') ?>"
                   placeholder="447950400919">
            <small>Formato: código do país + DDD + número (sem espaços ou símbolos)</small>
        </div>

        <div class="form-group">
            <label for="whatsapp_message">Mensagem Padrão WhatsApp</label>
            <textarea id="whatsapp_message" name="whatsapp_message" rows="2"><?= htmlspecialchars($settings['whatsapp_message']['setting_value'] ?? 'Olá! Gostaria de saber mais sobre os tours em Londres') ?></textarea>
        </div>
    </div>

    <!-- REDES SOCIAIS -->
    <div class="form-section">
        <h3>🌐 Redes Sociais</h3>
        <p class="text-muted mb-3">Cole a URL completa de cada rede social. Deixe em branco para não exibir.</p>

        <div class="social-grid">
            <!-- Instagram -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['instagram'] ?></div>
                <div class="social-input">
                    <label for="social_instagram">Instagram</label>
                    <input type="url" id="social_instagram" name="social_instagram"
                           value="<?= htmlspecialchars($settings['social_instagram']['setting_value'] ?? '') ?>"
                           placeholder="https://www.instagram.com/lovelylondonbycarol/">
                </div>
            </div>

            <!-- Facebook -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['facebook'] ?></div>
                <div class="social-input">
                    <label for="social_facebook">Facebook</label>
                    <input type="url" id="social_facebook" name="social_facebook"
                           value="<?= htmlspecialchars($settings['social_facebook']['setting_value'] ?? '') ?>"
                           placeholder="https://www.facebook.com/lovelylondonbycarol">
                </div>
            </div>

            <!-- X (Twitter) -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['x'] ?></div>
                <div class="social-input">
                    <label for="social_x">X (Twitter)</label>
                    <input type="url" id="social_x" name="social_x"
                           value="<?= htmlspecialchars($settings['social_x']['setting_value'] ?? '') ?>"
                           placeholder="https://x.com/lovelylondoncar">
                </div>
            </div>

            <!-- TikTok -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['tiktok'] ?></div>
                <div class="social-input">
                    <label for="social_tiktok">TikTok</label>
                    <input type="url" id="social_tiktok" name="social_tiktok"
                           value="<?= htmlspecialchars($settings['social_tiktok']['setting_value'] ?? '') ?>"
                           placeholder="https://www.tiktok.com/@lovelylondonbycarol">
                </div>
            </div>

            <!-- LinkedIn -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['linkedin'] ?></div>
                <div class="social-input">
                    <label for="social_linkedin">LinkedIn</label>
                    <input type="url" id="social_linkedin" name="social_linkedin"
                           value="<?= htmlspecialchars($settings['social_linkedin']['setting_value'] ?? '') ?>"
                           placeholder="https://www.linkedin.com/in/carol-pachi">
                </div>
            </div>

            <!-- YouTube -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['youtube'] ?></div>
                <div class="social-input">
                    <label for="social_youtube">YouTube</label>
                    <input type="url" id="social_youtube" name="social_youtube"
                           value="<?= htmlspecialchars($settings['social_youtube']['setting_value'] ?? '') ?>"
                           placeholder="https://www.youtube.com/@lovelylondonbycarol">
                </div>
            </div>

            <!-- Spotify -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['spotify'] ?></div>
                <div class="social-input">
                    <label for="social_spotify">Spotify</label>
                    <input type="url" id="social_spotify" name="social_spotify"
                           value="<?= htmlspecialchars($settings['social_spotify']['setting_value'] ?? '') ?>"
                           placeholder="https://open.spotify.com/user/...">
                </div>
            </div>

            <!-- Pinterest -->
            <div class="social-item">
                <div class="social-icon"><?= $social_icons['pinterest'] ?></div>
                <div class="social-input">
                    <label for="social_pinterest">Pinterest</label>
                    <input type="url" id="social_pinterest" name="social_pinterest"
                           value="<?= htmlspecialchars($settings['social_pinterest']['setting_value'] ?? '') ?>"
                           placeholder="https://www.pinterest.com/lovelylondonbycarol">
                </div>
            </div>
        </div>
    </div>

    <!-- OUTRAS CONFIGURAÇÕES -->
    <div class="form-section">
        <h3>🔧 Outras Configurações</h3>

        <div class="form-group">
            <label for="google_analytics">Google Analytics ID</label>
            <input type="text" id="google_analytics" name="google_analytics"
                   value="<?= htmlspecialchars($settings['google_analytics']['setting_value'] ?? '') ?>"
                   placeholder="G-XXXXXXXXXX">
            <small>ID do Google Analytics 4 (GA4)</small>
        </div>

        <div class="form-group checkbox">
            <input type="checkbox" id="booking_enabled" name="booking_enabled" value="1"
                   <?= ($settings['booking_enabled']['setting_value'] ?? '1') == '1' ? 'checked' : '' ?>>
            <label for="booking_enabled">Reservas Online Habilitadas</label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">
            💾 Salvar Todas as Configurações
        </button>
    </div>
</form>

<style>
.social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.social-item {
    display: flex;
    align-items: start;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #fafafa;
    transition: all 0.3s;
}

.social-item:hover {
    border-color: #700420;
    background: white;
    box-shadow: 0 2px 8px rgba(112, 4, 32, 0.1);
}

.social-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 8px;
    color: #700420;
}

.social-icon svg {
    width: 24px;
    height: 24px;
}

.social-input {
    flex: 1;
}

.social-input label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.social-input input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.social-input input:focus {
    outline: none;
    border-color: #700420;
    box-shadow: 0 0 0 3px rgba(112, 4, 32, 0.1);
}

.mb-3 {
    margin-bottom: 1.5rem;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 16px;
}
</style>
