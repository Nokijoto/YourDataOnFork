<?php

namespace App\Filament\Admin\Resources\SherlockServices\Pages;

use App\Filament\Admin\Resources\SherlockServices\SherlockServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSherlockService extends EditRecord
{
    protected static string $resource = SherlockServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
