<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegencyResource\Pages;
use App\Models\Regency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class RegencyResource extends Resource
{
    protected static ?string $model = Regency::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Kabupaten / Kota';

    protected static ?string $navigationGroup = 'Wilayah';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kabupaten/Kota')
                    ->schema([
                        Forms\Components\Select::make('province_id')
                            ->label('Provinsi')
                            ->relationship('province', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode BPS')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kab/Kota')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'kabupaten' => 'Kabupaten',
                                'kota' => 'Kota',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kota' => 'info',
                        'kabupaten' => 'success',
                    }),
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provinsi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('districts_count')
                    ->label('Jml Kecamatan')
                    ->counts('districts')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Provinsi')
                    ->relationship('province', 'name'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'kabupaten' => 'Kabupaten',
                        'kota' => 'Kota',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function canViewAny(): bool
    {
        // Hidden from village_admin (no relevance to single-village scope)
        return auth()->user()?->user_level !== 'village_admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegencies::route('/'),
            'create' => Pages\CreateRegency::route('/create'),
            'edit' => Pages\EditRegency::route('/{record}/edit'),
        ];
    }
}
