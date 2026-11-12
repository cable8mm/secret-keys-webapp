<?php

namespace App\Filament\Resources\Infras\RelationManagers;

use App\Filament\Resources\InfraSecrets\Schemas\InfraSecretForm;
use App\Filament\Resources\SecretCopy;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InfraSecretsRelationManager extends RelationManager
{
    protected static string $relationship = 'secrets';

    protected static bool $isLazy = false;

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return InfraSecretForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable(),
                TextColumn::make('folder.name')
                    ->label('Project')
                    ->placeholder('Global')
                    ->searchable(),
                TextColumn::make('url')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                SecretCopy::copyAction(),
                EditAction::make(),
            ]);
    }
}
