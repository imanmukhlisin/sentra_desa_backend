<?php

namespace App\Filament\Resources\VillageContentResource\Pages;

use App\Filament\Resources\VillageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVillageContent extends EditRecord
{
    protected static string $resource = VillageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
