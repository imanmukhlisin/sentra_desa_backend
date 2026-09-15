<?php

namespace App\Filament\Resources\BumdesResource\Pages;

use App\Filament\Resources\BumdesResource;
use Filament\Resources\Pages\EditRecord;

class EditBumdes extends EditRecord
{
    protected static string $resource = BumdesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
