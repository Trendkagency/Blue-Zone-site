@props([
    'title' => null,
    'description' => null,
])

<x-layouts.app :title="$title" :description="$description">
    <!-- Top Announcement Bar -->
    <div class="topbar">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <div class="text-xs font-semibold">
                <i class="fa-solid fa-wand-magic-sparkles mr-1.5 ml-1.5 text-accent"></i> {{ __('shop.cart.shipping_unlocked') }}
            </div>
            <div class="topbar-links">
                <!-- Language Switcher -->
                @if(app()->getLocale() === 'ar')
                    <a href="{{ route('locale.switch', 'en') }}" class="text-xs font-bold" style="color: var(--bz-accent-blue);">
                        English (EN)
                    </a>
                @else
                    <a href="{{ route('locale.switch', 'ar') }}" class="text-xs font-bold" style="color: var(--bz-accent-blue);">
                        العربية (AR)
                    </a>
                @endif

                <span style="opacity: 0.3;">|</span>

                <!-- Theme Toggle Button -->
                <button type="button" onclick="if(window.BLUEZONE_THEME){BLUEZONE_THEME.toggle();}else{toggleTheme();}" data-theme-toggle class="text-xs font-semibold" style="display: inline-flex; align-items: center; gap: 0.25rem;" title="Switch Theme">
                    <span data-theme-label><i class="fa-solid fa-circle-half-stroke mr-1 ml-1"></i> Dark Mode</span>
                </button>

                <span style="opacity: 0.3;">|</span>

                <!-- Admin Link -->
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold" style="color: #94A3B8;">
                    {{ __('app.nav.admin_portal') }} <i class="fa-solid fa-arrow-right mr-1 ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="site-header">
        <nav class="site-nav">
            <!-- Brand Logo -->
            <a href="{{ route('customer.home') }}" class="nav-brand">
                <img src="{{ asset('assets/logo/logo-light.webp') }}" alt="{{ __('app.brand_name') }}" class="nav-brand-logo dark:hidden" style="height: 32px;" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-light.png') }}';">
                <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="{{ __('app.brand_name') }}" class="nav-brand-logo hidden dark:block" style="height: 32px;" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                <span>{{ __('app.brand_name') }}</span>
            </a>

            <!-- Desktop Menu -->
            <div class="nav-menu">
                <a href="{{ route('customer.home') }}" class="nav-link {{ (request()->routeIs('customer.home') || request()->is('/')) ? 'active' : '' }}">
                    {{ __('app.nav.home') }}
                </a>
                <a href="{{ route('customer.shop') }}" class="nav-link {{ (request()->routeIs('customer.shop*') || request()->routeIs('customer.product*') || request()->is('shop*') || request()->is('products*')) ? 'active' : '' }}">
                    {{ __('app.nav.shop') }}
                </a>
                <a href="{{ route('customer.pages.science') }}" class="nav-link {{ (request()->routeIs('customer.pages.science*') || request()->is('science*')) ? 'active' : '' }}">
                    {{ __('app.nav.science') }}
                </a>
                <a href="{{ route('customer.pages.about') }}" class="nav-link {{ (request()->routeIs('customer.pages.about*') || request()->is('about*')) ? 'active' : '' }}">
                    {{ __('app.nav.about') }}
                </a>
                <a href="{{ route('customer.pages.team') }}" class="nav-link {{ (request()->routeIs('customer.pages.team*') || request()->is('team*')) ? 'active' : '' }}">
                    {{ __('app.nav.team') }}
                </a>
                <a href="{{ route('customer.pages.contact') }}" class="nav-link {{ (request()->routeIs('customer.pages.contact*') || request()->is('contact*')) ? 'active' : '' }}">
                    {{ __('app.nav.contact') }}
                </a>
                <a href="{{ route('customer.pages.faqs') }}" class="nav-link {{ (request()->routeIs('customer.pages.faqs*') || request()->is('faqs*')) ? 'active' : '' }}">
                    {{ __('app.nav.faqs') }}
                </a>
            </div>

            <!-- Header Actions -->
            <div class="nav-actions">
                <a href="{{ route('customer.account.dashboard') }}" class="btn btn-ghost btn-sm" title="{{ __('app.nav.account') }}">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-sm font-semibold" style="display: none; @media(min-width: 1024px){display:inline;}">
                        {{ __('app.nav.account') }}
                    </span>
                </a>

                <a href="{{ route('customer.cart') }}" class="cart-trigger btn-primary btn-sm btn" style="border-radius: var(--radius-full);">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="font-bold text-xs">2</span>
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="customer-main">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                        <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="{{ __('app.brand_name') }}" style="height: 32px;" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                        <span style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF;">{{ __('app.brand_name') }}</span>
                    </div>
                    <p style="color: #94A3B8; font-size: 0.9375rem; line-height: 1.7; margin-bottom: 1.5rem;">
                        {{ __('app.footer.tagline') }}
                    </p>
                    <div style="display: flex; gap: 0.75rem;">
                        <span class="badge badge-accent">ISO 17025 Tested</span>
                        <span class="badge badge-primary">cGMP Certified</span>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">{{ __('app.footer.quick_links') }}</h4>
                    <div class="footer-links">
                        <a href="{{ route('customer.home') }}" class="footer-link">{{ __('app.nav.home') }}</a>
                        <a href="{{ route('customer.shop') }}" class="footer-link">{{ __('app.nav.shop') }}</a>
                        <a href="{{ route('customer.pages.science') }}" class="footer-link">{{ __('app.nav.science') }}</a>
                        <a href="{{ route('customer.pages.about') }}" class="footer-link">{{ __('app.nav.about') }}</a>
                        <a href="{{ route('customer.pages.team') }}" class="footer-link">{{ __('app.nav.team') }}</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">{{ __('app.footer.protocols') }}</h4>
                    <div class="footer-links">
                        <a href="{{ route('customer.product.show', 'blue-mind') }}" class="footer-link">BLUE MIND (Cognition)</a>
                        <a href="{{ route('customer.product.show', 'blue-cell') }}" class="footer-link">BLUE CELL (Cellular NAD+)</a>
                        <a href="{{ route('customer.product.show', 'blue-defense') }}" class="footer-link">BLUE DEFENSE (Immunity)</a>
                        <a href="{{ route('customer.product.show', 'blue-metabolic') }}" class="footer-link">BLUE METABOLIC (AMPK)</a>
                        <a href="{{ route('customer.product.show', 'blue-sleep') }}" class="footer-link">BLUE SLEEP (REM Delta)</a>
                        <a href="{{ route('customer.product.show', 'blue-vitality') }}" class="footer-link">BLUE VITALITY (Vascular)</a>
                    </div>
                </div>

                <div>
                    <h4 class="footer-title">{{ __('app.footer.legal') }}</h4>
                    <div class="footer-links">
                        <a href="{{ route('customer.pages.privacy') }}" class="footer-link">{{ __('app.footer.privacy') }}</a>
                        <a href="{{ route('customer.pages.terms') }}" class="footer-link">{{ __('app.footer.terms') }}</a>
                        <a href="{{ route('customer.pages.contact') }}" class="footer-link">{{ __('app.nav.contact') }}</a>
                        <a href="{{ route('customer.pages.faqs') }}" class="footer-link">{{ __('app.nav.faqs') }}</a>
                    </div>
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.03); border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 2rem; border: 1px solid rgba(255, 255, 255, 0.05);">
                <p style="font-size: 0.75rem; color: #64748B; margin: 0; line-height: 1.6;">
                    <strong>{{ __('app.footer.disclaimer') }}</strong>
                </p>
            </div>

            <div class="footer-bottom">
                <div>
                    {{ __('app.footer.copyright', ['year' => date('Y')]) }}
                </div>
                <div>
                    Designed for Human Longevity & Cellular Peak Performance.
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Bottom Quick Action Dock -->
    <div class="mobile-bottom-dock">
        <a href="{{ route('customer.home') }}" class="dock-item {{ request()->routeIs('customer.home') ? 'active' : '' }}">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>{{ __('app.nav.home') }}</span>
        </a>

        <a href="{{ route('customer.shop') }}" class="dock-item {{ request()->routeIs('customer.shop*') ? 'active' : '' }}">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span>{{ __('app.nav.shop') }}</span>
        </a>

        <a href="{{ route('customer.cart') }}" class="dock-item {{ request()->routeIs('customer.cart*') ? 'active' : '' }}" style="position: relative;">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>{{ __('app.nav.cart') }}</span>
        </a>

        <a href="{{ route('customer.account.dashboard') }}" class="dock-item {{ request()->routeIs('customer.account*') ? 'active' : '' }}">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>{{ __('app.nav.account') }}</span>
        </a>
    </div>
</x-layouts.app>
