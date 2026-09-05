@props([
    'title' => null,
    'pageTitle' => null,
    'pageSubtitle' => null,
    'breadcrumbs' => [],
])

<x-layouts.app :title="($title ?? $pageTitle ?? 'Admin') . ' — ' . __('admin.portal_title')">
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                    <img src="{{ asset('assets/logo/logo-main.png') }}" alt="{{ __('app.brand_name') }}" style="height: 32px;" onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                    <span class="sidebar-brand-title">BZ-OS</span>
                </a>
            </div>

            <nav class="sidebar-menu">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>{{ __('admin.menu.dashboard') }}</span>
                </a>

                <!-- Catalog -->
                <div class="menu-category">{{ __('admin.menu.catalog') }}</div>
                <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>{{ __('admin.menu.products') }}</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <span>{{ __('admin.menu.categories') }}</span>
                </a>

                <!-- Inventory -->
                <div class="menu-category">{{ __('admin.menu.inventory') }}</div>
                <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.index') || request()->routeIs('admin.inventory.show') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>{{ __('admin.menu.stock_levels') }}</span>
                </a>
                <a href="{{ route('admin.inventory.transfers') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.transfers') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span>{{ __('admin.menu.stock_transfers') }}</span>
                </a>
                <a href="{{ route('admin.inventory.history') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ __('admin.menu.stock_history') }}</span>
                </a>

                <!-- Sales -->
                <div class="menu-category">{{ __('admin.menu.sales') }}</div>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>{{ __('admin.menu.online_orders') }}</span>
                </a>
                <a href="{{ route('admin.offline-sales.index') }}" class="sidebar-link {{ request()->routeIs('admin.offline-sales.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('admin.menu.offline_sales') }}</span>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="sidebar-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>{{ __('admin.menu.invoices') }}</span>
                </a>

                <!-- Customers & Analytics -->
                <div class="menu-category">{{ __('admin.menu.customers') }} & {{ __('admin.menu.reports') }}</div>
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ __('admin.menu.customers') }}</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>{{ __('admin.menu.reports') }}</span>
                </a>

                <!-- Content & Access -->
                <div class="menu-category">{{ __('admin.menu.content') }} & {{ __('admin.menu.access_control') }}</div>
                <a href="{{ route('admin.content.index') }}" class="sidebar-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>{{ __('admin.menu.content') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>{{ __('admin.menu.users') }}</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>{{ __('admin.menu.roles') }}</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ __('admin.menu.settings') }}</span>
                </a>
            </nav>
        </aside>

        <!-- Admin Content Shell -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button type="button" class="btn btn-ghost btn-icon" onclick="toggleAdminSidebar()" title="Toggle Sidebar">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="breadcrumbs">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('admin.menu.dashboard') }}</a>
                        @foreach($breadcrumbs as $label => $url)
                            <span class="breadcrumb-separator">›</span>
                            @if($loop->last)
                                <span class="breadcrumb-current">{{ $label }}</span>
                            @else
                                <a href="{{ $url }}" class="breadcrumb-link">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="header-right">
                    <!-- Language Switcher -->
                    @if(app()->getLocale() === 'ar')
                        <a href="{{ route('locale.switch', 'en') }}" class="btn btn-secondary btn-sm font-bold">
                            EN
                        </a>
                    @else
                        <a href="{{ route('locale.switch', 'ar') }}" class="btn btn-secondary btn-sm font-bold">
                            العربية
                        </a>
                    @endif

                    <!-- Theme Toggle -->
                    <button type="button" onclick="if(window.BLUEZONE_THEME){BLUEZONE_THEME.toggle();}else{toggleTheme();}" data-theme-toggle class="btn btn-ghost btn-icon cursor-pointer" title="{{ __('app.theme') }}">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>

                    <!-- View Storefront Link -->
                    <a href="{{ route('customer.home') }}" class="btn btn-outline btn-sm" target="_blank">
                        {{ __('app.nav.home') }} <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
                    </a>

                    <!-- User Pill -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding-inline-start: 0.5rem; border-inline-start: 1px solid var(--color-border);">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem;">
                            TM
                        </div>
                        <span class="text-sm font-bold" style="display: none; @media(min-width: 768px){display:inline;}">
                            Tariq M.
                        </span>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <div class="admin-content-body">
                @if($pageTitle)
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">{{ $pageTitle }}</h1>
                            @if($pageSubtitle)
                                <p class="page-subtitle">{{ $pageSubtitle }}</p>
                            @endif
                        </div>

                        @isset($actions)
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.app>
