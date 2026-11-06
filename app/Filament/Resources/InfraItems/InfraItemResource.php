<?php

namespace App\Filament\Resources\InfraItems;

use App\Filament\Resources\InfraItems\Pages\CreateInfraItem;
use App\Filament\Resources\InfraItems\Pages\EditInfraItem;
use App\Filament\Resources\InfraItems\Pages\ListInfraItems;
use App\Filament\Resources\InfraItems\Schemas\InfraItemForm;
use App\Filament\Resources\InfraItems\Tables\InfraItemsTable;
use App\Models\InfraItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InfraItemResource extends Resource
{
    protected static ?string $navigationLabel = 'Infra Items';

    protected static ?int $navigationSort = 4;

    protected static ?string $model = InfraItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return InfraItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InfraItemsTable::configure($table);
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
            'index' => ListInfraItems::route('/'),
            'create' => CreateInfraItem::route('/create'),
            'edit' => EditInfraItem::route('/{record}/edit'),
        ];
    }
}
