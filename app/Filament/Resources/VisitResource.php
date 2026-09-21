<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Models\Mr\Visit;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep CRM';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Executed Visits & GPS Log';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('checkin_at')
                    ->label('Check-In Time')
                    ->dateTime()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('representative.name')
                    ->label('Medical Rep')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Doctor / Clinic')
                    ->description(fn (Visit $record) => $record->contact?->hospital_clinic_name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.classification.code')
                    ->label('Class')
                    ->badge(),
                Tables\Columns\TextColumn::make('gps_flag')
                    ->label('GPS Validation')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'distance_exceeded' => 'danger',
                        'mock_suspected' => 'danger',
                        'gps_disabled' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('distance_from_contact_m')
                    ->label('Distance')
                    ->state(fn (Visit $record) => $record->distance_from_contact_m !== null ? "{$record->distance_from_contact_m}m" : 'N/A')
                    ->badge()
                    ->color(fn (Visit $record) => ($record->distance_from_contact_m ?? 999) <= 150 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->state(fn (Visit $record) => $record->duration_minutes ? "{$record->duration_minutes} min" : ($record->checkout_at ? '< 1 min' : 'In Progress'))
                    ->badge()
                    ->color(fn (Visit $record) => $record->checkout_at ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('outcome')
                    ->label('Outcome')
                    ->badge(),
                Tables\Columns\TextColumn::make('products.name_en')
                    ->label('Discussed Products')
                    ->badge()
                    ->separator(', '),
                Tables\Columns\TextColumn::make('checkout_at')
                    ->label('Check-Out')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('representative')
                    ->relationship('representative', 'name'),
                Tables\Filters\SelectFilter::make('cycle')
                    ->relationship('cycle', 'name'),
                Tables\Filters\SelectFilter::make('gps_flag')
                    ->options([
                        'verified' => 'Verified within radius',
                        'distance_exceeded' => 'Distance Exceeded',
                        'mock_suspected' => 'Mock Location Suspected',
                        'gps_disabled' => 'GPS Disabled',
                    ]),
                Tables\Filters\TernaryFilter::make('gps_verified')
                    ->label('GPS Verified'),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('checkin_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisits::route('/'),
            'view' => Pages\ViewVisit::route('/{record}'),
        ];
    }
}
