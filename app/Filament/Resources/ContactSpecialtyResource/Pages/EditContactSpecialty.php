<?php

namespace App\Filament\Resources\ContactSpecialtyResource\Pages;

use App\Filament\Resources\ContactSpecialtyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactSpecialty extends EditRecord
{
    protected static string $resource = ContactSpecialtyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
