<?php

namespace App\Filament\Resources\VillageProfileResource\Pages;

use App\Filament\Resources\VillageProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVillageProfile extends EditRecord
{
    protected static string $resource = VillageProfileResource::class;

    protected ?string $heading = 'Edit Profil Desa';

    protected function getHeaderActions(): array
    {
        return [
            // No delete — village data should not be deleted from here
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
