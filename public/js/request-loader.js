/**
 * BLUE ZONE Universal Request Lazy Loading & Progress Engine
 * Automatically tracks all Fetch requests, XHRs, form submissions, and page navigation.
 */
(function() {
    'use strict';

    // 1. Inject Style if not present
    if (!document.getElementById('bz-request-loader-styles')) {
        const style = document.createElement('style');
        style.id = 'bz-request-loader-styles';
        style.textContent = `
            #bz-request-progress {
                position: fixed;
                top: 0;
                left: 0;
                width: 0%;
                height: 3.5px;
                background: linear-gradient(90deg, #0A4F78 0%, #2A8FC2 50%, #67B34A 100%);
                z-index: 9999999;
                transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
                pointer-events: none;
                box-shadow: 0 0 12px rgba(42, 143, 194, 0.8), 0 0 6px rgba(103, 179, 74, 0.6);
                opacity: 0;
            }
            [dir="rtl"] #bz-request-progress {
                left: auto;
                right: 0;
            }
            .btn-is-loading {
                position: relative !important;
                pointer-events: none !important;
                opacity: 0.82 !important;
                cursor: wait !important;
            }
            .btn-loading-spinner {
                display: inline-block !important;
                width: 0.9em;
                height: 0.9em;
                border: 2px solid currentColor;
                border-right-color: transparent;
                border-radius: 50%;
                animation: bz-loader-spin 0.65s linear infinite;
                vertical-align: -0.125em;
                margin-inline-end: 0.45em;
            }
            @keyframes bz-loader-spin {
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }

    // 2. Create Progress Bar Element
    let progressBar = document.getElementById('bz-request-progress');
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'bz-request-progress';
        if (document.body) {
            document.body.appendChild(progressBar);
        } else {
            document.addEventListener('DOMContentLoaded', () => {
                if (!document.getElementById('bz-request-progress')) {
                    document.body.appendChild(progressBar);
                }
            });
        }
    }

    let activeRequests = 0;
    let progressTimer = null;
    let currentWidth = 0;

    function startProgress() {
        activeRequests++;
        if (activeRequests === 1) {
            if (progressTimer) clearInterval(progressTimer);
            currentWidth = 15;
            progressBar.style.opacity = '1';
            progressBar.style.width = currentWidth + '%';

            progressTimer = setInterval(() => {
                if (currentWidth < 70) {
                    currentWidth += Math.random() * 12 + 5;
                } else if (currentWidth < 90) {
                    currentWidth += Math.random() * 3 + 1;
                }
                progressBar.style.width = currentWidth + '%';
            }, 200);
        }
    }

    function completeProgress() {
        activeRequests = Math.max(0, activeRequests - 1);
        if (activeRequests === 0) {
            if (progressTimer) clearInterval(progressTimer);
            progressBar.style.width = '100%';
            setTimeout(() => {
                progressBar.style.opacity = '0';
                setTimeout(() => {
                    if (activeRequests === 0) {
                        progressBar.style.width = '0%';
                        currentWidth = 0;
                    }
                }, 300);
            }, 180);
        }
    }

    window.BZ_REQUEST_LOADER = {
        start: startProgress,
        done: completeProgress
    };

    // 3. Intercept Fetch API globally
    if (window.fetch) {
        const nativeFetch = window.fetch;
        window.fetch = function(...args) {
            startProgress();
            return nativeFetch.apply(this, args)
                .then(response => {
                    completeProgress();
                    return response;
                })
                .catch(error => {
                    completeProgress();
                    throw error;
                });
        };
    }

    // 4. Intercept XMLHttpRequest globally
    if (window.XMLHttpRequest) {
        const origOpen = XMLHttpRequest.prototype.open;
        const origSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function(...args) {
            this._tracked = true;
            return origOpen.apply(this, args);
        };

        XMLHttpRequest.prototype.send = function(...args) {
            if (this._tracked) {
                startProgress();
                const onDone = () => {
                    if (this._tracked) {
                        this._tracked = false;
                        completeProgress();
                    }
                };
                this.addEventListener('load', onDone);
                this.addEventListener('error', onDone);
                this.addEventListener('abort', onDone);
            }
            return origSend.apply(this, args);
        };
    }

    // 5. Automatic Form Submit Button Spinner & Anti-Double-Click
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.getAttribute('data-no-loading') === 'true') return;

        startProgress();
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn && !submitBtn.classList.contains('btn-is-loading')) {
            submitBtn.classList.add('btn-is-loading');
            submitBtn.setAttribute('data-original-html', submitBtn.innerHTML);

            const isRtl = document.documentElement.lang === 'ar' || document.documentElement.getAttribute('dir') === 'rtl';
            const loadingText = submitBtn.getAttribute('data-loading-text') || (isRtl ? 'جاري المعالجة...' : 'Processing...');
            
            submitBtn.innerHTML = `<span class="btn-loading-spinner"></span><span>${loadingText}</span>`;
            
            // Timeout safety in case navigation is cancelled or slow
            setTimeout(() => {
                if (submitBtn.classList.contains('btn-is-loading')) {
                    const original = submitBtn.getAttribute('data-original-html');
                    if (original) submitBtn.innerHTML = original;
                    submitBtn.classList.remove('btn-is-loading');
                    completeProgress();
                }
            }, 8000);
        }
    }, true);

    // 6. Navigation progress on link clicks
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;
        const href = link.getAttribute('href');
        if (href && !href.startsWith('#') && !href.startsWith('javascript:') && !link.hasAttribute('target') && !link.hasAttribute('download') && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
            try {
                const url = new URL(link.href, window.location.href);
                if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                    startProgress();
                }
            } catch (err) {}
        }
    });

    // 7. Enforce native lazy loading on images
    function setupImageLazyLoading() {
        const images = document.querySelectorAll('img:not([loading])');
        images.forEach(img => {
            img.setAttribute('loading', 'lazy');
            if (!img.hasAttribute('decoding')) {
                img.setAttribute('decoding', 'async');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupImageLazyLoading);
    } else {
        setupImageLazyLoading();
    }
})();
