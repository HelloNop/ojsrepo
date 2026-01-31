<?php

namespace App\Filament\Resources\HarvestLogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HarvestLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('journal_id')
                    ->relationship('journal', 'title')
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'partial' => 'Partial',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                TextInput::make('records_processed')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('records_failed')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_records')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('error_message')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
