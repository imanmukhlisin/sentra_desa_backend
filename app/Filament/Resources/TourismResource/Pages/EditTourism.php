<?php

namespace App\Filament\Resources\TourismResource\Pages;

use App\Filament\Resources\TourismResource;
use Filament\Resources\Pages\EditRecord;

class EditTourism extends EditRecord
{
    protected static string $resource = TourismResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
