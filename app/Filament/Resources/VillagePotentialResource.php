<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillagePotentialResource\Pages;
use App\Models\VillagePotential;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class VillagePotentialResource extends Resource
{
    protected static ?string $model = VillagePotential::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Potensi Desa';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Potensi')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Potensi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'pertanian' => 'Pertanian',
                                'perkebunan' => 'Perkebunan',
                                'perikanan' => 'Perikanan',
                                'peternakan' => 'Peternakan',
                                'kerajinan' => 'Kerajinan',
                                'makanan_minuman' => 'Makanan & Minuman',
                                'industri' => 'Industri',
                                'pariwisata' => 'Pariwisata',
                                'tambang' => 'Tambang',
                                'kehutanan' => 'Kehutanan',
                                'sdm' => 'SDM',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        Forms\Components\Select::make('development_status')
                            ->label('Status Pengembangan')
                            ->options([
                                'teridentifikasi' => 'Teridentifikasi',
                                'dikembangkan' => 'Dikembangkan',
                                'produktif' => 'Produktif',
                            ])
                            ->default('teridentifikasi'),
                    ])->columns(2),

                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('village-potentials')
                            ->visibility('public'),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto')
                            ->image()
                            ->disk('public')
                            ->directory('village-potentials/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Data Kuantitatif')
                    ->schema([
                        Forms\Components\TextInput::make('total_area')
                            ->label('Luas Lahan (ha)')
                            ->numeric(),
                        Forms\Components\TextInput::make('production_volume')
                            ->label('Volume Produksi (ton/tahun)')
                            ->numeric(),
                        Forms\Components\TextInput::make('economic_value')
                            ->label('Nilai Ekonomi (Rp/tahun)')
                            ->numeric()
                            ->prefix('Rp'),
                    ])->columns(3),

                Forms\Components\Section::make('Investasi')
                    ->schema([
                        Forms\Components\Toggle::make('is_investment_ready')
                            ->label('Siap Investasi'),
                        Forms\Components\Textarea::make('investment_needs')
                            ->label('Kebutuhan Investasi')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Potensi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pertanian' => 'Pertanian',
                        'perkebunan' => 'Perkebunan',
                        'perikanan' => 'Perikanan',
                        'peternakan' => 'Peternakan',
                        'kerajinan' => 'Kerajinan',
                        'makanan_minuman' => 'Makanan & Minuman',
                        'industri' => 'Industri',
                        'pariwisata' => 'Pariwisata',
                        'tambang' => 'Tambang',
                        'kehutanan' => 'Kehutanan',
                        'sdm' => 'SDM',
                        'lainnya' => 'Lainnya',
                        default => ucfirst((string) $state),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pertanian' => 'success',
                        'perikanan' => 'info',
                        'industri' => 'warning',
                        'pariwisata' => 'danger',
                        'makanan_minuman' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('economic_value')
                    ->label('Nilai Ekonomi')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('development_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'produktif' => 'success',
                        'dikembangkan' => 'info',
                        'teridentifikasi' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_investment_ready')
                    ->label('Investasi')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'pertanian' => 'Pertanian',
                        'perkebunan' => 'Perkebunan',
                        'perikanan' => 'Perikanan',
                        'peternakan' => 'Peternakan',
                        'industri' => 'Industri',
                        'pariwisata' => 'Pariwisata',
                        'tambang' => 'Tambang',
                        'kehutanan' => 'Kehutanan',
                        'sdm' => 'SDM',
                    ]),
                Tables\Filters\SelectFilter::make('development_status')
                    ->label('Status')
                    ->options([
                        'teridentifikasi' => 'Teridentifikasi',
                        'dikembangkan' => 'Dikembangkan',
                        'produktif' => 'Produktif',
                    ]),
                Tables\Filters\SelectFilter::make('village_id')
                    ->label('Desa')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\Village::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Village::find($value)?->name),
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
            'index' => Pages\ListVillagePotentials::route('/'),
            'create' => Pages\CreateVillagePotential::route('/create'),
            'edit' => Pages\EditVillagePotential::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['village']);
        $user = auth()->user();
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }
        return $query;
    }
}
