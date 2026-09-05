<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\TypographyService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class ManageTypography extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-paint-brush';

    protected static string | UnitEnum | null $navigationGroup = 'Settings & Taxes';

    protected static ?string $navigationLabel = 'Typography & Fonts (Live)';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Typography & Font System Control';

    protected string $view = 'filament.pages.manage-typography';

    public string $font_family = 'Mont Blanc';

    public string $font_heading_family = 'Mont Blanc';

    public string $font_size_base = '16px';

    public string $font_weight_headings = '700';

    public string $font_weight_body = '400';

    public string $font_letter_spacing = 'normal';

    public function mount(): void
    {
        $config = TypographyService::getActiveConfig();

        $this->font_family = $config['font_family'];
        $this->font_heading_family = $config['font_heading_family'];
        $this->font_size_base = $config['font_size_base'];
        $this->font_weight_headings = $config['font_weight_headings'];
        $this->font_weight_body = $config['font_weight_body'];
        $this->font_letter_spacing = $config['font_letter_spacing'];
    }

    public function selectFont(string $family): void
    {
        $this->font_family = $family;
        $this->font_heading_family = $family;
    }

    public function selectPrimaryFont(string $family): void
    {
        $this->font_family = $family;
    }

    public function selectHeadingFont(string $family): void
    {
        $this->font_heading_family = $family;
    }

    public function setBaseSize(string $size): void
    {
        $this->font_size_base = $size;
    }

    public function setHeadingWeight(string $weight): void
    {
        $this->font_weight_headings = $weight;
    }

    public function setBodyWeight(string $weight): void
    {
        $this->font_weight_body = $weight;
    }

    public function setLetterSpacing(string $spacing): void
    {
        $this->font_letter_spacing = $spacing;
    }

    public function save(): void
    {
        Setting::set('font_family', $this->font_family, 'general', 'string');
        Setting::set('font_heading_family', $this->font_heading_family, 'general', 'string');
        Setting::set('font_size_base', $this->font_size_base, 'general', 'string');
        Setting::set('font_weight_headings', $this->font_weight_headings, 'general', 'string');
        Setting::set('font_weight_body', $this->font_weight_body, 'general', 'string');
        Setting::set('font_letter_spacing', $this->font_letter_spacing, 'general', 'string');

        // Flush all relevant cache entries
        Cache::flush();

        Notification::make()
            ->title('Typography saved globally!')
            ->body("Active font: {$this->font_family} (Headings: {$this->font_heading_family}). Applied across Filament Admin, Storefront, and Custom Admin.")
            ->success()
            ->send();

        $this->dispatch('bz-typography-saved', [
            'font_family' => $this->font_family,
            'font_heading_family' => $this->font_heading_family,
            'font_size_base' => $this->font_size_base,
            'font_weight_headings' => $this->font_weight_headings,
            'font_weight_body' => $this->font_weight_body,
            'font_letter_spacing' => $this->font_letter_spacing,
        ]);
    }

    public function resetToDefault(): void
    {
        $this->font_family = 'Mont Blanc';
        $this->font_heading_family = 'Mont Blanc';
        $this->font_size_base = '16px';
        $this->font_weight_headings = '700';
        $this->font_weight_body = '400';
        $this->font_letter_spacing = 'normal';

        $this->save();
    }

    /**
     * @return array<string, mixed>
     */
    public function getAvailableFontsProperty(): array
    {
        return TypographyService::getAvailableFonts();
    }
}
