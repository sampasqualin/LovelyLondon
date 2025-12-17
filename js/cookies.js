// =============================================================================
// COOKIE CONSENT MANAGEMENT
// =============================================================================

(function() {
    'use strict';

    // Cookie utility functions
    const CookieManager = {
        set: function(name, value, days) {
            const date = new Date();
            date.setTime(date.setTime() + (days * 24 * 60 * 60 * 1000));
            const expires = "; expires=" + date.toUTCString();
            document.cookie = name + "=" + value + expires + "; path=/";
        },

        get: function(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },

        delete: function(name) {
            this.set(name, "", -1);
        }
    };

    // Initialize cookie consent
    function initCookieConsent() {
        const cookieConsent = document.getElementById('cookieConsent');
        const cookiePreferencesModal = document.getElementById('cookiePreferencesModal');
        const acceptAllBtn = document.getElementById('acceptAllCookies');
        const customizeBtn = document.getElementById('customizeCookies');
        const closePreferencesBtn = document.getElementById('closeCookiePreferences');
        const savePreferencesBtn = document.getElementById('savePreferences');
        const acceptAllFromModalBtn = document.getElementById('acceptAllFromModal');

        // Check if user has already made a choice
        const cookieChoice = CookieManager.get('cookie_consent');

        if (!cookieChoice && cookieConsent) {
            // Show cookie banner after 1 second
            setTimeout(() => {
                cookieConsent.classList.add('show');
            }, 1000);
        } else if (cookieChoice) {
            // Load scripts based on saved preferences
            loadScriptsBasedOnConsent(JSON.parse(cookieChoice));
        }

        // Accept all cookies
        if (acceptAllBtn) {
            acceptAllBtn.addEventListener('click', function() {
                const preferences = {
                    necessary: true,
                    analytics: true,
                    marketing: true
                };
                saveConsent(preferences);
                cookieConsent.classList.remove('show');
                loadScriptsBasedOnConsent(preferences);
            });
        }

        // Open preferences modal
        if (customizeBtn) {
            customizeBtn.addEventListener('click', function() {
                cookiePreferencesModal.classList.add('show');
            });
        }

        // Close preferences modal
        if (closePreferencesBtn) {
            closePreferencesBtn.addEventListener('click', function() {
                cookiePreferencesModal.classList.remove('show');
            });
        }

        // Accept all from modal
        if (acceptAllFromModalBtn) {
            acceptAllFromModalBtn.addEventListener('click', function() {
                const preferences = {
                    necessary: true,
                    analytics: true,
                    marketing: true
                };
                saveConsent(preferences);
                cookieConsent.classList.remove('show');
                cookiePreferencesModal.classList.remove('show');
                loadScriptsBasedOnConsent(preferences);
            });
        }

        // Save custom preferences
        if (savePreferencesBtn) {
            savePreferencesBtn.addEventListener('click', function() {
                const analyticsCheckbox = document.getElementById('analyticsCookies');
                const marketingCheckbox = document.getElementById('marketingCookies');

                const preferences = {
                    necessary: true,
                    analytics: analyticsCheckbox ? analyticsCheckbox.checked : false,
                    marketing: marketingCheckbox ? marketingCheckbox.checked : false
                };

                saveConsent(preferences);
                cookieConsent.classList.remove('show');
                cookiePreferencesModal.classList.remove('show');
                loadScriptsBasedOnConsent(preferences);
            });
        }

        // Close modal when clicking outside
        if (cookiePreferencesModal) {
            cookiePreferencesModal.addEventListener('click', function(e) {
                if (e.target === cookiePreferencesModal) {
                    cookiePreferencesModal.classList.remove('show');
                }
            });
        }
    }

    // Save consent preferences
    function saveConsent(preferences) {
        CookieManager.set('cookie_consent', JSON.stringify(preferences), 365);
    }

    // Load scripts based on consent
    function loadScriptsBasedOnConsent(preferences) {
        // Load Google Analytics if analytics cookies are accepted
        if (preferences.analytics) {
            loadGoogleAnalytics();
            loadMicrosoftClarity();
        }

        // Load marketing scripts if marketing cookies are accepted
        if (preferences.marketing) {
            loadMarketingScripts();
        }
    }

    // Load Google Analytics 4
    function loadGoogleAnalytics() {
        // Check if GA is already loaded
        if (window.gtag) return;

        // Replace 'G-XXXXXXXXXX' with your actual GA4 measurement ID
        const GA_MEASUREMENT_ID = 'G-XXXXXXXXXX'; // TODO: Replace with actual ID

        // Create script element
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${GA_MEASUREMENT_ID}`;
        document.head.appendChild(script);

        // Initialize gtag
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        window.gtag = gtag;
        gtag('js', new Date());
        gtag('config', GA_MEASUREMENT_ID, {
            'anonymize_ip': true
        });

        console.log('Google Analytics loaded');
    }

    // Load Microsoft Clarity
    function loadMicrosoftClarity() {
        // Check if Clarity is already loaded
        if (window.clarity) return;

        // Replace 'XXXXXXXXXX' with your actual Clarity project ID
        const CLARITY_PROJECT_ID = 'XXXXXXXXXX'; // TODO: Replace with actual ID

        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", CLARITY_PROJECT_ID);

        console.log('Microsoft Clarity loaded');
    }

    // Load marketing scripts (Facebook Pixel, etc.)
    function loadMarketingScripts() {
        // Add Facebook Pixel, Google Ads, etc. here if needed
        console.log('Marketing scripts loaded');
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCookieConsent);
    } else {
        initCookieConsent();
    }
})();
