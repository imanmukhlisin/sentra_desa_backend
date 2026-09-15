<?php

namespace App\Filament\Resources\VillageContentResource\Pages;

use App\Filament\Resources\VillageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVillageContents extends ListRecords
{
    protected static string $resource = VillageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
