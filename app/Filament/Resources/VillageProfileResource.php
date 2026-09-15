<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageProfileResource\Pages;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

/**
 * Resource khusus untuk mengelola Profil Desa.
 * Menampilkan desa yang sudah terverifikasi agar admin bisa mengedit
 * informasi profil (deskripsi, visi-misi, foto, dll).
 *
 * Data ini akan muncul di halaman "Profil Desa" pada Flutter.
 */
class VillageProfileResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Profil Desa';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Profil Desa';

    protected static ?string $pluralModelLabel = 'Profil Desa';

    protected static ?string $slug = 'profil-desa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Desa')
                    ->description('Data dasar desa — beberapa field tidak bisa diubah di sini.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Desa')
                            ->required()
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode BPS')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('district.regency.province.name')
                            ->label('Provinsi')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('district.regency.name')
                            ->label('Kabupaten / Kota')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('district.name')
                            ->label('Kecamatan')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Forms\Components\Section::make('Profil & Deskripsi')
                    ->description('Informasi ini akan ditampilkan di halaman Profil Desa pada aplikasi.')
                    ->schema([
                        Forms\Components\TextInput::make('head_name')
                            ->label('Nama Kepala Desa')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('dusun_count')
                            ->label('Jumlah Dusun')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Desa')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('vision')
                            ->label('Visi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('mission')
                            ->label('Misi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('history')
                            ->label('Sejarah Desa')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Foto & Media')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Desa')
                            ->image()
                            ->disk('public')
                            ->directory('villages')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Foto Cover')
                            ->image()
                            ->disk('public')
                            ->directory('villages')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto')
                            ->image()
                            ->disk('public')
                            ->directory('villages/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Kontak & Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->placeholder('https://namadesamu.co.id')
                            ->helperText('Wajib diawali https:// atau http://. Contoh: https://desa-samusa.co.id')
                            ->dehydrateStateUsing(fn ($state) => $state
                                ? (str_starts_with($state, 'http') ? $state : 'https://'.$state)
                                : null
                            )
                            ->rules(['nullable', 'url']),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
                    ])->columns(3),

                Forms\Components\Section::make('Demografi')
                    ->schema([
                        Forms\Components\TextInput::make('population')
                            ->label('Jumlah Penduduk')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('area_size')
                            ->label('Luas Wilayah (km²)')
                            ->numeric(),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Terverifikasi')
                            ->helperText('Desa terverifikasi akan tampil di aplikasi Flutter.'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Desa Unggulan')
                            ->helperText('Desa unggulan ditampilkan di beranda.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Desa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('head_name')
                    ->label('Kepala Desa')
                    ->searchable()
                    ->placeholder('- belum diisi -'),
                Tables\Columns\TextColumn::make('district.regency.name')
                    ->label('Kabupaten')
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('population')
                    ->label('Penduduk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verifikasi')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
            ])
            ->defaultSort('is_verified', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verifikasi'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Unggulan'),
                Tables\Filters\Filter::make('has_profile')
                    ->label('Sudah ada profil')
                    ->query(fn ($query) => $query->whereNotNull('description')->where('description', '!=', '')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Profil'),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillageProfiles::route('/'),
            'edit' => Pages\EditVillageProfile::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['district.regency.province']);
        $user = auth()->user();
        // Village admin hanya bisa melihat dan mengedit desa miliknya
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('id', $user->village_id);
        }
        return $query;
    }
}
