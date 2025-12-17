<?php
// mobile/pages/tours.php
// Lista de tours em formato de app

require_once __DIR__ . '/../includes/bootstrap.php';

$canDb = function_exists('isDatabaseAvailable') ? isDatabaseAvailable() : false;
$tours = $canDb && function_exists('getTours') ? getTours(null, false) : [];

include __DIR__ . '/../includes/header.php';
?>

<section class="app-section app-tours-grid">
    <h1 class="app-section-title app-section-title--small">Tours em Londres</h1>
    <p class="app-card-text" style="margin-bottom: 0.75rem;">
        Escolha um tour para conhecer Londres com a Carol.
    </p>
    <?php if (!empty($tours)): ?>
        <div class="app-tours-mosaic">
            <?php foreach ($tours as $tour): ?>
                <?php
                    $tourImage = $tour['image'] ?? ($tour['image_url'] ?? '');
                    $tourImage = $tourImage ?: 'https://images.unsplash.com/photo-1526129318478-62ed807ebdf9?q=80&w=400&auto=format&fit=crop&fm=webp';
                ?>
                <article class="app-tours-mosaic-item">
                    <div class="app-tour-image">
                        <img src="<?php echo htmlspecialchars($tourImage); ?>" alt="<?php echo htmlspecialchars(getContent($tour, 'title')); ?>">
                    </div>
                    <div class="app-tour-info">
                        <h3 class="app-tour-title"><?php echo htmlspecialchars(getContent($tour, 'title')); ?></h3>
                        <a href="<?php echo $mobile_base_path; ?>/pages/tour-detail.php?slug=<?php echo urlencode($tour['slug'] ?? ''); ?>" class="app-tour-link">Ver mais</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="app-empty">Nenhum tour cadastrado ainda.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>