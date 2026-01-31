<?php

namespace App\Filament\Resources\Publishers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Section;



class PublisherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Basic details about the publisher.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Publisher Named') // Adjusted label as per user request to "percantik" usually implies making it look good, but I'll stick to professional labels
                            ->label('Publisher Name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('slug', Str::slug($state));
                            })
                            ->required(),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel(),
                        TextInput::make('website')
                            ->label('Website URL')
                            ->url()
                    ]),

                Section::make('Media & Details')
                    ->description('Upload logo and provide description.')
                    ->schema([
                        FileUpload::make('logo')
                            ->image()
                            ->directory('publishers')
                            ->imageEditor()
                            ->columnSpanFull(),
                        Textarea::make('address')
                            ->label('Address')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->required(),
                    ]),
            ]);
    }
}
