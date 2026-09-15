<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantResource\Pages;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\ActionsPosition;

class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationLabel = 'Data UMKM';

    protected static ?string $navigationGroup = 'UMKM';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Toko')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pemilik (User)')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->relationship('village', 'name')
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Toko')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel(),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('business_type')
                            ->label('Jenis Usaha')
                            ->options([
                                'pertanian' => 'Pertanian',
                                'peternakan' => 'Peternakan',
                                'perikanan' => 'Perikanan',
                                'kerajinan' => 'Kerajinan',
                                'kuliner' => 'Kuliner',
                                'fashion' => 'Fashion',
                                'jasa' => 'Jasa',
                                'lainnya' => 'Lainnya',
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('established_year')
                            ->label('Tahun Berdiri')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2030),
                    ])->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Toko')
                            ->image()
                            ->disk('public')
                            ->directory('merchants')
                            ->visibility('public')
                            ->imageEditor(),

                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Bukti Transfer (Rp 50.000)')
                            ->image()
                            ->disk('public')
                            ->directory('merchants/payment')
                            ->visibility('public'),
                    ])->columns(2),

                Forms\Components\Section::make('Validasi & Membership')
                    ->description('Cek bukti transfer sebelum mengubah status. Membership aktif 1 tahun sejak approved.')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Validasi')
                            ->options([
                                'pending' => 'Pending (Menunggu)',
                                'approved' => 'Approved (Aktif)',
                                'rejected' => 'Rejected (Ditolak)',
                            ])
                            ->default('pending')
                            ->required()
                            ->live(),

                        Forms\Components\DateTimePicker::make('membership_expires_at')
                            ->label('Membership Berakhir')
                            ->helperText('Otomatis diisi 1 tahun saat approve. Bisa disesuaikan manual.')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'approved'),
                    ])->columns(2),

                Forms\Components\Section::make('Perpanjangan Membership')
                    ->description('Kelola permintaan perpanjangan dari UMKM.')
                    ->schema([
                        Forms\Components\FileUpload::make('renewal_proof')
                            ->label('Bukti Transfer Perpanjangan')
                            ->image()
                            ->disk('public')
                            ->directory('merchants/renewals')
                            ->visibility('public')
                            ->disabled(),

                        Forms\Components\Select::make('renewal_status')
                            ->label('Status Perpanjangan')
                            ->options([
                                'none' => 'Tidak Ada',
                                'pending' => 'Menunggu Persetujuan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('none'),

                        Forms\Components\DateTimePicker::make('renewal_requested_at')
                            ->label('Tanggal Request')
                            ->disabled(),
                    ])->columns(3)
                    ->visible(fn (?Merchant $record) => $record && $record->renewal_status !== 'none'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('payment_proof')
                    ->label('Bukti Bayar'),

                Tables\Columns\TextColumn::make('store_name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemilik')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('membership_expires_at')
                    ->label('Membership')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color(fn (?Merchant $record) => $record?->is_membership_active ? 'success' : 'danger')
                    ->description(fn (?Merchant $record) => $record?->is_membership_active
                        ? "{$record->days_until_expiry} hari lagi"
                        : ($record?->membership_expires_at ? 'Expired' : '-')),

                Tables\Columns\TextColumn::make('renewal_status')
                    ->label('Renewal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->visible(fn () => Merchant::where('renewal_status', 'pending')->exists()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('renewal_status')
                    ->label('Renewal')
                    ->options([
                        'pending' => 'Menunggu Perpanjangan',
                        'approved' => 'Perpanjangan Disetujui',
                        'rejected' => 'Perpanjangan Ditolak',
                    ]),
                Tables\Filters\Filter::make('expired')
                    ->label('Membership Expired')
                    ->query(fn ($query) => $query->where('status', 'approved')
                        ->where('membership_expires_at', '<', now())),
            ])
            ->paginated([10, 25, 50, 100])
            ->actions([
                Tables\Actions\Action::make('approve_renewal')
                    ->label('Setujui Renewal')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Merchant $record) => $record->renewal_status === 'pending')
                    ->action(function (Merchant $record) {
                        $baseDate = $record->membership_expires_at?->isFuture()
                            ? $record->membership_expires_at
                            : now();
                        $record->update([
                            'membership_expires_at' => $baseDate->copy()->addYear(),
                            'renewal_status' => 'approved',
                        ]);
                    }),
                Tables\Actions\Action::make('reject_renewal')
                    ->label('Tolak Renewal')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Merchant $record) => $record->renewal_status === 'pending')
                    ->action(fn (Merchant $record) => $record->update(['renewal_status' => 'rejected'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMerchants::route('/'),
            'create' => Pages\CreateMerchant::route('/create'),
            'edit' => Pages\EditMerchant::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['village', 'user']);
        $user = auth()->user();
        // Village admin hanya bisa melihat merchant di desa nya
        if ($user?->user_level === 'village_admin' && $user->village_id) {
            $query->where('village_id', $user->village_id);
        }
        return $query;
    }
}