<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog & Formulations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Products List';

    protected static ?string $recordTitleAttribute = 'name_en';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Product Form')
                ->tabs([
                    // Tab 1: Core & Categorization
                    Forms\Components\Tabs\Tab::make('Core & Categorization')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            Forms\Components\Section::make('Core Identifiers & Classification')
                                ->schema([
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('sku')
                                            ->label('SKU (Stock Keeping Unit)')
                                            ->placeholder('e.g. BZ-MND-001')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('barcode')
                                            ->label('GTIN / Barcode')
                                            ->placeholder('e.g. 628100091001')
                                            ->maxLength(50),
                                    ]),
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\Select::make('category_id')
                                            ->label('Primary Health System')
                                            ->relationship('category', 'name_en')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\TextInput::make('subcategory_en')
                                            ->label('Subcategory (EN)')
                                            ->placeholder('e.g. Nootropics'),
                                        Forms\Components\TextInput::make('brand')
                                            ->label('Laboratory Brand')
                                            ->default('Blue Zone Bioceuticals')
                                            ->required(),
                                    ]),
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('target_gender')
                                            ->label('Target Demographic')
                                            ->default('Unisex'),
                                        Forms\Components\TextInput::make('age_group')
                                            ->label('Age Cohort')
                                            ->default('18+'),
                                        Forms\Components\TextInput::make('product_size')
                                            ->label('Dosage Form')
                                            ->placeholder('e.g. 60 Vegetable Capsules'),
                                    ]),
                                    Forms\Components\TextInput::make('slug')
                                        ->label('URL Slug')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->maxLength(255)
                                        ->dehydrateStateUsing(fn ($state) => Str::slug($state)),
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Featured Product'),
                                        Forms\Components\Toggle::make('is_best_seller')
                                            ->label('Best Seller'),
                                        Forms\Components\Toggle::make('is_new')
                                            ->label('New Arrival'),
                                    ]),
                                    Forms\Components\Select::make('status')
                                        ->label('Publication Status')
                                        ->options([
                                            'active' => 'Active',
                                            'inactive' => 'Inactive',
                                            'draft' => 'Draft',
                                        ])
                                        ->default('active')
                                        ->required(),
                                ]),
                        ]),

                    // Tab 2: Multi-Lingual Content
                    Forms\Components\Tabs\Tab::make('Multi-Lingual Content (EN/AR)')
                        ->icon('heroicon-o-language')
                        ->schema([
                            Forms\Components\Section::make('English (LTR)')
                                ->schema([
                                    Forms\Components\TextInput::make('name_en')
                                        ->label('Product Name (EN)')
                                        ->placeholder('e.g. BLUE MIND')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('tagline_en')
                                        ->label('Tagline / Short Summary (EN)')
                                        ->placeholder('e.g. Daily Cognitive & Nootropic Support')
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('short_description_en')
                                        ->label('Short Description (EN)')
                                        ->rows(2),
                                    Forms\Components\RichEditor::make('description_en')
                                        ->label('Full Customer Description (EN)')
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('usage_en')
                                        ->label('Administration & Dosage Instructions (EN)')
                                        ->rows(2),
                                ]),
                            Forms\Components\Section::make('العربية (RTL)')
                                ->schema([
                                    Forms\Components\TextInput::make('name_ar')
                                        ->label('اسم المنتج بالعربية')
                                        ->placeholder('مثال: بلو مايند')
                                        ->required()
                                        ->maxLength(255)
                                        ->extraAttributes(['dir' => 'rtl']),
                                    Forms\Components\TextInput::make('tagline_ar')
                                        ->label('الوصف المختصر بالعربية')
                                        ->placeholder('مثال: دعم إدراكي وتركيز عصبي يومي متطور')
                                        ->maxLength(255)
                                        ->extraAttributes(['dir' => 'rtl']),
                                    Forms\Components\Textarea::make('short_description_ar')
                                        ->label('الوصف المختصر بالعربية')
                                        ->rows(2)
                                        ->extraAttributes(['dir' => 'rtl']),
                                    Forms\Components\RichEditor::make('description_ar')
                                        ->label('الوصف التفصيلي للعميل بالعربية')
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('usage_ar')
                                        ->label('طريقة الاستخدام والجرعات بالعربية')
                                        ->rows(2)
                                        ->extraAttributes(['dir' => 'rtl']),
                                ]),
                        ]),

                    // Tab 3: Variants & Pricing
                    Forms\Components\Tabs\Tab::make('Variants & Pricing')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\Section::make('Base Pricing & Multi-Variant Options')
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->label('Base Retail Price ($)')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->step(0.01),
                                        Forms\Components\TextInput::make('sale_price')
                                            ->label('Sale / Promotional Price ($)')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),
                                        Forms\Components\TextInput::make('cost_price')
                                            ->label('Manufacturing Unit Cost ($)')
                                            ->numeric()
                                            ->prefix('$')
                                            ->step(0.01),
                                    ]),
                                    Forms\Components\Grid::make(2)->schema([
                                        Forms\Components\TextInput::make('rating')
                                            ->label('Average Rating')
                                            ->numeric()
                                            ->step(0.1)
                                            ->maxValue(5)
                                            ->default(0),
                                        Forms\Components\TextInput::make('reviews_count')
                                            ->label('Reviews Count')
                                            ->numeric()
                                            ->default(0),
                                    ]),
                                ]),
                        ]),

                    // Tab 4: Media & Assets
                    Forms\Components\Tabs\Tab::make('Media & Assets')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\Section::make('Formulation Imagery & Laboratory Proofs')
                                ->schema([
                                    Forms\Components\FileUpload::make('image')
                                        ->label('Primary Image')
                                        ->image()
                                        ->directory('products')
                                        ->imageEditor()
                                        ->columnSpanFull(),
                                    Forms\Components\FileUpload::make('images')
                                        ->label('Gallery Images')
                                        ->image()
                                        ->multiple()
                                        ->reorderable()
                                        ->directory('products/gallery')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // Tab 5: Professional Section
                    Forms\Components\Tabs\Tab::make('🩺 Professional Section')
                        ->icon('heroicon-o-beaker')
                        ->schema([
                            Forms\Components\Section::make('Healthcare Professional Data')
                                ->description('Technical pharmacological data entered here will strictly render inside the verified Clinical Professional tab.')
                                ->schema([
                                    Forms\Components\Textarea::make('science_en')
                                        ->label('Scientific Background (EN)')
                                        ->rows(3),
                                    Forms\Components\Textarea::make('clinical_mechanism')
                                        ->label('Biochemical Mechanism of Action')
                                        ->rows(3)
                                        ->placeholder('Detail cellular pathways, neurotransmitter modulation, and enzymatic receptors...'),
                                    Forms\Components\Textarea::make('formula_details')
                                        ->label('Standardized Extraction Assay Details')
                                        ->rows(3)
                                        ->placeholder('Exact active bio-marker percentages and extraction solvent ratios...'),
                                    Forms\Components\Textarea::make('contraindications')
                                        ->label('Clinical Contraindications & Pharmaceutical Interactions')
                                        ->rows(3)
                                        ->placeholder('Known antagonism with anticoagulants, MAO inhibitors, etc...'),
                                    Forms\Components\Textarea::make('warnings')
                                        ->label('Specialist Precautions & Storage Specifications')
                                        ->rows(2)
                                        ->placeholder('Preservation temperatures, pediatric exclusion parameters...'),
                                ]),
                        ]),

                    // Tab 6: Inventory Thresholds
                    Forms\Components\Tabs\Tab::make('Inventory Thresholds')
                        ->icon('heroicon-o-archive-box')
                        ->schema([
                            Forms\Components\Section::make('Inventory Thresholds & Location Allocation')
                                ->schema([
                                    Forms\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('stock_online')
                                            ->label('Initial Online Stock')
                                            ->numeric()
                                            ->default(100)
                                            ->required(),
                                        Forms\Components\TextInput::make('stock_offline')
                                            ->label('Initial Flagship POS Stock')
                                            ->numeric()
                                            ->default(30)
                                            ->required(),
                                        Forms\Components\TextInput::make('low_stock_threshold')
                                            ->label('Low Stock Warning Threshold')
                                            ->numeric()
                                            ->default(15)
                                            ->required(),
                                    ]),
                                    Forms\Components\Toggle::make('enable_backorders')
                                        ->label('Allow Pre-orders when depleted')
                                        ->helperText('If enabled, customers may reserve items while a new laboratory batch is undergoing HPLC assay.'),
                                ]),
                        ]),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(asset('image.jpg')),
                Tables\Columns\TextColumn::make('name_en')
                    ->label('Product')
                    ->description(fn (Product $record) => $record->name_ar)
                    ->searchable(['name_en', 'name_ar'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU / GTIN')
                    ->description(fn (Product $record) => $record->barcode)
                    ->searchable()
                    ->copyable()
                    ->font('mono')
                    ->size('sm'),
                Tables\Columns\TextColumn::make('category.name_en')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Retail Price')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock_online')
                    ->label('Online Stock')
                    ->badge()
                    ->suffix(' units')
                    ->color(fn (Product $record): string =>
                        $record->stock_online <= $record->low_stock_threshold ? 'warning' : 'success'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_offline')
                    ->label('Offline Stock')
                    ->badge()
                    ->suffix(' units')
                    ->color(fn (Product $record): string =>
                        $record->stock_offline <= $record->low_stock_threshold ? 'danger' : 'gray'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'draft' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Health System')
                    ->relationship('category', 'name_en'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'draft' => 'Draft',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
