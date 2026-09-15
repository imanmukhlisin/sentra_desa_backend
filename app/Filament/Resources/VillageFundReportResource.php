<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageFundReportResource\Pages;
use App\Models\VillageFundReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class VillageFundReportResource extends Resource
{
    protected static ?string $model = VillageFundReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'LKDD';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 8;

    protected static ?string $modelLabel = 'Laporan Keuangan Dana Desa';

    protected static ?string $pluralModelLabel = 'Laporan Keuangan Dana Desa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Laporan')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa / Kelurahan')
                            ->relationship('village', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('fiscal_year')
                            ->label('Tahun Anggaran')
                            ->numeric()
                            ->required()
                            ->default(now()->year)
                            ->minValue(2000)
                            ->maxValue(2100),

                        Forms\Components\Select::make('period')
                            ->label('Periode')
                            ->options([
                                'tahunan' => 'Tahunan',
                                'semester_1' => 'Semester 1',
                                'semester_2' => 'Semester 2',
                            ])
                            ->default('tahunan')
                            ->required(),

                        Forms\Components\TextInput::make('head_name')
                            ->label('Nama Kepala Desa/Lurah'),

                        Forms\Components\TextInput::make('head_title')
                            ->label('Jabatan')
                            ->placeholder('Kepala Desa / Lurah'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Belanja Desa')
                    ->description('Rincian belanja/pengeluaran desa')
                    ->schema([
                        Forms\Components\TextInput::make('belanja_pemerintahan')
                            ->label('Penyelenggaraan Pemerintahan Desa')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('belanja_pembangunan')
                            ->label('Pelaksanaan Pembangunan Desa')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('belanja_pembinaan')
                            ->label('Pembinaan Kemasyarakatan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('belanja_pemberdayaan')
                            ->label('Pemberdayaan Masyarakat')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('belanja_bencana')
                            ->label('Penanggulangan Bencana, Darurat & Mendesak')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pendapatan Desa')
                    ->description('Sumber-sumber pendapatan desa')
                    ->schema([
                        Forms\Components\TextInput::make('pendapatan_asli_desa')
                            ->label('Pendapatan Asli Desa (PADes)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('dana_desa')
                            ->label('Dana Desa (APBN)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('bagi_hasil_pajak')
                            ->label('Bagi Hasil Pajak & Retribusi Daerah')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('alokasi_dana_desa')
                            ->label('Alokasi Dana Desa (ADD)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('bantuan_keuangan_kab')
                            ->label('Bantuan Keuangan Kabupaten')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('bantuan_keuangan_prov')
                            ->label('Bantuan Keuangan Provinsi')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('pendapatan_lainnya')
                            ->label('Pendapatan Lain-lain')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pembiayaan')
                    ->schema([
                        Forms\Components\TextInput::make('penerimaan_pembiayaan')
                            ->label('Penerimaan Pembiayaan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('silpa')
                            ->label('SiLPA (Sisa Lebih Pembiayaan Anggaran)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Infografis & Publikasi')
                    ->schema([
                        Forms\Components\FileUpload::make('infographic_image')
                            ->label('Foto Spanduk / Infografis')
                            ->image()
                            ->directory('lkdd')
                            ->disk('public')
                            ->maxSize(5120)
                            ->imageEditor()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->helperText('Centang untuk menampilkan di halaman publik'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fiscal_year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'semester_1' => 'Semester 1',
                        'semester_2' => 'Semester 2',
                        default => 'Tahunan',
                    }),

                Tables\Columns\TextColumn::make('total_belanja')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw(
                            '(belanja_pemerintahan + belanja_pembangunan + belanja_pembinaan + belanja_pemberdayaan + belanja_bencana) ' . $direction
                        );
                    }),

                Tables\Columns\TextColumn::make('total_pendapatan')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw(
                            '(pendapatan_asli_desa + dana_desa + bagi_hasil_pajak + alokasi_dana_desa + bantuan_keuangan_kab + bantuan_keuangan_prov + pendapatan_lainnya) ' . $direction
                        );
                    }),

                Tables\Columns\TextColumn::make('head_name')
                    ->label('Kepala Desa')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fiscal_year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('fiscal_year')
                    ->label('Tahun')
                    ->options(fn () => VillageFundReport::query()
                        ->select('fiscal_year')
                        ->distinct()
                        ->orderBy('fiscal_year', 'desc')
                        ->pluck('fiscal_year', 'fiscal_year')
                        ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('village_id')
                    ->label('Desa')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\Village::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Village::find($value)?->name),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillageFundReports::route('/'),
            'create' => Pages\CreateVillageFundReport::route('/create'),
            'edit' => Pages\EditVillageFundReport::route('/{record}/edit'),
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
