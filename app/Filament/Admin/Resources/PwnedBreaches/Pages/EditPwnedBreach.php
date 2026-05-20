<?php

namespace App\Filament\Admin\Resources\PwnedBreaches\Pages;

use App\Filament\Admin\Resources\PwnedBreaches\PwnedBreachResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPwnedBreach extends EditRecord
{
    protected static string $resource = PwnedBreachResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
