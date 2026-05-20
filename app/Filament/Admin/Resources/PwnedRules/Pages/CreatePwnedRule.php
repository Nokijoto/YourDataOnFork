<?php

namespace App\Filament\Admin\Resources\PwnedRules\Pages;

use App\Filament\Admin\Resources\PwnedRules\PwnedRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePwnedRule extends CreateRecord
{
    protected static string $resource = PwnedRuleResource::class;
}
