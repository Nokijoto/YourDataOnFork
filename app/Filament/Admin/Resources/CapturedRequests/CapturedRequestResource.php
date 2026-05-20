<?php

namespace App\Filament\Admin\Resources\CapturedRequests;

use App\Filament\Admin\Resources\CapturedRequests\Pages\CreateCapturedRequest;
use App\Filament\Admin\Resources\CapturedRequests\Pages\EditCapturedRequest;
use App\Filament\Admin\Resources\CapturedRequests\Pages\ListCapturedRequests;
use App\Filament\Admin\Resources\CapturedRequests\Schemas\CapturedRequestForm;
use App\Filament\Admin\Resources\CapturedRequests\Tables\CapturedRequestsTable;
use App\Models\CapturedRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CapturedRequestResource extends Resource
{
    protected static ?string $model = CapturedRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = '🔴 Przechwycone Dane';

    protected static ?string $modelLabel = 'Przechwycony Request';

    protected static ?string $pluralModelLabel = 'Przechwycone Requesty';

    protected static UnitEnum|string|null $navigationGroup = 'LIVE CAPTURE';

    public static function form(Schema $schema): Schema
    {
        return CapturedRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapturedRequestsTable::configure($table);
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
            'index' => ListCapturedRequests::route('/'),
            'create' => CreateCapturedRequest::route('/create'),
            'edit' => EditCapturedRequest::route('/{record}/edit'),
        ];
    }
}
