<?php

namespace App\Filament\Admin\Resources\PwnedRules\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PwnedRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Adres e-mail')
                    ->placeholder('np. admin@example.com')
                    ->email()
                    ->required(),
                Select::make('breach_id')
                    ->label('Baza wycieku')
                    ->relationship('breach', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Toggle::make('is_pwned')
                    ->label('Czy wyciekł? (PWNED)')
                    ->default(true),
            ]);
    }
}
