<?php

namespace App\Filament\Admin\Resources\SherlockServices;

use App\Filament\Admin\Resources\SherlockServices\Pages\CreateSherlockService;
use App\Filament\Admin\Resources\SherlockServices\Pages\EditSherlockService;
use App\Filament\Admin\Resources\SherlockServices\Pages\ListSherlockServices;
use App\Filament\Admin\Resources\SherlockServices\Schemas\SherlockServiceForm;
use App\Filament\Admin\Resources\SherlockServices\Tables\SherlockServicesTable;
use App\Models\SherlockService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SherlockServiceResource extends Resource
{
    protected static ?string $model = SherlockService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static ?string $navigationLabel = 'Serwisy Sherlock';

    protected static ?string $modelLabel = 'Serwis Sherlock';

    protected static ?string $pluralModelLabel = 'Serwisy Sherlock';

    public static function form(Schema $schema): Schema
    {
        return SherlockServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SherlockServicesTable::configure($table);
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
            'index' => ListSherlockServices::route('/'),
            'create' => CreateSherlockService::route('/create'),
            'edit' => EditSherlockService::route('/{record}/edit'),
        ];
    }
}
