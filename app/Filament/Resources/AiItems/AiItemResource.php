<?php

namespace App\Filament\Resources\AiItems;

use App\Filament\Resources\AiItems\Pages\CreateAiItem;
use App\Filament\Resources\AiItems\Pages\EditAiItem;
use App\Filament\Resources\AiItems\Pages\ListAiItems;
use App\Filament\Resources\AiItems\Pages\ViewAiItem;
use App\Filament\Resources\AiItems\Schemas\AiItemForm;
use App\Filament\Resources\AiItems\Schemas\AiItemInfolist;
use App\Filament\Resources\AiItems\Tables\AiItemsTable;
use App\Models\AiItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AiItemResource extends Resource
{
    protected static ?string $navigationLabel = 'AI Items';

    protected static string|\UnitEnum|null $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 3;

    protected static ?string $model = AiItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AiItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiItemsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AiItemInfolist::configure($schema);
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
            'index' => ListAiItems::route('/'),
            'create' => CreateAiItem::route('/create'),
            'view' => ViewAiItem::route('/{record}'),
            'edit' => EditAiItem::route('/{record}/edit'),
        ];
    }
}
