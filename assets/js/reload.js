(function () {
      const RELOAD_DELAY = 120000; // 2 minutes
      let reloadTimeoutId;
      let inflightRequests = 0;

      const safeDecrease = () => {
            inflightRequests = Math.max(0, inflightRequests - 1);
      };

      const scheduleReload = () => {
            clearTimeout(reloadTimeoutId);
            reloadTimeoutId = setTimeout(() => {
                  if (inflightRequests === 0 && !document.hidden) {
                        window.location.reload();
                  } else {
                        scheduleReload();
                  }
            }, RELOAD_DELAY);
      };

      const markActivity = () => {
            scheduleReload();
      };

      const activityEvents = ['click', 'keydown', 'mousemove', 'scroll', 'touchstart', 'touchmove', 'focus', 'visibilitychange'];
      activityEvents.forEach(eventName => {
            window.addEventListener(eventName, markActivity, { passive: true });
      });

      if (typeof window.fetch === 'function') {
            const originalFetch = window.fetch;
            window.fetch = function (...args) {
                  inflightRequests += 1;
                  scheduleReload();
                  return originalFetch.apply(this, args)
                        .finally(() => {
                              safeDecrease();
                              scheduleReload();
                        });
            };
      }

      if (typeof window.XMLHttpRequest === 'function') {
            const OriginalXHR = window.XMLHttpRequest;
            function WrappedXHR() {
                  const xhr = new OriginalXHR();
                  let counted = false;

                  const increment = () => {
                        if (!counted) {
                              counted = true;
                              inflightRequests += 1;
                              scheduleReload();
                        }
                  };

                  const finalize = () => {
                        if (counted) {
                              counted = false;
                              safeDecrease();
                              scheduleReload();
                        }
                  };

                  xhr.addEventListener('loadstart', increment);
                  xhr.addEventListener('loadend', finalize);
                  xhr.addEventListener('error', finalize);
                  xhr.addEventListener('abort', finalize);

                  return xhr;
            }

            WrappedXHR.prototype = OriginalXHR.prototype;
            window.XMLHttpRequest = WrappedXHR;
      }

      scheduleReload();
})();
