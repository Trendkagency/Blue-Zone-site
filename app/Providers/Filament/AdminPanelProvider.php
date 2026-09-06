<?php

namespace App\Providers\Filament;

use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin/filament')
            ->login()
            ->brandName('BLUE ZONE — BZ-OS')
            ->brandLogo(asset('assets/logo/logo-main.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->darkMode(true)
            ->defaultThemeMode(ThemeMode::Light)
            ->colors([
                'primary' => [
                    50 => '#f0f7fb',
                    100 => '#dfedf6',
                    200 => '#c4def0',
                    300 => '#9bc6e6',
                    400 => '#4fb0e6',
                    500 => '#2a8fc2',
                    600 => '#0a4f78',
                    700 => '#083f61',
                    800 => '#062b49',
                    900 => '#031827',
                    950 => '#02101c',
                ],
            ])
            ->font(
                function () {
                    $font = (string) \App\Models\Setting::get('font_family', 'Mont Blanc');
                    if ($font === 'Mont Blanc') {
                        return 'Montserrat';
                    }
                    return $font ?: 'Cairo';
                },
                provider: \Filament\FontProviders\BunnyFontProvider::class
            )
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.hooks.custom-font-styles')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
