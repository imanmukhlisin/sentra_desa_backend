<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Produk UMKM'; // Sudah benar

    protected static ?string $navigationGroup = 'UMKM';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Produk')
                    ->schema([
                        Forms\Components\Select::make('merchant_id')
                            ->label('Merchant / Toko')
                            ->relationship('merchant', 'store_name')
                            ->required()
                            ->searchable()
                            ->default(fn () => auth()->user()->merchant?->id)
                            ->disabled(fn () => auth()->user()->hasRole('merchant')),

                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'makanan_minuman' => 'Makanan & Minuman',
                                'kerajinan' => 'Kerajinan',
                                'fashion' => 'Fashion',
                                'pertanian' => 'Pertanian',
                                'perikanan' => 'Perikanan',
                                'peternakan' => 'Peternakan',
                                'jasa' => 'Jasa',
                                'lainnya' => 'Lainnya',
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('discount_price')
                            ->label('Harga Diskon')
                            ->numeric()
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->default(0),

                        Forms\Components\TextInput::make('unit')
                            ->label('Satuan')
                            ->default('pcs')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('weight')
                            ->label('Berat (gram)')
                            ->numeric(),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Produk')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Produk')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->required(),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto')
                            ->image()
                            ->disk('public')
                            ->directory('products/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Tersedia')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Unggulan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('merchant.store_name')
                    ->label('Toko')
                    ->sortable(),

                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'makanan_minuman' => 'Makanan & Minuman',
                        'kerajinan' => 'Kerajinan',
                        'fashion' => 'Fashion',
                        'pertanian' => 'Pertanian',
                        'perikanan' => 'Perikanan',
                        'peternakan' => 'Peternakan',
                        'jasa' => 'Jasa',
                        'lainnya' => 'Lainnya',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pertanian' => 'success',
                        'makanan_minuman' => 'danger',
                        'kerajinan' => 'warning',
                        'fashion' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('merchant_id')
                    ->label('Filter Toko')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\Merchant::where('store_name', 'like', "%{$search}%")->limit(50)->pluck('store_name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Merchant::find($value)?->store_name),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['merchant', 'village']);
        $user = auth()->user();

        // Merchant hanya bisa lihat produk miliknya
        if ($user?->hasRole('merchant')) {
            return $query->where('merchant_id', $user->merchant?->id);
        }

        // Village admin hanya bisa lihat produk di desa nya
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }

        return $query;
    }
}