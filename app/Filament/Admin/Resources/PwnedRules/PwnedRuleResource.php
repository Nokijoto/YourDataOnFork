<?php

namespace App\Filament\Admin\Resources\PwnedRules;

use App\Filament\Admin\Resources\PwnedRules\Pages\CreatePwnedRule;
use App\Filament\Admin\Resources\PwnedRules\Pages\EditPwnedRule;
use App\Filament\Admin\Resources\PwnedRules\Pages\ListPwnedRules;
use App\Filament\Admin\Resources\PwnedRules\Schemas\PwnedRuleForm;
use App\Filament\Admin\Resources\PwnedRules\Tables\PwnedRulesTable;
use App\Models\PwnedRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PwnedRuleResource extends Resource
{
    protected static ?string $model = PwnedRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static ?string $navigationLabel = 'Reguły Wycieków HIBP';

    protected static ?string $modelLabel = 'Reguła Wycieku';

    protected static ?string $pluralModelLabel = 'Reguły Wycieków';

    public static function form(Schema $schema): Schema
    {
        return PwnedRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PwnedRulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPwnedRules::route('/'),
            'create' => CreatePwnedRule::route('/create'),
            'edit' => EditPwnedRule::route('/{record}/edit'),
        ];
    }
}
