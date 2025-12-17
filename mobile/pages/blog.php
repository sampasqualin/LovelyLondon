<?php
// mobile/pages/blog.php
// Lista de posts do blog em formato app

require_once __DIR__ . '/../includes/bootstrap.php';

$blog_posts = function_exists('getBlogPosts') ? getBlogPosts(null, false) : [];

include __DIR__ . '/../includes/header.php';
?>

<section class="app-section app-blog-grid">
    <h1 class="app-section-title app-section-title--small">Blog</h1>
    <p class="app-card-text" style="margin-bottom: 0.75rem;">
        Inspirações e dicas para sua viagem a Londres.
    </p>
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
                        <a href="<?php echo $mobile_base_path; ?>/../pages/blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="app-tour-link">Ler mais</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="app-empty">Nenhum artigo publicado ainda.</p>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>