<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\City;
use App\Models\Mr\Contact;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use UnitEnum;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep CRM';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Doctors & Clinics (Contacts)';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Doctor Profile & Classification')
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Doctor / Contact Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'DOC-' . strtoupper(substr(uniqid(), -6)))
                            ->maxLength(50),
                        Forms\Components\TextInput::make('name')
                            ->label('Doctor Full Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hospital_clinic_name')
                            ->label('Hospital / Clinic Center')
                            ->maxLength(255),
                    ]),
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('specialty_id')
                            ->label('Medical Specialty')
                            ->relationship('specialty', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('classification_id')
                            ->label('Classification Tier (Class)')
                            ->relationship('classification', 'label')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone / WhatsApp')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255),
                    ]),
                ]),

            Section::make('Location & GPS Coordinates (Geofencing)')
                ->description('Specify exact physical coordinates for GPS check-in radius verification.')
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),
                        Forms\Components\Select::make('city_id')
                            ->label('City')
                            ->options(fn (Get $get): Collection => City::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('region')
                            ->label('District / Territory')
                            ->maxLength(255),
                    ]),
                    Forms\Components\Textarea::make('address')
                        ->label('Full Physical Address')
                        ->rows(2),
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude (GPS)')
                            ->numeric()
                            ->placeholder('e.g. 30.044420'),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude (GPS)')
                            ->numeric()
                            ->placeholder('e.g. 31.235712'),
                    ]),
                    Forms\Components\Textarea::make('notes')
                        ->label('Doctor Preferences / Clinic Notes')
                        ->rows(2),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active Status')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Doctor Name')
                    ->description(fn (Contact $record) => $record->hospital_clinic_name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specialty.name')
                    ->label('Specialty')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('classification.code')
                    ->label('Class')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A+', 'VIP' => 'danger',
                        'A' => 'warning',
                        'B' => 'info',
                        'C' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('City / Region')
                    ->description(fn (Contact $record) => $record->region)
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_gps')
                    ->label('GPS Set')
                    ->boolean()
                    ->state(fn (Contact $record): bool => !empty($record->latitude) && !empty($record->longitude)),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('specialty')
                    ->relationship('specialty', 'name'),
                Tables\Filters\SelectFilter::make('classification')
                    ->relationship('classification', 'code'),
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
