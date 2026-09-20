<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactClassificationResource\Pages;
use App\Models\Mr\ContactClassification;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ContactClassificationResource extends Resource
{
    protected static ?string $model = ContactClassification::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep CRM';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Doctor Classes (A+/A/B/C)';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Classification Tier & Target Configuration')
                ->description('Configure target visit frequencies and score points per cycle for this doctor classification tier.')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Class Code (e.g. A+, A, B, C, VIP)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10),
                        Forms\Components\TextInput::make('label')
                            ->label('Label / Description')
                            ->required()
                            ->maxLength(255),
                    ]),
                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('points')
                            ->label('Points per Completed Visit')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('required_visits')
                            ->label('Required Visits (Per Cycle)')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0),
                    ]),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active in System')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Class')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A+', 'VIP', 'S' => 'danger',
                        'A' => 'warning',
                        'B' => 'info',
                        'C' => 'success',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Tier Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Points / Visit')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('required_visits')
                    ->label('Req. Visits / Cycle')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Assigned Doctors')
                    ->counts('contacts')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactClassifications::route('/'),
            'create' => Pages\CreateContactClassification::route('/create'),
            'edit' => Pages\EditContactClassification::route('/{record}/edit'),
        ];
    }
}
