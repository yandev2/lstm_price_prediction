<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(4)
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Foto')
                            ->columnSpan(1)
                            ->hiddenLabel()
                            ->image()
                            ->imageEditorMode(2)
                            ->panelAspectRatio('1:1')
                            ->maxSize(2048)
                            ->helperText('Maksimal ukuran file adalah 2 MB.')
                            ->directory(fn() => 'user/profile'),

                        Section::make()
                            ->columnSpan(3)
                            ->columns([
                                'default' => 1,
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 2,
                                'xl' => 2,
                                '2xl' => 2,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->default('12345678')
                                    ->hint('Default password 12345678'),
                                Select::make('roles')
                                    ->multiple()
                                    ->label('Role')
                                    ->relationship('roles', 'name')
                            ]),
                    ])
            ]);
    }
}
