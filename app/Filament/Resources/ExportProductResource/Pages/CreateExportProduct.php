<?php

namespace App\Filament\Resources\ExportProductResource\Pages;

use App\Filament\Resources\ExportProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExportProduct extends CreateRecord
{
    protected static string $resource = ExportProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
