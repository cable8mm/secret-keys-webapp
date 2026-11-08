<?php

namespace App\Filament\Resources\ProjectItems;

use App\Filament\Resources\ProjectItems\Pages\CreateProjectItem;
use App\Filament\Resources\ProjectItems\Pages\EditProjectItem;
use App\Filament\Resources\ProjectItems\Pages\ListProjectItems;
use App\Filament\Resources\ProjectItems\Pages\ViewProjectItem;
use App\Filament\Resources\ProjectItems\Schemas\ProjectItemForm;
use App\Filament\Resources\ProjectItems\Schemas\ProjectItemInfolist;
use App\Filament\Resources\ProjectItems\Tables\ProjectItemsTable;
use App\Models\ProjectItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectItemResource extends Resource
{
    protected static ?string $navigationLabel = 'Project Items';

    protected static string|\UnitEnum|null $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 2;

    protected static ?string $model = ProjectItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ProjectItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectItemsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectItemInfolist::configure($schema);
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
            'index' => ListProjectItems::route('/'),
            'create' => CreateProjectItem::route('/create'),
            'view' => ViewProjectItem::route('/{record}'),
            'edit' => EditProjectItem::route('/{record}/edit'),
        ];
    }
}
