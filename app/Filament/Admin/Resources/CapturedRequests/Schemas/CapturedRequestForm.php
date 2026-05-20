<?php

namespace App\Filament\Admin\Resources\CapturedRequests\Schemas;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CapturedRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('source')
                    ->label('Źródło')
                    ->required()
                    ->default('unknown'),

                TextInput::make('ip_address')
                    ->label('IP adres'),

                Textarea::make('user_agent')
                    ->label('User-Agent')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('referer')
                    ->label('Referer')
                    ->columnSpanFull(),

                CodeEditor::make('headers')
                    ->label('Headers')
                    ->language(Language::Json)
                    ->formatStateUsing(fn (mixed $state): string => self::prettyJson($state))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                CodeEditor::make('cookie_metadata')
                    ->label('Cookies metadata')
                    ->language(Language::Json)
                    ->formatStateUsing(fn (mixed $state): string => self::prettyJson($state))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                CodeEditor::make('payload')
                    ->label('Payload')
                    ->language(Language::Json)
                    ->formatStateUsing(fn (mixed $state): string => self::prettyJson($state))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                CodeEditor::make('request_body')
                    ->label('Body requestu')
                    ->language(Language::Json)
                    ->formatStateUsing(fn (mixed $state): string => self::prettyJson($state))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                CodeEditor::make('geo')
                    ->label('Geo')
                    ->language(Language::Json)
                    ->formatStateUsing(fn (mixed $state): string => self::prettyJson($state))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),

                TextInput::make('request_method')
                    ->label('Metoda')
                    ->required()
                    ->default('POST'),

                TextInput::make('request_url')
                    ->label('URL')
                    ->columnSpanFull()
                    ->url(),
            ]);
    }

    private static function prettyJson(mixed $state): string
    {
        if (blank($state)) {
            return '{}';
        }

        if (is_string($state)) {
            $decoded = json_decode($state, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $state = $decoded;
            }
        }

        return json_encode(
            $state,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) ?: '{}';
    }
}
