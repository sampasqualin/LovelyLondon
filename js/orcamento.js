/**
 * LOVELY LONDON - FORMULÁRIO INTERATIVO DE ORÇAMENTO
 * Navegação estilo Google Forms com steps condicionais
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('interactiveForm');
    const steps = document.querySelectorAll('.form-step');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');
    const progressBar = document.getElementById('progressBar');

    let currentStep = 1;
    let formData = {};

    // Set minimum date to today
    const dateInput = document.getElementById('data_preferida');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    // Initialize
    updateUI();

    // Event Listeners
    btnNext.addEventListener('click', nextStep);
    btnPrev.addEventListener('click', prevStep);

    // Auto-advance on option selection
    document.querySelectorAll('.option-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Add visual feedback
            const card = this.closest('.option-card');
            document.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');

            // Save data
            saveStepData();

            // Auto-advance after 300ms
            setTimeout(() => {
                nextStep();
            }, 300);
        });
    });

    // Track option list selections (radio)
    document.querySelectorAll('.option-list-item input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const item = this.closest('.option-list-item');
            document.querySelectorAll('.option-list-item').forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
            saveStepData();
        });
    });

    // Track option list selections (checkbox)
    document.querySelectorAll('.option-list-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const item = this.closest('.option-list-item');
            if (this.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
            saveStepData();
        });
    });

    /**
     * Navigate to next step
     */
    function nextStep() {
        const currentStepEl = getCurrentStepElement();

        // Validate current step
        if (!validateStep(currentStepEl)) {
            showError('Por favor, preencha todos os campos obrigatórios');
            return;
        }

        // Save data
        saveStepData();

        // Determine next step based on conditions
        const nextStepNumber = getNextStep();

        if (nextStepNumber) {
            currentStep = nextStepNumber;
            updateUI();
            scrollToTop();
        }
    }

    /**
     * Navigate to previous step
     */
    function prevStep() {
        const prevStepNumber = getPrevStep();

        if (prevStepNumber) {
            currentStep = prevStepNumber;
            updateUI();
            scrollToTop();
        }
    }

    /**
     * Get next step based on conditions
     */
    function getNextStep() {
        const currentStepEl = getCurrentStepElement();
        const currentStepId = currentStepEl.dataset.step;

        // Step 1 -> Step 2a (tour) or 2b (servico)
        if (currentStepId === '1') {
            const tipoServico = document.querySelector('input[name="tipo_servico"]:checked')?.value;
            formData.tipo_servico = tipoServico;

            if (tipoServico === 'tour') {
                return getStepNumber('2a');
            } else if (tipoServico === 'servico') {
                return getStepNumber('2b');
            }
        }

        // Step 2a -> Step 3a (classico) or 3b (exclusivo)
        if (currentStepId === '2a') {
            const categoriaTour = document.querySelector('input[name="categoria_tour"]:checked')?.value;
            formData.categoria_tour = categoriaTour;

            if (categoriaTour === 'classico') {
                return getStepNumber('3a');
            } else if (categoriaTour === 'exclusivo') {
                return getStepNumber('3b');
            }
        }

        // Step 2b (servico) -> Step 4
        if (currentStepId === '2b') {
            return getStepNumber('4');
        }

        // Step 3a or 3b (tour selection) -> Step 4
        if (currentStepId === '3a' || currentStepId === '3b') {
            return getStepNumber('4');
        }

        // Step 4 -> Step 5
        if (currentStepId === '4') {
            return getStepNumber('5');
        }

        // Step 5 -> Step 6 (summary)
        if (currentStepId === '5') {
            updateSummary();
            return getStepNumber('6');
        }

        return null;
    }

    /**
     * Get previous step based on conditions
     */
    function getPrevStep() {
        const currentStepEl = getCurrentStepElement();
        const currentStepId = currentStepEl.dataset.step;

        // Step 6 -> Step 5
        if (currentStepId === '6') {
            return getStepNumber('5');
        }

        // Step 5 -> Step 4
        if (currentStepId === '5') {
            return getStepNumber('4');
        }

        // Step 4 -> depends on previous choice
        if (currentStepId === '4') {
            if (formData.tipo_servico === 'tour') {
                // Go back to tour selection (3a or 3b)
                if (formData.categoria_tour === 'classico') {
                    return getStepNumber('3a');
                } else {
                    return getStepNumber('3b');
                }
            } else {
                // Go back to service selection (2b)
                return getStepNumber('2b');
            }
        }

        // Step 3a or 3b -> Step 2a
        if (currentStepId === '3a' || currentStepId === '3b') {
            return getStepNumber('2a');
        }

        // Step 2a or 2b -> Step 1
        if (currentStepId === '2a' || currentStepId === '2b') {
            return getStepNumber('1');
        }

        return null;
    }

    /**
     * Get step number by data-step attribute
     */
    function getStepNumber(stepId) {
        for (let i = 0; i < steps.length; i++) {
            if (steps[i].dataset.step === stepId) {
                return i + 1;
            }
        }
        return null;
    }

    /**
     * Get current step element
     */
    function getCurrentStepElement() {
        return steps[currentStep - 1];
    }

    /**
     * Validate current step
     */
    function validateStep(stepEl) {
        const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');

        for (let input of inputs) {
            // Radio buttons - check if at least one is checked
            if (input.type === 'radio') {
                const radioGroup = stepEl.querySelectorAll(`input[name="${input.name}"]`);
                const isChecked = Array.from(radioGroup).some(r => r.checked);
                if (!isChecked) {
                    return false;
                }
            }
            // Other inputs
            else if (!input.value.trim()) {
                return false;
            }
        }

        // Validar checkboxes (pelo menos um deve estar marcado)
        const checkboxGroups = new Set();
        stepEl.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            if (cb.name.includes('[]')) {
                checkboxGroups.add(cb.name);
            }
        });

        for (let groupName of checkboxGroups) {
            const checkboxes = stepEl.querySelectorAll(`input[name="${groupName}"]`);
            const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (!isAnyChecked && checkboxes.length > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Save current step data
     */
    function saveStepData() {
        const currentStepEl = getCurrentStepElement();
        const inputs = currentStepEl.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            if (input.type === 'radio' && input.checked) {
                formData[input.name] = input.value;
            } else if (input.type === 'checkbox') {
                // Processar checkboxes (podem ser múltiplos)
                if (input.name.includes('[]')) {
                    // Array de checkboxes
                    if (!formData[input.name]) {
                        formData[input.name] = [];
                    }
                    if (input.checked) {
                        if (!formData[input.name].includes(input.value)) {
                            formData[input.name].push(input.value);
                        }
                    } else {
                        // Remover do array se desmarcado
                        formData[input.name] = formData[input.name].filter(v => v !== input.value);
                    }
                } else {
                    // Checkbox único
                    formData[input.name] = input.checked;
                }
            } else if (input.type !== 'radio') {
                formData[input.name] = input.value;
            }
        });
    }

    /**
     * Update UI (show/hide steps, buttons, progress)
     */
    function updateUI() {
        const currentStepEl = getCurrentStepElement();
        const currentStepId = currentStepEl.dataset.step;

        // Hide all steps
        steps.forEach(step => step.classList.remove('active'));

        // Show current step
        currentStepEl.classList.add('active');

        // Update progress bar
        const totalVisibleSteps = 6; // Fixed for user display
        const progress = (getVisualStepNumber() / totalVisibleSteps) * 100;
        progressBar.style.width = progress + '%';

        // Update navigation buttons
        btnPrev.style.display = currentStep > 1 ? 'flex' : 'none';

        if (currentStepId === '6') {
            btnNext.style.display = 'none';
            btnSubmit.style.display = 'flex';
        } else {
            btnNext.style.display = 'flex';
            btnSubmit.style.display = 'none';
        }

        // Update step numbers in UI
        updateStepNumbers();
    }

    /**
     * Get visual step number (1-6 for user display)
     */
    function getVisualStepNumber() {
        const currentStepEl = getCurrentStepElement();
        const stepId = currentStepEl.dataset.step;

        const stepMap = {
            '1': 1,
            '2a': 2,
            '2b': 2,
            '3a': 3,
            '3b': 3,
            '4': 4,
            '5': 5,
            '6': 6
        };

        return stepMap[stepId] || 1;
    }

    /**
     * Update step number displays
     */
    function updateStepNumbers() {
        const visualStep = getVisualStepNumber();
        const stepNumberEls = document.querySelectorAll('.step-number');
        stepNumberEls.forEach(el => {
            if (el.closest('.form-step').classList.contains('active')) {
                el.textContent = `${visualStep}/6`;
            }
        });
    }

    /**
     * Update summary before final step
     */
    function updateSummary() {
        const summaryContent = document.getElementById('summaryContent');

        let html = '<div class="summary-items">';

        // Tipo de serviço
        if (formData.tipo_servico) {
            html += `
                <div class="summary-item">
                    <strong>Tipo:</strong>
                    <span>${formData.tipo_servico === 'tour' ? 'Tour Guiado' : 'Serviço Personalizado'}</span>
                </div>
            `;
        }

        // Tours escolhidos (múltiplos)
        if (formData['tours_escolhidos[]'] && formData['tours_escolhidos[]'].length > 0) {
            html += `
                <div class="summary-item">
                    <strong>Tours Selecionados:</strong>
                    <span>${formData['tours_escolhidos[]'].join(', ')}</span>
                </div>
            `;
        }

        // Serviços escolhidos (múltiplos)
        if (formData['servicos_escolhidos[]'] && formData['servicos_escolhidos[]'].length > 0) {
            html += `
                <div class="summary-item">
                    <strong>Serviços Selecionados:</strong>
                    <span>${formData['servicos_escolhidos[]'].join(', ')}</span>
                </div>
            `;
        }

        // Detalhes
        if (formData.num_pessoas) {
            html += `
                <div class="summary-item">
                    <strong>Número de Pessoas:</strong>
                    <span>${formData.num_pessoas === '7+' ? '7 ou mais' : formData.num_pessoas}</span>
                </div>
            `;
        }

        if (formData.data_preferida) {
            const date = new Date(formData.data_preferida + 'T00:00:00');
            const formattedDate = date.toLocaleDateString('pt-BR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            html += `
                <div class="summary-item">
                    <strong>Data:</strong>
                    <span>${formattedDate}</span>
                </div>
            `;
        }

        if (formData.periodo) {
            html += `
                <div class="summary-item">
                    <strong>Período:</strong>
                    <span>${formData.periodo}</span>
                </div>
            `;
        }

        // Dados pessoais
        if (formData.nome) {
            html += `
                <div class="summary-item">
                    <strong>Nome:</strong>
                    <span>${formData.nome}</span>
                </div>
            `;
        }

        if (formData.email) {
            html += `
                <div class="summary-item">
                    <strong>E-mail:</strong>
                    <span>${formData.email}</span>
                </div>
            `;
        }

        if (formData.telefone) {
            html += `
                <div class="summary-item">
                    <strong>Telefone:</strong>
                    <span>${formData.telefone}</span>
                </div>
            `;
        }

        if (formData.observacoes && formData.observacoes.trim()) {
            html += `
                <div class="summary-item full-width">
                    <strong>Observações:</strong>
                    <span>${formData.observacoes}</span>
                </div>
            `;
        }

        html += '</div>';

        summaryContent.innerHTML = html;
    }

    /**
     * Show error message
     */
    function showError(message) {
        // Simple alert for now - can be enhanced with custom modal
        alert(message);
    }

    /**
     * Scroll to top of form
     */
    function scrollToTop() {
        const formContainer = document.querySelector('.interactive-form-container');
        if (formContainer) {
            formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    /**
     * Form submission
     */
    form.addEventListener('submit', function(e) {
        // Form will submit naturally to Formspree
        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('submit', 'conversion', 'orcamento_form_submission', 1);
        }

        // Show loading state
        btnSubmit.innerHTML = '⏳ Enviando...';
        btnSubmit.disabled = true;
    });
});
