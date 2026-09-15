<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExportProductResource\Pages;
use App\Models\ExportProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class ExportProductResource extends Resource
{
    protected static ?string $model = ExportProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Desa Ekspor';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk Ekspor')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('product_id')
                            ->label('Link Produk (opsional)')
                            ->relationship('product', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk Ekspor')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hs_code')
                            ->label('HS Code')
                            ->maxLength(255),
                        Forms\Components\Select::make('export_status')
                            ->label('Status Ekspor')
                            ->options([
                                'potensial' => 'Potensial',
                                'proses_ekspor' => 'Proses Ekspor',
                                'sudah_ekspor' => 'Sudah Ekspor',
                            ])
                            ->default('potensial')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Produk')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Foto Produk')
                            ->image()
                            ->disk('public')
                            ->directory('export-products')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri')
                            ->image()
                            ->disk('public')
                            ->directory('export-products/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable(),
                    ])->columns(2),

                Forms\Components\Section::make('Volume & Nilai Ekspor')
                    ->schema([
                        Forms\Components\TextInput::make('export_volume')
                            ->label('Volume Ekspor (kg/bulan)')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('export_value')
                            ->label('Nilai Ekspor (USD/bulan)')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('unit')
                            ->label('Satuan')
                            ->default('kg'),
                    ])->columns(3),

                Forms\Components\Section::make('Sertifikasi & Lisensi')
                    ->schema([
                        Forms\Components\Toggle::make('has_export_license')
                            ->label('Memiliki Izin Ekspor'),
                        Forms\Components\TagsInput::make('certifications')
                            ->label('Sertifikasi (HALAL, ORGANIC, dll)')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('destination_countries')
                            ->label('Negara Tujuan')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('contact_person')
                            ->label('Nama Kontak'),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Email')
                            ->email(),
                    ])->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
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
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Produk Ekspor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('export_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sudah_ekspor' => 'success',
                        'proses_ekspor' => 'info',
                        'potensial' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('export_value')
                    ->label('Nilai (USD)')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_export_license')
                    ->label('Izin')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('export_status')
                    ->label('Status')
                    ->options([
                        'potensial' => 'Potensial',
                        'proses_ekspor' => 'Proses Ekspor',
                        'sudah_ekspor' => 'Sudah Ekspor',
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
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExportProducts::route('/'),
            'create' => Pages\CreateExportProduct::route('/create'),
            'edit' => Pages\EditExportProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['village', 'product']);
        $user = auth()->user();
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }
        return $query;
    }
}
