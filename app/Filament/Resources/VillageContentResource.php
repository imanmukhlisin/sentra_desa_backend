<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageContentResource\Pages;
use App\Models\VillageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Str;

class VillageContentResource extends Resource
{
    protected static ?string $model = VillageContent::class;

    // Icon di sidebar (Bisa diganti: heroicon-o-document-text, dll)
    protected static ?string $navigationIcon = 'heroicon-o-map';
    
    protected static ?string $navigationLabel = 'Berita Desa';

    protected static ?string $navigationGroup = 'Desa Kita';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Konten')
                    ->description('Masukkan detail informasi desa di bawah ini.')
                    ->schema([
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Konten')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'profil' => 'Profil Desa',
                                'informasi' => 'Informasi & Berita',
                                'pengumuman' => 'Pengumuman',
                                'kegiatan' => 'Kegiatan Desa',
                                'pembangunan' => 'Pembangunan',
                                'berkarya' => 'Desa Berkarya',
                                'belajar' => 'Desa Belajar',
                            ])
                            ->required()
                            ->searchable()
                            ->helperText('Untuk Wisata, Potensi, Ekspor, BUMDes, KDMP gunakan menu khusus di sidebar.'),

                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Konten')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'blockquote', 'bold', 'bulletList', 'codeBlock', 'h2', 'h3',
                                'italic', 'link', 'orderedList', 'redo', 'strike', 'undo',
                            ]),

                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Utama')
                            ->image()
                            ->disk('public')
                            ->directory('village-contents')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('gallery')
                            ->label('Galeri Foto')
                            ->image()
                            ->disk('public')
                            ->directory('village-contents/gallery')
                            ->visibility('public')
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(10)
                            ->columnSpanFull(),
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
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'wisata' => 'success',
                        'bumdes' => 'warning',
                        'profil' => 'info',
                        'ekspor' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'profil' => 'Profil Desa',
                        'informasi' => 'Informasi & Berita',
                        'pengumuman' => 'Pengumuman',
                        'kegiatan' => 'Kegiatan Desa',
                        'pembangunan' => 'Pembangunan',
                        'berkarya' => 'Desa Berkarya',
                        'belajar' => 'Desa Belajar',
                    ]),
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
            'index' => Pages\ListVillageContents::route('/'),
            'create' => Pages\CreateVillageContent::route('/create'),
            'edit' => Pages\EditVillageContent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }
        return $query;
    }
}