<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
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

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

<<<<<<< HEAD
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Commerce & Sales';
=======
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|UnitEnum|null $navigationGroup = 'Commerce & Sales';
>>>>>>> origin/main

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Online Orders';

    protected static ?string $recordTitleAttribute = 'order_number';

<<<<<<< HEAD
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Details')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
=======
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Order Details')
                ->schema([
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('order_number')->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('invoice_number'),
                        Forms\Components\Select::make('channel')
                            ->options(['online' => 'Online', 'offline' => 'Boutique POS'])
                            ->default('online'),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(3)->schema([
=======
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('customer_name')->required(),
                        Forms\Components\TextInput::make('customer_email')->email(),
                        Forms\Components\TextInput::make('customer_phone'),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(2)->schema([
=======
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Forms\Components\DatePicker::make('date')->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Confirmed' => 'Confirmed',
                                'Processing' => 'Processing',
                                'Shipped' => 'Shipped',
                                'Delivered' => 'Delivered',
                                'Cancelled' => 'Cancelled',
                                'Returned' => 'Returned',
                            ])
                            ->required(),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(2)->schema([
=======
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('payment_method'),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'Pending' => 'Pending',
                                'Paid' => 'Paid',
                                'Refunded' => 'Refunded',
                                'Failed' => 'Failed',
                            ]),
                    ]),
                ]),
<<<<<<< HEAD
            Forms\Components\Section::make('Financials')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
=======
            Section::make('Financials')
                ->schema([
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('discount')->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('coupon_code'),
                    ]),
<<<<<<< HEAD
                    Forms\Components\Grid::make(3)->schema([
=======
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Forms\Components\TextInput::make('shipping')->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('tax')->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('total')->numeric()->prefix('$')->required(),
                    ]),
                ]),
        ]);
    }

<<<<<<< HEAD
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Order Information')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
=======
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Order Information')
                ->schema([
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('order_number')->label('Order #')->weight('bold')->copyable(),
                        Infolists\Components\TextEntry::make('invoice_number')->label('Invoice'),
                        Infolists\Components\TextEntry::make('channel')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'online' ? 'primary' : 'warning'),
                    ]),
<<<<<<< HEAD
                    Infolists\Components\Grid::make(3)->schema([
=======
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('customer_name')->label('Customer')->weight('bold'),
                        Infolists\Components\TextEntry::make('customer_email'),
                        Infolists\Components\TextEntry::make('date')->date(),
                    ]),
<<<<<<< HEAD
                    Infolists\Components\Grid::make(2)->schema([
=======
                    Grid::make(2)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Delivered' => 'success',
                                'Processing', 'Shipped' => 'primary',
                                'Confirmed' => 'info',
                                'Pending' => 'warning',
                                'Cancelled', 'Returned' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Paid' => 'success',
                                'Pending' => 'warning',
                                'Refunded' => 'info',
                                'Failed' => 'danger',
                                default => 'gray',
                            }),
                    ]),
                ]),
<<<<<<< HEAD
            Infolists\Components\Section::make('Financial Summary')
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
=======
            Section::make('Financial Summary')
                ->schema([
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('subtotal')->money('USD'),
                        Infolists\Components\TextEntry::make('discount')->money('USD'),
                        Infolists\Components\TextEntry::make('coupon_code')->placeholder('—'),
                    ]),
<<<<<<< HEAD
                    Infolists\Components\Grid::make(3)->schema([
=======
                    Grid::make(3)->schema([
>>>>>>> origin/main
                        Infolists\Components\TextEntry::make('shipping')->money('USD'),
                        Infolists\Components\TextEntry::make('tax')->money('USD'),
                        Infolists\Components\TextEntry::make('total')->money('USD')->weight('bold')->size('lg'),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->description(fn (Order $record) => $record->date?->format('M d, Y'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'online' ? 'primary' : 'warning')
                    ->formatStateUsing(fn (string $state) => $state === 'online' ? 'Online' : 'Boutique POS'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Delivered' => 'success',
                        'Processing', 'Shipped' => 'primary',
                        'Confirmed' => 'info',
                        'Pending' => 'warning',
                        'Cancelled', 'Returned' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Pending' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options(['online' => 'Online', 'offline' => 'Boutique POS']),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending', 'Confirmed' => 'Confirmed',
                        'Processing' => 'Processing', 'Shipped' => 'Shipped',
                        'Delivered' => 'Delivered', 'Cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
