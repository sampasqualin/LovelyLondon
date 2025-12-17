<?php
include '../includes/header.php';
require_once __DIR__ . '/../includes/content_helpers.php';

// Carregar tours e serviços do JSON
$all_tours = getTours();
$all_services = getServices();

// Separar tours por tipo
$tours_classicos = array_filter($all_tours, function($tour) {
    return isset($tour['tour_type']) && $tour['tour_type'] === 'classica';
});

$tours_exclusivos = array_filter($all_tours, function($tour) {
    return isset($tour['tour_type']) && $tour['tour_type'] === 'exclusiva';
});
?>

<!-- Estilos específicos do formulário de orçamento -->
<link rel="stylesheet" href="<?php echo $base_path; ?>/css/orcamento.css">

<main id="main-content">
    <section class="interactive-form-section" style="min-height: 100vh;">
        <div class="container">
            <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success" style="padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 30px; color: #155724; text-align: center;">
                <h3 style="margin: 0 0 10px 0;">✅ Solicitação Enviada com Sucesso!</h3>
                <p style="margin: 0;">Recebemos seu pedido de orçamento e entraremos em contato em breve. Verifique seu email (inclusive spam/lixo eletrônico) para a confirmação.</p>
            </div>
            <?php elseif (isset($_GET['erro'])): ?>
            <div class="alert alert-error" style="padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 30px; color: #721c24; text-align: center;">
                <h3 style="margin: 0 0 10px 0;">❌ Erro ao Enviar Solicitação</h3>
                <p style="margin: 0;"><?= htmlspecialchars($_GET['erro']) ?></p>
                <p style="margin: 10px 0 0 0; font-size: 0.9em;">Por favor, tente novamente ou entre em contato diretamente pelo WhatsApp.</p>
            </div>
            <?php endif; ?>

            <!-- Progress Bar -->
            <div class="form-progress">
                <div class="form-progress-bar" id="progressBar"></div>
            </div>

            <!-- Form Container -->
            <div class="interactive-form-container">
                <form id="interactiveForm" class="interactive-form" action="<?php echo $base_path; ?>/pages/orcamento_handler.php" method="POST">

                    <!-- Step 1: Tipo de Serviço -->
                    <div class="form-step active" data-step="1">
                        <div class="step-header">
                            <span class="step-number">1/6</span>
                            <h2 class="step-title">O que você está procurando?</h2>
                            <p class="step-subtitle">Escolha o tipo de experiência que deseja em Londres</p>
                        </div>
                        <div class="step-content">
                            <div class="option-cards">
                                <label class="option-card" data-value="tour">
                                    <input type="radio" name="tipo_servico" value="tour" required>
                                    <div class="option-card-content">
                                        <div class="option-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                        </div>
                                        <h3>Tours Guiados</h3>
                                        <p>Explore Londres com guia certificado</p>
                                    </div>
                                </label>
                                <label class="option-card" data-value="servico">
                                    <input type="radio" name="tipo_servico" value="servico" required>
                                    <div class="option-card-content">
                                        <div class="option-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                                <polyline points="14 2 14 8 20 8"></polyline>
                                            </svg>
                                        </div>
                                        <h3>Serviços Personalizados</h3>
                                        <p>Consultoria, planejamento e muito mais</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2a: Categoria de Tour (condicional - apenas se escolheu "tour") -->
                    <div class="form-step" data-step="2a" data-condition="tour">
                        <div class="step-header">
                            <span class="step-number">2/6</span>
                            <h2 class="step-title">Que tipo de tour você prefere?</h2>
                            <p class="step-subtitle">Escolha a categoria que mais combina com você</p>
                        </div>
                        <div class="step-content">
                            <div class="option-cards">
                                <label class="option-card" data-value="classico">
                                    <input type="radio" name="categoria_tour" value="classico">
                                    <div class="option-card-content">
                                        <div class="option-icon">🎭</div>
                                        <h3>Tours Clássicos</h3>
                                        <p>Os principais pontos turísticos</p>
                                    </div>
                                </label>
                                <label class="option-card" data-value="exclusivo">
                                    <input type="radio" name="categoria_tour" value="exclusivo">
                                    <div class="option-card-content">
                                        <div class="option-icon">👑</div>
                                        <h3>Tours Exclusivos</h3>
                                        <p>Experiências VIP e premium</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2b: Seleção de Serviço (condicional - apenas se escolheu "servico") -->
                    <div class="form-step" data-step="2b" data-condition="servico">
                        <div class="step-header">
                            <span class="step-number">2/6</span>
                            <h2 class="step-title">Quais serviços você precisa?</h2>
                            <p class="step-subtitle">Selecione um ou mais serviços</p>
                        </div>
                        <div class="step-content">
                            <div class="option-list">
                                <?php if (!empty($all_services)): ?>
                                    <?php foreach ($all_services as $service): ?>
                                    <label class="option-list-item">
                                        <input type="checkbox" name="servicos_escolhidos[]" value="<?= htmlspecialchars(getContent($service, 'title')) ?>">
                                        <span class="option-list-content">
                                            <span class="option-list-icon">📋</span>
                                            <span class="option-list-text"><?= htmlspecialchars(getContent($service, 'title')) ?></span>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Nenhum serviço disponível no momento.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3a: Tours Clássicos (condicional) -->
                    <div class="form-step" data-step="3a" data-condition="classico">
                        <div class="step-header">
                            <span class="step-number">3/6</span>
                            <h2 class="step-title">Escolha seus Tours Clássicos</h2>
                            <p class="step-subtitle">Selecione um ou mais tours</p>
                        </div>
                        <div class="step-content">
                            <div class="option-list">
                                <?php if (!empty($tours_classicos)): ?>
                                    <?php foreach ($tours_classicos as $tour): ?>
                                    <label class="option-list-item">
                                        <input type="checkbox" name="tours_escolhidos[]" value="<?= htmlspecialchars(getContent($tour, 'title')) ?>">
                                        <span class="option-list-content">
                                            <span class="option-list-text">
                                                <strong><?= htmlspecialchars(getContent($tour, 'title')) ?></strong>
                                                <?php if (!empty(getContent($tour, 'short_description'))): ?>
                                                    <small><?= htmlspecialchars(getContent($tour, 'short_description')) ?></small>
                                                <?php endif; ?>
                                            </span>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Nenhum tour clássico disponível no momento.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3b: Tours Exclusivos (condicional) -->
                    <div class="form-step" data-step="3b" data-condition="exclusivo">
                        <div class="step-header">
                            <span class="step-number">3/6</span>
                            <h2 class="step-title">Escolha seus Tours Exclusivos</h2>
                            <p class="step-subtitle">Selecione um ou mais tours premium</p>
                        </div>
                        <div class="step-content">
                            <div class="option-list">
                                <?php if (!empty($tours_exclusivos)): ?>
                                    <?php foreach ($tours_exclusivos as $tour): ?>
                                    <label class="option-list-item">
                                        <input type="checkbox" name="tours_escolhidos[]" value="<?= htmlspecialchars(getContent($tour, 'title')) ?>">
                                        <span class="option-list-content">
                                            <span class="option-list-text">
                                                <strong><?= htmlspecialchars(getContent($tour, 'title')) ?></strong>
                                                <?php if (!empty(getContent($tour, 'short_description'))): ?>
                                                    <small><?= htmlspecialchars(getContent($tour, 'short_description')) ?></small>
                                                <?php endif; ?>
                                            </span>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>Nenhum tour exclusivo disponível no momento.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Detalhes da Reserva -->
                    <div class="form-step" data-step="4">
                        <div class="step-header">
                            <span class="step-number">4/6</span>
                            <h2 class="step-title">Detalhes da sua experiência</h2>
                            <p class="step-subtitle">Quando e quantas pessoas?</p>
                        </div>
                        <div class="step-content">
                            <div class="form-fields">
                                <div class="form-field">
                                    <label for="num_pessoas">Número de Pessoas</label>
                                    <select id="num_pessoas" name="num_pessoas" required>
                                        <option value="">Selecione...</option>
                                        <option value="1">1 pessoa</option>
                                        <option value="2">2 pessoas</option>
                                        <option value="3">3 pessoas</option>
                                        <option value="4">4 pessoas</option>
                                        <option value="5">5 pessoas</option>
                                        <option value="6">6 pessoas</option>
                                        <option value="7+">7 ou mais pessoas</option>
                                    </select>
                                </div>
                                <div class="form-field">
                                    <label for="data_preferida">Data Preferida</label>
                                    <input type="date" id="data_preferida" name="data_preferida" required>
                                </div>
                                <div class="form-field">
                                    <label for="periodo">Período do Dia</label>
                                    <select id="periodo" name="periodo" required>
                                        <option value="">Selecione...</option>
                                        <option value="Manhã (9h-12h)">Manhã (9h-12h)</option>
                                        <option value="Tarde (13h-17h)">Tarde (13h-17h)</option>
                                        <option value="Dia Completo (9h-17h)">Dia Completo (9h-17h)</option>
                                        <option value="Noite (18h-22h)">Noite (18h-22h)</option>
                                        <option value="Flexível">Flexível</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Dados Pessoais -->
                    <div class="form-step" data-step="5">
                        <div class="step-header">
                            <span class="step-number">5/6</span>
                            <h2 class="step-title">Seus dados de contato</h2>
                            <p class="step-subtitle">Para enviarmos o orçamento personalizado</p>
                        </div>
                        <div class="step-content">
                            <div class="form-fields">
                                <div class="form-field">
                                    <label for="nome">Nome Completo *</label>
                                    <input type="text" id="nome" name="nome" required placeholder="Seu nome">
                                </div>
                                <div class="form-field">
                                    <label for="email">E-mail *</label>
                                    <input type="email" id="email" name="email" required placeholder="seu@email.com">
                                </div>
                                <div class="form-field">
                                    <label for="telefone">Telefone/WhatsApp *</label>
                                    <input type="tel" id="telefone" name="telefone" required placeholder="+55 (11) 99999-9999">
                                </div>
                                <div class="form-field full-width">
                                    <label for="observacoes">Observações (opcional)</label>
                                    <textarea id="observacoes" name="observacoes" rows="4" placeholder="Alguma preferência especial, necessidade de acessibilidade, ou informação adicional que queira compartilhar?"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Resumo e Confirmação -->
                    <div class="form-step" data-step="6">
                        <div class="step-header">
                            <span class="step-number">6/6</span>
                            <h2 class="step-title">Revise seu orçamento</h2>
                            <p class="step-subtitle">Confira os dados antes de enviar</p>
                        </div>
                        <div class="step-content">
                            <div class="summary-card">
                                <h3>Resumo do Orçamento</h3>
                                <div id="summaryContent" class="summary-content">
                                    <!-- Preenchido dinamicamente via JavaScript -->
                                </div>
                            </div>
                            <div class="form-consent">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="consent" required>
                                    <span>Concordo em receber o orçamento e comunicações da Lovely London por e-mail</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-navigation">
                        <button type="button" class="btn-nav btn-prev" id="btnPrev" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                            Voltar
                        </button>
                        <button type="button" class="btn-nav btn-next" id="btnNext">
                            Continuar
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </button>
                        <button type="submit" class="btn-nav btn-submit" id="btnSubmit" style="display: none;">
                            📧 Enviar Orçamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- JavaScript específico do formulário de orçamento -->
<script src="<?php echo $base_path; ?>/js/orcamento.js"></script>

<!-- Auto-redirect após sucesso -->
<script>
    // Verificar se foi enviado com sucesso
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('sucesso') === '1') {
        // Redirecionar para index após 3 segundos
        setTimeout(function() {
            window.location.href = '<?php echo $base_path; ?>/index.php';
        }, 3000);
    }
</script>

<?php include '../includes/footer.php'; ?>
