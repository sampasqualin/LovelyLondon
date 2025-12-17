<?php
// mobile/pages/tour-detail.php
// Detalhe de tour em estilo app, usando os mesmos dados da versão principal

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/content_helpers.php';

$slug = $_GET['slug'] ?? '';
$tour = $slug && function_exists('getTourBySlug') ? getTourBySlug($slug) : null;

include __DIR__ . '/../includes/header.php';
?>

<section class="app-section app-tours-grid">
    <a href="<?php echo $mobile_base_path; ?>/pages/tours.php" class="app-card-link" style="display:inline-block;margin-bottom:0.5rem;">← Voltar para tours</a>
    <?php if ($tour): ?>
        <?php
            $tourImage = $tour['image'] ?? ($tour['image_url'] ?? '');
            $tourImage = $tourImage ?: 'https://images.unsplash.com/photo-1526129318478-62ed807ebdf9?q=80&w=800&auto=format&fit=crop&fm=webp';
            $whatsText = 'Ol%C3%A1!%20Gostaria%20de%20informações%20sobre%20o%20tour%20' . urlencode(getContent($tour, 'title'));
        ?>
        <article class="app-card">
            <div class="app-tour-image" style="margin-bottom: 0.75rem;">
                <img src="<?php echo htmlspecialchars($tourImage); ?>" alt="<?php echo htmlspecialchars(getContent($tour, 'title')); ?>" style="width: 100%; border-radius: 12px; object-fit: cover;">
            </div>
            <h1 class="app-card-title" style="margin-bottom: 0.5rem;">
                <?php echo htmlspecialchars(getContent($tour, 'title')); ?>
            </h1>
            <?php if (!empty($tour['short_description_' . $lang])): ?>
                <p class="app-card-text"><?php echo htmlspecialchars($tour['short_description_' . $lang]); ?></p>
            <?php endif; ?>

            <?php if (!empty($tour['duration_' . $lang]) || !empty($tour['group_size']) || !empty($tour['price_from'])): ?>
            <ul class="app-card-meta-list">
                <?php if (!empty($tour['duration_' . $lang])): ?>
                    <li><?php echo htmlspecialchars($tour['duration_' . $lang]); ?></li>
                <?php endif; ?>
                <?php if (!empty($tour['group_size'])): ?>
                    <li>Grupo até <?php echo (int)$tour['group_size']; ?> pessoas</li>
                <?php endif; ?>
                <?php if (!empty($tour['price_from'])): ?>
                    <li>A partir de <?php echo htmlspecialchars($tour['price_from']); ?></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>

            <?php if (!empty($tour['description_' . $lang])): ?>
                <p class="app-card-text"><?php echo nl2br(htmlspecialchars($tour['description_' . $lang])); ?></p>
            <?php endif; ?>

            <a href="https://wa.me/447950400919?text=<?php echo $whatsText; ?>" class="app-primary-btn" style="margin-top: 0.5rem;">
                Falar sobre este tour
            </a>
        </article>
    <?php else: ?>
        <p class="app-empty">Tour não encontrado.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>