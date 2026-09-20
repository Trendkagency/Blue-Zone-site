<?php

namespace App\Filament\Resources\ContactAssignmentResource\Pages;

use App\Filament\Resources\ContactAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactAssignments extends ListRecords
{
    protected static string $resource = ContactAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
