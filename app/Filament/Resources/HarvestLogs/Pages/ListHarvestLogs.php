<?php

namespace App\Filament\Resources\HarvestLogs\Pages;

use App\Filament\Resources\HarvestLogs\HarvestLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHarvestLogs extends ListRecords
{
    protected static string $resource = HarvestLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
