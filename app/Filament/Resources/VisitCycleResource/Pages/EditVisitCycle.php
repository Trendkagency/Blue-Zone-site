<?php

namespace App\Filament\Resources\VisitCycleResource\Pages;

use App\Filament\Resources\VisitCycleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitCycle extends EditRecord
{
    protected static string $resource = VisitCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
