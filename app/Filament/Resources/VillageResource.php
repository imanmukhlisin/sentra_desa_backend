<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageResource\Pages;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class VillageResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Desa / Kelurahan';

    protected static ?string $navigationGroup = 'Wilayah';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Select::make('district_id')
                            ->label('Kecamatan')
                            ->relationship('district', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode BPS')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(15),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Desa')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url(),
                    ])->columns(2),

                Forms\Components\Section::make('Profil Desa')
                    ->schema([
                        Forms\Components\TextInput::make('head_name')
                            ->label('Nama Kepala Desa')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('dusun_count')
                            ->label('Jumlah Dusun')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('vision')
                            ->label('Visi')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('mission')
                            ->label('Misi')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('history')
                            ->label('Sejarah Desa')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Desa')
                            ->image()
                            ->disk('public')
                            ->directory('villages')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->image()
                            ->disk('public')
                            ->directory('villages')
                            ->visibility('public'),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto Desa')
                            ->image()
                            ->disk('public')
                            ->directory('villages/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Geolokasi & Demografi')
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
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
                            ->label('Terverifikasi'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Unggulan'),
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
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Desa')
                    ->searchable()
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
            ->filters([
                Tables\Filters\SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\District::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\District::find($value)?->name),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Verifikasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillages::route('/'),
            'create' => Pages\CreateVillage::route('/create'),
            'edit' => Pages\EditVillage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        // Village admin hanya bisa melihat desa nya sendiri
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('id', $user->village_id);
        }
        return $query;
    }
}
