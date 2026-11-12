<?php

namespace App\Filament\Resources\Infras;

use App\Filament\Resources\Infras\Pages\CreateInfra;
use App\Filament\Resources\Infras\Pages\EditInfra;
use App\Filament\Resources\Infras\Pages\ListInfras;
use App\Filament\Resources\Infras\Pages\ViewInfra;
use App\Filament\Resources\Infras\RelationManagers\InfraSecretsRelationManager;
use App\Filament\Resources\Infras\RelationManagers\RegionsRelationManager;
use App\Filament\Resources\Infras\Schemas\InfraForm;
use App\Filament\Resources\Infras\Schemas\InfraInfolist;
use App\Filament\Resources\Infras\Tables\InfrasTable;
use App\Models\Infra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InfraResource extends Resource
{
    protected static ?string $navigationLabel = 'Infra';

    protected static string|\UnitEnum|null $navigationGroup = 'Vault';

    protected static ?int $navigationSort = 4;

    protected static ?string $model = Infra::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    public static function form(Schema $schema): Schema
    {
        return InfraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InfrasTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InfraInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            RegionsRelationManager::class,
            InfraSecretsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInfras::route('/'),
            'create' => CreateInfra::route('/create'),
            'view' => ViewInfra::route('/{record}'),
            'edit' => EditInfra::route('/{record}/edit'),
        ];
    }
}
