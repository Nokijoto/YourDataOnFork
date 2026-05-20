<?php

namespace App\Filament\Admin\Resources\CapturedPackets\Pages;

use App\Filament\Admin\Resources\CapturedPackets\CapturedPacketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCapturedPacket extends EditRecord
{
    protected static string $resource = CapturedPacketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
