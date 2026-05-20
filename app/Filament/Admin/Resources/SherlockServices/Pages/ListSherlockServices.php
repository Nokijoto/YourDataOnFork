<?php

namespace App\Filament\Admin\Resources\SherlockServices\Pages;

use App\Filament\Admin\Resources\SherlockServices\SherlockServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSherlockServices extends ListRecords
{
    protected static string $resource = SherlockServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
