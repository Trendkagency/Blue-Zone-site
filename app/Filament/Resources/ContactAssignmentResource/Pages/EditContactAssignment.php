<?php

namespace App\Filament\Resources\ContactAssignmentResource\Pages;

use App\Filament\Resources\ContactAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactAssignment extends EditRecord
{
    protected static string $resource = ContactAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
