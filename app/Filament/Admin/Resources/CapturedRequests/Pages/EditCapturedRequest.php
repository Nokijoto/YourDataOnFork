<?php

namespace App\Filament\Admin\Resources\CapturedRequests\Pages;

use App\Filament\Admin\Resources\CapturedRequests\CapturedRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCapturedRequest extends EditRecord
{
    protected static string $resource = CapturedRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
