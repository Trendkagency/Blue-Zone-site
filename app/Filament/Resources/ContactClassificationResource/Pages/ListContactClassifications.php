<?php

namespace App\Filament\Resources\ContactClassificationResource\Pages;

use App\Filament\Resources\ContactClassificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactClassifications extends ListRecords
{
    protected static string $resource = ContactClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
