<?php

namespace App\Filament\Resources\VillagePotentialResource\Pages;

use App\Filament\Resources\VillagePotentialResource;
use Filament\Resources\Pages\EditRecord;

class EditVillagePotential extends EditRecord
{
    protected static string $resource = VillagePotentialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
