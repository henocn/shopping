/**
 * GESTIONNAIRE DE TRACKING MULTI-PLATEFORMES
 * Permet d'ajouter facilement plusieurs pixels Facebook, Google Analytics, TikTok, etc.
 */

class TrackingManager {
    constructor() {
        this.pixels = [];
        this.isInitialized = false;
    }

    /**
     * Ajouter un nouveau pixel Facebook
     * @param {string} pixelId - ID du pixel Facebook
     * @param {string} name - Nom du pixel (pour identification)
     */
    addFacebookPixel(pixelId, name = 'default') {
        this.pixels.push({
            type: 'facebook',
            id: pixelId,
            name: name,
            initialized: false
        });
        return this;
    }

    /**
     * Ajouter Google Analytics
     * @param {string} trackingId - ID de tracking GA
     * @param {string} name - Nom du tracker
     */
    addGoogleAnalytics(trackingId, name = 'default') {
        this.pixels.push({
            type: 'google_analytics',
            id: trackingId,
            name: name,
            initialized: false
        });
        return this;
    }

    /**
     * Ajouter TikTok Pixel
     * @param {string} pixelId - ID du pixel TikTok
     * @param {string} name - Nom du pixel
     */
    addTikTokPixel(pixelId, name = 'default') {
        this.pixels.push({
            type: 'tiktok',
            id: pixelId,
            name: name,
            initialized: false
        });
        return this;
    }

    /**
     * Initialiser tous les pixels
     */
    initialize() {
        this.pixels.forEach(pixel => {
            switch (pixel.type) {
                case 'facebook':
                    this.initFacebookPixel(pixel);
                    break;
                case 'google_analytics':
                    this.initGoogleAnalytics(pixel);
                    break;
                case 'tiktok':
                    this.initTikTokPixel(pixel);
                    break;
            }
        });
        this.isInitialized = true;
        return this;
    }

    /**
     * Initialiser un pixel Facebook
     */
    initFacebookPixel(pixel) {
        if (pixel.initialized) return;

        // Créer une instance fbq pour ce pixel
        const fbqName = pixel.name === 'default' ? 'fbq' : `fbq_${pixel.name}`;
        
        if (!window[fbqName]) {
            !function(f,b,e,v,n,t,s) {
                if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)
            }(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        }

        // Initialiser le pixel
        fbq('init', pixel.id);
        fbq('track', 'PageView');

        pixel.initialized = true;
        console.log(`Facebook Pixel ${pixel.name} (${pixel.id}) initialisé`);
    }

    /**
     * Initialiser Google Analytics
     */
    initGoogleAnalytics(pixel) {
        if (pixel.initialized) return;

        // Charger gtag
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${pixel.id}`;
        document.head.appendChild(script);

        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', pixel.id);

        pixel.initialized = true;
        console.log(`Google Analytics ${pixel.name} (${pixel.id}) initialisé`);
    }

    /**
     * Initialiser TikTok Pixel
     */
    initTikTokPixel(pixel) {
        if (pixel.initialized) return;

        !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
        }(window, document, 'ttq');

        ttq.load(pixel.id);
        ttq.page();

        pixel.initialized = true;
        console.log(`TikTok Pixel ${pixel.name} (${pixel.id}) initialisé`);
    }

    /**
     * Envoyer un événement à tous les pixels configurés
     */
    track(eventName, eventData = {}) {
        if (!this.isInitialized) {
            console.warn('TrackingManager pas encore initialisé');
            return;
        }

        this.pixels.forEach(pixel => {
            if (!pixel.initialized) return;

            switch (pixel.type) {
                case 'facebook':
                    this.trackFacebook(eventName, eventData, pixel);
                    break;
                case 'google_analytics':
                    this.trackGoogleAnalytics(eventName, eventData, pixel);
                    break;
                case 'tiktok':
                    this.trackTikTok(eventName, eventData, pixel);
                    break;
            }
        });
    }

    /**
     * Tracking Facebook
     */
    trackFacebook(eventName, eventData, pixel) {
        const standardEvents = ['Purchase', 'Lead', 'InitiateCheckout', 'AddToCart', 'ViewContent'];
        
        if (standardEvents.includes(eventName)) {
            fbq('track', eventName, eventData);
        } else {
            fbq('trackCustom', eventName, eventData);
        }
    }

    /**
     * Tracking Google Analytics
     */
    trackGoogleAnalytics(eventName, eventData, pixel) {
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                ...eventData,
                send_to: pixel.id
            });
        }
    }

    /**
     * Tracking TikTok
     */
    trackTikTok(eventName, eventData, pixel) {
        if (typeof ttq !== 'undefined') {
            const tiktokEvents = ['ClickButton', 'Contact', 'SubmitForm', 'CompleteRegistration', 'CompletePayment'];
            
            if (tiktokEvents.includes(eventName)) {
                ttq.track(eventName, eventData);
            } else {
                // Événement personnalisé
                ttq.track('ClickButton', {
                    ...eventData,
                    custom_event: eventName
                });
            }
        }
    }

    /**
     * Obtenir la liste des pixels configurés
     */
    getPixels() {
        return this.pixels;
    }

    /**
     * Supprimer un pixel
     */
    removePixel(name) {
        this.pixels = this.pixels.filter(pixel => pixel.name !== name);
        return this;
    }
}

// Instance globale
window.trackingManager = new TrackingManager();

// Configuration par défaut - vous pouvez ajouter autant de pixels que vous voulez
window.trackingManager
    .addFacebookPixel('1087210050149446', 'main') // Votre pixel principal
    // .addFacebookPixel('123456789', 'secondary')    // Pixel secondaire
    // .addGoogleAnalytics('G-XXXXXXXXXX', 'main')   // Google Analytics
    // .addTikTokPixel('XXXXXXXXXX', 'main')         // TikTok Pixel
    .initialize();

// Fonction d'aide pour l'utilisation simple
window.trackEvent = function(eventName, eventData = {}) {
    window.trackingManager.track(eventName, eventData);
};

console.log('TrackingManager initialisé avec les pixels:', window.trackingManager.getPixels());