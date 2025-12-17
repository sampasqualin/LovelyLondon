/**
 * CONTACT FORM WITH AJAX SUBMISSION
 * Versão Moderna e Otimizada
 */

(function() {
    'use strict';

    // Esperar DOM carregar
    document.addEventListener('DOMContentLoaded', initContactForms);

    function initContactForms() {
        const forms = document.querySelectorAll('.contact-form');

        forms.forEach(form => {
            form.addEventListener('submit', handleFormSubmit);
        });
    }

    async function handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector('[type="submit"], .submit-btn');
        const formData = new FormData(form);

        // Desabilitar botão e mostrar loading
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalText = submitBtn.textContent;
            submitBtn.innerHTML = `
                <svg class="btn-spinner" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                    <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-width="4" fill="none"/>
                </svg>
                <span>Enviando...</span>
            `;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                showSuccessMessage(form);
                form.reset();

                // Track conversion (se analytics disponível)
                if (typeof trackEvent === 'function') {
                    trackEvent('submit', 'conversion', 'contact_form_success', 1);
                }
            } else {
                throw new Error('Falha no envio');
            }
        } catch (error) {
            showErrorMessage(form, error.message);

            // Track error
            if (typeof trackEvent === 'function') {
                trackEvent('error', 'form', 'contact_form_error', 1);
            }
        } finally {
            // Restaurar botão
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.dataset.originalText || 'Enviar Mensagem';
            }
        }
    }

    function showSuccessMessage(form) {
        removeMessages(form);

        const successMsg = createMessage(
            'success',
            '✅ Mensagem enviada com sucesso!',
            'Entraremos em contato em breve. Obrigado!'
        );

        form.insertAdjacentElement('beforebegin', successMsg);
        scrollToMessage(successMsg);

        // Auto-remover após 10 segundos
        setTimeout(() => {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 300);
        }, 10000);
    }

    function showErrorMessage(form, errorText = '') {
        removeMessages(form);

        const errorMsg = createMessage(
            'error',
            '❌ Erro ao enviar mensagem',
            errorText || 'Por favor, tente novamente ou entre em contato via WhatsApp.'
        );

        form.insertAdjacentElement('beforebegin', errorMsg);
        scrollToMessage(errorMsg);
    }

    function createMessage(type, title, text) {
        const msg = document.createElement('div');
        msg.className = `form-message form-message-${type}`;
        msg.setAttribute('role', 'alert');
        msg.innerHTML = `
            <div class="form-message-content">
                <strong class="form-message-title">${title}</strong>
                <p class="form-message-text">${text}</p>
            </div>
            <button class="form-message-close" aria-label="Fechar mensagem">&times;</button>
        `;

        // Botão de fechar
        msg.querySelector('.form-message-close').addEventListener('click', () => {
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 300);
        });

        return msg;
    }

    function removeMessages(form) {
        const messages = document.querySelectorAll('.form-message');
        messages.forEach(msg => msg.remove());
    }

    function scrollToMessage(element) {
        const headerHeight = document.querySelector('.header')?.offsetHeight || 80;
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - headerHeight - 20;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
})();
