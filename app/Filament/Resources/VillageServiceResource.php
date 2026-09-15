<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageServiceResource\Pages;
use App\Models\VillageService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class VillageServiceResource extends Resource
{
    protected static ?string $model = VillageService::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Layanan Desa';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Layanan')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Layanan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'administrasi_kependudukan' => 'Administrasi Kependudukan',
                                'pelayanan_umum' => 'Pelayanan Umum',
                                'kesehatan' => 'Kesehatan',
                                'pendidikan' => 'Pendidikan',
                                'ekonomi' => 'Ekonomi',
                                'sosial' => 'Sosial',
                                'infrastruktur' => 'Infrastruktur',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('estimated_days')
                            ->label('Estimasi Hari Penyelesaian')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('fee')
                            ->label('Biaya Layanan')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Layanan')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('requirements')
                            ->label('Persyaratan Dokumen')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('process_steps')
                            ->label('Langkah Proses')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Kontak & Jadwal')
                    ->schema([
                        Forms\Components\TextInput::make('contact_person')
                            ->label('Petugas'),
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('office_hours')
                            ->label('Jam Operasional')
                            ->placeholder('Senin-Jumat, 08:00-15:00'),
                    ])->columns(3),

                Forms\Components\Section::make('Layanan Online')
                    ->schema([
                        Forms\Components\Toggle::make('is_online_available')
                            ->label('Tersedia Online'),
                        Forms\Components\TextInput::make('online_url')
                            ->label('URL Layanan Online')
                            ->url(),
                    ])->columns(2),

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
                Tables\Columns\TextColumn::make('name')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'administrasi_kependudukan' => 'info',
                        'kesehatan' => 'danger',
                        'pendidikan' => 'warning',
                        'ekonomi' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('fee')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_days')
                    ->label('Est. Hari')
                    ->suffix(' hari')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_online_available')
                    ->label('Online')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Penggunaan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'administrasi_kependudukan' => 'Administrasi Kependudukan',
                        'pelayanan_umum' => 'Pelayanan Umum',
                        'kesehatan' => 'Kesehatan',
                        'pendidikan' => 'Pendidikan',
                        'ekonomi' => 'Ekonomi',
                        'sosial' => 'Sosial',
                        'infrastruktur' => 'Infrastruktur',
                    ]),
                Tables\Filters\SelectFilter::make('village_id')
                    ->label('Desa')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => \App\Models\Village::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => \App\Models\Village::find($value)?->name),
                Tables\Filters\TernaryFilter::make('is_online_available')
                    ->label('Online'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillageServices::route('/'),
            'create' => Pages\CreateVillageService::route('/create'),
            'edit' => Pages\EditVillageService::route('/{record}/edit'),
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
