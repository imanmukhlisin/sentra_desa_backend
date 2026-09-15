<?php

namespace App\Filament\Resources\VillagePotentialResource\Pages;

use App\Filament\Resources\VillagePotentialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVillagePotential extends CreateRecord
{
    protected static string $resource = VillagePotentialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
