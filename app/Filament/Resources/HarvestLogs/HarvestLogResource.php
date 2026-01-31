<?php

namespace App\Filament\Resources\HarvestLogs;

use App\Filament\Resources\HarvestLogs\Pages\CreateHarvestLog;
use App\Filament\Resources\HarvestLogs\Pages\EditHarvestLog;
use App\Filament\Resources\HarvestLogs\Pages\ListHarvestLogs;
use App\Filament\Resources\HarvestLogs\Schemas\HarvestLogForm;
use App\Filament\Resources\HarvestLogs\Tables\HarvestLogsTable;
use App\Models\HarvestLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HarvestLogResource extends Resource
{
    protected static ?string $model = HarvestLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Harvest Logs';

    // protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return HarvestLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HarvestLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHarvestLogs::route('/'),
            // Disable create and edit since logs are auto-generated
            // 'create' => CreateHarvestLog::route('/create'),
            // 'edit' => EditHarvestLog::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Logs are created automatically by harvest jobs
    }
}
