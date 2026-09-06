<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryItemResource\Pages;
use App\Models\InventoryItem;
<<<<<<< HEAD
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
=======
use BackedEnum;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;
>>>>>>> origin/main

class InventoryItemResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

<<<<<<< HEAD
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Inventory Management';
=======
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Inventory Management';
>>>>>>> origin/main

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Stock Details';

<<<<<<< HEAD
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Stock Item')
=======
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Stock Item')
>>>>>>> origin/main
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name_en')
                        ->searchable()
                        ->preload()
                        ->required(),
<<<<<<< HEAD
                    Forms\Components\Grid::make(2)->schema([
=======
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Forms\Components\Select::make('location_id')
                            ->options([
                                'online' => 'Online Fulfillment Hub',
                                'offline' => 'Flagship Boutique / POS',
                                'central_wh' => 'Central Quarantine Warehouse',
                            ])->required(),
                        Forms\Components\TextInput::make('location_name_en')->required(),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(3)->schema([
=======
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('current_stock')->numeric()->required(),
                        Forms\Components\TextInput::make('available_stock')->numeric()->required(),
                        Forms\Components\TextInput::make('reserved_stock')->numeric()->default(0),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(2)->schema([
=======
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('low_stock_threshold')->numeric()->default(15),
                        Forms\Components\Select::make('status')
                            ->options([
                                'in_stock' => 'In Stock',
                                'low_stock' => 'Low Stock',
                                'out_of_stock' => 'Out of Stock',
                            ])->required(),
                    ]),
                ]),
        ]);
    }

<<<<<<< HEAD
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Stock Details')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
=======
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Stock Details')
                ->schema([
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('product.name_en')->label('Product')->weight('bold'),
                        Infolists\Components\TextEntry::make('location_name_en')->label('Location'),
                        Infolists\Components\TextEntry::make('variant_en')->label('Variant'),
                    ]),
<<<<<<< HEAD
                    Infolists\Components\Grid::make(4)->schema([
=======
                    Grid::make(4)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('current_stock')->label('Current')->badge()->color('primary'),
                        Infolists\Components\TextEntry::make('available_stock')->label('Available')->badge()->color('success'),
                        Infolists\Components\TextEntry::make('reserved_stock')->label('Reserved')->badge()->color('warning'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'in_stock' => 'success',
                                'low_stock' => 'warning',
                                'out_of_stock' => 'danger',
                                default => 'gray',
                            }),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name_en')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('product.sku')
                    ->label('SKU')
                    ->font('mono')
                    ->size('sm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location_name_en')
                    ->label('Location')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Current')
                    ->sortable()
                    ->badge()
<<<<<<< HEAD
                    ->color(fn (InventoryItem $record) =>
                        $record->current_stock <= $record->low_stock_threshold ? 'warning' : 'success'
=======
                    ->color(fn (InventoryItem $record) => $record->current_stock <= $record->low_stock_threshold ? 'warning' : 'success'
>>>>>>> origin/main
                    ),
                Tables\Columns\TextColumn::make('available_stock')
                    ->label('Available')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reserved_stock')
                    ->label('Reserved'),
                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Threshold'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'in_stock' => 'success',
                        'low_stock' => 'warning',
                        'out_of_stock' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucfirst($state))),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Location')
                    ->options([
                        'online' => 'Online Hub',
                        'offline' => 'Flagship Boutique',
                        'central_wh' => 'Central Warehouse',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'low_stock' => 'Low Stock',
                        'out_of_stock' => 'Out of Stock',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryItems::route('/'),
            'view' => Pages\ViewInventoryItem::route('/{record}'),
            'edit' => Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
