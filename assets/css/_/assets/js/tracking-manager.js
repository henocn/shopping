class TrackingManager {
    constructor(config = {}) {
        this.config = {
            facebook: {
                enabled: true,
                pixels: ['1087210050149446','1373481401089526'],
                timeout: 5000
            },
            googleAnalytics: {
                enabled: false,
                trackingId: null
            },
            tiktok: {
                enabled: false,
                pixelId: null
            },
            debug: false,
            ...config
        };
        this.isReady = false;
        this.eventQueue = [];
        this.scriptsLoaded = { facebook: false, googleAnalytics: false, tiktok: false };
        this.init();
    }

    async init() {
        try {
            if (this.config.facebook.enabled) await this.initFacebook();
            if (this.config.googleAnalytics.enabled) await this.initGoogleAnalytics();
            if (this.config.tiktok.enabled) await this.initTikTok();
            this.isReady = true;
            this.processQueue();
        } catch (error) {
            this.isReady = true;
            this.processQueue();
        }
    }

    async initFacebook() {
        try {
            // Éviter la double initialisation
            if (window.fbEventsInitialized) {
                return;
            }
            window.fbEventsInitialized = true;

            // Snippet officiel Meta Pixel pour éviter les conflits de versions
            !(function(f,b,e,v,n,t,s){
                if(f.fbq) return; n=f.fbq=function(){ n.callMethod ?
                    n.callMethod.apply(n,arguments) : n.queue.push(arguments) };
                if(!f._fbq) f._fbq=n; n.push=n; n.loaded=!0; n.version='2.0';
                n.queue=[]; t=b.createElement(e); t.async=!0; t.src=v;
                s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s);
            })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

            // Alias de compatibilité
            window._fbq = window._fbq || window.fbq;

            // Initialiser seulement les pixels non initialisés
            if (!window.fbInitializedPixels) window.fbInitializedPixels = [];
            this.config.facebook.pixels.forEach(pixelId => {
                if (!window.fbInitializedPixels.includes(pixelId)) {
                    fbq('init', pixelId);
                    window.fbInitializedPixels.push(pixelId);
                }
            });

            fbq('track', 'PageView');
            this.scriptsLoaded.facebook = true;
        } catch (error) {
            // Pas de fallback immédiat ici; les événements utiliseront l'image si fbq indisponible
        }
    }

    loadFacebookScript() {
        try {
            // Éviter de charger le script plusieurs fois
            if (document.querySelector('script[src*="fbevents.js"]') || window.fbScriptLoaded) {
                return;
            }
            window.fbScriptLoaded = true;

            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://connect.facebook.net/en_US/fbevents.js';
            const timeout = setTimeout(() => {}, this.config.facebook.timeout);
            script.onload = () => clearTimeout(timeout);
            script.onerror = () => clearTimeout(timeout);
            const firstScript = document.getElementsByTagName('script')[0];
            if (firstScript && firstScript.parentNode) {
                firstScript.parentNode.insertBefore(script, firstScript);
            }
        } catch (error) {}
    }

    addFacebookFallbackImages() {
        this.config.facebook.pixels.forEach(pixelId => {
            const noscript = document.createElement('noscript');
            noscript.innerHTML = '<img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' + pixelId + '&ev=PageView&noscript=1" />';
            document.head.appendChild(noscript);
        });
    }

    track(eventName, eventData = {}, platforms = ['facebook']) {
        if (!this.isReady) {
            this.eventQueue.push({ eventName, eventData, platforms });
            return;
        }
        platforms.forEach(platform => {
            try {
                switch (platform) {
                    case 'facebook': this.trackFacebook(eventName, eventData); break;
                    case 'googleAnalytics': this.trackGoogleAnalytics(eventName, eventData); break;
                    case 'tiktok': this.trackTikTok(eventName, eventData); break;
                }
            } catch (error) {}
        });
    }

    trackFacebook(eventName, eventData) {
        if (!this.config.facebook.enabled) return;
        try {
            if (typeof fbq === 'function' && fbq.callMethod) {
                const standardEvents = ['Purchase', 'Lead', 'InitiateCheckout', 'ViewContent', 'CompleteRegistration'];
                if (standardEvents.includes(eventName)) {
                    fbq('track', eventName, eventData);
                } else {
                    fbq('trackCustom', eventName, eventData);
                }
            } else {
                // Fallback via image uniquement si fbq indisponible
                this.trackFacebookViaImage(eventName, eventData);
            }
        } catch (error) {
            this.trackFacebookViaImage(eventName, eventData);
        }
    }

    trackFacebookViaImage(eventName, eventData) {
        this.config.facebook.pixels.forEach(pixelId => {
            try {
                const params = new URLSearchParams({
                    id: pixelId,
                    ev: eventName,
                    noscript: '1',
                    t: Date.now()
                });
                if (eventData.value) params.append('cd[value]', eventData.value);
                if (eventData.currency) params.append('cd[currency]', eventData.currency);
                if (eventData.content_ids) params.append('cd[content_ids]', JSON.stringify(eventData.content_ids));
                const img = new Image();
                img.src = 'https://www.facebook.com/tr?' + params.toString();
            } catch (error) {}
        });
    }

    processQueue() {
        while (this.eventQueue.length > 0) {
            const event = this.eventQueue.shift();
            this.track(event.eventName, event.eventData, event.platforms);
        }
    }

    addFacebookPixel(pixelId) {
        if (!this.config.facebook.pixels.includes(pixelId)) {
            this.config.facebook.pixels.push(pixelId);
            
            // Initialiser seulement si pas déjà fait
            if (typeof fbq === 'function') {
                if (!window.fbInitializedPixels) {
                    window.fbInitializedPixels = [];
                }
                if (!window.fbInitializedPixels.includes(pixelId)) {
                    fbq('init', pixelId);
                    window.fbInitializedPixels.push(pixelId);
                }
            }
        }
    }

    removeFacebookPixel(pixelId) {
        const index = this.config.facebook.pixels.indexOf(pixelId);
        if (index > -1) this.config.facebook.pixels.splice(index, 1);
    }
}

// Créer l'instance TrackingManager seulement si elle n'existe pas déjà
if (!window.trackingManager) {
    window.trackingManager = new TrackingManager();
}

// Fonction globale pour tracking
if (!window.trackEvent) {
    window.trackEvent = function(eventName, eventData = {}, platforms = ['facebook']) {
        if (window.trackingManager) {
            window.trackingManager.track(eventName, eventData, platforms);
        }
    };
}
