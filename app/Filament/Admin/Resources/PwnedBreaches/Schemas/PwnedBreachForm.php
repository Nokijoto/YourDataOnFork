<?php

namespace App\Filament\Admin\Resources\PwnedBreaches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PwnedBreachForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 TextInput::make('name')
                    ->label('Nazwa bazy / wycieku')
                    ->placeholder('np. Adobe')
                    ->required()
                    ->unique(ignoreRecord: true),
                 TextInput::make('breach_date')
                    ->label('Data wycieku')
                    ->placeholder('np. Październik 2013'),
                 TextInput::make('compromised_data')
                    ->label('Skompromitowane dane')
                    ->placeholder('np. Adresy e-mail, Hasła, Podpowiedzi do haseł')
                    ->helperText('Wypisz typy danych oddzielone przecinkami.'),
                 Toggle::make('is_active')
                    ->label('Aktywny')
                    ->default(true),
            ]);
    }
}
