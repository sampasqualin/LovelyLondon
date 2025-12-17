<?php
/**
 * Button Component
 * Sistema padronizado de botões para todo o site
 *
 * @param string $text - Texto do botão
 * @param string $url - URL de destino
 * @param string $variant - Variante do botão (primary, outline, ghost, danger)
 * @param string $size - Tamanho (small, medium, large)
 * @param string|null $icon - SVG do ícone (opcional)
 * @param string $icon_position - Posição do ícone (left, right)
 * @param array $attrs - Atributos HTML adicionais (class, data-*, aria-*)
 * @param string $element - Elemento HTML (a, button)
 *
 * @example
 * renderButton('Enviar', '#', 'primary', 'large', $icon_svg, 'right', ['data-action' => 'submit']);
 */

if (!function_exists('renderButton')) {
    function renderButton(
    string $text,
    string $url = '#',
    string $variant = 'primary',
    string $size = 'medium',
    ?string $icon = null,
    string $icon_position = 'left',
    array $attrs = [],
    string $element = 'a'
): void {
    // Classes base
    $classes = ['btn'];

    // Adicionar variante
    $variant_classes = [
        'primary' => 'btn-primary',
        'outline' => 'btn-outline',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
    ];

    if (isset($variant_classes[$variant])) {
        $classes[] = $variant_classes[$variant];
    }

    // Adicionar tamanho
    $size_classes = [
        'small' => 'btn-small',
        'medium' => '',
        'large' => 'btn-large',
    ];

    if ($size !== 'medium' && isset($size_classes[$size])) {
        $classes[] = $size_classes[$size];
    }

    // Adicionar classe de ícone se existir
    if ($icon) {
        $classes[] = 'btn-with-icon';
        $classes[] = "btn-icon-{$icon_position}";
    }

    // Mesclar classes adicionais dos attrs
    if (isset($attrs['class'])) {
        $classes[] = $attrs['class'];
        unset($attrs['class']);
    }

    $class_string = implode(' ', array_filter($classes));

    // Montar atributos HTML
    $attrs_string = '';
    foreach ($attrs as $key => $value) {
        $attrs_string .= sprintf(' %s="%s"', htmlspecialchars($key), htmlspecialchars($value));
    }

    // Renderizar botão
    if ($element === 'button'): ?>
        <button class="<?= $class_string ?>" <?= $attrs_string ?>>
            <?php if ($icon && $icon_position === 'left'): ?>
                <span class="btn-icon btn-icon-left" aria-hidden="true"><?= $icon ?></span>
            <?php endif; ?>

            <span class="btn-text"><?= htmlspecialchars($text) ?></span>

            <?php if ($icon && $icon_position === 'right'): ?>
                <span class="btn-icon btn-icon-right" aria-hidden="true"><?= $icon ?></span>
            <?php endif; ?>
        </button>
    <?php else: ?>
        <a href="<?= htmlspecialchars($url) ?>" class="<?= $class_string ?>" <?= $attrs_string ?>>
            <?php if ($icon && $icon_position === 'left'): ?>
                <span class="btn-icon btn-icon-left" aria-hidden="true"><?= $icon ?></span>
            <?php endif; ?>

            <span class="btn-text"><?= htmlspecialchars($text) ?></span>

            <?php if ($icon && $icon_position === 'right'): ?>
                <span class="btn-icon btn-icon-right" aria-hidden="true"><?= $icon ?></span>
            <?php endif; ?>
        </a>
    <?php endif;
    }
}

/**
 * Renderiza um botão com ícone pré-definido
 */
if (!function_exists('renderIconButton')) {
    function renderIconButton(string $text, string $url, string $icon_name, string $variant = 'primary', string $size = 'medium'): void {
    $icons = [
        'whatsapp' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
        'send' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>',
        'arrow-right' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
        'check' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'document' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>',
    ];

    $icon_svg = $icons[$icon_name] ?? null;
    renderButton($text, $url, $variant, $size, $icon_svg, 'right');
    }
}
