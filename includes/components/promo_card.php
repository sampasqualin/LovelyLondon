<?php
/**
 * Promo Card Component
 * Componente reutilizável para cards de tours, services, blog posts
 *
 * @param string $type - Tipo do card (tour, service, blog)
 * @param array $item - Dados do item (precisa ter: title_pt, title_en, image ou image_url)
 * @param string|null $link - URL do card (opcional, se não passar usa #)
 * @param bool $show_cta - Mostrar botão CTA (default: true)
 * @param string $cta_text - Texto do botão CTA (default: "Consultar")
 * @param string|null $fallback_image - URL de imagem fallback
 * @param int|null $index - Índice para cores de fallback
 *
 * @example
 * renderPromoCard('tour', $tour, '/pages/tour-detail.php?id=123', true, 'Ver Detalhes');
 */

// As funções getContent() e lang já estão disponíveis globalmente
// Não precisa re-incluir aqui para evitar warnings

if (!function_exists('renderPromoCard')) {
    function renderPromoCard(
    string $type,
    array $item,
    ?string $link = null,
    bool $show_cta = true,
    string $cta_text = 'Consultar',
    ?string $fallback_image = null,
    ?int $index = null
): void {
    global $base_path;

    // Determinar imagem baseado no tipo
    $image = null;
    if (isset($item['image']) && !empty($item['image'])) {
        $image = $item['image'];
    } elseif (isset($item['image_url']) && !empty($item['image_url'])) {
        $image = $base_path . $item['image_url'];
    } elseif (isset($item['featured_image']) && !empty($item['featured_image'])) {
        $image = $base_path . $item['featured_image'];
    }

    // Fallback images por tipo
    $default_fallbacks = [
        'tour' => 'https://images.unsplash.com/photo-1526129318478-62ed807ebdf9?q=80&w=400&auto=format&fit=crop&fm=webp',
        'service' => 'https://images.unsplash.com/photo-1489515217757-5fd1be406fef?q=80&w=400&auto=format&fit=crop&fm=webp',
        'blog' => 'https://images.unsplash.com/photo-1518638150499-23c375fb443b?q=80&w=400&auto=format&fit=crop&fm=webp',
    ];

    if (!$image) {
        $image = $fallback_image ?? $default_fallbacks[$type] ?? $default_fallbacks['tour'];
    }

    // Título traduzido
    $title = getContent($item, 'title');

    // Link padrão
    $href = $link ?? '#contact';

    // Classes do card baseadas no tipo
    $card_class = "promo-card {$type}-card";
    if ($type === 'service') {
        $card_class .= '-page';
    } elseif ($type === 'tour') {
        $card_class = "promo-card tour-card-simple";
    } elseif ($type === 'blog') {
        $card_class = "promo-card blog-card-simple";
    }

    // Data attributes (útil para filtros)
    $data_attrs = '';
    if (isset($item['category_slug'])) {
        $data_attrs = sprintf('data-category="%s"', htmlspecialchars($item['category_slug']));
    }
    ?>

    <a href="<?= htmlspecialchars($href) ?>"
       class="<?= $card_class ?>"
       <?= $data_attrs ?>
       aria-label="<?= htmlspecialchars($title) ?>">

        <div class="promo-card-image-wrapper">
            <?php if ($image): ?>
                <img src="<?= htmlspecialchars($image) ?>"
                     alt="<?= htmlspecialchars($title) ?>"
                     class="promo-card-bg"
                     loading="lazy"
                     <?php if ($index !== null): ?>
                     onerror="this.onerror=null; this.style.display='none'; this.parentElement.style.background='hsl(<?= (($index % 4) * 90) ?>, 70%, 50%)';"
                     <?php endif; ?>>
            <?php else: ?>
                <div class="promo-card-bg promo-card-bg-fallback"
                     style="background: hsl(<?= (($index ?? 0) % 4) * 90 ?>, 70%, 50%);"></div>
            <?php endif; ?>

            <!-- Overlay para melhor contraste -->
            <div class="promo-card-overlay"></div>
        </div>

        <div class="promo-card-content">
            <h3 class="promo-card-title"><?= htmlspecialchars($title) ?></h3>

            <?php if ($show_cta): ?>
                <span class="btn btn-small" role="button">
                    <?= htmlspecialchars($cta_text) ?>
                </span>
            <?php endif; ?>
        </div>
    </a>

    <?php
    }
}
