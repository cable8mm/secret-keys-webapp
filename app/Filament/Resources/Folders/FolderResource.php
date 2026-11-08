<?php

namespace App\Filament\Resources\Folders;

use App\Filament\Resources\Folders\Pages\CreateFolder;
use App\Filament\Resources\Folders\Pages\EditFolder;
use App\Filament\Resources\Folders\Pages\ListFolders;
use App\Filament\Resources\Folders\Pages\ViewFolder;
use App\Filament\Resources\Folders\RelationManagers\SecretsRelationManager;
use App\Filament\Resources\Folders\Schemas\FolderForm;
use App\Filament\Resources\Folders\Schemas\FolderInfolist;
use App\Filament\Resources\Folders\Tables\FoldersTable;
use App\Models\Folder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FolderResource extends Resource
{
    protected static ?string $navigationLabel = 'Folders';

    protected static string|\UnitEnum|null $navigationGroup = 'Vault';

    protected static ?int $navigationSort = 1;

    protected static ?string $model = Folder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FolderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoldersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FolderInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            SecretsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFolders::route('/'),
            'create' => CreateFolder::route('/create'),
            'view' => ViewFolder::route('/{record}'),
            'edit' => EditFolder::route('/{record}/edit'),
        ];
    }
}
