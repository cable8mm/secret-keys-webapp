<?php

namespace App\Filament\Resources\InfraSecrets;

use App\Filament\Resources\InfraSecrets\Pages\CreateInfraSecret;
use App\Filament\Resources\InfraSecrets\Pages\EditInfraSecret;
use App\Filament\Resources\InfraSecrets\Pages\ListInfraSecrets;
use App\Filament\Resources\InfraSecrets\Pages\ViewInfraSecret;
use App\Filament\Resources\InfraSecrets\Schemas\InfraSecretForm;
use App\Filament\Resources\InfraSecrets\Schemas\InfraSecretInfolist;
use App\Filament\Resources\InfraSecrets\Tables\InfraSecretsTable;
use App\Models\InfraSecret;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InfraSecretResource extends Resource
{
    protected static ?string $navigationLabel = 'Infra Secrets';

    protected static string|\UnitEnum|null $navigationGroup = 'Vault';

    protected static ?int $navigationSort = 5;

    protected static ?string $model = InfraSecret::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    public static function form(Schema $schema): Schema
    {
        return InfraSecretForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InfraSecretsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InfraSecretInfolist::configure($schema);
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
            'index' => ListInfraSecrets::route('/'),
            'create' => CreateInfraSecret::route('/create'),
            'view' => ViewInfraSecret::route('/{record}'),
            'edit' => EditInfraSecret::route('/{record}/edit'),
        ];
    }
}
