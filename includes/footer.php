<?php
// Buscar logo customizado do footer
if (!function_exists('getCustomLogo')) {
    require_once(__DIR__ . '/get_section_config.php');
}
$footerLogo = getCustomLogo('footer', $base_path . '/assets/images/art/logo.png');
$footerTextColor = getSectionTextColor('footer');

// Buscar configurações do site
if (!function_exists('getSiteSetting')) {
    require_once(__DIR__ . '/site_settings_helpers.php');
}
$contactEmail = getSiteSetting('contact_email', 'carol@lovelylondonbycarol.com');
$contactPhone = getSiteSetting('contact_phone', '+44 7950 400919');
$whatsappNumber = getSiteSetting('whatsapp_number', '447950400919');
$whatsappMessage = getSiteSetting('whatsapp_message', 'Olá! Gostaria de saber mais sobre os tours em Londres');
$socialMedia = getSocialMedia();
?>
    <footer class="footer section-footer" id="footer-section">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="<?php echo $footerLogo; ?>" alt="Lovely London by Carol" style="max-width: 180px; margin-bottom: 16px;">
                    <p>Criando memórias inesquecíveis em Londres, com a sua guia brasileira Carol.</p>
                </div>
                <div class="footer-section">
                    <h4>Navegação</h4>
                    <a href="<?php echo $base_path; ?>/index.php#home">Início</a>
                    <a id="open-about-modal-footer" href="#">Sobre</a>
                    <a href="<?php echo $base_path; ?>/pages/tours.php">Tours</a>
                    <a href="<?php echo $base_path; ?>/pages/blog.php">Blog</a>
                    <a href="<?php echo $base_path; ?>/pages/privacidade.php">Privacidade</a>
                    <a href="<?php echo $base_path; ?>/pages/termos.php">Termos de Uso</a>
                </div>
                <div class="footer-section">
                    <h4>Serviços</h4>
                    <a href="<?php echo $base_path; ?>/pages/tours.php">Tours Privados</a>
                    <a href="<?php echo $base_path; ?>/pages/services.php">Roteiros Personalizados</a>
                    <a href="<?php echo $base_path; ?>/pages/services.php">Consultoria de Viagem</a>
                </div>
                <div class="footer-section">
                    <h4>Contato</h4>
                    <?php if ($whatsappNumber): ?>
                    <p><a href="https://wa.me/<?= htmlspecialchars($whatsappNumber) ?>?text=<?= urlencode($whatsappMessage) ?>">WhatsApp</a></p>
                    <?php endif; ?>
                    <?php if ($contactEmail): ?>
                    <p><a href="mailto:<?= htmlspecialchars($contactEmail) ?>"><?= htmlspecialchars($contactEmail) ?></a></p>
                    <?php endif; ?>
                    <?php if ($contactPhone): ?>
                    <p><?= htmlspecialchars($contactPhone) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($socialMedia)): ?>
                    <div style="display: flex; gap: 16px; margin-top: 16px; align-items: center; flex-wrap: wrap;">
                        <?php foreach ($socialMedia as $network => $url): ?>
                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars(getSocialLabel($network)) ?>" style="color: var(--skyline);">
                            <?= getSocialIcon($network, 20) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Mindrush. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

     <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightboxModal">
        <button class="lightbox-close" id="lightboxClose" aria-label="Fechar galeria">&times;</button>
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Imagem anterior">&larr;</button>
        <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Próxima imagem">&rarr;</button>
        <div class="lightbox-content">
            <img class="lightbox-image" id="lightboxImage" src="" alt="">
        </div>
        <div class="lightbox-caption" id="lightboxCaption"></div>
        <div class="lightbox-thumbnails" id="lightboxThumbnails"></div>
    </div>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/447950400919?text=Ol%C3%A1!%20Gostaria%20de%20saber%20mais%20sobre%20os%20tours%20em%20Londres"
       class="whatsapp-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Fale conosco no WhatsApp">
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 0C7.164 0 0 7.164 0 16c0 2.825.74 5.478 2.028 7.772L0 32l8.448-2.016A15.928 15.928 0 0016 32c8.836 0 16-7.164 16-16S24.836 0 16 0zm0 29.332c-2.492 0-4.876-.672-6.92-1.932l-.496-.292-5.144 1.228 1.292-4.776-.328-.516A13.3 13.3 0 012.668 16c0-7.364 5.968-13.332 13.332-13.332S29.332 8.636 29.332 16 23.364 29.332 16 29.332zm7.308-9.964c-.4-.2-2.368-1.168-2.736-1.3-.368-.132-.636-.2-.904.2-.268.4-1.036 1.3-1.272 1.568-.236.268-.472.3-.872.1-.4-.2-1.688-.624-3.216-1.988-1.188-1.064-1.992-2.376-2.224-2.776-.232-.4-.024-.616.176-.816.18-.18.4-.472.6-.708.2-.236.268-.4.4-.668.132-.268.068-.5-.032-.7-.1-.2-.904-2.176-1.24-2.98-.328-.78-.66-.676-.904-.688-.232-.012-.5-.016-.768-.016s-.7.1-1.068.5c-.368.4-1.404 1.372-1.404 3.348s1.436 3.884 1.636 4.152c.2.268 2.824 4.312 6.84 6.048.956.412 1.7.66 2.284.844.96.304 1.832.26 2.52.156.768-.116 2.368-.968 2.7-1.904.332-.936.332-1.74.232-1.904-.1-.164-.368-.264-.768-.464z"/>
        </svg>
    </a>

    <!-- Back to Top Button -->
    <button class="back-to-top" aria-label="Voltar ao topo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="19" x2="12" y2="5"></line>
            <polyline points="5 12 12 5 19 12"></polyline>
        </svg>
    </button>

    <!-- Cookie Consent Banner -->
    <div class="cookie-consent" id="cookieConsent">
        <div class="cookie-consent-content">
            <div class="cookie-consent-text">
                <h3>🍪 Privacidade & Cookies</h3>
                <p>Usamos cookies para melhorar sua experiência, analisar o tráfego do site e personalizar conteúdo. Ao clicar em "Aceitar Todos", você concorda com nosso uso de cookies. <a href="<?php echo $base_path; ?>/pages/privacy.php">Política de Privacidade</a></p>
            </div>
            <div class="cookie-consent-actions">
                <button class="cookie-btn cookie-btn-accept" id="acceptAllCookies">Aceitar Todos</button>
                <button class="cookie-btn cookie-btn-customize" id="customizeCookies">Personalizar</button>
            </div>
        </div>
    </div>

    <!-- Cookie Preferences Modal -->
    <div class="cookie-preferences-modal" id="cookiePreferencesModal">
        <div class="cookie-preferences-content">
            <div class="cookie-preferences-header">
                <h3>Preferências de Cookies</h3>
                <button class="cookie-close-btn" id="closeCookiePreferences">&times;</button>
            </div>

            <div class="cookie-option">
                <div class="cookie-option-header">
                    <h4>Cookies Necessários</h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" checked disabled>
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>Essenciais para o funcionamento do site. Não podem ser desativados.</p>
            </div>

            <div class="cookie-option">
                <div class="cookie-option-header">
                    <h4>Cookies Analíticos</h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="analyticsCookies" checked>
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>Nos ajudam a entender como os visitantes interagem com o site coletando informações anônimas (Google Analytics, Microsoft Clarity).</p>
            </div>

            <div class="cookie-option">
                <div class="cookie-option-header">
                    <h4>Cookies de Marketing</h4>
                    <label class="cookie-toggle">
                        <input type="checkbox" id="marketingCookies">
                        <span class="cookie-toggle-slider"></span>
                    </label>
                </div>
                <p>Usados para rastrear visitantes e exibir anúncios relevantes e envolventes (Facebook Pixel, Google Ads).</p>
            </div>

            <div class="cookie-preferences-actions">
                <button class="cookie-btn cookie-btn-accept" id="savePreferences">Salvar Preferências</button>
                <button class="cookie-btn cookie-btn-customize" id="acceptAllFromModal">Aceitar Todos</button>
            </div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="<?php echo $base_path; ?>/js/cookies.js"></script>
    <script src="<?php echo $base_path; ?>/js/tour-modal.js"></script>
    <script src="<?php echo $base_path; ?>/js/mobile-video-handler.js"></script>
    <script src="<?php echo $base_path; ?>/js/main.js"></script>

</body>
</html>