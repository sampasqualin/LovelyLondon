<?php
include '../includes/header.php';
require_once __DIR__ . '/../includes/content_helpers.php';

// Buscar categorias e tipos para filtros
$categories = getWidgetCategories();
$types = getWidgetTypes();

// Buscar todos os widgets (sem filtros inicialmente)
$widgets = getGetYourGuideWidgets();
?>

<!-- Hero Section com Buscador -->
<section class="experience-hero">
    <div class="experience-hero-bg"></div>
    <div class="container">
        <div class="experience-hero-content">
            <h1 class="experience-title">Explore Londres</h1>
            <p class="experience-subtitle">Descubra as melhores experiências, atrações e tours em Londres. Reserve agora com os melhores preços!</p>

            <!-- Buscador -->
            <div class="experience-search-box">
                <div class="search-input-wrapper">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input type="text"
                           id="widgetSearch"
                           class="experience-search-input"
                           placeholder="Buscar por atividades, atrações, tours..."
                           autocomplete="off">
                    <button type="button" id="clearSearch" class="clear-search-btn" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtros -->
<section class="experience-filters-section">
    <div class="container">
        <div class="experience-filters">
            <!-- Filtro por Categoria -->
            <div class="filter-group">
                <label for="categoryFilter" class="filter-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    </svg>
                    Categoria
                </label>
                <select id="categoryFilter" class="filter-select">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category']) ?>">
                            <?= htmlspecialchars(getContent($cat, 'category')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtro por Tipo -->
            <div class="filter-group">
                <label for="typeFilter" class="filter-label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                    Tipo
                </label>
                <select id="typeFilter" class="filter-select">
                    <option value="">Todos os Tipos</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= htmlspecialchars($type['type']) ?>">
                            <?= htmlspecialchars(getContent($type, 'type')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botão Limpar Filtros -->
            <button type="button" id="clearFilters" class="btn-outline btn-clear-filters">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Limpar Filtros
            </button>
        </div>

        <!-- Contador de resultados -->
        <div class="results-count">
            <span id="resultsCount"><?= count($widgets) ?></span> experiências encontradas
        </div>
    </div>
</section>

<!-- Grid de Widgets -->
<section class="experience-widgets-section section">
    <div class="container">
        <div id="widgetsGrid" class="widgets-grid">
            <?php if (!empty($widgets)): ?>
                <?php foreach ($widgets as $widget): ?>
                    <div class="widget-card"
                         data-category="<?= htmlspecialchars($widget['category']) ?>"
                         data-type="<?= htmlspecialchars($widget['type']) ?>"
                         data-title="<?= htmlspecialchars(strtolower($widget['title'])) ?>"
                         data-description="<?= htmlspecialchars(strtolower($widget['description'] ?? $widget['title'])) ?>"
                         data-tags="<?= htmlspecialchars(strtolower(implode(' ', $widget['tags'] ?? []))) ?>">

                        <!-- GetYourGuide Widget -->
                        <?= renderGetYourGuideWidget($widget) ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" id="emptyState">
                    <div class="empty-state-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </div>
                    <h3>Nenhuma experiência encontrada</h3>
                    <p>Tente ajustar seus filtros ou busca para encontrar o que procura.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Estado vazio para busca (hidden por padrão) -->
        <div class="empty-state" id="noResultsState" style="display: none;">
            <div class="empty-state-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </div>
            <h3>Nenhuma experiência encontrada</h3>
            <p>Tente ajustar seus filtros ou busca para encontrar o que procura.</p>
            <button type="button" class="btn" onclick="clearAllFilters()">Limpar Filtros</button>
        </div>
    </div>
</section>

<!-- GetYourGuide Script -->
<script src="https://widget.getyourguide.com/dist/pa.umd.production.min.js" data-gyg-partner-id="MJKDHZZ"></script>

<!-- Script de Busca e Filtros -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('widgetSearch');
    const clearSearchBtn = document.getElementById('clearSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const widgetsGrid = document.getElementById('widgetsGrid');
    const resultsCount = document.getElementById('resultsCount');
    const noResultsState = document.getElementById('noResultsState');

    // Função de busca e filtro
    function filterWidgets() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value;
        const selectedType = typeFilter.value;

        const cards = widgetsGrid.querySelectorAll('.widget-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const title = card.dataset.title || '';
            const description = card.dataset.description || '';
            const tags = card.dataset.tags || '';
            const category = card.dataset.category || '';
            const type = card.dataset.type || '';

            // Verifica busca por texto
            const matchesSearch = !searchTerm ||
                                  title.includes(searchTerm) ||
                                  description.includes(searchTerm) ||
                                  tags.includes(searchTerm);

            // Verifica filtro de categoria
            const matchesCategory = !selectedCategory || category === selectedCategory;

            // Verifica filtro de tipo
            const matchesType = !selectedType || type === selectedType;

            // Mostra/oculta card
            if (matchesSearch && matchesCategory && matchesType) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Atualiza contador
        resultsCount.textContent = visibleCount;

        // Mostra/oculta estado vazio
        if (visibleCount === 0) {
            widgetsGrid.style.display = 'none';
            noResultsState.style.display = 'flex';
        } else {
            widgetsGrid.style.display = '';
            noResultsState.style.display = 'none';
        }

        // Mostra/oculta botão de limpar busca
        clearSearchBtn.style.display = searchTerm ? 'flex' : 'none';
    }

    // Event listeners
    searchInput.addEventListener('input', filterWidgets);
    categoryFilter.addEventListener('change', filterWidgets);
    typeFilter.addEventListener('change', filterWidgets);

    // Limpar busca
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        filterWidgets();
        searchInput.focus();
    });

    // Limpar todos os filtros
    clearFiltersBtn.addEventListener('click', clearAllFilters);

    // Função global para limpar filtros
    window.clearAllFilters = function() {
        searchInput.value = '';
        categoryFilter.value = '';
        typeFilter.value = '';
        clearSearchBtn.style.display = 'none';
        filterWidgets();
    };
});
</script>

<?php include '../includes/footer.php'; ?>
