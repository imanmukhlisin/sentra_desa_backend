<?php

namespace App\Filament\Resources\VillageServiceResource\Pages;

use App\Filament\Resources\VillageServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVillageService extends CreateRecord
{
    protected static string $resource = VillageServiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
