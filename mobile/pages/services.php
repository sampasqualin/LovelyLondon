<?php
// mobile/pages/services.php
// Lista de serviços em formato app

require_once __DIR__ . '/../includes/bootstrap.php';

$services = function_exists('getServices') ? getServices(null) : [];

include __DIR__ . '/../includes/header.php';
?>

<section class="app-section app-services-grid">
    <h1 class="app-section-title app-section-title--small">Serviços</h1>
    <p class="app-card-text" style="margin-bottom: 0.75rem;">
        Apoio completo para planejar e viver sua viagem a Londres.
    </p>
    <?php if (!empty($services)): ?>
        <div class="app-tours-mosaic">
            <?php foreach ($services as $service): ?>
                <?php
                    $serviceImage = $service['image_url'] ?? '';
                    $serviceImage = $serviceImage ?: 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?q=80&w=400&auto=format&fit=crop&fm=webp';
                ?>
                <article class="app-tours-mosaic-item">
                    <div class="app-tour-image">
                        <img src="<?php echo htmlspecialchars($serviceImage); ?>" alt="<?php echo htmlspecialchars(getContent($service, 'title')); ?>">
                    </div>
                    <div class="app-tour-info">
                        <h3 class="app-tour-title"><?php echo htmlspecialchars(getContent($service, 'title')); ?></h3>
                        <a href="<?php echo $mobile_base_path; ?>/pages/services.php" class="app-tour-link">Ver mais</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="app-empty">Nenhum serviço cadastrado ainda.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>