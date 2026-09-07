@props([
    'context' => 'login',
    'label' => null,
])

@php
    $isEnabled = \App\Services\CaptchaService::isEnabled($context);
    $captcha = $isEnabled ? \App\Services\CaptchaService::generate() : null;
    $isAr = app()->getLocale() === 'ar';
    $defaultLabel = $isAr ? 'التحقق الأمني (مكافحة الروبوت)' : 'Security Verification (Anti-Bot)';
    $placeholder = $isAr ? 'أدخل ناتج العملية الحسابية' : 'Enter the calculation result';
    $uniqueId = 'captcha_' . $context . '_' . uniqid();
@endphp

@if($isEnabled)
<div class="space-y-2 pt-1 bz-captcha-wrapper" id="{{ $uniqueId }}">
    <div class="flex items-center justify-between">
        <label for="{{ $uniqueId }}_input" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-[#0A4F78] dark:text-[#2A8FC2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>{{ $label ?? $defaultLabel }}</span>
            <span class="text-red-500">*</span>
        </label>
        <span class="text-[10px] text-[#0A4F78]/60 dark:text-[#2A8FC2]/60 font-medium">
            {{ $isAr ? 'حل المسألة' : 'Solve Math' }}
        </span>
    </div>

    <div class="flex items-center gap-2.5">
        <!-- SVG Challenge Container -->
        <div class="bz-captcha-preview flex-shrink-0 w-36 sm:w-44 h-12 bg-white dark:bg-[#031827] rounded-xl border border-[#0A4F78]/25 dark:border-[#0A4F78]/40 p-1 flex items-center justify-center shadow-xs overflow-hidden">
            {!! $captcha['svg'] !!}
        </div>

        <!-- Reload / Refresh Button -->
        <button 
            type="button" 
            class="bz-captcha-reload p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] hover:bg-[#0A4F78]/10 dark:hover:bg-[#0A4F78]/30 border border-[#0A4F78]/20 text-[#0A4F78] dark:text-[#2A8FC2] transition-all cursor-pointer hover:rotate-180 duration-300 flex-shrink-0 shadow-xs" 
            title="{{ $isAr ? 'تحديث العملية' : 'Refresh security challenge' }}"
            aria-label="Refresh Captcha">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>

        <!-- Captcha Answer Input -->
        <div class="flex-1">
            <input 
                id="{{ $uniqueId }}_input"
                name="captcha" 
                type="text" 
                inputmode="numeric"
                autocomplete="off"
                placeholder="{{ $placeholder }}" 
                required 
                class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border @error('captcha') border-red-500 focus:border-red-500 @else border-[#0A4F78]/20 focus:border-[#2A8FC2] @enderror text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all font-mono font-bold"
            />
        </div>
    </div>

    @error('captcha')
        <p class="text-xs text-red-500 dark:text-red-400 font-semibold flex items-center gap-1.5 mt-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>

<script>
(function() {
    const wrap = document.getElementById('{{ $uniqueId }}');
    if (!wrap) return;
    const btn = wrap.querySelector('.bz-captcha-reload');
    const preview = wrap.querySelector('.bz-captcha-preview');

    if (btn && preview) {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            btn.classList.add('animate-spin');
            try {
                const res = await fetch('{{ route('captcha.refresh') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.svg) {
                        preview.innerHTML = data.svg;
                        const input = wrap.querySelector('input[name="captcha"]');
                        if (input) {
                            input.value = '';
                            input.focus();
                        }
                    }
                }
            } catch (err) {
                console.warn('Captcha refresh error:', err);
            } finally {
                setTimeout(() => btn.classList.remove('animate-spin'), 350);
            }
        });
    }
})();
</script>
@endif
