<?php
include 'includes/header.php';
require_once __DIR__ . '/includes/content_helpers.php';
require_once __DIR__ . '/includes/image_helper.php';

// Buscar conteúdo dinâmico com tolerância (sem 500 quando não há BD)
$canDb = function_exists('isDatabaseAvailable') ? isDatabaseAvailable() : false;
$hero_slides = $canDb && function_exists('getHeroSlides') ? getHeroSlides() : [];
$featured_tours = $canDb && function_exists('getTours') ? getTours(4, true) : [];
$services = $canDb && function_exists('getServices') ? getServices(4) : [];
$blog_posts = $canDb && function_exists('getBlogPosts') ? getBlogPosts(4) : [];
$testimonials = $canDb && function_exists('getTestimonials') ? getTestimonials(4, false) : [];
$clients = function_exists('getClients') ? getClients() : [];
?>

    <main id="main-content">
        <!-- Seção Hero Slider -->
        <section class="hero-slider-container section-hero" id="hero-section" aria-label="Destaques principais">
            <div class="hero-slides" role="region" aria-label="Carrossel de imagens">
                <?php if (!empty($hero_slides)): ?>
                    <?php foreach ($hero_slides as $index => $slide): ?>
                    <!-- Slide Dinâmico <?= $index + 1 ?> - <?= htmlspecialchars($slide['slide_type']) ?> -->
                    <div class="hero-slide <?= $slide['slide_type'] === 'split' ? 'hero-slide-split' : '' ?>">
                        <?php if (!empty($slide['background_video']) && ($slide['media_type'] === 'video' || strpos($slide['background_video'], 'http') === 0)): ?>
                            <?php if (strpos($slide['background_video'], 'http') === 0): ?>
                                <iframe class="hero-bg-video" src="<?= htmlspecialchars($slide['background_video']) ?>" title="Hero Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            <?php else: ?>
                                <video class="hero-bg-video"
                                       autoplay
                                       muted
                                       loop
                                       playsinline
                                       disablePictureInPicture
                                       <?php if (!empty($slide['background_image'])): ?>
                                       poster="<?= htmlspecialchars(processImagePath($slide['background_image'])) ?>"
                                       <?php endif; ?>>
                                    <?php
                                        $video_path = htmlspecialchars(processImagePath($slide['background_video']));
                                        $video_ext = strtolower(pathinfo($video_path, PATHINFO_EXTENSION));
                                    ?>
                                    <?php if ($video_ext === 'webm'): ?>
                                        <source src="<?= $video_path ?>" type="video/webm">
                                        <source src="<?= str_replace('.webm', '.mp4', $video_path) ?>" type="video/mp4">
                                    <?php else: ?>
                                        <source src="<?= $video_path ?>" type="video/mp4">
                                    <?php endif; ?>
                                    <!-- Fallback para navegadores que não suportam vídeo -->
                                    <?php if (!empty($slide['background_image'])): ?>
                                    <div class="hero-bg" style="background-image: url('<?= htmlspecialchars(processImagePath($slide['background_image'])) ?>');"></div>
                                    <?php endif; ?>
                                </video>
                            <?php endif; ?>
                        <?php elseif (!empty($slide['background_image'])): ?>
                            <div class="hero-bg" style="background-image: url('<?= htmlspecialchars(processImagePath($slide['background_image'])) ?>');"></div>
                        <?php endif; ?>

                        <?php if ($slide['slide_type'] === 'split' && !empty($slide['items'])): ?>
                            <!-- Split Layout com Items -->
                            <div class="hero-split-content container">
                                <div class="hero-split-left">
                                    <h1><?= formatHeroText(getContent($slide, 'title')) ?></h1>
                                    <?php if (getContent($slide, 'subtitle')): ?>
                                        <p><?= formatHeroText(getContent($slide, 'subtitle')) ?></p>
                                    <?php endif; ?>
                                    <?php if ($slide['cta_url']): ?>
                                        <a href="<?= htmlspecialchars($slide['cta_url']) ?>" class="btn">
                                            <?= htmlspecialchars(getContent($slide, 'cta_text') ?: 'Saiba Mais') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="hero-split-right">
                                    <div class="hero-features-grid">
                                        <?php if (!empty($slide['items'])): foreach ($slide['items'] as $item): ?>
                                        <div class="hero-feature-item">
                                            <?php if ($item['icon_svg']): ?>
                                                <div class="icon"><?= $item['icon_svg'] ?></div>
                                            <?php endif; ?>
                                            <h4><?= htmlspecialchars(getContent($item, 'title')) ?></h4>
                                            <?php if (getContent($item, 'description')): ?>
                                                <p><?= htmlspecialchars(getContent($item, 'description')) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Full Layout -->
                            <div class="hero-content container">
                                <h1><?= formatHeroText(getContent($slide, 'title')) ?></h1>
                                <?php if (getContent($slide, 'subtitle')): ?>
                                    <p><?= formatHeroText(getContent($slide, 'subtitle')) ?></p>
                                <?php endif; ?>
                                <?php if ($slide['cta_url']): ?>
                                    <a href="<?= htmlspecialchars($slide['cta_url']) ?>" class="btn">
                                        <?= htmlspecialchars(getContent($slide, 'cta_text') ?: 'Saiba Mais') ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Estado vazio quando não há hero slides -->
                    <div class="hero-slide">
                        <div class="hero-bg" style="background: linear-gradient(135deg, var(--lovely) 0%, var(--notting-hill) 100%);"></div>
                        <div class="hero-content container">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                </div>
                                <h1><?= formatHeroText('Come with me! [skyline]London[/skyline] is [skyline]Lovely[/skyline]') ?></h1>
                                <p>Em breve teremos conteúdo incrível para você!</p>
                                <a href="#contact" class="btn">Entre em Contato</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="slider-controls">
                <div class="slider-play-pause">
                    <button class="slider-control-btn" id="sliderPlayPause" aria-label="Pausar carrossel">
                        <svg class="play-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                        <svg class="pause-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="6" y="4" width="4" height="16"></rect>
                            <rect x="14" y="4" width="4" height="16"></rect>
                        </svg>
                    </button>
                </div>
                <div class="slider-nav" role="group" aria-label="Controles do carrossel">
                    <button class="slider-dot active" data-slide="0" aria-label="Ir para slide 1" aria-current="true"></button>
                    <button class="slider-dot" data-slide="1" aria-label="Ir para slide 2"></button>
                    <button class="slider-dot" data-slide="2" aria-label="Ir para slide 3"></button>
                </div>
            </div>
            
            <!-- Logos de Certificação Fixos -->
            <div class="hero-certification-logos">
                <img src="<?php echo $base_path; ?>/assets/images/hero/ITG-CMYK-LOGO.png" alt="Institute of Tourist Guiding" class="hero-cert-logo">
                <img src="<?php echo $base_path; ?>/assets/images/hero/APTG-Logo.png" alt="Association of Professional Tourist Guides" class="hero-cert-logo">
            </div>
        </section>

        <!-- Seção de Destaques (Features) - Removida para manter apenas dados do BD -->

        <!-- Seção Os Mais Populares do Momento -->
        <section id="tours-section" class="section section-tours bg-pattern fade-in" aria-labelledby="tours-heading">
            <div class="container">
                <h2 id="tours-heading" class="section-title">Os Mais Populares do Momento</h2>
                <p class="section-subtitle">Experiências cuidadosamente selecionadas que encantam nossos visitantes.</p>

                <div class="promo-grid promo-grid-4col fade-in-stagger">
                    <?php if (!empty($featured_tours)): ?>
                        <?php foreach ($featured_tours as $tour): ?>
                        <div class="promo-item">
                            <div class="promo-card tour-card-simple"
                                 style="cursor: pointer;"
                                 onclick='openTourModal({
                                    title: <?= json_encode(getContent($tour, 'title')) ?>,
                                    description: <?= json_encode(getContent($tour, 'description')) ?>,
                                    image: <?= json_encode(processImagePath($tour['image'])) ?>,
                                    basePath: <?= json_encode($base_path) ?>
                                 }, "tour")'>
                                <img src="<?= htmlspecialchars(processImagePath($tour['image']) ?: 'https://images.unsplash.com/photo-1526129318478-62ed807ebdf9?q=80&w=320&auto=format&fit=crop&fm=webp') ?>"
                                     alt="<?= htmlspecialchars(getContent($tour, 'title')) ?>"
                                     loading="lazy">
                            </div>
                            <h3 class="promo-title"><?= htmlspecialchars(getContent($tour, 'title')) ?></h3>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <h3>Nenhum Tour Cadastrado</h3>
                            <p>Em breve teremos tours incríveis para você!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($featured_tours)): ?>
                <div class="section-footer">
                    <a href="<?php echo $base_path; ?>/pages/tours.php" class="btn-outline">Ver Mais Tours</a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Empresas que confiam na Lovely London -->
        <section class="clients-section fade-in" aria-labelledby="clients-heading">
            <div class="container">
                <h2 id="clients-heading">Empresas que confiam na Lovely London</h2>
            </div>
            <?php if (!empty($clients)): ?>
            <div class="logos" aria-label="Logotipos de empresas clientes">
                <div class="logos-slide">
                    <?php foreach ($clients as $client): ?>
                    <img src="<?php echo $base_path; ?><?= htmlspecialchars($client['logo_url']) ?>" alt="Logo <?= htmlspecialchars($client['name']) ?>" loading="lazy" width="200" height="100">
                    <?php endforeach; ?>
                </div>
                <div class="logos-slide" aria-hidden="true">
                    <?php foreach ($clients as $client): ?>
                    <img src="<?php echo $base_path; ?><?= htmlspecialchars($client['logo_url']) ?>" alt="" loading="lazy" width="200" height="100">
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="logos" aria-label="Logotipos de empresas clientes">
                <div style="text-align: center; padding: 2rem; color: #999;">
                    <p>Nenhum cliente cadastrado no momento.</p>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <!-- Seção de Serviços -->
        <section id="services-section" class="section section-services fade-in" aria-labelledby="services-heading">
             <div class="container">
                <h2 id="services-heading" class="section-title">Nossos Serviços</h2>
                <p class="section-subtitle">Tudo que você precisa para uma viagem tranquila e inesquecível.</p>
                <div class="promo-grid promo-grid-4col fade-in-stagger">
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $index => $service): ?>
                        <div class="promo-item">
                            <a href="<?php echo $base_path; ?>/pages/services.php" class="promo-card service-card">
                            <?php if (!empty($service['image_url'])): ?>
                                <img src="<?= htmlspecialchars(processImagePath($service['image_url'])) ?>"
                                     alt="<?= htmlspecialchars(getContent($service, 'title')) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="promo-card-placeholder" style="background: hsl(<?= (($index % 4) * 90) ?>, 70%, 50%);"></div>
                            <?php endif; ?>
                            </a>
                            <h3 class="promo-title"><?= htmlspecialchars(getContent($service, 'title')) ?></h3>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M8 12h8"></path>
                                    <path d="M12 8v8"></path>
                                </svg>
                            </div>
                            <h3>Nenhum Serviço Cadastrado</h3>
                            <p>Em breve teremos serviços incríveis para você!</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($services)): ?>
                <div class="section-footer">
                    <a href="<?php echo $base_path; ?>/pages/services.php" class="btn-outline">Ver Mais Serviços</a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Seção de Reviews - Carousel Horizontal -->
        <section class="reviews-section section section-testimonials bg-pattern fade-in" id="testimonials-section" aria-labelledby="reviews-heading">
            <div class="container">
                <h2 id="reviews-heading" class="section-title">Experiências que marcam</h2>
                <p class="section-subtitle">O que nossos viajantes dizem sobre os momentos com a Lovely London.</p>

                <div class="testimonials-carousel-wrapper">
                    <?php if (!empty($testimonials) && count($testimonials) > 3): ?>
                    <button class="testimonials-nav testimonials-prev" aria-label="Depoimento anterior">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <?php endif; ?>

                    <div class="testimonials-carousel">
                        <div class="testimonials-track" id="testimonialsTrack">
                            <?php if (!empty($testimonials)): ?>
                                <?php foreach ($testimonials as $testimonial): ?>
                                <!-- Testimonial Horizontal -->
                                <div class="testimonial-card-horizontal">
                                    <div class="testimonial-left">
                                        <?php if ($testimonial['client_photo']): ?>
                                            <img src="<?= htmlspecialchars(processImagePath($testimonial['client_photo'])) ?>"
                                                 alt="Foto de <?= htmlspecialchars($testimonial['client_name']) ?>"
                                                 class="testimonial-photo-horizontal" loading="lazy">
                                        <?php else: ?>
                                            <div class="testimonial-photo-horizontal testimonial-avatar-initial">
                                                <?= strtoupper(substr($testimonial['client_name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="testimonial-author-info">
                                            <div class="testimonial-author"><?= htmlspecialchars($testimonial['client_name']) ?></div>
                                            <div class="testimonial-location"><?= htmlspecialchars($testimonial['client_location']) ?></div>
                                            <div class="testimonial-stars" role="img" aria-label="Avaliação: <?= $testimonial['rating'] ?> de 5 estrelas">
                                                <span aria-hidden="true"><?= str_repeat('★', $testimonial['rating']) . str_repeat('☆', 5 - $testimonial['rating']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="testimonial-right">
                                        <p class="testimonial-text">"<?= htmlspecialchars(getContent($testimonial, 'testimonial')) ?>"</p>
                                        <?php if ($testimonial['tour_name_' . $lang]): ?>
                                            <div class="testimonial-tour">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                <?= htmlspecialchars($testimonial['tour_name_' . $lang]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Estado vazio quando não há testimonials -->
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </div>
                                    <h3>Nenhum Depoimento Cadastrado</h3>
                                    <p>Em breve teremos depoimentos incríveis de nossos clientes!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($testimonials) && count($testimonials) > 3): ?>
                    <button class="testimonials-nav testimonials-next" aria-label="Próximo depoimento">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Seção do Blog -->
        <section id="blog-section" class="section section-blog bg-pattern fade-in" aria-labelledby="blog-heading">
             <div class="container">
                <h2 id="blog-heading" class="section-title left-aligned">Blog</h2>

                <!-- Blog Header with Search and Categories -->
                <div class="blog-header fade-in">
                    <div class="blog-search">
                        <label for="blogSearch" class="visually-hidden">Buscar artigos no blog</label>
                        <svg class="blog-search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="blogSearch" placeholder="Buscar artigos..." aria-label="Campo de busca de artigos">
                    </div>
                    <div class="blog-categories">
                        <button class="category-tag active" data-category="all">Todos</button>
                        <button class="category-tag" data-category="dicas">Dicas</button>
                        <button class="category-tag" data-category="gastronomia">Gastronomia</button>
                        <button class="category-tag" data-category="cultura">Cultura</button>
                    </div>
                </div>

                <div class="promo-grid promo-grid-4col fade-in-stagger" id="blogGrid">
                    <?php if (!empty($blog_posts)): ?>
                        <?php foreach ($blog_posts as $post): ?>
                        <div class="promo-item">
                            <a href="<?php echo $base_path; ?>/pages/blog-post.php?slug=<?= htmlspecialchars($post['slug']) ?>" class="promo-card blog-card-simple" data-category="<?= htmlspecialchars($post['category_slug'] ?? 'geral') ?>">
                                <?php $blog_img = $post['featured_image'] ?? ''; ?>
                                <img src="<?= $blog_img ? htmlspecialchars(processImagePath($blog_img)) : 'https://images.unsplash.com/photo-1518638150499-23c375fb443b?q=80&w=400&auto-format&fit=crop' ?>"
                                     alt="<?= htmlspecialchars(getContent($post, 'title')) ?>"
                                     loading="lazy">
                            </a>
                            <div class="blog-content-mobile">
                                <h3 class="promo-title"><?= htmlspecialchars(getContent($post, 'title')) ?></h3>
                                <?php if (!empty(getContent($post, 'excerpt'))): ?>
                                <p class="blog-excerpt-mobile"><?= htmlspecialchars(getContent($post, 'excerpt')) ?></p>
                                <?php endif; ?>
                                <a href="<?php echo $base_path; ?>/pages/blog-post.php?slug=<?= htmlspecialchars($post['slug']) ?>" class="blog-read-more">
                                    Ler mais
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <h3>Nenhum Post Cadastrado</h3>
                            <p>Em breve teremos artigos incríveis para você!</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($blog_posts)): ?>
                <div class="section-footer">
                    <a href="<?php echo $base_path; ?>/pages/blog.php" class="btn-outline">Ver Mais Posts</a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <?php 
        // Incluir componente de formulário de contato
        include 'includes/contact_form.php';
        ?>
    </main>

<?php include 'includes/footer.php'; ?>
