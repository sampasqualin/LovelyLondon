<?php
// mobile/pages/contact.php
// Página de contato em estilo app, usando o formulário existente

require_once __DIR__ . '/../includes/bootstrap.php';

include __DIR__ . '/../includes/header.php';
?>

<section class="app-section">
    <h1 class="app-section-title app-section-title--small">Contato</h1>
    <p class="app-card-text">Preencha o formulário abaixo e vamos planejar sua experiência em Londres.</p>
    <div class="app-card">
        <?php include __DIR__ . '/../../includes/contact_form.php'; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>