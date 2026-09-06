<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
<<<<<<< HEAD
=======
use Filament\Actions\CreateAction;
>>>>>>> origin/main
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
<<<<<<< HEAD
            \Filament\Actions\CreateAction::make()
=======
            CreateAction::make()
>>>>>>> origin/main
                ->label('+ Create Product'),
        ];
    }
}
