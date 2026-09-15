<?php

namespace App\Filament\Resources\ExportProductResource\Pages;

use App\Filament\Resources\ExportProductResource;
use Filament\Resources\Pages\EditRecord;

class EditExportProduct extends EditRecord
{
    protected static string $resource = ExportProductResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
