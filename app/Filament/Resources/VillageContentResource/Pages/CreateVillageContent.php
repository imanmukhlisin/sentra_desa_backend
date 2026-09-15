<?php

namespace App\Filament\Resources\VillageContentResource\Pages;

use App\Filament\Resources\VillageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVillageContent extends CreateRecord
{
    protected static string $resource = VillageContentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
