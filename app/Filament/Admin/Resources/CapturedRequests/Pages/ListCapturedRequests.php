<?php

namespace App\Filament\Admin\Resources\CapturedRequests\Pages;

use App\Filament\Admin\Resources\CapturedRequests\CapturedRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCapturedRequests extends ListRecords
{
    protected static string $resource = CapturedRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
