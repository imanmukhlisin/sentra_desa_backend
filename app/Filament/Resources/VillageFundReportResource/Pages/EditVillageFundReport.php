<?php

namespace App\Filament\Resources\VillageFundReportResource\Pages;

use App\Filament\Resources\VillageFundReportResource;
use Filament\Resources\Pages\EditRecord;

class EditVillageFundReport extends EditRecord
{
    protected static string $resource = VillageFundReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
