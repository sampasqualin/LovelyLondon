<?php
include '../includes/header.php';
require_once '../includes/about_helper.php';
require_once '../includes/image_helper.php';

// Carregar conteúdo do About
$carol = getAboutCarol();
$lovely = getAboutLovelyLondon();
$social = getSocialLinks();

// Tratamento de erros - se alguma função retornar erro, usar dados vazios
if (isset($carol['error'])) {
    $carol = [
        'title_pt' => 'Guia brasileira certificada em Londres',
        'subtitle_pt' => '',
        'content_pt' => '',
        'image' => getImagePath('assets/images/sobre/carol-profile.png')
    ];
}

if (isset($lovely['error'])) {
    $lovely = [
        'title_pt' => 'Sobre a Lovely London',
        'subtitle_pt' => '',
        'content_pt' => '',
        'image' => getImagePath('assets/images/sobre/galeria1.jpg')
    ];
}

if (isset($social['error'])) {
    $social = [
        'instagram' => '',
        'facebook' => '',
        'tiktok' => ''
    ];
}
?>

    <!-- Seção Sobre -->
    <section class="section fade-in">
        <div class="container">
            <div class="about-content">
            <div class="about-text">
                    <h1 class="section-title" style="text-align: left; margin-left: 0;"><?php echo htmlspecialchars($carol['title_pt']); ?></h1>
                    <p class="section-subtitle" style="text-align: left; margin-left: 0; max-width: 100%;">
                        <?php echo htmlspecialchars($carol['subtitle_pt']); ?>
                    </p>
                    <p>
                        <?php echo htmlspecialchars($carol['content_pt']); ?>
                    </p>

                    <div class="social-links" style="margin-top: var(--spacing-xl); display: flex; gap: var(--spacing-lg); align-items: center; flex-wrap: wrap;">
                        <?php if (!empty($social['instagram'])): ?>
                        <a href="<?php echo htmlspecialchars($social['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" style="color: var(--lovely); transition: color 0.3s;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="m16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social['facebook'])): ?>
                        <a href="<?php echo htmlspecialchars($social['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="color: var(--lovely); transition: color 0.3s;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($social['tiktok'])): ?>
                        <a href="<?php echo htmlspecialchars($social['tiktok']); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok" style="color: var(--lovely); transition: color 0.3s;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path>
                            </svg>
                        </a>
                        <?php endif; ?>
                        
                        <!-- Divider -->
                        <span style="width: 2px; height: 32px; background: rgba(112, 4, 32, 0.2); margin: 0 8px;"></span>
                        
                        <!-- Logos de Certificação -->
                        <img src="<?php echo $base_path; ?>/assets/images/hero/ITG-CMYK-LOGO.png" alt="Institute of Tourist Guiding" style="height: 40px; width: auto; opacity: 0.9; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                        <img src="<?php echo $base_path; ?>/assets/images/hero/APTG-Logo.png" alt="Association of Professional Tourist Guides" style="height: 40px; width: auto; opacity: 0.9; transition: opacity 0.3s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.9'">
                    </div>
                </div>
                <div class="about-image-wrapper">
                    <img src="<?php echo $base_path . $carol['image']; ?>" alt="Carol - Guia turística brasileira certificada em Londres" class="about-image">
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Sobre Lovely London -->
    <section class="section fade-in bg-pattern">
        <div class="container">
            <div class="about-content about-content-reversed">
                <div class="about-image-wrapper">
                    <img src="<?php echo $base_path . $lovely['image']; ?>" alt="Lovely London - Experiências em Londres" class="about-image">
                </div>
                <div class="about-text">
                    <h2 class="section-title" style="text-align: left; margin-left: 0;"><?php echo htmlspecialchars($lovely['title_pt']); ?></h2>
                    <p class="section-subtitle" style="text-align: left; margin-left: 0; max-width: 100%;">
                        <?php echo htmlspecialchars($lovely['subtitle_pt']); ?>
                    </p>
                    <?php 
                    // Mostrar conteúdo com parágrafos separados
                    $paragraphs = explode('\n\n', trim($lovely['content_pt']));
                    foreach ($paragraphs as $paragraph):
                        if (!empty(trim($paragraph))):
                    ?>
                    <p><?php echo htmlspecialchars(trim($paragraph)); ?></p>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção FAQ -->
    <section id="faq" class="faq-section section bg-pattern fade-in" aria-labelledby="faq-heading">
        <div class="container">
            <h2 id="faq-heading" class="section-title">Perguntas Frequentes</h2>
            <p class="section-subtitle">Tudo que você precisa saber antes de reservar seu tour</p>

            <div class="faq-container">
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Como funciona a reserva de um tour?</span>
                        <svg class="faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>É muito simples! Entre em contato através do WhatsApp ou pelo formulário de contato, informe suas datas e interesses, e criaremos um roteiro personalizado para você. Após a confirmação, você receberá todos os detalhes por e-mail.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Qual é o tamanho dos grupos?</span>
                        <svg class="faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Trabalhamos principalmente com tours privados e em pequenos grupos (máximo 6 pessoas) para garantir uma experiência mais personalizada e intimista. Isso permite que você aproveite ao máximo cada momento sem pressa.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Os tours incluem ingressos para atrações?</span>
                        <svg class="faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>O valor do tour cobre o serviço de guia e planejamento. Ingressos para museus, monumentos e transporte são pagos separadamente. Posso ajudar na compra antecipada e organização de tudo que você precisar!</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Qual é a política de cancelamento?</span>
                        <svg class="faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Cancelamentos com 48 horas de antecedência têm reembolso total. Entre 24-48h, reembolso de 50%. Menos de 24h não há reembolso. Em caso de emergência ou problemas de saúde, avaliamos caso a caso com flexibilidade.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Posso personalizar completamente meu tour?</span>
                        <svg class="faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Absolutamente! Essa é nossa especialidade. Me conte seus interesses (história, gastronomia, arquitetura, fotografia, compras...) e criarei um roteiro único baseado no que você ama. Cada detalhe é pensado para você.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span class="faq-question-text">Os tours são adequados para crianças?</span>
                        <svg class="faq-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Sim! Adapto o ritmo e o conteúdo para famílias com crianças. Podemos incluir parques, museus interativos e paradas para lanches. A ideia é que todos se divirtam e aprendam juntos!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php 
    // Incluir componente de formulário de contato
    $contact_form_id = 'contact';
    include '../includes/contact_form.php';
    ?>

<?php include '../includes/footer.php'; ?>
