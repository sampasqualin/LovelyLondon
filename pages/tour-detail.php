<?php
// Página de detalhes do tour
require_once '../includes/pdo_connection.php';
require_once '../includes/image_helper.php';

// Obter slug do tour da URL
$tour_slug = $_GET['slug'] ?? '';

if (empty($tour_slug)) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

try {
    // Buscar tour pelo slug
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE slug = ? AND is_active = 1");
    $stmt->execute([$tour_slug]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tour) {
        header('HTTP/1.0 404 Not Found');
        include '404.php';
        exit;
    }
    
    // Definir título da página
    $page_title = $tour['title_pt'];
    $page_description = $tour['short_description_pt'];
    
} catch (Exception $e) {
    error_log("Erro ao buscar tour: " . $e->getMessage());
    header('HTTP/1.0 500 Internal Server Error');
    exit;
}

// Incluir header
require_once '../includes/header.php';
?>

<div class="tour-detail-page">
    <!-- Hero Section -->
    <section class="tour-hero">
        <div class="container">
            <div class="tour-hero-content">
                <div class="tour-hero-text">
                    <h1><?= htmlspecialchars($tour['title_pt']) ?></h1>
                    <p class="tour-subtitle"><?= htmlspecialchars($tour['short_description_pt']) ?></p>
                    
                    <div class="tour-meta">
                        <div class="tour-duration">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8z"/>
                                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                            </svg>
                            <span><?= htmlspecialchars($tour['duration']) ?></span>
                        </div>
                        
                        <div class="tour-people">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16c-.8 0-1.54.37-2.01.99L12 11l-1.99-2.01A2.5 2.5 0 0 0 8 8H5.46c-.8 0-1.54.37-2.01.99L1 12.37V22h2v-6h2.5l2.5 2.5V22h2v-4h2v4h2z"/>
                            </svg>
                            <span>Até <?= $tour['max_people'] ?> pessoas</span>
                        </div>
                        
                        <?php if ($tour['price'] > 0): ?>
                        <div class="tour-price">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                            <span>£<?= number_format($tour['price'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($tour['image']): ?>
                <div class="tour-hero-image">
                    <img src="<?= htmlspecialchars(processImagePath($tour['image'])) ?>"
                         alt="<?= htmlspecialchars($tour['title_pt']) ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Tour Description -->
    <section class="tour-description">
        <div class="container">
            <div class="tour-content">
                <div class="tour-main-content">
                    <h2>Sobre este tour</h2>
                    <div class="tour-description-text">
                        <?= nl2br(htmlspecialchars($tour['description_pt'])) ?>
                    </div>
                    
                    <?php if (!empty($tour['includes_pt'])): ?>
                    <div class="tour-includes">
                        <h3>O que está incluído</h3>
                        <div class="includes-content">
                            <?= nl2br(htmlspecialchars($tour['includes_pt'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="tour-sidebar">
                    <div class="booking-card">
                        <h3>Reservar este tour</h3>
                        <div class="booking-info">
                            <div class="booking-duration">
                                <strong>Duração:</strong> <?= htmlspecialchars($tour['duration']) ?>
                            </div>
                            <div class="booking-people">
                                <strong>Grupo:</strong> Até <?= $tour['max_people'] ?> pessoas
                            </div>
                            <?php if ($tour['price'] > 0): ?>
                            <div class="booking-price">
                                <strong>Preço:</strong> £<?= number_format($tour['price'], 2) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="booking-actions">
                            <a href="https://wa.me/447950400919?text=Olá! Gostaria de reservar o tour: <?= urlencode($tour['title_pt']) ?>"
                               class="btn btn-primary btn-whatsapp" target="_blank">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                Reservar via WhatsApp
                            </a>
                            
                            <a href="mailto:carol@lovelylondonbycarol.com?subject=Reserva: <?= urlencode($tour['title_pt']) ?>" 
                               class="btn btn-secondary">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                                Enviar Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Tours -->
    <section class="related-tours">
        <div class="container">
            <h2>Outros tours que você pode gostar</h2>
            <div class="tours-grid">
                <?php
                try {
                    // Buscar outros tours ativos
                    $stmt = $pdo->prepare("SELECT * FROM tours WHERE is_active = 1 AND id != ? ORDER BY is_featured DESC, display_order ASC LIMIT 3");
                    $stmt->execute([$tour['id']]);
                    $related_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($related_tours as $related_tour):
                ?>
                <div class="tour-card">
                    <?php if ($related_tour['image']): ?>
                    <div class="tour-card-image">
                        <img src="<?= htmlspecialchars(processImagePath($related_tour['image'])) ?>"
                             alt="<?= htmlspecialchars($related_tour['title_pt']) ?>"
                             loading="lazy">
                    </div>
                    <?php endif; ?>
                    
                    <div class="tour-card-content">
                        <h3><?= htmlspecialchars($related_tour['title_pt']) ?></h3>
                        <p><?= htmlspecialchars($related_tour['short_description_pt']) ?></p>
                        
                        <div class="tour-card-meta">
                            <span class="tour-duration"><?= htmlspecialchars($related_tour['duration']) ?></span>
                            <?php if ($related_tour['price'] > 0): ?>
                            <span class="tour-price">£<?= number_format($related_tour['price'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="tour-detail.php?slug=<?= htmlspecialchars($related_tour['slug']) ?>" 
                           class="btn btn-outline">Ver detalhes</a>
                    </div>
                </div>
                <?php 
                    endforeach;
                } catch (Exception $e) {
                    error_log("Erro ao buscar tours relacionados: " . $e->getMessage());
                }
                ?>
            </div>
        </div>
    </section>
</div>

<style>
/* Estilos para a página de detalhes do tour */
.tour-detail-page {
    padding-top: 80px;
}

.tour-hero {
    background: linear-gradient(135deg, #700420 0%, #dab59a 100%);
    color: white;
    padding: 60px 0;
}

.tour-hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
}

.tour-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    font-weight: 700;
}

.tour-subtitle {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.tour-meta {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.tour-meta > div {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.tour-hero-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
}

.tour-description {
    padding: 80px 0;
    background: #f8f9fa;
}

.tour-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 60px;
}

.tour-main-content h2 {
    font-size: 2rem;
    margin-bottom: 30px;
    color: #700420;
}

.tour-description-text {
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 40px;
    color: #333;
}

.tour-includes {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.tour-includes h3 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #700420;
}

.includes-content {
    font-size: 1rem;
    line-height: 1.6;
    color: #555;
}

.booking-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    position: sticky;
    top: 100px;
}

.booking-card h3 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #700420;
}

.booking-info > div {
    margin-bottom: 15px;
    font-size: 1rem;
}

.booking-actions {
    margin-top: 30px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.btn-whatsapp {
    background: #25D366;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-whatsapp:hover {
    background: #1ea952;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.related-tours {
    padding: 80px 0;
    background: white;
}

.related-tours h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 50px;
    color: #700420;
}

.tours-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.tour-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.tour-card:hover {
    transform: translateY(-5px);
}

.tour-card-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.tour-card-content {
    padding: 25px;
}

.tour-card-content h3 {
    font-size: 1.3rem;
    margin-bottom: 15px;
    color: #700420;
}

.tour-card-content p {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.6;
}

.tour-card-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    font-size: 0.9rem;
    color: #666;
}

.tour-price {
    font-weight: 600;
    color: #700420;
}

.btn-outline {
    display: inline-block;
    padding: 10px 20px;
    border: 2px solid #700420;
    color: #700420;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    background: #700420;
    color: white;
}

@media (max-width: 768px) {
    .tour-hero-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .tour-content {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .tour-meta {
        gap: 20px;
    }
    
    .tours-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// Incluir footer
require_once '../includes/footer.php';
?>



