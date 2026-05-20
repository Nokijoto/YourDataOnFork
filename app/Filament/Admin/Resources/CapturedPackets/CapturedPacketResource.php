<?php

namespace App\Filament\Admin\Resources\CapturedPackets;

use App\Filament\Admin\Resources\CapturedPackets\Pages\CreateCapturedPacket;
use App\Filament\Admin\Resources\CapturedPackets\Pages\EditCapturedPacket;
use App\Filament\Admin\Resources\CapturedPackets\Pages\ListCapturedPackets;
use App\Filament\Admin\Resources\CapturedPackets\Schemas\CapturedPacketForm;
use App\Filament\Admin\Resources\CapturedPackets\Tables\CapturedPacketsTable;
use App\Models\CapturedPacket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CapturedPacketResource extends Resource
{
    protected static ?string $model = CapturedPacket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?string $navigationLabel = '📡 Pakiety Sieciowe';

    protected static ?string $modelLabel = 'Pakiet Sieciowy';

    protected static ?string $pluralModelLabel = 'Pakiety Sieciowe';

    protected static UnitEnum|string|null $navigationGroup = 'LIVE CAPTURE';

    public static function form(Schema $schema): Schema
    {
        return CapturedPacketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapturedPacketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCapturedPackets::route('/'),
            'create' => CreateCapturedPacket::route('/create'),
            'edit' => EditCapturedPacket::route('/{record}/edit'),
        ];
    }
}
