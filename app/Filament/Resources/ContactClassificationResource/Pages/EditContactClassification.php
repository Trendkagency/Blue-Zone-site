<?php

namespace App\Filament\Resources\ContactClassificationResource\Pages;

use App\Filament\Resources\ContactClassificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactClassification extends EditRecord
{
    protected static string $resource = ContactClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
