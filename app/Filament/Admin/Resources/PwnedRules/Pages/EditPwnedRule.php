<?php

namespace App\Filament\Admin\Resources\PwnedRules\Pages;

use App\Filament\Admin\Resources\PwnedRules\PwnedRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPwnedRule extends EditRecord
{
    protected static string $resource = PwnedRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
