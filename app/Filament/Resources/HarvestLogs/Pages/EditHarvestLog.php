<?php

namespace App\Filament\Resources\HarvestLogs\Pages;

use App\Filament\Resources\HarvestLogs\HarvestLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHarvestLog extends EditRecord
{
    protected static string $resource = HarvestLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
