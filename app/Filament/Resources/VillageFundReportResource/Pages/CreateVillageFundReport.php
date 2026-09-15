<?php

namespace App\Filament\Resources\VillageFundReportResource\Pages;

use App\Filament\Resources\VillageFundReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVillageFundReport extends CreateRecord
{
    protected static string $resource = VillageFundReportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
