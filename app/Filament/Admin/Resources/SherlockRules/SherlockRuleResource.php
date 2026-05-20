<?php

namespace App\Filament\Admin\Resources\SherlockRules;

use App\Filament\Admin\Resources\SherlockRules\Pages\CreateSherlockRule;
use App\Filament\Admin\Resources\SherlockRules\Pages\EditSherlockRule;
use App\Filament\Admin\Resources\SherlockRules\Pages\ListSherlockRules;
use App\Filament\Admin\Resources\SherlockRules\Schemas\SherlockRuleForm;
use App\Filament\Admin\Resources\SherlockRules\Tables\SherlockRulesTable;
use App\Models\SherlockRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SherlockRuleResource extends Resource
{
    protected static ?string $model = SherlockRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFingerPrint;

    protected static ?string $navigationLabel = 'Reguły Sherlock';

    protected static ?string $modelLabel = 'Reguła Sherlock';

    protected static ?string $pluralModelLabel = 'Reguły Sherlock';

    public static function form(Schema $schema): Schema
    {
        return SherlockRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SherlockRulesTable::configure($table);
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
            'index' => ListSherlockRules::route('/'),
            'create' => CreateSherlockRule::route('/create'),
            'edit' => EditSherlockRule::route('/{record}/edit'),
        ];
    }
}
