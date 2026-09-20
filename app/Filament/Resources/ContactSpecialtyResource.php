<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSpecialtyResource\Pages;
use App\Models\Mr\ContactSpecialty;
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

class ContactSpecialtyResource extends Resource
{
    protected static ?string $model = ContactSpecialty::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep CRM';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Medical Specialties';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Specialty Information')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Specialty Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Code (e.g. CARD, NEUR)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                    ]),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Specialty')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contacts_count')
                    ->label('Doctors Registered')
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSpecialties::route('/'),
            'create' => Pages\CreateContactSpecialty::route('/create'),
            'edit' => Pages\EditContactSpecialty::route('/{record}/edit'),
        ];
    }
}
