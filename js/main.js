// =============================================================================
// ANALYTICS TRACKING UTILITIES
// =============================================================================

function trackEvent(eventName, eventCategory, eventLabel, value) {
    if (window.gtag) {
        window.gtag('event', eventName, {
            'event_category': eventCategory,
            'event_label': eventLabel,
            'value': value
        });
    }
    if (window.clarity) {
        window.clarity('event', eventLabel);
    }
}

// =============================================================================
// A/B TESTING FRAMEWORK
// =============================================================================

const ABTest = {
    // Get or create user's test group (stored in localStorage)
    getTestGroup: function(testName, variants) {
        const storageKey = `ab_test_${testName}`;
        let group = localStorage.getItem(storageKey);

        if (!group || !variants.includes(group)) {
            // Randomly assign to a variant
            group = variants[Math.floor(Math.random() * variants.length)];
            localStorage.setItem(storageKey, group);
        }

        return group;
    },

    // Track which variant user saw
    trackVariant: function(testName, variant) {
        if (typeof trackEvent === 'function') {
            trackEvent('ab_test_view', 'experiment', `${testName}_${variant}`, 1);
        }
    },

    // Track conversion for a test
    trackConversion: function(testName, variant, value = 1) {
        if (typeof trackEvent === 'function') {
            trackEvent('ab_test_conversion', 'experiment', `${testName}_${variant}`, value);
        }
    }
};

// Test 1: CTA Button Text
const ctaTest = ABTest.getTestGroup('cta_button_text', ['reserve', 'descubra', 'comece']);
ABTest.trackVariant('cta_button_text', ctaTest);

const ctaTexts = {
    'reserve': 'Reserve Agora',
    'descubra': 'Descubra Londres',
    'comece': 'Comece Sua Jornada'
};

// Test 2: Hero Title
const heroTitleTest = ABTest.getTestGroup('hero_title', ['dreams', 'authentic', 'unforgettable']);
ABTest.trackVariant('hero_title', heroTitleTest);

const heroTitles = {
    'dreams': 'Descubra a Londres dos Seus Sonhos',
    'authentic': 'Londres Autêntica com Guia Brasileira',
    'unforgettable': 'Experiências Inesquecíveis em Londres'
};

// Test 3: Primary Color Accent
const colorTest = 'default';
ABTest.trackVariant('color_accent', colorTest);

const colorVariants = {
    'default': '#700420', // --lovely (original)
    'warm': '#955425',    // --notting-hill (terracota)
    'elegant': '#DAB59A'  // --skyline (bege/rosé)
};

// Apply A/B test variations on page load
window.addEventListener('DOMContentLoaded', () => {
    // Apply CTA text variation
    document.querySelectorAll('.btn-header').forEach(btn => {
        if (btn.textContent.includes('Reserve')) {
            btn.textContent = ctaTexts[ctaTest];
            btn.dataset.abTest = ctaTest;
        }
    });

    // Apply Hero title variation
    const heroTitle = document.querySelector('.hero-slide h1');
    if (heroTitle && heroTitle.textContent.includes('Descubra')) {
        heroTitle.textContent = heroTitles[heroTitleTest];
        heroTitle.dataset.abTest = heroTitleTest;
    }

    // Apply color variation
    if (colorTest !== 'default') {
        document.documentElement.style.setProperty('--lovely', colorVariants[colorTest]);
    }

    // Track conversions when users click CTAs
    document.querySelectorAll('.btn, .btn-primary, .btn-header').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.abTest) {
                ABTest.trackConversion('cta_button_text', this.dataset.abTest);
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', () => {
    // =============================================================================
    // EVENT TRACKING FOR CTA BUTTONS
    // =============================================================================

    // Track all "Reserve Agora", "Reservar", "Consultar" buttons
    document.querySelectorAll('a.btn, a.btn-primary, a.btn-header').forEach(button => {
        button.addEventListener('click', function(e) {
            const buttonText = this.textContent.trim();
            const href = this.getAttribute('href');

            if (buttonText.includes('Reserve') || buttonText.includes('Reservar') || buttonText.includes('Consultar')) {
                // Extract tour name from nearby elements if available
                const card = this.closest('.service-card, .tour-card, .promo-card');
                const tourName = card ? (card.querySelector('h3, h4')?.textContent || 'unknown') : 'general';

                // Track WhatsApp clicks
                if (href && href.includes('wa.me')) {
                    trackEvent('click', 'engagement', 'whatsapp_cta_' + tourName.toLowerCase().replace(/\s+/g, '_'), 1);
                }
                // Track internal navigation clicks
                else if (href && href.includes('#contact')) {
                    trackEvent('click', 'engagement', 'contact_form_cta_' + tourName.toLowerCase().replace(/\s+/g, '_'), 1);
                }
            }
        });
    });

    // Track form submissions
    const contactForms = document.querySelectorAll('form.contact-form, form[action*="formspree"]');
    contactForms.forEach(form => {
        form.addEventListener('submit', function() {
            trackEvent('submit', 'engagement', 'contact_form_submission', 1);
        });
    });

    // =============================================================================
    // SCROLL ANIMATIONS
    // =============================================================================

    // Intersection Observer para animações de scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observar elementos com fade-in
    document.querySelectorAll('.fade-in, .fade-in-stagger').forEach(el => {
        observer.observe(el);
    });

    // =============================================================================
    // BACK TO TOP BUTTON
    // =============================================================================

    const backToTopButton = document.querySelector('.back-to-top');

    if (backToTopButton) {
        // Mostrar/ocultar botão ao scroll
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 500) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });

        // Scroll suave ao topo
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // =============================================================================
    // FAQ ACCORDION
    // =============================================================================

    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            // Fechar todos os itens
            faqItems.forEach(faqItem => {
                faqItem.classList.remove('active');
                faqItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            // Abrir o item clicado (se não estava ativo)
            if (!isActive) {
                item.classList.add('active');
                question.setAttribute('aria-expanded', 'true');
            }
        });
    });

    // =============================================================================
    // MOBILE MENU & MODALS
    // =============================================================================

    // Simple mobile hamburger menu (header)
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    let mobileMenuOpen = false;

    function openMobileMenu() {
        if (!mainNav) return;
        mainNav.classList.add('nav--open');
        document.body.classList.add('mobile-menu-open');
        if (mobileMenuToggle) {
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
        }
        mobileMenuOpen = true;
    }

    function closeMobileMenu() {
        if (!mainNav) return;
        mainNav.classList.remove('nav--open');
        document.body.classList.remove('mobile-menu-open');
        if (mobileMenuToggle) {
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
        }
        mobileMenuOpen = false;
    }

    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', () => {
            if (mobileMenuOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    // Modal Logic
    const aboutModal = document.getElementById('about-modal');
    const openModalLinks = [
        document.getElementById('open-about-modal'),
        document.getElementById('open-about-modal-footer')
    ];
    const closeModalButton = document.getElementById('close-about-modal');

    function openModal(e) {
        e.preventDefault();
        if (aboutModal) {
            aboutModal.classList.add('visible');
            document.body.classList.add('modal-open');
        }
        // No mobile menu to close
    }

    function closeModal() {
        if (aboutModal) {
            aboutModal.classList.remove('visible');
            document.body.classList.remove('modal-open');
        }
    }

    openModalLinks.forEach(link => {
        if (link) link.addEventListener('click', openModal);
    });

    if(closeModalButton) closeModalButton.addEventListener('click', closeModal);
    if(aboutModal) aboutModal.addEventListener('click', (e) => {
        if (e.target === aboutModal) closeModal();
    });

    // FAQ Modal Logic
    const faqModal = document.getElementById('faq-modal');
    const openFaqModalButton = document.getElementById('open-faq-modal');
    const closeFaqModalButton = document.getElementById('close-faq-modal');

    function openFaqModal(e) {
        e.preventDefault();
        if (faqModal) {
            faqModal.classList.add('visible');
            document.body.classList.add('modal-open');
        }
        // Fechar menu mobile se estiver aberto
        if (mobileMenuOpen) {
            closeMobileMenu();
        }
    }

    function closeFaqModal() {
        if (faqModal) {
            faqModal.classList.remove('visible');
            document.body.classList.remove('modal-open');
        }
    }

    if (openFaqModalButton) openFaqModalButton.addEventListener('click', openFaqModal);
    if (closeFaqModalButton) closeFaqModalButton.addEventListener('click', closeFaqModal);
    if (faqModal) faqModal.addEventListener('click', (e) => {
        if (e.target === faqModal) closeFaqModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (faqModal && faqModal.classList.contains('visible')) {
                closeFaqModal();
            }
            if (aboutModal && aboutModal.classList.contains('visible')) {
                closeModal();
            } else if (mobileMenuOpen) {
                closeMobileMenu();
            }
        }
    });

    const contactBtnInModal = document.querySelector('#about-modal .btn');
    if(contactBtnInModal) {
        contactBtnInModal.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
            const contactSection = document.getElementById('contact');
            if (contactSection) {
                contactSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }

    // Hero Slider Logic
    const slides = document.querySelector('.hero-slides');
    const dots = document.querySelectorAll('.slider-dot');
    const playPauseBtn = document.getElementById('sliderPlayPause');
    const playIcon = playPauseBtn ? playPauseBtn.querySelector('.play-icon') : null;
    const pauseIcon = playPauseBtn ? playPauseBtn.querySelector('.pause-icon') : null;
    let currentSlide = 0;
    const slideCount = slides ? slides.children.length : 0;
    let slideInterval;
    let isPlaying = true;

    function goToSlide(slideIndex) {
        if (!slides || slideCount <= 1) return;
        const denom = Math.max(1, slideCount);
        slides.style.transform = `translateX(-${slideIndex * (100 / denom)}%)`;
        dots.forEach((dot, index) => {
            dot.classList.remove('active');
            dot.setAttribute('aria-current', 'false');
        });
        if (dots[slideIndex]) {
            dots[slideIndex].classList.add('active');
            dots[slideIndex].setAttribute('aria-current', 'true');
        }
        currentSlide = slideIndex;
    }

    function nextSlide() {
        if (slideCount <= 1) return;
        let next = (currentSlide + 1) % slideCount;
        goToSlide(next);
    }

    function startSlider() {
        if (slideCount <= 1) return;
        slideInterval = setInterval(nextSlide, 10000);
        isPlaying = true;
        if (playIcon && pauseIcon) {
            playIcon.style.display = 'none';
            pauseIcon.style.display = 'block';
        }
        if (playPauseBtn) {
            playPauseBtn.setAttribute('aria-label', 'Pausar carrossel');
        }
    }

    function stopSlider() {
        clearInterval(slideInterval);
        isPlaying = false;
        if (playIcon && pauseIcon) {
            playIcon.style.display = 'block';
            pauseIcon.style.display = 'none';
        }
        if (playPauseBtn) {
            playPauseBtn.setAttribute('aria-label', 'Reproduzir carrossel');
        }
    }

    function toggleSlider() {
        if (isPlaying) {
            stopSlider();
        } else {
            startSlider();
        }
    }

    // Play/Pause button
    if (playPauseBtn && slideCount > 1) {
        playPauseBtn.addEventListener('click', toggleSlider);
    }

    // Event listeners para dots do slider
    if (slideCount > 1) {
        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const slideIndex = parseInt(dot.dataset.slide);
                if (!isNaN(slideIndex)) {
                    goToSlide(slideIndex);
                }
            });
        });
    }

    // Iniciar slider
    if (slides && slideCount > 1) {
        startSlider();
    }

    // =============================================================================
    // MENU EXTRAS (HAMBURGUER)
    // =============================================================================

    const menuExtrasToggle = document.getElementById('menuExtrasToggle');
    const menuExtrasDropdown = document.getElementById('menuExtrasDropdown');

    if (menuExtrasToggle && menuExtrasDropdown) {
        menuExtrasToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const isActive = menuExtrasDropdown.classList.contains('active');

            if (isActive) {
                menuExtrasDropdown.classList.remove('active');
                menuExtrasToggle.setAttribute('aria-expanded', 'false');
            } else {
                menuExtrasDropdown.classList.add('active');
                menuExtrasToggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Fechar ao clicar fora
        document.addEventListener('click', function(e) {
            if (!menuExtrasToggle.contains(e.target) && !menuExtrasDropdown.contains(e.target)) {
                menuExtrasDropdown.classList.remove('active');
                menuExtrasToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Fechar ao pressionar ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && menuExtrasDropdown.classList.contains('active')) {
                menuExtrasDropdown.classList.remove('active');
                menuExtrasToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // =============================================================================
    // LIGHTBOX GALLERY
    // =============================================================================

    const lightbox = document.getElementById('lightboxModal');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxThumbnails = document.getElementById('lightboxThumbnails');
    const galleryItems = document.querySelectorAll('.gallery-item');

    let currentImageIndex = 0;
    let images = [];

    // Build images array from gallery
    galleryItems.forEach((item, index) => {
        const img = item.querySelector('img');
        const caption = item.dataset.caption || img.alt;

        images.push({
            src: img.src.replace('w=800', 'w=1920'), // Load higher res for lightbox
            caption: caption,
            thumbnail: img.src
        });

        // Click to open lightbox
        item.addEventListener('click', () => {
            openLightbox(index);
        });
    });

    // Build thumbnails
    function buildThumbnails() {
        lightboxThumbnails.innerHTML = '';
        images.forEach((img, index) => {
            const thumb = document.createElement('img');
            thumb.src = img.thumbnail;
            thumb.classList.add('lightbox-thumbnail');
            thumb.addEventListener('click', () => {
                showImage(index);
            });
            lightboxThumbnails.appendChild(thumb);
        });
    }

    function openLightbox(index) {
        currentImageIndex = index;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
        buildThumbnails();
        showImage(index);
        if (typeof trackEvent === 'function') {
            trackEvent('open', 'engagement', 'gallery_lightbox', 1);
        }
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function showImage(index) {
        currentImageIndex = index;
        const img = images[index];
        lightboxImage.src = img.src;
        lightboxImage.alt = img.caption;
        lightboxCaption.textContent = img.caption;

        // Update thumbnail active state
        document.querySelectorAll('.lightbox-thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        showImage(currentImageIndex);
    }

    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        showImage(currentImageIndex);
    }

    // Event listeners
    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxNext) lightboxNext.addEventListener('click', nextImage);
    if (lightboxPrev) lightboxPrev.addEventListener('click', prevImage);

    // Click outside image to close
    if (lightbox) {
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (lightbox && lightbox.classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        }
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    if (lightbox) {
        lightbox.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        lightbox.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
    }

    function handleSwipe() {
        if (touchEndX < touchStartX - 50) nextImage(); // Swipe left
        if (touchEndX > touchStartX + 50) prevImage(); // Swipe right
    }

    // =============================================================================
    // VIRTUAL TOUR NAVIGATION
    // =============================================================================

    const tourLocationCards = document.querySelectorAll('.tour-location-card');
    const streetviewFrame = document.getElementById('streetviewFrame');
    const currentLocationName = document.getElementById('currentLocationName');
    const fullscreenBtn = document.getElementById('fullscreenBtn');

    const streetviewUrls = {
        'big-ben': 'https://www.google.com/maps/embed?pb=!4v1234567890!6m8!1m7!1sCAoSLEFGMVFpcE5fRXBsYjNsSmNhMGdXVV9oSzZYYWVsQnJfR3A5bXRHUWtqbnZW!2m2!1d51.5007292!2d-0.1246254!3f0!4f0!5f0.7820865974627469',
        'tower-bridge': 'https://www.google.com/maps/embed?pb=!4v1234567891!6m8!1m7!1sCAoSLEFGMVFpcFBYdTN1emdGdWlOaHljMlZucXJNS21JVVRvRVp5VEdhS0ZYR2V1!2m2!1d51.5055!2d-0.0754!3f0!4f0!5f0.7820865974627469',
        'buckingham': 'https://www.google.com/maps/embed?pb=!4v1234567892!6m8!1m7!1sCAoSLEFGMVFpcE5IOUhxUHBCY1dzR1ViX0RDVlVnczBxd0FDQWNrRl9SN1lrT0Z3!2m2!1d51.5014!2d-0.1419!3f0!4f0!5f0.7820865974627469',
        'notting-hill': 'https://www.google.com/maps/embed?pb=!4v1234567893!6m8!1m7!1sCAoSLEFGMVFpcE9DcVB5Rlh1RWY1R1p2WmJqN1JCX1FYX1JMeVROV2JtVElUa3dp!2m2!1d51.5158!2d-0.2058!3f0!4f0!5f0.7820865974627469'
    };

    tourLocationCards.forEach(card => {
        card.addEventListener('click', function() {
            const location = this.dataset.location;
            const locationTitle = this.querySelector('h3').textContent;

            // Update active state
            tourLocationCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            // Update iframe src
            if (streetviewFrame && streetviewUrls[location]) {
                streetviewFrame.src = streetviewUrls[location];
            }

            // Update location name
            if (currentLocationName) {
                currentLocationName.textContent = locationTitle;
            }

            // Track location change
            if (typeof trackEvent === 'function') {
                trackEvent('click', 'engagement', `virtual_tour_${location}`, 1);
            }
        });
    });

    // Fullscreen button
    if (fullscreenBtn && streetviewFrame) {
        fullscreenBtn.addEventListener('click', function() {
            if (streetviewFrame.requestFullscreen) {
                streetviewFrame.requestFullscreen();
            } else if (streetviewFrame.webkitRequestFullscreen) {
                streetviewFrame.webkitRequestFullscreen();
            } else if (streetviewFrame.msRequestFullscreen) {
                streetviewFrame.msRequestFullscreen();
            }

            if (typeof trackEvent === 'function') {
                trackEvent('click', 'engagement', 'virtual_tour_fullscreen', 1);
            }
        });
    }

    // =============================================================================
    // BLOG SEARCH AND FILTER
    // =============================================================================

    const blogSearch = document.getElementById('blogSearch');
    const categoryTags = document.querySelectorAll('.category-tag');
    const blogPosts = document.querySelectorAll('.blog-post-card');

    let currentCategory = 'all';

    // Category filter
    categoryTags.forEach(tag => {
        tag.addEventListener('click', function() {
            categoryTags.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category;
            filterPosts();
        });
    });

    // Search filter
    if (blogSearch) {
        blogSearch.addEventListener('input', function() {
            filterPosts();
        });
    }

    function filterPosts() {
        const searchTerm = blogSearch ? blogSearch.value.toLowerCase() : '';

        blogPosts.forEach(post => {
            const postCategory = post.dataset.category;
            const postTitle = post.querySelector('h3').textContent.toLowerCase();

            const matchesCategory = currentCategory === 'all' || postCategory === currentCategory;
            const matchesSearch = postTitle.includes(searchTerm);

            if (matchesCategory && matchesSearch) {
                post.style.display = '';
            } else {
                post.style.display = 'none';
            }
        });
    }

    // =============================================================================
    // TESTIMONIALS CAROUSEL - GRID-BASED WITH TRANSFORM ANIMATION
    // =============================================================================

    const testimonialsTrack = document.getElementById('testimonialsTrack');
    const carouselPrev = document.getElementById('carouselPrev');
    const carouselNext = document.getElementById('carouselNext');
    const carouselDotsContainer = document.getElementById('carouselDots');

    if (testimonialsTrack && testimonialsTrack.dataset.mode !== 'static') {
        const testimonials = testimonialsTrack.querySelectorAll('.testimonial-card');
        const VISIBLE_CARDS = 4;
        let currentPosition = 0;
        let carouselInterval;
        const maxSlides = Math.max(1, testimonials.length - VISIBLE_CARDS + 1);

        // Criar array de slides (grupos de 4 cards)
        const slides = [];
        for (let i = 0; i < testimonials.length; i += VISIBLE_CARDS) {
            const slideCards = Array.from(testimonials).slice(i, i + VISIBLE_CARDS);
            if (slideCards.length > 0) {
                slides.push(slideCards);
            }
        }

        // Se temos menos de 4 cards, não carousel
        if (testimonials.length < VISIBLE_CARDS) {
            carouselDotsContainer.style.display = 'none';
            carouselPrev.style.display = 'none';
            carouselNext.style.display = 'none';
            return;
        }

        // Create dots (um por slide)
        const totalDots = slides.length;
        for (let i = 0; i < totalDots; i++) {
            const dot = document.createElement('button');
            dot.classList.add('carousel-dot');
            dot.setAttribute('aria-label', `Slide ${i + 1} de ${totalDots}`);
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToPosition(i);
                resetCarouselInterval();
            });
            carouselDotsContainer.appendChild(dot);
        }

        const dots = carouselDotsContainer.querySelectorAll('.carousel-dot');

        function goToPosition(position) {
            currentPosition = Math.max(0, Math.min(position, slides.length - 1));
            const translateValue = -currentPosition * 100;
            testimonialsTrack.style.transform = `translateX(${translateValue}%)`;
            
            // Update dots
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentPosition);
            });
        }

        function nextSlide() {
            currentPosition = (currentPosition + 1) % slides.length;
            goToPosition(currentPosition);
        }

        function prevSlide() {
            currentPosition = (currentPosition - 1 + slides.length) % slides.length;
            goToPosition(currentPosition);
        }

        function startCarousel() {
            carouselInterval = setInterval(nextSlide, 6000);
        }

        function stopCarousel() {
            clearInterval(carouselInterval);
        }

        function resetCarouselInterval() {
            stopCarousel();
            startCarousel();
        }

        // Add transition to track
        testimonialsTrack.style.transition = 'transform 0.5s ease-in-out';

        // Event listeners
        if (carouselNext) {
            carouselNext.addEventListener('click', () => {
                nextSlide();
                resetCarouselInterval();
            });
        }

        if (carouselPrev) {
            carouselPrev.addEventListener('click', () => {
                prevSlide();
                resetCarouselInterval();
            });
        }

        // Pause on hover
        testimonialsTrack.addEventListener('mouseenter', stopCarousel);
        testimonialsTrack.addEventListener('mouseleave', startCarousel);

        // Start auto-play
        startCarousel();
    }

    // =============================================================================
    // HORIZONTAL TESTIMONIALS CAROUSEL (NEW DESIGN)
    // =============================================================================

    const horizontalCarousel = document.querySelector('.testimonials-carousel-wrapper');

    if (horizontalCarousel) {
        const track = horizontalCarousel.querySelector('.testimonials-track');
        const prevBtn = horizontalCarousel.querySelector('.testimonials-prev');
        const nextBtn = horizontalCarousel.querySelector('.testimonials-next');

        if (track && prevBtn && nextBtn) {
            const cards = track.querySelectorAll('.testimonial-card-horizontal');
            const VISIBLE_CARDS = 3;
            let currentIndex = 0;

            // Calculate total pages
            const totalPages = Math.ceil(cards.length / VISIBLE_CARDS);

            function updateCarousel() {
                // Calculate transform value
                const cardWidth = cards[0].offsetWidth;
                const gap = 16; // var(--spacing-md)
                const offset = currentIndex * VISIBLE_CARDS * (cardWidth + gap);

                track.style.transform = `translateX(-${offset}px)`;
                track.style.transition = 'transform 0.5s ease-in-out';

                // Update button states
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex >= totalPages - 1;
            }

            function goNext() {
                if (currentIndex < totalPages - 1) {
                    currentIndex++;
                    updateCarousel();
                }
            }

            function goPrev() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            }

            // Event listeners
            nextBtn.addEventListener('click', goNext);
            prevBtn.addEventListener('click', goPrev);

            // Keyboard navigation
            horizontalCarousel.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    goPrev();
                } else if (e.key === 'ArrowRight') {
                    goNext();
                }
            });

            // Initialize
            updateCarousel();

            // Update on window resize
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    updateCarousel();
                }, 250);
            });
        }
    }

    // =============================================================================
    // NEWSLETTER POPUP WITH EXIT-INTENT
    // =============================================================================

    const newsletterPopup = document.getElementById('newsletterPopup');
    const newsletterClose = document.getElementById('newsletterClose');
    const newsletterForm = document.getElementById('newsletterForm');
    const newsletterName = document.getElementById('newsletterName');
    const newsletterEmail = document.getElementById('newsletterEmail');

    let newsletterShown = false;
    let exitIntentTriggered = false;

    // Check if newsletter was already shown in this session
    const newsletterDismissed = sessionStorage.getItem('newsletter_dismissed');

    function showNewsletter() {
        if (!newsletterShown && !newsletterDismissed) {
            newsletterPopup.classList.add('active');
            document.body.style.overflow = 'hidden';
            newsletterShown = true;

            if (typeof trackEvent === 'function') {
                trackEvent('show', 'engagement', 'newsletter_popup', 1);
            }
        }
    }

    function hideNewsletter() {
        newsletterPopup.classList.remove('active');
        document.body.style.overflow = '';
        sessionStorage.setItem('newsletter_dismissed', 'true');
    }

    // Exit-intent detection ONLY (removed aggressive 30s timer)
    document.addEventListener('mouseleave', (e) => {
        if (e.clientY <= 0 && !exitIntentTriggered && !newsletterDismissed) {
            exitIntentTriggered = true;
            setTimeout(showNewsletter, 500);
        }
    });

    // Optional: Show after significant engagement (60s + scroll > 50%)
    let engagementTimer = null;
    let hasScrolledHalfway = false;

    window.addEventListener('scroll', () => {
        const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        if (scrollPercent > 50) {
            hasScrolledHalfway = true;
        }
    });

    // Only show after 60s IF user has scrolled significantly (shows real interest)
    engagementTimer = setTimeout(() => {
        if (!newsletterShown && !exitIntentTriggered && !newsletterDismissed && hasScrolledHalfway) {
            showNewsletter();
        }
    }, 60000);

    // Close button
    if (newsletterClose) {
        newsletterClose.addEventListener('click', hideNewsletter);
    }

    // Click outside to close
    if (newsletterPopup) {
        newsletterPopup.addEventListener('click', (e) => {
            if (e.target === newsletterPopup) {
                hideNewsletter();
            }
        });
    }

    // Form submission
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = newsletterName.value;
            const email = newsletterEmail.value;

            // Track newsletter signup
            if (typeof trackEvent === 'function') {
                trackEvent('submit', 'conversion', 'newsletter_signup', 1);
            }

            // Send to WhatsApp or email service
            const message = `🎉 Nova inscrição na Newsletter!\n\n` +
                `👤 Nome: ${name}\n` +
                `📧 Email: ${email}\n\n` +
                `Solicitou cupom de 10% OFF`;

            const whatsappUrl = `https://wa.me/447950400919?text=${encodeURIComponent(message)}`;

            // Show success message
            alert('✅ Inscrito com sucesso! Você receberá seu cupom de 10% OFF por email em breve!');

            hideNewsletter();

            // Optional: Open WhatsApp
            window.open(whatsappUrl, '_blank');
        });
    }

    // =============================================================================
    // ENHANCED CONTACT/NEWSLETTER FORM
    // =============================================================================

    const enhancedContactForms = document.querySelectorAll('.contact-form');

    enhancedContactForms.forEach(form => {
        const messageField = form.querySelector('#message');
        const messageCounter = form.querySelector('#messageCounter');
        const submitBtn = form.querySelector('.submit-btn');
        const btnText = submitBtn?.querySelector('.btn-text');
        const btnLoader = submitBtn?.querySelector('.btn-loader');
        const formSuccess = document.getElementById('formSuccess');
        const formError = document.getElementById('formError');

        // Character counter for textarea
        if (messageField && messageCounter) {
            messageField.addEventListener('input', function() {
                const currentLength = this.value.length;
                const maxLength = this.getAttribute('maxlength') || 1000;
                messageCounter.textContent = `${currentLength}/${maxLength}`;

                // Change color based on length
                messageCounter.classList.remove('warning', 'danger');
                if (currentLength > maxLength * 0.9) {
                    messageCounter.classList.add('danger');
                } else if (currentLength > maxLength * 0.75) {
                    messageCounter.classList.add('warning');
                }
            });
        }

        // Real-time validation
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                // Clear error on input
                if (this.classList.contains('error')) {
                    this.classList.remove('error');
                    const errorSpan = document.getElementById(`${this.id}Error`);
                    if (errorSpan) {
                        errorSpan.textContent = '';
                    }
                }
            });
        });

        function validateField(field) {
            const errorSpan = document.getElementById(`${field.id}Error`);
            let errorMessage = '';

            if (!field.value.trim()) {
                errorMessage = 'Este campo é obrigatório';
                field.classList.add('error');
            } else if (field.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    errorMessage = 'Por favor, insira um e-mail válido';
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            } else {
                field.classList.remove('error');
            }

            if (errorSpan) {
                errorSpan.textContent = errorMessage;
            }

            return !errorMessage;
        }

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate all fields
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                return;
            }

            // Show loading state
            if (submitBtn && btnText && btnLoader) {
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoader.style.display = 'flex';
            }

            // Hide previous messages
            if (formSuccess) formSuccess.style.display = 'none';
            if (formError) formError.style.display = 'none';

            // Submit form via fetch
            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    // Success
                    if (formSuccess) {
                        formSuccess.style.display = 'flex';
                        formSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    form.reset();
                    if (messageCounter) {
                        messageCounter.textContent = '0/1000';
                        messageCounter.classList.remove('warning', 'danger');
                    }
                } else {
                    throw new Error('Form submission failed');
                }
            })
            .catch(error => {
                // Error
                if (formError) {
                    formError.style.display = 'flex';
                    formError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .finally(() => {
                // Reset button state
                if (submitBtn && btnText && btnLoader) {
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoader.style.display = 'none';
                }
            });
        });
    });

    // Smooth scroll para links âncora
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href && href !== '#') {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    // Fechar menu mobile se estiver aberto
                    if (mobileMenuOpen) {
                        closeMobileMenu();
                    }
                    // Scroll suave
                    const headerHeight = document.querySelector('.header')?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - headerHeight - 20;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // =============================================================================
    // TRANSIÇÕES SUAVES ENTRE PÁGINAS
    // =============================================================================

    const pageTransitionOverlay = document.getElementById('pageTransitionOverlay');

    // Fade-out ao sair da página (ao carregar)
    if (pageTransitionOverlay) {
        // Remove overlay ao carregar página
        setTimeout(() => {
            pageTransitionOverlay.classList.remove('active');
        }, 50);
    }

    // Interceptar todos os links internos e aplicar transição antes da navegação
    document.querySelectorAll('a').forEach(link => {
        // Pular links âncora, externos, e modais
        const href = link.getAttribute('href');
        if (!href ||
            href.startsWith('#') ||
            href.startsWith('http') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:') ||
            link.target === '_blank') {
            return;
        }

        link.addEventListener('click', (e) => {
            e.preventDefault();
            const destination = href;

            // Mostrar overlay
            if (pageTransitionOverlay) {
                pageTransitionOverlay.classList.add('active');
            }

            // Navegar após a animação
            setTimeout(() => {
                window.location.href = destination;
            }, 400);
        });
    });

    // Handle browser back/forward buttons
    window.addEventListener('pageshow', (event) => {
        if (event.persisted && pageTransitionOverlay) {
            pageTransitionOverlay.classList.remove('active');
        }
    });
});
