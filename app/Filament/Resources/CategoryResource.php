<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
<<<<<<< HEAD
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
=======
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;
>>>>>>> origin/main

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

<<<<<<< HEAD
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catalog & Formulations';
=======
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Catalog & Formulations';
>>>>>>> origin/main

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Categories & Systems';

<<<<<<< HEAD
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category Details')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
=======
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Category Details')
                ->schema([
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('name_en')
                            ->label('Name (EN)')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
<<<<<<< HEAD
                            ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
=======
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))
>>>>>>> origin/main
                            ),
                        Forms\Components\TextInput::make('name_ar')
                            ->label('الاسم بالعربية')
                            ->required()
                            ->maxLength(255)
                            ->extraAttributes(['dir' => 'rtl']),
                    ]),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
<<<<<<< HEAD
                    Forms\Components\Grid::make(2)->schema([
=======
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Forms\Components\Textarea::make('description_en')
                            ->label('Description (EN)')
                            ->rows(3),
                        Forms\Components\Textarea::make('description_ar')
                            ->label('الوصف بالعربية')
                            ->rows(3)
                            ->extraAttributes(['dir' => 'rtl']),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(3)->schema([
=======
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Forms\Components\Select::make('icon')
                            ->label('Icon')
                            ->options([
                                'brain' => '🧠 Brain',
                                'sparkles' => '✨ Sparkles',
                                'shield-check' => '🛡️ Shield',
                                'flame' => '🔥 Flame',
                                'moon' => '🌙 Moon',
                                'heart' => '❤️ Heart',
                            ]),
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Category')
                            ->relationship('parent', 'name_en')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (Top Level)'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ]),
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
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Category')
                    ->description(fn (Category $record) => $record->name_ar)
                    ->searchable(['name_en', 'name_ar'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray')
                    ->size('sm'),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
