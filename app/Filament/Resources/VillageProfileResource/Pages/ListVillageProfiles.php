<?php

namespace App\Filament\Resources\VillageProfileResource\Pages;

use App\Filament\Resources\VillageProfileResource;
use Filament\Resources\Pages\ListRecords;

class ListVillageProfiles extends ListRecords
{
    protected static string $resource = VillageProfileResource::class;

    protected ?string $heading = 'Profil Desa';

    protected ?string $subheading = 'Kelola informasi profil desa yang akan ditampilkan di aplikasi. Pilih desa lalu klik "Edit Profil" untuk melengkapi data.';
}
