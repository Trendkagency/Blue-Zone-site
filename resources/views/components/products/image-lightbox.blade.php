@php
    $isAr = app()->getLocale() === 'ar';
@endphp

<!-- =========================================================================
     Blue Zone BZ-OS High-Resolution Product Image Studio & Lightbox Inspector
     ========================================================================= -->
<div id="bzImageStudioModal" 
     class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 sm:p-6 transition-opacity duration-300 backdrop-blur-md"
     style="background: rgba(2, 19, 31, 0.88);"
     role="dialog" 
     aria-modal="true" 
     tabindex="-1"
     onkeydown="handleStudioKeydown(event)">
    
    <!-- Outer Container -->
    <div class="relative w-full max-w-5xl max-h-[92vh] bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-700/80 rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden text-slate-900 dark:text-white transition-all">
        
        <!-- Header Bar -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/60 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400 flex items-center justify-center text-sm font-bold shadow-xs">
                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                </span>
                <div>
                    <h3 class="font-black text-sm sm:text-base tracking-tight flex items-center gap-2" id="studioModalTitle">
                        <span>{{ $isAr ? 'مستعرض الفحص عالي الدقة للصور' : 'Product Image Specification Studio' }}</span>
                        <span id="studioBadgeIndex" class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold">#1</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 hidden sm:block truncate max-w-md" id="studioImageFilename">
                        filename.jpg
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-2">
                <!-- Zoom Controls -->
                <div class="hidden sm:flex items-center bg-slate-200/70 dark:bg-slate-800/80 rounded-xl p-0.5 border border-slate-300 dark:border-slate-700">
                    <button type="button" onclick="studioZoomOut()" class="p-1.5 px-2.5 hover:text-sky-500 text-xs font-bold transition" title="{{ $isAr ? 'تصغير' : 'Zoom Out' }}">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <span id="studioZoomLevelText" class="text-[11px] font-mono font-bold px-1 select-none min-w-[42px] text-center">100%</span>
                    <button type="button" onclick="studioZoomIn()" class="p-1.5 px-2.5 hover:text-sky-500 text-xs font-bold transition" title="{{ $isAr ? 'تكبير' : 'Zoom In' }}">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                    <button type="button" onclick="studioResetZoom()" class="p-1.5 px-2 hover:text-sky-500 text-xs transition border-s border-slate-300 dark:border-slate-700" title="{{ $isAr ? 'إعادة ضبط' : 'Reset' }}">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </button>
                </div>

                <!-- Close Button -->
                <button type="button" 
                        onclick="closeImageStudioModal()" 
                        class="w-9 h-9 rounded-xl bg-slate-200/80 dark:bg-slate-800 hover:bg-rose-500 hover:text-white text-slate-500 dark:text-slate-300 flex items-center justify-center text-sm font-bold transition shadow-xs cursor-pointer"
                        title="{{ $isAr ? 'إغلاق (Esc)' : 'Close (Esc)' }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Main Body: Image Viewport & Navigation -->
        <div class="relative flex-1 min-h-[360px] max-h-[62vh] overflow-hidden bg-[#02131F] flex items-center justify-center select-none" id="studioViewport">
            <!-- Stage Image -->
            <img id="studioStageImg" 
                 src="" 
                 alt="Specification Image" 
                 class="max-w-full max-h-[60vh] object-contain transition-transform duration-200 ease-out cursor-grab active:cursor-grabbing drop-shadow-2xl"
                 style="transform: scale(1) translate(0px, 0px);"
                 onload="onStudioImageLoaded(this)">

            <!-- Previous Image Button -->
            <button type="button" 
                    id="studioBtnPrev" 
                    onclick="studioNavigate(-1)" 
                    class="absolute start-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-2xl bg-black/60 hover:bg-sky-600 text-white flex items-center justify-center text-sm backdrop-blur-sm shadow-xl transition-transform hover:scale-110 cursor-pointer"
                    title="{{ $isAr ? 'الصورة السابقة (السهم الأيسر)' : 'Previous Image (Left Arrow)' }}">
                <i class="fa-solid fa-chevron-left {{ $isAr ? 'fa-flip-horizontal' : '' }}"></i>
            </button>

            <!-- Next Image Button -->
            <button type="button" 
                    id="studioBtnNext" 
                    onclick="studioNavigate(1)" 
                    class="absolute end-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-2xl bg-black/60 hover:bg-sky-600 text-white flex items-center justify-center text-sm backdrop-blur-sm shadow-xl transition-transform hover:scale-110 cursor-pointer"
                    title="{{ $isAr ? 'الصورة التالية (السهم الأيمن)' : 'Next Image (Right Arrow)' }}">
                <i class="fa-solid fa-chevron-right {{ $isAr ? 'fa-flip-horizontal' : '' }}"></i>
            </button>

            <!-- Loading Spinner -->
            <div id="studioSpinner" class="absolute inset-0 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden pointer-events-none">
                <i class="fa-solid fa-spinner fa-spin text-3xl text-sky-400"></i>
            </div>
        </div>

        <!-- Footer Control Strip & Metadata -->
        <div class="p-4 sm:px-6 bg-slate-50 dark:bg-slate-900/90 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs flex-shrink-0">
            <!-- Metadata details -->
            <div class="flex items-center gap-4 flex-wrap text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-image text-sky-500"></i>
                    <strong class="text-slate-700 dark:text-slate-200" id="studioMetaResolution">-- x -- px</strong>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-hard-drive text-amber-500"></i>
                    <span id="studioMetaMime">Image Asset</span>
                </span>
                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold" id="studioHeroBadge" style="display: none;">
                    <i class="fa-solid fa-star"></i>
                    <span>{{ $isAr ? 'الصورة الرئيسية الحالية' : 'Current Hero Image' }}</span>
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap justify-end">
                <!-- 1-Click Set as Primary Hero Image -->
                <button type="button" 
                        id="studioBtnSetHero" 
                        onclick="studioSetAsPrimaryHero()" 
                        class="px-3 py-1.5 rounded-xl font-extrabold text-xs bg-emerald-600 hover:bg-emerald-700 text-white transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-star text-[11px]"></i>
                    <span>{{ $isAr ? 'تعيين كصورة رئيسية للمنتج' : 'Set as Hero Image' }}</span>
                </button>

                <!-- Copy Link -->
                <button type="button" 
                        onclick="studioCopyImageUrl()" 
                        class="px-3 py-1.5 rounded-xl font-bold text-xs border border-slate-300 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-copy text-[11px]"></i>
                    <span id="studioCopyBtnText">{{ $isAr ? 'نسخ الرابط' : 'Copy URL' }}</span>
                </button>

                <!-- Open in New Tab -->
                <a id="studioBtnOpenNewTab" 
                   href="#" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="px-3 py-1.5 rounded-xl font-bold text-xs border border-slate-300 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                    <span>{{ $isAr ? 'فتح بدقة كاملة' : 'Full Res' }}</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    // =========================================================================
    // PRODUCT IMAGE STUDIO / LIGHTBOX ENGINE
    // =========================================================================
    let studioGalleryItems = [];
    let studioCurrentIndex = 0;
    let studioZoomScale = 1;

    // Open Studio for a single image or gallery list
    window.openImageStudio = function(initialUrl, allUrls = [], title = '') {
        const modal = document.getElementById('bzImageStudioModal');
        if (!modal) return;

        // Build list of valid URLs
        studioGalleryItems = (Array.isArray(allUrls) && allUrls.length > 0) ? allUrls : [initialUrl];
        studioCurrentIndex = studioGalleryItems.findIndex(u => u === initialUrl);
        if (studioCurrentIndex < 0) studioCurrentIndex = 0;

        studioResetZoom();
        updateStudioDisplay();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };
    var openImageStudio = window.openImageStudio;

    window.closeImageStudioModal = function() {
        const modal = document.getElementById('bzImageStudioModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    };
    var closeImageStudioModal = window.closeImageStudioModal;

    function updateStudioDisplay() {
        const url = studioGalleryItems[studioCurrentIndex] || '';
        const stageImg = document.getElementById('studioStageImg');
        const spinner = document.getElementById('studioSpinner');
        const filenameEl = document.getElementById('studioImageFilename');
        const badgeIndex = document.getElementById('studioBadgeIndex');
        const openLink = document.getElementById('studioBtnOpenNewTab');
        const btnPrev = document.getElementById('studioBtnPrev');
        const btnNext = document.getElementById('studioBtnNext');
        const heroBadge = document.getElementById('studioHeroBadge');
        const btnSetHero = document.getElementById('studioBtnSetHero');

        if (spinner) spinner.classList.remove('hidden');
        if (stageImg) stageImg.src = url;
        if (openLink) openLink.href = url;

        const fname = url.split('/').pop() || 'image';
        if (filenameEl) filenameEl.textContent = fname;
        if (badgeIndex) {
            badgeIndex.textContent = `#${studioCurrentIndex + 1} / ${studioGalleryItems.length}`;
        }

        // Show/hide navigation chevrons
        if (btnPrev) btnPrev.style.display = studioGalleryItems.length > 1 ? 'flex' : 'none';
        if (btnNext) btnNext.style.display = studioGalleryItems.length > 1 ? 'flex' : 'none';

        // Check if current image is already primary hero
        let currentPrimary = '';
        const heroInput = document.getElementById('inputMainImage') || document.getElementById('primary_image_url_input');
        if (heroInput) currentPrimary = heroInput.value.trim();

        const isHero = currentPrimary && (url.includes(currentPrimary) || currentPrimary.includes(url));
        if (heroBadge) heroBadge.style.display = isHero ? 'inline-flex' : 'none';
        if (btnSetHero) btnSetHero.style.display = isHero ? 'none' : 'inline-flex';
    }

    function onStudioImageLoaded(img) {
        const spinner = document.getElementById('studioSpinner');
        const resEl = document.getElementById('studioMetaResolution');
        const mimeEl = document.getElementById('studioMetaMime');
        if (spinner) spinner.classList.add('hidden');

        if (resEl && img.naturalWidth && img.naturalHeight) {
            resEl.textContent = `${img.naturalWidth} × ${img.naturalHeight} px`;
        }
        if (mimeEl) {
            const ext = (img.src.split('.').pop() || 'JPG').toUpperCase().split('?')[0];
            mimeEl.textContent = `${ext} Format`;
        }
    }

    function studioNavigate(direction) {
        if (studioGalleryItems.length <= 1) return;
        studioCurrentIndex = (studioCurrentIndex + direction + studioGalleryItems.length) % studioGalleryItems.length;
        studioResetZoom();
        updateStudioDisplay();
    }

    // Zoom Controls
    function studioZoomIn() {
        studioZoomScale = Math.min(3, studioZoomScale + 0.25);
        applyStudioZoom();
    }
    function studioZoomOut() {
        studioZoomScale = Math.max(0.5, studioZoomScale - 0.25);
        applyStudioZoom();
    }
    function studioResetZoom() {
        studioZoomScale = 1;
        applyStudioZoom();
    }
    function applyStudioZoom() {
        const stageImg = document.getElementById('studioStageImg');
        const text = document.getElementById('studioZoomLevelText');
        if (stageImg) stageImg.style.transform = `scale(${studioZoomScale})`;
        if (text) text.textContent = `${Math.round(studioZoomScale * 100)}%`;
    }

    // 1-Click Set as Primary Hero Image
    async function studioSetAsPrimaryHero() {
        const activeUrl = studioGalleryItems[studioCurrentIndex];
        if (!activeUrl) return;

        const isAr = {{ $isAr ? 'true' : 'false' }};
        const prodId = typeof bzActiveProductId !== 'undefined' ? bzActiveProductId : null;

        // If product already exists in DB, dispatch Ajax
        if (prodId) {
            try {
                const response = await fetch(`/admin/products/${prodId}/set-primary-image`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ image_url: activeUrl }),
                });
                const data = await response.json();
                if (data.success) {
                    syncHeroPreviewInDOM(activeUrl);
                    updateStudioDisplay();
                    if (typeof showFloatingToast === 'function') {
                        showFloatingToast(
                            isAr ? 'تم تحديث الصورة الرئيسية' : 'Hero Image Updated',
                            data.message || (isAr ? 'تم تعيين الصورة كغلاف أساسي للمنتج.' : 'Image set as cover.')
                        );
                    }
                } else {
                    alert(data.message || 'Error setting primary image.');
                }
            } catch (err) {
                console.error(err);
                alert('Network error updating primary image.');
            }
        } else {
            // Create mode: update form inputs & preview directly
            syncHeroPreviewInDOM(activeUrl);
            updateStudioDisplay();
            if (typeof showFloatingToast === 'function') {
                showFloatingToast(
                    isAr ? 'تم تحديد الغلاف' : 'Cover Selected',
                    isAr ? 'تم تحديد هذه الصورة كصورة رئيسية للمنتج.' : 'Selected as primary product cover.'
                );
            }
        }
    }

    function syncHeroPreviewInDOM(url) {
        const heroImg = document.getElementById('primaryHeroLiveImg');
        const heroInput = document.getElementById('inputMainImage') || document.getElementById('primary_image_url_input');
        const detailsEl = document.getElementById('primaryFileDetails');
        if (heroImg) heroImg.src = url;
        if (heroInput) heroInput.value = url;
        if (detailsEl) {
            detailsEl.classList.remove('hidden');
            const fname = url.split('/').pop();
            const nameEl = document.getElementById('primaryFileName');
            if (nameEl) nameEl.textContent = fname;
            else detailsEl.textContent = fname;
        }
    }

    function studioCopyImageUrl() {
        const activeUrl = studioGalleryItems[studioCurrentIndex];
        if (!activeUrl) return;
        const fullUrl = activeUrl.startsWith('http') ? activeUrl : (window.location.origin + (activeUrl.startsWith('/') ? '' : '/') + activeUrl);
        navigator.clipboard.writeText(fullUrl).then(() => {
            const btnText = document.getElementById('studioCopyBtnText');
            if (btnText) {
                const original = btnText.textContent;
                btnText.textContent = "{{ $isAr ? 'تم النسخ!' : 'Copied!' }}";
                setTimeout(() => { btnText.textContent = original; }, 2000);
            }
        });
    }

    function handleStudioKeydown(e) {
        if (e.key === 'Escape') closeImageStudioModal();
        if (e.key === 'ArrowLeft') studioNavigate({{ $isAr ? 1 : -1 }});
        if (e.key === 'ArrowRight') studioNavigate({{ $isAr ? -1 : 1 }});
    }

    // Close on clicking backdrop
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('bzImageStudioModal');
        if (modal && !modal.classList.contains('hidden') && e.target === modal) {
            closeImageStudioModal();
        }
    });
</script>
