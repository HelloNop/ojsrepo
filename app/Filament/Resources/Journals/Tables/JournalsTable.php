<?php

namespace App\Filament\Resources\Journals\Tables;

use App\Jobs\HarvestJournalJob;
use App\Models\Journal;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
class JournalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s') // Auto-refresh table every 10s checks for status updates
            ->columns([
                ImageColumn::make('publisher.logo'),
                TextColumn::make('slug')
                    ->label('Journal')
                    ->searchable(),
                TextColumn::make('oai_base_url')
                    ->label('OAI URL')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('last_harvested_at')
                    ->dateTime()
                    ->since()
                    ->sortable(),
                TextColumn::make('articles_count')
                    ->counts('articles')
                    ->label('Total Articles')
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('harvest_status')
                    ->state(fn (Journal $record) => $record->isHarvesting() ? 'Running' : 'Idle')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Running' => 'warning',
                        'Idle' => 'gray',
                    }),
                ToggleColumn::make('enabled'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make()
                // ->button()
                // ->color('primary'),
                Action::make('harvest')
                    ->label('Harvest Now')
                    ->icon('heroicon-o-arrow-path')
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Start Manual Harvest')
                    ->modalDescription('Are you sure you want to trigger a harvest for this journal immediately? This will run in the background.')
                    ->hidden(fn (Journal $record) => ! $record->enabled)
                    ->disabled(fn (Journal $record) => ! $record->canStartHarvest())
                    ->action(function (Journal $record) {
                        HarvestJournalJob::dispatch($record);
                        Notification::make()
                            ->title('Harvest Job Dispatched')
                            ->body('The harvest process has started in the background.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                // 
            ]);
    }
}
