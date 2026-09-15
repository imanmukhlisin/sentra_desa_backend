<?php

namespace App\Filament\Resources\VillageServiceResource\Pages;

use App\Filament\Resources\VillageServiceResource;
use Filament\Resources\Pages\EditRecord;

class EditVillageService extends EditRecord
{
    protected static string $resource = VillageServiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
