<?php
// mobile/index.php
// Home mobile em estilo app, consumindo os mesmos dados da versão web

require_once __DIR__ . '/includes/bootstrap.php';

// Reaproveita helpers existentes
$canDb = function_exists('isDatabaseAvailable') ? isDatabaseAvailable() : false;
$hero_slides = $canDb && function_exists('getHeroSlides') ? getHeroSlides() : [];
$featured_tours = $canDb && function_exists('getTours') ? getTours(4, true) : [];
$services = $canDb && function_exists('getServices') ? getServices(4) : [];
$blog_posts = $canDb && function_exists('getBlogPosts') ? getBlogPosts(3) : [];
$testimonials = $canDb && function_exists('getTestimonials') ? getTestimonials(3, false) : [];

include __DIR__ . '/includes/header.php';
?>

<section class="app-section app-widgets">
    <div class="app-widgets-grid">
        <!-- Widget 1: Clima em Londres (placeholder, pode ser ligado a API depois) -->
        <article class="app-widget widget-weather">
            <h2 class="app-widget-title">Clima em Londres</h2>
            <p class="app-widget-subtitle">Previsão rápida para hoje</p>
            <div class="app-widget-content" id="weatherWidget">
                <div class="app-widget-temp" id="weatherTemp">--°C</div>
                <div class="app-widget-meta" id="weatherMeta">Carregando previsão...</div>
            </div>
        </article>

        <!-- Widget 2: Câmbio simples -->
        <article class="app-widget widget-exchange">
            <h2 class="app-widget-title">Câmbio hoje</h2>
            <p class="app-widget-subtitle">Valores aproximados em relação ao real</p>
            <div class="app-widget-content app-widget-rates" id="exchangeWidget">
                <div><span>€ 1</span><span id="rateEUR">...</span></div>
                <div><span>US$ 1</span><span id="rateUSD">...</span></div>
            </div>
        </article>

    </div>
</section>

<!-- Tours Populares -->
<section class="app-section app-tours-grid">
    <h2 class="app-section-title">Tours Populares</h2>
    <?php if (!empty($featured_tours)): ?>
        <div class="app-tours-mosaic">
            <?php foreach ($featured_tours as $tour): ?>
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
                        <a href="<?php echo $mobile_base_path; ?>/pages/tours.php" class="app-tour-link">Ver mais</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="app-empty">Em breve tours disponíveis aqui.</p>
    <?php endif; ?>
</section>

<!-- Serviços em mosaico -->
<section class="app-section app-services-grid">
    <h2 class="app-section-title app-section-title--small">Serviços</h2>
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
        <p class="app-empty">Serviços serão adicionados em breve.</p>
    <?php endif; ?>
</section>

<!-- Blog em mosaico -->
<section class="app-section app-blog-grid">
    <h2 class="app-section-title app-section-title--small">Blog</h2>
    <?php if (!empty($blog_posts)): ?>
        <div class="app-tours-mosaic">
            <?php foreach ($blog_posts as $post): ?>
                <?php
                    $blogImage = $post['featured_image'] ?? '';
                    $blogImage = $blogImage ?: 'https://images.unsplash.com/photo-1518638150499-23c375fb443b?q=80&w=400&auto=format&fit=crop&fm=webp';
                ?>
                <article class="app-tours-mosaic-item">
                    <div class="app-tour-image">
                        <img src="<?php echo htmlspecialchars($blogImage); ?>" alt="<?php echo htmlspecialchars(getContent($post, 'title')); ?>">
                    </div>
                    <div class="app-tour-info">
                        <h3 class="app-tour-title"><?php echo htmlspecialchars(getContent($post, 'title')); ?></h3>
                        <a href="<?php echo $mobile_base_path; ?>/pages/blog.php" class="app-tour-link">Ver mais</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="app-empty">Nenhum artigo publicado ainda.</p>
    <?php endif; ?>
</section>

<!-- Depoimentos por último -->
<section class="app-section">
    <h2 class="app-section-title app-section-title--small">Depoimentos</h2>
    <div class="app-list">
        <?php if (!empty($testimonials)): ?>
            <?php foreach ($testimonials as $testimonial): ?>
                <article class="app-card">
                    <p class="app-card-text">"<?php echo htmlspecialchars(getContent($testimonial, 'testimonial')); ?>"</p>
                    <p class="app-card-meta"><?php echo htmlspecialchars($testimonial['client_name'] ?? ''); ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="app-empty">Em breve depoimentos de viajantes.</p>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
