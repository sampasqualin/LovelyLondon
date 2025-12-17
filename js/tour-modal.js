/**
 * Modal de Detalhes para Tours e Serviços
 */

// Configuração do WhatsApp
const WHATSAPP_NUMBER = '447950400919'; // +44 7950 400919

/**
 * Cria e injeta o HTML do modal na página
 */
function createModal() {
    // Verificar se o modal já existe
    if (document.getElementById('tourServiceModal')) {
        return;
    }

    const modalHTML = `
        <div id="tourServiceModal" class="tour-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="tour-modal-overlay"></div>
            <div class="tour-modal-container">
                <div class="tour-modal-content">
                    <!-- Header com botão fechar -->
                    <button class="tour-modal-close" aria-label="Fechar modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>

                    <!-- Imagem -->
                    <div class="tour-modal-image">
                        <img id="modalImage" src="" alt="" loading="lazy">
                    </div>

                    <!-- Corpo do modal -->
                    <div class="tour-modal-body">
                        <h2 id="modalTitle" class="tour-modal-title"></h2>
                        <div id="modalDescription" class="tour-modal-description"></div>
                    </div>

                    <!-- Footer com botões -->
                    <div class="tour-modal-footer">
                        <a id="modalWhatsAppBtn" href="#" target="_blank" class="btn btn-whatsapp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                            </svg>
                            Falar no WhatsApp
                        </a>
                        <a id="modalContactBtn" href="#" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            Solicitar Orçamento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Inserir modal no body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Configurar eventos
    setupModalEvents();
}

/**
 * Configura eventos do modal
 */
function setupModalEvents() {
    const modal = document.getElementById('tourServiceModal');
    const closeBtn = modal.querySelector('.tour-modal-close');
    const overlay = modal.querySelector('.tour-modal-overlay');

    // Fechar ao clicar no X
    closeBtn.addEventListener('click', closeModal);

    // Fechar ao clicar no overlay
    overlay.addEventListener('click', closeModal);

    // Fechar ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}

/**
 * Abre o modal com os dados do tour/serviço
 * @param {Object} data - Dados do tour/serviço
 * @param {string} type - 'tour' ou 'service'
 */
function openTourModal(data, type = 'tour') {
    // Criar modal se não existir
    createModal();

    const modal = document.getElementById('tourServiceModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const whatsappBtn = document.getElementById('modalWhatsAppBtn');
    const contactBtn = document.getElementById('modalContactBtn');

    // Preencher conteúdo
    modalImage.src = data.image || '';
    modalImage.alt = data.title || '';
    modalTitle.textContent = data.title || '';

    // Formatar descrição (preservar quebras de linha)
    const description = data.description || '';
    modalDescription.innerHTML = description.replace(/\n/g, '<br>');

    // Configurar botão WhatsApp
    const whatsappMessage = type === 'tour'
        ? `Olá, gostaria de mais informações sobre a tour ${data.title}`
        : `Olá, gostaria de saber mais sobre o serviço ${data.title}`;

    const whatsappUrl = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(whatsappMessage)}`;
    whatsappBtn.href = whatsappUrl;

    // Configurar botão de contato/orçamento
    const basePath = data.basePath || '';
    contactBtn.href = `${basePath}/pages/orcamento.php`;

    // Mostrar modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; // Prevenir scroll da página

    // Focar no título para acessibilidade
    modalTitle.focus();
}

/**
 * Fecha o modal
 */
function closeModal() {
    const modal = document.getElementById('tourServiceModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restaurar scroll
    }
}

// Exportar funções globalmente
window.openTourModal = openTourModal;
window.closeModal = closeModal;
