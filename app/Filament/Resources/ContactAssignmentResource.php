<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactAssignmentResource\Pages;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\Mr\CrmAssignmentService;
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
use UnitEnum;

class ContactAssignmentResource extends Resource
{
    protected static ?string $model = ContactAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep CRM';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Doctor Assignments';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Assignment Parameters')
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\Select::make('cycle_id')
                            ->label('Visit Cycle')
                            ->relationship('cycle', 'name')
                            ->default(fn () => VisitCycle::where('status', 'active')->value('id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('mr_id')
                            ->label('Medical Representative')
                            ->options(fn () => User::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('contact_id')
                            ->label('Doctor (Contact)')
                            ->relationship('contact', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($state) {
                                    $contact = Contact::with('classification')->find($state);
                                    if ($contact && $contact->classification) {
                                        $set('target_visits', $contact->classification->required_visits);
                                        $set('target_points', $contact->classification->points * $contact->classification->required_visits);
                                    }
                                }
                            }),
                    ]),
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('target_visits')
                            ->label('Target Visits')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('target_points')
                            ->label('Target Points')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ]),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active in Cycle')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cycle.name')
                    ->label('Cycle')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('representative.name')
                    ->label('Medical Rep')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('contact.name')
                    ->label('Doctor Name')
                    ->description(fn (ContactAssignment $record) => $record->contact?->hospital_clinic_name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact.classification.code')
                    ->label('Class')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A+', 'VIP' => 'danger',
                        'A' => 'warning',
                        'B' => 'info',
                        'C' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Visits Done / Target')
                    ->state(fn (ContactAssignment $record) => "{$record->visits_done} / {$record->target_visits}")
                    ->badge()
                    ->color(fn (ContactAssignment $record) => $record->visits_done >= $record->target_visits ? 'success' : ($record->visits_done > 0 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('points_summary')
                    ->label('Achieved / Target Pts')
                    ->state(fn (ContactAssignment $record) => "{$record->achieved_points} / {$record->target_points}")
                    ->badge(),
                Tables\Columns\TextColumn::make('compliance_pct')
                    ->label('Compliance %')
                    ->state(fn (ContactAssignment $record) => $record->compliance_percentage . '%')
                    ->color(fn (ContactAssignment $record) => $record->compliance_percentage >= 100 ? 'success' : 'warning'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cycle')
                    ->relationship('cycle', 'name'),
                Tables\Filters\SelectFilter::make('representative')
                    ->relationship('representative', 'name'),
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
            'index' => Pages\ListContactAssignments::route('/'),
            'create' => Pages\CreateContactAssignment::route('/create'),
            'edit' => Pages\EditContactAssignment::route('/{record}/edit'),
        ];
    }
}
