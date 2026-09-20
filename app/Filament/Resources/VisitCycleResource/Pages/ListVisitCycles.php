<?php

namespace App\Filament\Resources\VisitCycleResource\Pages;

use App\Filament\Resources\VisitCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisitCycles extends ListRecords
{
    protected static string $resource = VisitCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
