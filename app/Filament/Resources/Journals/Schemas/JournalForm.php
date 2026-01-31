<?php

namespace App\Filament\Resources\Journals\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->description('Basic details about the journal.')
                    ->schema([
                        Select::make('publisher_id')
                            ->relationship('publisher', 'name')
                            ->required(),
                        TextInput::make('title')
                            ->required(),
                        TextInput::make('slug')
                            ->label('Singkatan')
                            ->required(),
                        TextInput::make('issn')
                            ->label('Print ISSN'),
                        TextInput::make('eissn')
                            ->label('Online ISSN'),
                        TextInput::make('website_url')
                            ->url()
                            ->required(),
                        
                    ]),
                Section::make()
                    ->description('OAI details.')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Enabled Harvesting')
                            ->required(),
                        TextInput::make('oai_base_url')
                            ->label('OAI Base URL')
                            ->url()
                            ->required(),
                        TextInput::make('set_spec')
                            ->helperText('Leave empty to harvest all sets'),
                        FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->directory('journals')
                            ->disk('public')
                            ->imageEditor()
                            ->image(),
                        Textarea::make('description')
                            ->label('Journal Description')
                            ->rows(4)
                            ->columnSpanFull(),
                        
                    ])
            ]);
    }
}
