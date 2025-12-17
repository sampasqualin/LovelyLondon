<?php
// mobile/includes/footer.php
?>
        </main>

        <footer class="app-footer">
            <p class="app-footer-copy">
                &copy; <?php echo date('Y'); ?> Lovely London by Carol
            </p>
        </footer>
    </div>

    <!-- Bottom navigation estilo app -->
    <nav class="bottom-nav">
        <a href="<?php echo $mobile_base_path; ?>/index.php" class="bottom-nav-item <?php echo $mobile_current_page === 'index' ? 'active' : ''; ?>">
            <span class="bottom-nav-icon">
                <!-- Home icon -->
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 11l9-8 9 8v9a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-5H9v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
            </span>
            <span class="bottom-nav-label">Home</span>
        </a>
        <a href="<?php echo $mobile_base_path; ?>/pages/tours.php" class="bottom-nav-item <?php echo $mobile_current_page === 'tours' ? 'active' : ''; ?>">
            <span class="bottom-nav-icon">
                <!-- Map / tours icon -->
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 6l7-3 7 3 4-2v14l-7 3-7-3-4 2V4z"/>
                    <path d="M10 3v15"/>
                    <path d="M17 6v15"/>
                </svg>
            </span>
            <span class="bottom-nav-label">Tours</span>
        </a>
        <a href="<?php echo $mobile_base_path; ?>/pages/services.php" class="bottom-nav-item <?php echo $mobile_current_page === 'services' ? 'active' : ''; ?>">
            <span class="bottom-nav-icon">
                <!-- Services / suitcase icon -->
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="7" width="18" height="13" rx="2"/>
                    <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                </svg>
            </span>
            <span class="bottom-nav-label">Serviços</span>
        </a>
        <a href="<?php echo $mobile_base_path; ?>/pages/blog.php" class="bottom-nav-item <?php echo $mobile_current_page === 'blog' ? 'active' : ''; ?>">
            <span class="bottom-nav-icon">
                <!-- Blog / document icon -->
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 2h9l5 5v15H6z"/>
                    <path d="M15 2v5h5"/>
                    <path d="M9 13h6"/>
                    <path d="M9 17h6"/>
                </svg>
            </span>
            <span class="bottom-nav-label">Blog</span>
        </a>
        <a href="<?php echo $mobile_base_path; ?>/pages/contact.php" class="bottom-nav-item <?php echo $mobile_current_page === 'contact' ? 'active' : ''; ?>">
            <span class="bottom-nav-icon">
                <!-- Contact / mail icon -->
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M3 7l9 6 9-6"/>
                </svg>
            </span>
            <span class="bottom-nav-label">Contato</span>
        </a>
    </nav>

    <!-- Botão WhatsApp flutuante -->
    <a href="https://wa.me/447950400919?text=Ol%C3%A1!%20Gostaria%20de%20saber%20mais%20sobre%20os%20tours%20em%20Londres"
       class="whatsapp-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Fale conosco no WhatsApp">
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 0C7.164 0 0 7.164 0 16c0 2.825.74 5.478 2.028 7.772L0 32l8.448-2.016A15.928 15.928 0 0016 32c8.836 0 16-7.164 16-16S24.836 0 16 0zm0 29.332c-2.492 0-4.876-.672-6.92-1.932l-.496-.292-5.144 1.228 1.292-4.776-.328-.516A13.3 13.3 0 012.668 16c0-7.364 5.968-13.332 13.332-13.332S29.332 8.636 29.332 16 23.364 29.332 16 29.332zm7.308-9.964c-.4-.2-2.368-1.168-2.736-1.3-.368-.132-.636-.2-.904.2-.268.4-1.036 1.3-1.272 1.568-.236.268-.472.3-.872.1-.4-.2-1.688-.624-3.216-1.988-1.188-1.064-1.992-2.376-2.224-2.776-.232-.4-.024-.616.176-.816.18-.18.4-.472.6-.708.2-.236.268-.4.4-.668.132-.268.068-.5-.032-.7-.1-.2-.904-2.176-1.24-2.98-.328-.78-.66-.676-.904-.688-.232-.012-.5-.016-.768-.016s-.7.1-1.068.5c-.368.4-1.404 1.372-1.404 3.348s1.436 3.884 1.636 4.152c.2.268 2.824 4.312 6.84 6.048.956.412 1.7.66 2.284.844.96.304 1.832.26 2.52.156.768-.116 2.368-.968 2.7-1.904.332-.936.332-1.74.232-1.904-.1-.164-.368-.264-.768-.464z"/>
        </svg>
    </a>

    <script src="<?php echo $mobile_base_path; ?>/js/app.js"></script>
</body>
</html>
