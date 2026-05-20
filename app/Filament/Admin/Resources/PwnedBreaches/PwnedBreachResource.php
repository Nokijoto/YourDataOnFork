<?php

namespace App\Filament\Admin\Resources\PwnedBreaches;

use App\Filament\Admin\Resources\PwnedBreaches\Pages\CreatePwnedBreach;
use App\Filament\Admin\Resources\PwnedBreaches\Pages\EditPwnedBreach;
use App\Filament\Admin\Resources\PwnedBreaches\Pages\ListPwnedBreaches;
use App\Filament\Admin\Resources\PwnedBreaches\Schemas\PwnedBreachForm;
use App\Filament\Admin\Resources\PwnedBreaches\Tables\PwnedBreachesTable;
use App\Models\PwnedBreach;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PwnedBreachResource extends Resource
{
    protected static ?string $model = PwnedBreach::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?string $navigationLabel = 'Bazy Wycieków HIBP';

    protected static ?string $modelLabel = 'Baza Wycieków';

    protected static ?string $pluralModelLabel = 'Bazy Wycieków';

    public static function form(Schema $schema): Schema
    {
        return PwnedBreachForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PwnedBreachesTable::configure($table);
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
            'index' => ListPwnedBreaches::route('/'),
            'create' => CreatePwnedBreach::route('/create'),
            'edit' => EditPwnedBreach::route('/{record}/edit'),
        ];
    }
}
