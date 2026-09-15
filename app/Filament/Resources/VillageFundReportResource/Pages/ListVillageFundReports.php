<?php

namespace App\Filament\Resources\VillageFundReportResource\Pages;

use App\Filament\Resources\VillageFundReportResource;
use Filament\Resources\Pages\ListRecords;

class ListVillageFundReports extends ListRecords
{
    protected static string $resource = VillageFundReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
