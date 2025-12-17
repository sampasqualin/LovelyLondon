/**
 * LOVELY LONDON - Mobile Video Handler
 * Gerencia vídeos de background no hero em dispositivos móveis
 * Fornece fallback automático para imagem quando vídeo não carrega
 */

(function() {
    'use strict';

    // Detectar se é mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const isTablet = /iPad|Android/i.test(navigator.userAgent) && window.innerWidth >= 768;

    /**
     * Inicializar handler de vídeo
     */
    function initVideoHandler() {
        const videos = document.querySelectorAll('.hero-bg-video');

        videos.forEach(function(video) {
            // Skip iframes (embedded videos)
            if (video.tagName === 'IFRAME') {
                return;
            }

            // Em mobile, tentar forçar autoplay
            if (isMobile) {
                // Adicionar atributos necessários para mobile
                video.setAttribute('playsinline', '');
                video.setAttribute('muted', '');
                video.muted = true;

                // Tentar reproduzir o vídeo
                const playPromise = video.play();

                if (playPromise !== undefined) {
                    playPromise
                        .then(function() {
                            // Vídeo iniciou com sucesso
                            console.log('Hero video playing successfully');
                        })
                        .catch(function(error) {
                            // Autoplay falhou - usar fallback de imagem
                            console.log('Hero video autoplay failed, using image fallback:', error);
                            handleVideoFallback(video);
                        });
                }

                // Listener para detectar erro de carregamento
                video.addEventListener('error', function() {
                    console.log('Hero video failed to load, using image fallback');
                    handleVideoFallback(video);
                });

                // Timeout: se vídeo não começar em 3 segundos, usar fallback
                setTimeout(function() {
                    if (video.paused || video.currentTime === 0) {
                        console.log('Hero video not playing after timeout, using image fallback');
                        handleVideoFallback(video);
                    }
                }, 3000);
            }

            // Em tablets, permitir vídeo mas com fallback se necessário
            if (isTablet) {
                video.addEventListener('error', function() {
                    handleVideoFallback(video);
                });
            }
        });
    }

    /**
     * Ativar fallback para imagem quando vídeo falha
     */
    function handleVideoFallback(video) {
        const poster = video.getAttribute('poster');
        const heroSlide = video.closest('.hero-slide');

        if (poster && heroSlide) {
            // Esconder o vídeo
            video.style.display = 'none';

            // Verificar se já existe um .hero-bg
            let heroBg = heroSlide.querySelector('.hero-bg');

            if (!heroBg) {
                // Criar elemento de background com imagem
                heroBg = document.createElement('div');
                heroBg.className = 'hero-bg';
                heroBg.style.backgroundImage = 'url(' + poster + ')';
                heroBg.style.backgroundSize = 'cover';
                heroBg.style.backgroundPosition = 'center';
                heroBg.style.position = 'absolute';
                heroBg.style.top = '0';
                heroBg.style.left = '0';
                heroBg.style.width = '100%';
                heroBg.style.height = '100%';
                heroBg.style.zIndex = '0';

                // Inserir antes do vídeo
                video.parentNode.insertBefore(heroBg, video);
            } else {
                // Garantir que o .hero-bg esteja visível
                heroBg.style.display = 'block';
            }
        }
    }

    /**
     * Otimizar performance em mobile
     */
    function optimizeMobilePerformance() {
        if (isMobile) {
            // Reduzir qualidade de vídeo em mobile se possível
            const videos = document.querySelectorAll('.hero-bg-video');
            videos.forEach(function(video) {
                if (video.tagName === 'VIDEO') {
                    // Forçar preload=metadata para economizar dados
                    video.preload = 'metadata';
                }
            });

            // Pausar vídeos quando fora da viewport (economizar bateria)
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    const video = entry.target;
                    if (video.tagName === 'VIDEO') {
                        if (entry.isIntersecting) {
                            video.play().catch(function(e) {
                                console.log('Could not resume video:', e);
                            });
                        } else {
                            video.pause();
                        }
                    }
                });
            }, {
                threshold: 0.25
            });

            videos.forEach(function(video) {
                if (video.tagName === 'VIDEO') {
                    observer.observe(video);
                }
            });
        }
    }

    /**
     * Inicializar quando DOM estiver pronto
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initVideoHandler();
            optimizeMobilePerformance();
        });
    } else {
        initVideoHandler();
        optimizeMobilePerformance();
    }

    /**
     * Reagir a mudanças de orientação
     */
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            initVideoHandler();
        }, 500);
    });

})();
