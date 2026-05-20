<?php

namespace App\Filament\Admin\Resources\SherlockRules\Pages;

use App\Filament\Admin\Resources\SherlockRules\SherlockRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSherlockRule extends EditRecord
{
    protected static string $resource = SherlockRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
