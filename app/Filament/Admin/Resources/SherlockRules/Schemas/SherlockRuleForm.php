<?php

namespace App\Filament\Admin\Resources\SherlockRules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SherlockRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label('Nazwa użytkownika (nick)')
                    ->placeholder('np. admin')
                    ->required(),
                Select::make('service_id')
                    ->label('Serwis')
                    ->relationship('service', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Toggle::make('is_found')
                    ->label('Konto istnieje? (FOUND)')
                    ->default(true),
            ]);
    }
}
