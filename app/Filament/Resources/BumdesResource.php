<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BumdesResource\Pages;
use App\Models\Bumdes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class BumdesResource extends Resource
{
    protected static ?string $model = Bumdes::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'BUMDes';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi BUMDes')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama BUMDes')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('director_name')
                            ->label('Nama Direktur')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('legal_number')
                            ->label('Nomor SK Pendirian'),
                        Forms\Components\DatePicker::make('established_date')
                            ->label('Tanggal Pendirian'),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),
                    ])->columns(2),

                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo BUMDes')
                            ->image()
                            ->disk('public')
                            ->directory('bumdes')
                            ->visibility('public'),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto')
                            ->image()
                            ->disk('public')
                            ->directory('bumdes/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Keuangan & Kinerja')
                    ->schema([
                        Forms\Components\TextInput::make('initial_capital')
                            ->label('Modal Awal')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('annual_revenue')
                            ->label('Omzet Tahunan')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('employee_count')
                            ->label('Jumlah Karyawan')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('performance_category')
                            ->label('Kategori Kinerja')
                            ->options([
                                'berkembang' => 'Berkembang',
                                'maju' => 'Maju',
                                'mandiri' => 'Mandiri',
                            ])
                            ->default('berkembang'),
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
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama BUMDes')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('director_name')
                    ->label('Direktur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('annual_revenue')
                    ->label('Omzet')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('performance_category')
                    ->label('Kinerja')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mandiri' => 'success',
                        'maju' => 'info',
                        'berkembang' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('performance_category')
                    ->label('Kinerja')
                    ->options([
                        'berkembang' => 'Berkembang',
                        'maju' => 'Maju',
                        'mandiri' => 'Mandiri',
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
            'index' => Pages\ListBumdes::route('/'),
            'create' => Pages\CreateBumdes::route('/create'),
            'edit' => Pages\EditBumdes::route('/{record}/edit'),
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
