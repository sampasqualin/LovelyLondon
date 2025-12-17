<?php
include '../includes/header.php';
require_once __DIR__ . '/../includes/content_helpers.php';
require_once __DIR__ . '/../includes/image_helper.php';

// Buscar post por slug
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header("Location: " . $base_path . "/pages/blog.php");
    exit();
}

$post = getPostBySlug($slug);

if (!$post) {
    header("Location: " . $base_path . "/pages/blog.php");
    exit();
}

// Buscar posts relacionados (mesma categoria)
$related_posts = [];
try {
    global $pdo;
    // Primeiro tenta buscar posts da mesma categoria
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' AND id != ? AND category_pt = ? ORDER BY published_at DESC LIMIT 3");
    $stmt->execute([$post['id'], $post['category_pt']]);
    $related_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se não houver posts da mesma categoria, busca os últimos posts publicados
    if (empty($related_posts)) {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' AND id != ? ORDER BY published_at DESC LIMIT 3");
        $stmt->execute([$post['id']]);
        $related_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar posts relacionados: " . $e->getMessage());
}

$post_date = new DateTime($post['published_at'] ?? $post['created_at']);
?>

    <section class="post-hero-section">
        <div class="hero-bg" style="background-image: url('<?php echo $base_path . htmlspecialchars($post['featured_image']); ?>');"></div>
        <div class="hero-content container">
            <div class="post-hero-content">
                <div class="post-meta-header">
                    <span class="post-category"><?php echo htmlspecialchars(getContent($post, 'category')); ?></span>
                    <time datetime="<?php echo $post_date->format('Y-m-d'); ?>" class="post-date"><?php echo $post_date->format('d \d\e F \d\e Y'); ?></time>
                    <?php if ($post['reading_time']): ?>
                        <span class="post-reading-time"><?php echo $post['reading_time']; ?> min de leitura</span>
                    <?php endif; ?>
                </div>
                <h1><?php echo htmlspecialchars(getContent($post, 'title')); ?></h1>
                <?php if (getContent($post, 'excerpt')): ?>
                    <p class="post-excerpt"><?php echo htmlspecialchars(getContent($post, 'excerpt')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <article class="post-article-section section">
        <div class="container">
            <div class="post-body-container">
                <div class="post-body">
                    <?php
                        // Renderizar conteúdo com formatação amigável a partir de texto plano
                        $rawContent = getContent($post, 'content');
                        echo formatPostContent($rawContent);
                        // Aviso discreto quando em EN e sem versão traduzida
                        if (($lang ?? 'pt') === 'en' && empty(trim($post['content_en'] ?? '')) && !empty(trim($post['content_pt'] ?? ''))) {
                            echo '<div class="content-fallback-note" style="margin-top:16px;font-size:0.95em;color:#6c757d;">This post is currently available in Portuguese. An English version is coming soon.</div>';
                        }
                    ?>
                </div>

                <div class="post-footer">
                    <div class="author-bio">
                        <img src="<?php echo getImagePath('assets/images/carol-avatar.jpg'); ?>" alt="Foto de Carol" class="author-avatar">
                        <div class="author-info">
                            <h4>Escrito por Carol</h4>
                            <p>Guia brasileira certificada e apaixonada por desvendar os segredos de Londres. A minha missão é transformar a sua viagem numa experiência inesquecível.</p>
                        </div>
                    </div>
                    <div class="share-buttons">
                        <span>Partilhar:</span>
                        <?php
                        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $post_title = getContent($post, 'title');
                        $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($current_url);
                        $twitter_url = 'https://twitter.com/intent/tweet?url=' . urlencode($current_url) . '&text=' . urlencode($post_title);
                        $linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($current_url);
                        ?>
                        <a href="<?= $facebook_url ?>" class="share-btn share-btn-facebook" target="_blank" rel="noopener noreferrer" aria-label="Partilhar no Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="<?= $twitter_url ?>" class="share-btn share-btn-twitter" target="_blank" rel="noopener noreferrer" aria-label="Partilhar no X (Twitter)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="<?= $linkedin_url ?>" class="share-btn share-btn-linkedin" target="_blank" rel="noopener noreferrer" aria-label="Partilhar no LinkedIn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.5 2h-17A1.5 1.5 0 002 3.5v17A1.5 1.5 0 003.5 22h17a1.5 1.5 0 001.5-1.5v-17A1.5 1.5 0 0020.5 2zM8 19H5v-9h3zM6.5 8.25A1.75 1.75 0 118.3 6.5a1.78 1.78 0 01-1.8 1.75zM19 19h-3v-4.74c0-1.42-.6-1.93-1.38-1.93A1.74 1.74 0 0013 14.19a.66.66 0 000 .14V19h-3v-9h2.9v1.3a3.11 3.11 0 012.7-1.4c1.55 0 3.36.86 3.36 3.66z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="post-navigation">
                    <a href="/v2/pages/blog.php" class="btn-outline">Voltar ao Blog</a>
                </div>
            </div>
        </div>
    </article>

    <section class="related-posts-section section bg-pattern">
        <div class="container">
            <h2 class="section-title">Você Também Pode Gostar</h2>
            <div class="promo-grid">
                <?php foreach ($related_posts as $related): ?>
                <div class="promo-item">
                    <a href="<?php echo $base_path; ?>/pages/blog-post.php?slug=<?= urlencode($related['slug']) ?>" class="promo-card blog-card">
                        <?php $rel_img = $related['featured_image'] ?? ''; ?>
                        <img src="<?= $rel_img ? htmlspecialchars(processImagePath($rel_img)) : 'https://images.unsplash.com/photo-1518638150499-23c375fb443b?q=80&w=1770' ?>"
                             alt="<?= htmlspecialchars(getContent($related, 'title')) ?>"
                             loading="lazy">
                    </a>
                    <h3 class="promo-title"><?= htmlspecialchars(getContent($related, 'title')) ?></h3>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php
// $conn->close(); // Descomente quando a base de dados estiver ativa
include '../includes/footer.php';
?>

