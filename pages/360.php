<?php include '../includes/header.php'; ?>

    <section class="section bg-pattern">
        <div class="container">
            <nav class="breadcrumbs" aria-label="Navegação estrutural">
                <ol class="breadcrumbs-list">
                    <li class="breadcrumbs-item">
                        <a href="<?php echo $base_path; ?>/index.php" class="breadcrumbs-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                            Início
                        </a>
                    </li>
                    <li class="breadcrumbs-item">
                        <a href="<?php echo $base_path; ?>/pages/experience.php" class="breadcrumbs-link">Experience</a>
                    </li>
                    <li class="breadcrumbs-item breadcrumbs-item--active" aria-current="page">
                        Tour Virtual 360°
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Seção Virtual Tour Completa -->
    <section class="virtual-tour-section fade-in" aria-labelledby="virtual-tour-heading">
        <div class="container">
            <div class="virtual-tour-header">
                <span class="virtual-tour-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    PREVIEW 360°
                </span>
                <h1 id="virtual-tour-heading" class="section-title">Explore Londres em 360°</h1>
                <p class="section-subtitle">Navegue pelos principais pontos turísticos e planeje seu tour perfeito</p>
            </div>

            <div class="tour-locations fade-in-stagger">
                <!-- Big Ben -->
                <div class="tour-location-card active" data-location="big-ben" data-coords="51.5007,-0.1246">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <h3>Big Ben & Parlamento</h3>
                    <p>Ícone mais famoso de Londres</p>
                    <span class="tour-location-badge">Tour Clássico</span>
                </div>

                <!-- Tower Bridge -->
                <div class="tour-location-card" data-location="tower-bridge" data-coords="51.5055,-0.0754">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                        </svg>
                    </div>
                    <h3>Tower Bridge</h3>
                    <p>Ponte icônica vitoriana</p>
                    <span class="tour-location-badge">Tour Histórico</span>
                </div>

                <!-- Buckingham Palace -->
                <div class="tour-location-card" data-location="buckingham" data-coords="51.5014,-0.1419">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3>Buckingham Palace</h3>
                    <p>Residência da monarquia</p>
                    <span class="tour-location-badge">Tour Clássico</span>
                </div>

                <!-- Notting Hill -->
                <div class="tour-location-card" data-location="notting-hill" data-coords="51.5158,-0.2058">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="11" cy="11" r="2"></circle>
                        </svg>
                    </div>
                    <h3>Notting Hill</h3>
                    <p>Casas coloridas charmosas</p>
                    <span class="tour-location-badge">Tour Fotográfico</span>
                </div>

                <!-- London Eye -->
                <div class="tour-location-card" data-location="london-eye" data-coords="51.5033,-0.1196">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"></circle>
                        </svg>
                    </div>
                    <h3>London Eye</h3>
                    <p>Roda gigante mais famosa</p>
                    <span class="tour-location-badge">Tour Clássico</span>
                </div>

                <!-- Camden Market -->
                <div class="tour-location-card" data-location="camden" data-coords="51.5413,-0.1460">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <h3>Camden Market</h3>
                    <p>Mercado alternativo vibrante</p>
                    <span class="tour-location-badge">Tour Cultural</span>
                </div>

                <!-- Trafalgar Square -->
                <div class="tour-location-card" data-location="trafalgar" data-coords="51.5081,-0.1280">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                        </svg>
                    </div>
                    <h3>Trafalgar Square</h3>
                    <p>Praça histórica central</p>
                    <span class="tour-location-badge">Tour Histórico</span>
                </div>

                <!-- Covent Garden -->
                <div class="tour-location-card" data-location="covent-garden" data-coords="51.5119,-0.1235">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        </svg>
                    </div>
                    <h3>Covent Garden</h3>
                    <p>Mercado coberto elegante</p>
                    <span class="tour-location-badge">Tour Cultural</span>
                </div>

                <!-- St Paul's Cathedral -->
                <div class="tour-location-card" data-location="st-pauls" data-coords="51.5138,-0.0984">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <h3>St Paul's Cathedral</h3>
                    <p>Catedral barroca imponente</p>
                    <span class="tour-location-badge">Tour Histórico</span>
                </div>

                <!-- Borough Market -->
                <div class="tour-location-card" data-location="borough" data-coords="51.5055,-0.0910">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <h3>Borough Market</h3>
                    <p>Mercado gastronômico histórico</p>
                    <span class="tour-location-badge">Tour Gastronômico</span>
                </div>

                <!-- The Shard -->
                <div class="tour-location-card" data-location="shard" data-coords="51.5045,-0.0865">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <polygon points="12 2 2 22 22 22"></polygon>
                        </svg>
                    </div>
                    <h3>The Shard</h3>
                    <p>Arranha-céu moderno icônico</p>
                    <span class="tour-location-badge">Tour Moderno</span>
                </div>

                <!-- Hyde Park -->
                <div class="tour-location-card" data-location="hyde-park" data-coords="51.5074,-0.1657">
                    <div class="tour-location-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                        </svg>
                    </div>
                    <h3>Hyde Park</h3>
                    <p>Parque real expansivo</p>
                    <span class="tour-location-badge">Tour Natural</span>
                </div>
            </div>

            <div class="streetview-container">
                <iframe
                    id="streetviewFrame"
                    class="streetview-iframe"
                    src="https://www.google.com/maps/embed?pb=!4v1234567890!6m8!1m7!1sCAoSLEFGMVFpcE5fRXBsYjNsSmNhMGdXVV9oSzZYYWVsQnJfR3A5bXRHUWtqbnZW!2m2!1d51.5007292!2d-0.1246254!3f0!4f0!5f0.7820865974627469"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                <div class="streetview-controls">
                    <span class="streetview-location-name" id="currentLocationName">Big Ben & Parlamento</span>
                    <button class="streetview-btn" id="fullscreenBtn">Tela Cheia</button>
                </div>
            </div>

            <div style="text-align: center; margin-top: var(--spacing-2xl);">
                <a href="<?php echo $base_path; ?>/pages/experience.php" class="btn-outline">Voltar para Experience</a>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>
