<?php

namespace App\Filament\Admin\Resources\CapturedPackets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CapturedPacketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('interface'),
                TextInput::make('protocol')
                    ->required()
                    ->default('OTHER'),
                TextInput::make('src_ip'),
                TextInput::make('dst_ip'),
                TextInput::make('src_port')
                    ->numeric(),
                TextInput::make('dst_port')
                    ->numeric(),
                TextInput::make('packet_size')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('ttl')
                    ->numeric(),
                TextInput::make('flags'),
                Textarea::make('payload_preview')
                    ->columnSpanFull(),
                TextInput::make('summary'),
                TextInput::make('raw'),
            ]);
    }
}
