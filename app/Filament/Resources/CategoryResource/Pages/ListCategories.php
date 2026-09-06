<?php
<<<<<<< HEAD
namespace App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\ListRecords;
class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
=======

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
>>>>>>> origin/main
    }
}
