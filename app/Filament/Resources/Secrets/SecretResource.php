<?php

namespace App\Filament\Resources\Secrets;

use App\Filament\Resources\Secrets\Pages\CreateSecret;
use App\Filament\Resources\Secrets\Pages\EditSecret;
use App\Filament\Resources\Secrets\Pages\ListSecrets;
use App\Filament\Resources\Secrets\Pages\ViewSecret;
use App\Filament\Resources\Secrets\RelationManagers\DerivedSecretsRelationManager;
use App\Filament\Resources\Secrets\Schemas\SecretForm;
use App\Filament\Resources\Secrets\Schemas\SecretInfolist;
use App\Filament\Resources\Secrets\Tables\SecretsTable;
use App\Models\Secret;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SecretResource extends Resource
{
    protected static ?string $navigationLabel = 'Secrets';

    protected static string|\UnitEnum|null $navigationGroup = 'Vault';

    protected static ?int $navigationSort = 2;

    protected static ?string $model = Secret::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SecretForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SecretsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SecretInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            DerivedSecretsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecrets::route('/'),
            'create' => CreateSecret::route('/create'),
            'view' => ViewSecret::route('/{record}'),
            'edit' => EditSecret::route('/{record}/edit'),
        ];
    }
}
