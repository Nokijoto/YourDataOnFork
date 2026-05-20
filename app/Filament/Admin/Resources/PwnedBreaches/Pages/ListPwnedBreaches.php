<?php

namespace App\Filament\Admin\Resources\PwnedBreaches\Pages;

use App\Filament\Admin\Resources\PwnedBreaches\PwnedBreachResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPwnedBreaches extends ListRecords
{
    protected static string $resource = PwnedBreachResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
