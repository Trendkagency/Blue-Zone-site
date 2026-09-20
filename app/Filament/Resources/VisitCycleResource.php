<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitCycleResource\Pages;
use App\Models\Mr\VisitCycle;
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

class VisitCycleResource extends Resource
{
    protected static ?string $model = VisitCycle::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep CRM';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Visit Cycles';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Cycle Parameters')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Cycle Name (e.g. September 2026)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Cycle Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'CYCLE-' . now()->format('Y-m'))
                            ->maxLength(50),
                    ]),
                    Grid::make(3)->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->default(now()->endOfMonth()),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'upcoming' => 'Upcoming',
                                'active' => 'Active',
                                'closed' => 'Closed',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                    Forms\Components\Textarea::make('notes')
                        ->label('Cycle Goals & Notes')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cycle Name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'upcoming' => 'warning',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Doctor Assignments')
                    ->counts('assignments')
                    ->badge()
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'upcoming' => 'Upcoming',
                        'closed' => 'Closed',
                    ]),
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
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitCycles::route('/'),
            'create' => Pages\CreateVisitCycle::route('/create'),
            'edit' => Pages\EditVisitCycle::route('/{record}/edit'),
        ];
    }
}
