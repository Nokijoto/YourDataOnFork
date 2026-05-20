<?php

namespace App\Filament\Admin\Resources\SherlockServices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SherlockServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 TextInput::make('name')
                    ->label('Nazwa serwisu')
                    ->placeholder('np. GitHub')
                    ->required()
                    ->unique(ignoreRecord: true),
                 TextInput::make('url_pattern')
                    ->label('Wzór URL')
                    ->placeholder('https://github.com/{}')
                    ->required()
                    ->helperText('Użyj {} jako symbolu zastępczego dla nazwy użytkownika (np. https://github.com/{}).'),
                 Toggle::make('is_active')
                    ->label('Aktywny')
                    ->default(true),
            ]);
    }
}
