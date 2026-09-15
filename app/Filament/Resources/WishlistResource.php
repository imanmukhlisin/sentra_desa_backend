<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistResource\Pages;
use App\Models\Wishlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\Builder;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Wishlist Antar Desa';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Wishlist';

    protected static ?string $pluralModelLabel = 'Wishlist Antar Desa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Kebutuhan')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa Pemohon')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable()
                            ->default(fn () => auth()->user()?->village_id),
                        Forms\Components\TextInput::make('title')
                            ->label('Nama Barang / Kebutuhan')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Contoh: Beras organik, Kerajinan rotan, Kayu jati.'),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options(Wishlist::categoryOptions())
                            ->required()
                            ->searchable(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Kuantitas & Waktu')
                    ->schema([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('unit')
                            ->label('Satuan')
                            ->placeholder('kg, ton, buah, ...')
                            ->maxLength(32),
                        Forms\Components\DatePicker::make('needed_by')
                            ->label('Dibutuhkan Sebelum')
                            ->native(false),
                    ])->columns(3),

                Forms\Components\Section::make('Media & Status')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Referensi')
                            ->image()
                            ->disk('public')
                            ->directory('wishlists')
                            ->visibility('public'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'open' => 'Terbuka',
                                'fulfilled' => 'Terpenuhi',
                                'closed' => 'Ditutup',
                            ])
                            ->default('open')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Kebutuhan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa Pemohon')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Wishlist::categoryOptions()[$state] ?? $state),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state, Wishlist $record) =>
                        $state ? trim($state . ' ' . ($record->unit ?? '')) : '-'
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'info',
                        'fulfilled' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (Wishlist $record) => $record->statusLabel()),
                Tables\Columns\TextColumn::make('notified_count')
                    ->label('Desa Ternotifikasi')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Terbuka',
                        'fulfilled' => 'Terpenuhi',
                        'closed' => 'Ditutup',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options(Wishlist::categoryOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWishlists::route('/'),
            'create' => Pages\CreateWishlist::route('/create'),
            'edit' => Pages\EditWishlist::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Village admins only see wishlists created by their own village.
        // Superadmin & regency/province admins see all.
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', 'open')->count();
        return $count > 0 ? (string) $count : null;
    }
}
