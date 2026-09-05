/**
 * BLUE ZONE™ Luxury Toast Notification Engine (v2.0)
 * Ultra-Modern Glassmorphic Notifications with Rich Vector Graphics & Spring Physics
 */

(function () {
    'use strict';

    function getOrCreateContainer() {
        let container = document.getElementById('bz-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'bz-toast-container';
            container.className = 'bz-toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    // Audio Synthesis Engine for Luxury Micro-interactions
    let audioCtx = null;

    function getAudioContext() {
        if (!audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    /**
     * Play distinct synthesized audio cues for different toast types.
     * @param {'success'|'error'|'warning'|'info'} type 
     * @param {boolean} force - Force play even if global sound setting is off (used for testing)
     */
    function playToastSound(type, force = false) {
        // Check if sound is disabled globally via server setting or user config
        const isEnabled = window.toastConfig?.soundEnabled !== false;
        if (!isEnabled && !force) {
            return;
        }

        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;

            if (type === 'success') {
                // Harmonic Major Arpeggio Chime (C5, E5, G5, C6)
                const notes = [523.25, 659.25, 783.99, 1046.50];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.06);

                    gain.gain.setValueAtTime(0.001, now + idx * 0.06);
                    gain.gain.exponentialRampToValueAtTime(0.12, now + idx * 0.06 + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + idx * 0.06 + 0.35);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + idx * 0.06);
                    osc.stop(now + idx * 0.06 + 0.36);
                });
            } else if (type === 'error') {
                // Soft Damped Descending Double Tone (Attention-grabbing but non-harsh)
                const tones = [
                    { freq: 280, time: 0, dur: 0.18 },
                    { freq: 196, time: 0.12, dur: 0.28 }
                ];
                tones.forEach(t => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(t.freq, now + t.time);

                    gain.gain.setValueAtTime(0.001, now + t.time);
                    gain.gain.exponentialRampToValueAtTime(0.18, now + t.time + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + t.time + t.dur);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + t.time);
                    osc.stop(now + t.time + t.dur);
                });
            } else if (type === 'warning') {
                // Amber Alert Double Ping (440Hz -> 587Hz)
                const notes = [440, 587.33];
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.1);

                    gain.gain.setValueAtTime(0.001, now + idx * 0.1);
                    gain.gain.exponentialRampToValueAtTime(0.14, now + idx * 0.1 + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + idx * 0.1 + 0.25);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + idx * 0.1);
                    osc.stop(now + idx * 0.1 + 0.26);
                });
            } else {
                // Info / Default: Soft High Bell Ping (659Hz -> 880Hz)
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(659.25, now);
                osc.frequency.exponentialRampToValueAtTime(880, now + 0.08);

                gain.gain.setValueAtTime(0.001, now);
                gain.gain.exponentialRampToValueAtTime(0.12, now + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.3);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now);
                osc.stop(now + 0.31);
            }
        } catch (e) {
            console.debug('Toast audio synthesis error:', e);
        }
    }

    /**
     * Show a luxury toast notification.
     * @param {Object|string} options - { type: 'success'|'error'|'warning'|'info', title: string, message: string, duration: number, playSound: boolean }
     */
    function showToast(options) {
        if (typeof options === 'string') {
            options = { message: options, type: 'info' };
        }

        const type = options.type || 'info';
        const isAr = document.documentElement.getAttribute('dir') === 'rtl' || document.documentElement.lang === 'ar';
        const title = options.title || getDefaultTitle(type, isAr);
        const message = options.message || '';
        const duration = options.duration !== undefined ? options.duration : (type === 'error' ? 5500 : 4500);

        // Trigger sound effect
        if (options.playSound !== false) {
            playToastSound(type);
        }

        const container = getOrCreateContainer();
        const toast = document.createElement('div');
        toast.className = `bz-toast bz-toast-${type}`;

        const iconSvg = getIconSvg(type);

        toast.innerHTML = `
            <div class="bz-toast-accent-line"></div>
            <div class="bz-toast-icon-wrapper">
                <div class="bz-toast-icon">${iconSvg}</div>
            </div>
            <div class="bz-toast-body">
                ${title ? `<div class="bz-toast-title">${escapeHtml(title)}</div>` : ''}
                <div class="bz-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="bz-toast-close-btn" aria-label="Close">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            ${duration > 0 ? '<div class="bz-toast-progress-track"><div class="bz-toast-progress-bar"></div></div>' : ''}
        `;

        container.appendChild(toast);

        // Trigger entrance transition
        requestAnimationFrame(() => {
            toast.classList.add('bz-toast-visible');
        });

        const closeBtn = toast.querySelector('.bz-toast-close-btn');
        let timer = null;
        let progressBar = toast.querySelector('.bz-toast-progress-bar');

        function dismiss() {
            if (timer) clearTimeout(timer);
            toast.classList.remove('bz-toast-visible');
            toast.classList.add('bz-toast-hiding');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dismiss();
            });
        }

        if (duration > 0) {
            if (progressBar) {
                progressBar.style.transition = `width ${duration}ms linear`;
                requestAnimationFrame(() => {
                    progressBar.style.width = '0%';
                });
            }

            timer = setTimeout(dismiss, duration);

            // Pause timer on hover
            toast.addEventListener('mouseenter', () => {
                if (timer) clearTimeout(timer);
                if (progressBar) {
                    const computedWidth = window.getComputedStyle(progressBar).width;
                    progressBar.style.transition = 'none';
                    progressBar.style.width = computedWidth;
                }
            });

            toast.addEventListener('mouseleave', () => {
                if (progressBar) {
                    progressBar.style.transition = 'width 1800ms linear';
                    progressBar.style.width = '0%';
                }
                timer = setTimeout(dismiss, 1800);
            });
        }

        return toast;
    }

    function getDefaultTitle(type, isAr) {
        switch (type) {
            case 'success':
                return isAr ? 'تمت العملية بنجاح' : 'Success';
            case 'error':
                return isAr ? 'تنبيه: مراجعة البيانات' : 'Action Required';
            case 'warning':
                return isAr ? 'تنبيه هام' : 'Warning';
            case 'info':
            default:
                return isAr ? 'إشعار المنصة' : 'System Notice';
        }
    }

    function getIconSvg(type) {
        switch (type) {
            case 'success':
                return `<svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2"/>
                    <path d="M8 12.5L10.5 15L16 9.5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;
            case 'error':
                return `<svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2"/>
                    <path d="M15 9L9 15M9 9L15 15" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;
            case 'warning':
                return `<svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3L2 20H22L12 3Z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    <line x1="12" y1="9" x2="12" y2="14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="12" cy="17" r="1.25" fill="currentColor"/>
                </svg>`;
            case 'info':
            default:
                return `<svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2"/>
                    <line x1="12" y1="11" x2="12" y2="16" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="12" cy="8" r="1.25" fill="currentColor"/>
                </svg>`;
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function (m) { return map[m]; });
    }

    // Expose global window.toast API
    window.showToast = showToast;
    window.toast = {
        success: (message, title) => showToast({ type: 'success', message, title }),
        error: (message, title) => showToast({ type: 'error', message, title }),
        warning: (message, title) => showToast({ type: 'warning', message, title }),
        info: (message, title) => showToast({ type: 'info', message, title }),
        playSound: (type, force = false) => playToastSound(type, force),
        testSound: (type) => {
            const isAr = document.documentElement.getAttribute('dir') === 'rtl' || document.documentElement.lang === 'ar';
            const sampleMessages = {
                success: isAr ? 'تم تشغيل نغمة النجاح الصوتية بنجاح!' : 'Success chime tone executed perfectly!',
                error: isAr ? 'تم تشغيل نغمة التنبيه والخطأ الصوتية بنجاح!' : 'Error warning acoustic tone played successfully!',
                warning: isAr ? 'تم تشغيل نغمة التحذير الصوتية بنجاح!' : 'Caution warning acoustic alert played!',
                info: isAr ? 'تم تشغيل نغمة الإشعار العام الصوتية بنجاح!' : 'System notice info chime played!'
            };
            playToastSound(type, true);
            showToast({
                type: type,
                message: sampleMessages[type] || sampleMessages.info,
                playSound: false
            });
        },
        setSoundEnabled: (enabled) => {
            window.toastConfig = window.toastConfig || {};
            window.toastConfig.soundEnabled = !!enabled;
        }
    };

    // Override native browser alert gracefully
    window.alert = function (message) {
        window.toast.info(message);
    };

})();
