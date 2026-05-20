<?php

namespace App\Filament\Admin\Resources\PwnedRules\Pages;

use App\Filament\Admin\Resources\PwnedRules\PwnedRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPwnedRules extends ListRecords
{
    protected static string $resource = PwnedRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
