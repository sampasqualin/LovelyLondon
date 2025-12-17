<?php
/**
 * Empty State Component
 * Componente reutilizável para estados vazios
 *
 * @param string $icon - SVG icon path data
 * @param string $title - Título do empty state
 * @param string $message - Mensagem explicativa
 * @param string|null $cta_text - Texto do botão (opcional)
 * @param string|null $cta_url - URL do botão (opcional)
 * @param string $icon_type - Tipo de ícone predefinido (book, tour, service, blog, testimonial, custom)
 *
 * @example
 * renderEmptyState('tour', 'Nenhum Tour Disponível', 'Em breve...', 'Contato', '#contact');
 */

if (!function_exists('renderEmptyState')) {
    function renderEmptyState(
    string $icon_type = 'book',
    string $title = 'Nenhum Conteúdo Disponível',
    string $message = 'Em breve teremos novidades para você!',
    ?string $cta_text = null,
    ?string $cta_url = null
): void {
    // Ícones predefinidos (SVG paths otimizados)
    $icons = [
        'book' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>',
        'tour' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
        'service' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>',
        'blog' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>',
        'testimonial' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
        'gallery' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>',
        'search' => '<circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>',
    ];

    $icon_svg = $icons[$icon_type] ?? $icons['book'];
    ?>

    <div class="empty-state" role="status" aria-live="polite">
        <div class="empty-state-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="64"
                 height="64"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <?= $icon_svg ?>
            </svg>
        </div>
        <h3 class="empty-state-title"><?= htmlspecialchars($title) ?></h3>
        <p class="empty-state-message"><?= htmlspecialchars($message) ?></p>

        <?php if ($cta_text && $cta_url): ?>
            <a href="<?= htmlspecialchars($cta_url) ?>" class="btn btn-primary">
                <?= htmlspecialchars($cta_text) ?>
            </a>
        <?php endif; ?>
    </div>

    <?php
    }
}
