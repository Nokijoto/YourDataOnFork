<?php

namespace App\Filament\Admin\Resources\CapturedPackets\Pages;

use App\Filament\Admin\Resources\CapturedPackets\CapturedPacketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCapturedPackets extends ListRecords
{
    protected static string $resource = CapturedPacketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
