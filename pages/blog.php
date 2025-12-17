<?php
include '../includes/header.php';
require_once __DIR__ . '/../includes/content_helpers.php';
require_once __DIR__ . '/../includes/image_helper.php';

// Buscar posts do database
$posts = getBlogPosts();
?>

    <section class="section bg-pattern">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Navegação estrutural">
                <ol class="breadcrumbs-list">
                    <li class="breadcrumbs-item">
                        <a href="<?php echo $base_path; ?>/index.php" class="breadcrumbs-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                            Início
                        </a>
                    </li>
                    <li class="breadcrumbs-item breadcrumbs-item--active" aria-current="page">
                        Blog
                    </li>
                </ol>
            </nav>

            <h2 class="section-title left-aligned">Blog Lovely London</h2>
            <p class="section-subtitle" style="text-align: left; max-width: none;">Dicas exclusivas, histórias fascinantes e segredos de Londres</p>

            <div class="blog-list">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                    <article class="blog-post-card">
                        <div class="blog-post-content">
                            <?php if (getContent($post, 'category')): ?>
                                <span class="blog-post-category">
                                    <?= htmlspecialchars(getContent($post, 'category')) ?>
                                </span>
                            <?php endif; ?>

                            <h3 class="blog-post-title">
                                <a href="<?php echo $base_path; ?>/pages/blog-post.php?slug=<?= urlencode($post['slug']) ?>">
                                    <?= htmlspecialchars(getContent($post, 'title')) ?>
                                </a>
                            </h3>

                            <?php if (isset($post['published_at']) && $post['published_at']): ?>
                                <time class="blog-post-date" datetime="<?= htmlspecialchars($post['published_at']) ?>">
                                    <?= date('d/m/Y', strtotime($post['published_at'])) ?>
                                </time>
                            <?php endif; ?>

                            <div class="blog-post-text-image">
                                <div class="blog-post-excerpt">
                                    <?php if (getContent($post, 'excerpt')): ?>
                                        <p><?= htmlspecialchars(getContent($post, 'excerpt')) ?></p>
                                    <?php endif; ?>
                                    <a href="<?php echo $base_path; ?>/pages/blog-post.php?slug=<?= urlencode($post['slug']) ?>" class="blog-post-read-more">
                                        Leia Mais <span>→</span>
                                    </a>
                                </div>

                                <div class="blog-post-image">
                                    <?php $blog_img = $post['featured_image'] ?? ''; ?>
                                    <a href="<?php echo $base_path; ?>/pages/blog-post.php?slug=<?= urlencode($post['slug']) ?>">
                                        <img src="<?= $blog_img ? htmlspecialchars(processImagePath($blog_img)) : 'https://images.unsplash.com/photo-1518638150499-23c375fb443b?q=80&w=1770' ?>"
                                             alt="<?= htmlspecialchars(getContent($post, 'title')) ?>"
                                             loading="lazy">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Estado vazio: Nenhum post cadastrado -->
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        <h3>Nenhum Post Cadastrado</h3>
                        <p>No momento não temos posts disponíveis. Em breve teremos conteúdo exclusivo sobre Londres para você!</p>
                        <a href="#contact-form" class="btn">Entrar em Contato</a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </section>


    <?php 
    // Incluir componente de formulário de contato
    $contact_title = "Não Perca Nenhuma Dica";
    $contact_subtitle = "Assine nossa newsletter e receba conteúdo exclusivo sobre Londres";
    $contact_button_text = "Assinar Newsletter";
    $contact_show_message_field = false;
    include '../includes/contact_form.php';
    ?>

<?php include '../includes/footer.php'; ?>
