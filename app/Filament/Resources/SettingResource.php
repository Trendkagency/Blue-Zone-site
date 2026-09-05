<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Settings & Taxes';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Platform Settings & Taxes';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Setting Configuration')
                ->description('Configure enterprise tax rates, VAT numbers, and store operational parameters.')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Configuration Key')
                            ->placeholder('e.g. tax_percentage, tax_number, site_name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        Forms\Components\Select::make('group')
                            ->label('Setting Group')
                            ->options([
                                'tax' => '💳 Taxes & VAT (ZATCA)',
                                'font' => '🔤 Typography & Design',
                                'general' => '🏢 General & Brand',
                                'store' => '🏬 Store & Inventory',
                                'commerce' => '💰 Payments & Gateways',
                                'shipping' => '🚚 Logistics & Shipping',
                                'alerts' => '🔔 Notifications',
                            ])
                            ->required()
                            ->default('tax'),
                    ]),
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('type')
                            ->label('Data Type')
                            ->options([
                                'string' => 'String / Text',
                                'float' => 'Float / Decimal (e.g. Tax Rate 15.0)',
                                'integer' => 'Integer (e.g. Threshold 10)',
                                'boolean' => 'Boolean (True / False)',
                                'json' => 'JSON / Array',
                            ])
                            ->required()
                            ->default('string'),
                        Forms\Components\Textarea::make('value')
                            ->label('Configuration Value')
                            ->placeholder('e.g. 15, 31004829100003, true')
                            ->rows(2),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Key')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Configured Value')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->colors([
                        'warning' => 'tax',
                        'info' => 'font',
                        'primary' => 'general',
                        'success' => 'store',
                        'secondary' => 'shipping',
                    ]),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Synced')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'tax' => 'Taxes & VAT',
                        'font' => 'Typography & Design',
                        'general' => 'General & Brand',
                        'store' => 'Store & Inventory',
                        'commerce' => 'Payments',
                        'shipping' => 'Shipping',
                        'alerts' => 'Alerts',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
