<?php
include '../includes/header.php';
require_once __DIR__ . '/../includes/content_helpers.php';
require_once __DIR__ . '/../includes/image_helper.php';

// Buscar services do database
$services = getServices();
?>

    <section class="section bg-pattern">
        <div class="container">
            <h2 class="section-title left-aligned">Nossos Serviços</h2>
            <p class="section-subtitle">Experiências completas e personalizadas para sua viagem a Londres</p>

            <div class="promo-grid promo-grid-4col services-grid">
                <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $service): ?>
                    <div class="promo-item">
                        <div class="promo-card service-card-page"
                             style="cursor: pointer;"
                             onclick='openTourModal({
                                title: <?= json_encode(getContent($service, 'title')) ?>,
                                description: <?= json_encode(getContent($service, 'description')) ?>,
                                image: <?= json_encode(processImagePath($service['image_url'])) ?>,
                                basePath: <?= json_encode($base_path) ?>
                             }, "service")'>
                        <?php if (!empty($service['image_url'])): ?>
                            <img src="<?= htmlspecialchars(processImagePath($service['image_url'])) ?>"
                                 alt="<?= htmlspecialchars(getContent($service, 'title')) ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="promo-card-placeholder" style="background: hsl(<?= (($index % 4) * 90) ?>, 70%, 50%);"></div>
                        <?php endif; ?>
                        </div>
                        <h3 class="promo-title"><?= htmlspecialchars(getContent($service, 'title')) ?></h3>
                        <span class="btn btn-small"
                              style="cursor: pointer;"
                              onclick='openTourModal({
                                title: <?= json_encode(getContent($service, 'title')) ?>,
                                description: <?= json_encode(getContent($service, 'description')) ?>,
                                image: <?= json_encode(processImagePath($service['image_url'])) ?>,
                                basePath: <?= json_encode($base_path) ?>
                              }, "service")'>Ver Detalhes</span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Estado vazio: Nenhum serviço cadastrado -->
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                            </svg>
                        </div>
                        <h3>Nenhum Serviço Cadastrado</h3>
                        <p>No momento não temos serviços disponíveis. Entre em contato conosco para mais informações sobre nossos serviços.</p>
                        <a href="#contact-form" class="btn">Entrar em Contato</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="contact-section section" id="contact-form">
        <div class="container" style="text-align: center;">
            <h2 class="section-title">Pronto para Contratar Nossos Serviços?</h2>
            <p class="section-subtitle" style="max-width: 600px; margin: 0 auto var(--spacing-xl);">
                Preencha nosso formulário interativo e receba um orçamento personalizado em minutos
            </p>
            <a href="<?php echo $base_path; ?>/pages/orcamento.php" class="btn" style="display: inline-flex; align-items: center; gap: 12px; font-size: 1.15rem; padding: 18px 36px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Solicitar Orçamento Personalizado
            </a>
            <p style="margin-top: var(--spacing-md); font-size: 0.9rem; color: var(--white); opacity: 0.8;">
                ✓ Resposta em até 24h &nbsp; • &nbsp; ✓ Sem compromisso &nbsp; • &nbsp; ✓ 100% gratuito
            </p>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>
