<?php

namespace App\Filament\Resources\KdmpResource\Pages;

use App\Filament\Resources\KdmpResource;
use Filament\Resources\Pages\EditRecord;

class EditKdmp extends EditRecord
{
    protected static string $resource = KdmpResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
