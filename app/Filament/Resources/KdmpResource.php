<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KdmpResource\Pages;
use App\Models\Kdmp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class KdmpResource extends Resource
{
    protected static ?string $model = Kdmp::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Koperasi Desa (KDMP)';

    protected static ?string $modelLabel = 'Koperasi Desa Merah Putih';

    protected static ?string $pluralModelLabel = 'Koperasi Desa Merah Putih';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Koperasi')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Koperasi')
                            ->required()
                            ->placeholder('KopDes Merah Putih [Nama Desa]')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Registrasi')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30),
                        Forms\Components\TextInput::make('nomor_badan_hukum')
                            ->label('Nomor Badan Hukum')
                            ->nullable()
                            ->maxLength(100),
                    ])->columns(2),

                Forms\Components\Section::make('Profil Koperasi')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Foto Cover')
                            ->image()
                            ->disk('public')
                            ->directory('kdmp')
                            ->visibility('public'),
                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto')
                            ->image()
                            ->disk('public')
                            ->directory('kdmp/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
                        Forms\Components\CheckboxList::make('unit_usaha')
                            ->label('Unit Usaha')
                            ->options(Kdmp::unitUsahaOptions())
                            ->columns(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Pengurus Koperasi')
                    ->schema([
                        Forms\Components\TextInput::make('ketua_name')
                            ->label('Ketua'),
                        Forms\Components\TextInput::make('sekretaris_name')
                            ->label('Sekretaris'),
                        Forms\Components\TextInput::make('bendahara_name')
                            ->label('Bendahara'),
                    ])->columns(3),

                Forms\Components\Section::make('Data & Keuangan')
                    ->schema([
                        Forms\Components\TextInput::make('total_members')
                            ->label('Jumlah Anggota')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('modal_awal')
                            ->label('Modal Awal (Rp)')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('total_assets')
                            ->label('Total Aset (Rp)')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\DatePicker::make('established_date')
                            ->label('Tanggal Berdiri'),
                        Forms\Components\TextInput::make('address')
                            ->label('Alamat')
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel(),
                    ])->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif'       => 'Aktif',
                                'persiapan'   => 'Dalam Persiapan',
                                'tidak_aktif' => 'Tidak Aktif',
                            ])
                            ->default('persiapan')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Tampil di Aplikasi')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Koperasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_members')
                    ->label('Anggota')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif'       => 'success',
                        'persiapan'   => 'warning',
                        'tidak_aktif' => 'danger',
                        default       => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Tampil')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif'       => 'Aktif',
                        'persiapan'   => 'Dalam Persiapan',
                        'tidak_aktif' => 'Tidak Aktif',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Tampil di Aplikasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKdmps::route('/'),
            'create' => Pages\CreateKdmp::route('/create'),
            'edit'   => Pages\EditKdmp::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['village.district.regency']);
        $user  = auth()->user();
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }
        return $query;
    }
}
