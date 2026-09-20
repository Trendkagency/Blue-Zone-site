<?php

namespace App\Filament\Resources\ContactSpecialtyResource\Pages;

use App\Filament\Resources\ContactSpecialtyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactSpecialties extends ListRecords
{
    protected static string $resource = ContactSpecialtyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
