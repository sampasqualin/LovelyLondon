<?php
include '../includes/header.php';
require_once __DIR__ . '/../includes/content_helpers.php';
require_once __DIR__ . '/../includes/image_helper.php';

// Buscar tours do database
$all_tours = getTours();

// Separar tours por tipo
$tours_exclusivos = array_filter($all_tours, function($tour) {
    return isset($tour['tour_type']) && $tour['tour_type'] === 'exclusiva';
});

$tours_classicos = array_filter($all_tours, function($tour) {
    return isset($tour['tour_type']) && $tour['tour_type'] === 'classica';
});
?>

    <!-- Seção Tours Exclusivos -->
    <?php if (!empty($tours_exclusivos)): ?>
    <section class="section bg-pattern">
        <div class="container">
            <h2 class="section-title left-aligned">Tours Exclusivos</h2>
            <p class="section-subtitle">Experiências únicas e especiais em Londres</p>

            <div class="promo-grid promo-grid-4col tours-grid">
                <?php foreach ($tours_exclusivos as $tour): ?>
                    <div class="promo-item">
                        <div class="promo-card tour-card"
                             style="cursor: pointer;"
                             onclick='openTourModal({
                                title: <?= json_encode(getContent($tour, 'title')) ?>,
                                description: <?= json_encode(getContent($tour, 'description')) ?>,
                                image: <?= json_encode(processImagePath($tour['image'])) ?>,
                                basePath: <?= json_encode($base_path) ?>
                             }, "tour")'>
                            <img src="<?= htmlspecialchars(processImagePath($tour['image']) ?: 'https://images.unsplash.com/photo-1526129318478-62ed807ebdf9?q=80&w=1887') ?>"
                                 alt="<?= htmlspecialchars(getContent($tour, 'title')) ?>"
                                 loading="lazy">
                        </div>
                        <h3 class="promo-title"><?= htmlspecialchars(getContent($tour, 'title')) ?></h3>
                        <span class="btn btn-small"
                              style="cursor: pointer;"
                              onclick='openTourModal({
                                title: <?= json_encode(getContent($tour, 'title')) ?>,
                                description: <?= json_encode(getContent($tour, 'description')) ?>,
                                image: <?= json_encode(processImagePath($tour['image'])) ?>,
                                basePath: <?= json_encode($base_path) ?>
                              }, "tour")'>Ver Detalhes</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Seção Tours Clássicos -->
    <?php if (!empty($tours_classicos)): ?>
    <section class="section bg-pattern">
        <div class="container">
            <h2 class="section-title left-aligned">Tours Clássicos</h2>
            <p class="section-subtitle">Experiências imperdíveis que definem Londres</p>

            <div class="promo-grid promo-grid-4col tours-grid">
                <?php foreach ($tours_classicos as $tour): ?>
                <div class="promo-item">
                    <div class="promo-card tour-card"
                         style="cursor: pointer;"
                         onclick='openTourModal({
                            title: <?= json_encode(getContent($tour, 'title')) ?>,
                            description: <?= json_encode(getContent($tour, 'description')) ?>,
                            image: <?= json_encode(processImagePath($tour['image'])) ?>,
                            basePath: <?= json_encode($base_path) ?>
                         }, "tour")'>
                        <img src="<?= htmlspecialchars(processImagePath($tour['image']) ?: 'https://images.unsplash.com/photo-1526129318478-62ed807ebdf9?q=80&w=1887') ?>"
                             alt="<?= htmlspecialchars(getContent($tour, 'title')) ?>"
                             loading="lazy">
                    </div>
                    <h3 class="promo-title"><?= htmlspecialchars(getContent($tour, 'title')) ?></h3>
                    <span class="btn btn-small"
                          style="cursor: pointer;"
                          onclick='openTourModal({
                            title: <?= json_encode(getContent($tour, 'title')) ?>,
                            description: <?= json_encode(getContent($tour, 'description')) ?>,
                            image: <?= json_encode(processImagePath($tour['image'])) ?>,
                            basePath: <?= json_encode($base_path) ?>
                          }, "tour")'>Ver Detalhes</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Estado vazio: Se não houver nenhum tour -->
    <?php if (empty($tours_exclusivos) && empty($tours_classicos)): ?>
    <section class="section bg-pattern">
        <div class="container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h3>Nenhum Tour Cadastrado</h3>
                <p>No momento não temos tours disponíveis. Entre em contato conosco para mais informações sobre nossos serviços.</p>
                <a href="#contact-form" class="btn">Entrar em Contato</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="contact-section section" id="contact-form">
        <div class="container" style="text-align: center;">
            <h2 class="section-title">Pronto para Reservar seu Tour?</h2>
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
