<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon  = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Artikel';
    protected static ?string $navigationGroup = 'Konten & Media';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informasi Artikel')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Artikel')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        )
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug URL')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->helperText('Otomatis terisi dari judul. Edit jika perlu.'),

                    Forms\Components\Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'umum'        => 'Umum',
                            'pembangunan' => 'Pembangunan',
                            'kesehatan'   => 'Kesehatan',
                            'pendidikan'  => 'Pendidikan',
                            'ekonomi'     => 'Ekonomi & UMKM',
                            'lingkungan'  => 'Lingkungan',
                            'budaya'      => 'Seni & Budaya',
                            'teknologi'   => 'Teknologi',
                            'wisata'      => 'Pariwisata',
                            'nasional'    => 'Berita Nasional',
                        ])
                        ->required()
                        ->default('umum'),

                    Forms\Components\Select::make('village_id')
                        ->label('Desa')
                        ->relationship('village', 'name')
                        ->searchable()
                        ->nullable()
                        ->placeholder('Artikel Global (semua desa)')
                        ->helperText('Kosongkan untuk artikel global yang tidak terikat satu desa.'),

                    Forms\Components\TextInput::make('author_name')
                        ->label('Nama Penulis')
                        ->maxLength(150)
                        ->placeholder('Nama redaksi atau penulis'),

                    Forms\Components\Textarea::make('excerpt')
                        ->label('Ringkasan')
                        ->rows(3)
                        ->maxLength(500)
                        ->helperText('Ringkasan singkat untuk preview artikel (maks. 500 karakter).')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Isi Artikel')
                ->schema([
                    Forms\Components\RichEditor::make('content')
                        ->label('Konten')
                        ->required()
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('articles/attachments')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Media')
                ->schema([
                    Forms\Components\FileUpload::make('thumbnail')
                        ->label('Thumbnail / Cover')
                        ->image()
                        ->imageEditor()
                        ->directory('articles/thumbnails')
                        ->maxSize(5120)
                        ->helperText('Format: JPG, PNG, WEBP. Maks. 5 MB.'),

                    Forms\Components\FileUpload::make('gallery')
                        ->label('Galeri Foto')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->directory('articles/gallery')
                        ->maxSize(5120)
                        ->helperText('Foto-foto pendukung artikel.'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Pengaturan Publikasi')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'     => 'Draft',
                            'published' => 'Dipublikasikan',
                            'archived'  => 'Diarsipkan',
                        ])
                        ->required()
                        ->default('draft'),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Artikel Unggulan')
                        ->helperText('Tampilkan di posisi hero / banner utama.'),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Tanggal Publikasi')
                        ->nullable()
                        ->helperText('Kosongkan untuk langsung publish saat status diubah.'),

                    Forms\Components\TextInput::make('read_time')
                        ->label('Estimasi Baca (menit)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(120)
                        ->suffix('menit'),

                    Forms\Components\TagsInput::make('tags')
                        ->label('Tags')
                        ->placeholder('Tambah tag...')
                        ->columnSpanFull(),
                ])
                ->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->square()
                    ->size(56)
                    ->defaultImageUrl(fn () => null),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->title),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'umum'        => 'gray',
                        'pembangunan' => 'warning',
                        'kesehatan'   => 'success',
                        'pendidikan'  => 'info',
                        'ekonomi'     => 'warning',
                        'lingkungan'  => 'success',
                        'budaya'      => 'danger',
                        'teknologi'   => 'info',
                        'wisata'      => 'success',
                        'nasional'    => 'primary',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'umum'        => 'Umum',
                        'pembangunan' => 'Pembangunan',
                        'kesehatan'   => 'Kesehatan',
                        'pendidikan'  => 'Pendidikan',
                        'ekonomi'     => 'Ekonomi',
                        'lingkungan'  => 'Lingkungan',
                        'budaya'      => 'Budaya',
                        'teknologi'   => 'Teknologi',
                        'wisata'      => 'Wisata',
                        'nasional'    => 'Nasional',
                        default       => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'archived'  => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Publik',
                        'draft'     => 'Draft',
                        'archived'  => 'Arsip',
                        default     => $state,
                    }),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('author_name')
                    ->label('Penulis')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publikasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Belum dipublikasi'),

                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->placeholder('Global')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Dipublikasikan',
                        'archived'  => 'Diarsipkan',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'umum'        => 'Umum',
                        'pembangunan' => 'Pembangunan',
                        'kesehatan'   => 'Kesehatan',
                        'pendidikan'  => 'Pendidikan',
                        'ekonomi'     => 'Ekonomi & UMKM',
                        'lingkungan'  => 'Lingkungan',
                        'budaya'      => 'Seni & Budaya',
                        'teknologi'   => 'Teknologi',
                        'wisata'      => 'Pariwisata',
                        'nasional'    => 'Berita Nasional',
                    ]),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Unggulan'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        if ($user && $user->hasRole('village_admin') && $user->village_id) {
            return parent::getEloquentQuery()->where(function ($q) use ($user) {
                $q->where('village_id', $user->village_id)->orWhereNull('village_id');
            });
        }
        return parent::getEloquentQuery();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
