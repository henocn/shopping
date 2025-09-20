class TrackingManager {
    constructor(config = {}) {
        this.config = {
            facebook: {
                enabled: true,
                pixels: ['1087210050149446'],
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
            if (!window.fbq) {
                window.fbq = function() {
                    window.fbq.queue = window.fbq.queue || [];
                    window.fbq.queue.push(arguments);
                };
                window.fbq.queue = [];
                window.fbq.loaded = true;
                window.fbq.version = '2.0';
            }
            this.config.facebook.pixels.forEach(pixelId => fbq('init', pixelId));
            fbq('track', 'PageView');
            this.addFacebookFallbackImages();
            this.loadFacebookScript();
            this.scriptsLoaded.facebook = true;
        } catch (error) {
            this.addFacebookFallbackImages();
        }
    }

    loadFacebookScript() {
        try {
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
            if (typeof fbq === 'function') {
                const standardEvents = ['Purchase', 'Lead', 'InitiateCheckout', 'ViewContent', 'CompleteRegistration'];
                if (standardEvents.includes(eventName)) {
                    fbq('track', eventName, eventData);
                } else {
                    fbq('trackCustom', eventName, eventData);
                }
            }
            this.trackFacebookViaImage(eventName, eventData);
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
            if (typeof fbq === 'function') fbq('init', pixelId);
        }
    }

    removeFacebookPixel(pixelId) {
        const index = this.config.facebook.pixels.indexOf(pixelId);
        if (index > -1) this.config.facebook.pixels.splice(index, 1);
    }
}

window.trackingManager = new TrackingManager();
window.trackEvent = function(eventName, eventData = {}, platforms = ['facebook']) {
    window.trackingManager.track(eventName, eventData, platforms);
};
