<?php

namespace App\Filament\Resources\BumdesResource\Pages;

use App\Filament\Resources\BumdesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBumdes extends CreateRecord
{
    protected static string $resource = BumdesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
