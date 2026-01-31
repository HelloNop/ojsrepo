<?php

namespace App\Filament\Resources\HarvestLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HarvestLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('journal.slug')
                    ->label('Journal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'running' => 'warning',
                        'success' => 'success',
                        'partial' => 'warning',
                        'failed' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('records_processed')
                    ->label('Processed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('records_failed')
                    ->label('Failed')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),
                TextColumn::make('total_records')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('formatted_duration')
                    ->label('Duration')
                    ->placeholder('N/A')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw("TIMESTAMPDIFF(SECOND, started_at, completed_at) {$direction}");
                    }),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->started_at?->format('M d, Y H:i:s')),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'running' => 'Running',
                        'success' => 'Success',
                        'partial' => 'Partial',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('journal')
                    ->relationship('journal', 'title'),
            ])
            ->recordActions([
                // 
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('started_at', 'desc');
    }
}
