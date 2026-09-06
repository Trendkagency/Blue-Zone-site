@props([
    'title' => null,
])

<x-layouts.app :title="$title ?? __('app.nav.login')">
    <div style="min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 2rem 1rem; background-color: var(--color-bg-body);">
        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="{{ route('customer.home') }}" style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                <img src="{{ asset('assets/logo/logo-light.webp') }}" alt="{{ __('app.brand_name') }}" class="dark:hidden" style="height: 38px;" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-light.png') }}';">
                <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="{{ __('app.brand_name') }}" class="hidden dark:block" style="height: 38px;" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                <span style="font-size: 1.5rem; font-weight: 800; color: var(--color-text-primary);">{{ __('app.brand_name') }}</span>
            </a>
        </div>

        <div style="width: 100%; max-width: 440px;">
            {{ $slot }}
        </div>

        <div style="margin-top: 2rem; display: flex; align-items: center; gap: 1rem; font-size: 0.8125rem; color: var(--color-text-muted);">
            @if(app()->getLocale() === 'ar')
                <a href="{{ route('locale.switch', 'en') }}" style="font-weight: 700; color: var(--color-primary);">English (EN)</a>
            @else
                <a href="{{ route('locale.switch', 'ar') }}" style="font-weight: 700; color: var(--color-primary);">العربية (AR)</a>
            @endif
            <span>•</span>
            <button type="button" onclick="toggleTheme()" style="color: inherit;">🌓 {{ __('app.theme') }}</button>
            <span>•</span>
            <a href="{{ route('customer.home') }}" style="color: inherit;">{{ __('app.nav.home') }}</a>
        </div>
    </div>
</x-layouts.app>
