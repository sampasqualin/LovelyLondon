<?php
/**
 * Contact Form Component
 * Componente reutilizável do formulário de contato
 * 
 * Parâmetros opcionais que podem ser passados:
 * $contact_title - Título da seção
 * $contact_subtitle - Subtítulo da seção
 * $contact_button_text - Texto do botão
 * $contact_form_id - ID da seção
 * $contact_show_message_field - Mostrar campo de mensagem
 * 
 * Se não forem passados, busca do banco de dados ou usa valores padrão
 */

// Tentar buscar configurações do banco de dados
global $db;
$db_config = null;
if (isset($db)) {
    try {
        $result = $db->query("SELECT * FROM section_backgrounds LIMIT 1");
        if ($result) {
            $db_config = $result->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // Ignorar erro e usar valores padrão
    }
}

// Valores padrão
$contact_title = $contact_title ?? ($db_config['form_title'] ?? "Planeje Sua Viagem Perfeita");
$contact_subtitle = $contact_subtitle ?? ($db_config['form_subtitle'] ?? "Conte-me sobre seus sonhos para Londres e criaremos uma experiência única, pensada especialmente para você.");
$contact_button_text = $contact_button_text ?? ($db_config['form_button_text'] ?? "Enviar Mensagem");
$contact_form_id = $contact_form_id ?? "contact-section";
$contact_show_message_field = $contact_show_message_field ?? ($db_config['form_show_message_field'] ?? 1);
?>

<!-- Seção de Contato -->
<section id="<?= htmlspecialchars($contact_form_id) ?>" class="section section-contact fade-in" aria-labelledby="contact-heading">
    <div class="container">
        <h2 id="contact-heading" class="section-title"><?= htmlspecialchars($contact_title) ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($contact_subtitle) ?></p>

        <!-- Success Message -->
        <div class="form-message form-success" id="formSuccess" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div>
                <h4>Mensagem Enviada!</h4>
                <p>Obrigado pelo contato. Responderemos em breve!</p>
            </div>
        </div>

        <!-- Error Message -->
        <div class="form-message form-error" id="formError" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <div>
                <h4>Erro ao Enviar</h4>
                <p>Houve um problema ao enviar sua mensagem. Por favor, tente novamente.</p>
            </div>
        </div>

        <form class="contact-form" action="https://formspree.io/f/xblaekww" method="POST" aria-label="Formulário de contato">
            <div class="form-group">
                <label for="name">
                    Nome <span class="required-indicator">*</span>
                </label>
                <input type="text" id="name" name="name" required placeholder="Seu nome completo">
                <span class="field-error" id="nameError"></span>
            </div>
            <div class="form-group">
                <label for="email">
                    E-mail <span class="required-indicator">*</span>
                </label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
                <span class="field-error" id="emailError"></span>
            </div>
            <?php if ($contact_show_message_field): ?>
            <div class="form-group">
                <label for="message">
                    Mensagem <span class="required-indicator">*</span>
                </label>
                <textarea id="message" name="message" rows="5" placeholder="Conte-me sobre seus sonhos para Londres..." required maxlength="1000"></textarea>
                <div class="textarea-footer">
                    <span class="field-error" id="messageError"></span>
                    <span class="char-counter" id="messageCounter">0/1000</span>
                </div>
            </div>
            <?php endif; ?>
            <button type="submit" class="submit-btn">
                <span class="btn-text"><?= htmlspecialchars($contact_button_text) ?></span>
                <span class="btn-loader" style="display: none;">
                    <svg class="spinner" viewBox="0 0 50 50">
                        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                    </svg>
                    Enviando...
                </span>
            </button>
        </form>
    </div>
</section>
