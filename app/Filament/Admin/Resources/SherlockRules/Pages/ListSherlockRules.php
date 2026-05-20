<?php

namespace App\Filament\Admin\Resources\SherlockRules\Pages;

use App\Filament\Admin\Resources\SherlockRules\SherlockRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSherlockRules extends ListRecords
{
    protected static string $resource = SherlockRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
