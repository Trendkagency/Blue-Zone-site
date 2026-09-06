<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('typography')
                ->label('🔤 Live Font & Typography Customizer')
                ->icon('heroicon-o-paint-brush')
                ->color('info')
                ->url(fn () => route('filament.admin.pages.manage-typography')),
            Actions\CreateAction::make(),
        ];
    }
}
