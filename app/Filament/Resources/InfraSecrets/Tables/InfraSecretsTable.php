<?php

namespace App\Filament\Resources\InfraSecrets\Tables;

use App\Filament\Resources\SecretCopy;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InfraSecretsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('infra.name')
                    ->searchable(),
                TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable(),
                TextColumn::make('folder.name')
                    ->label('Project')
                    ->placeholder('Global')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('infra')
                    ->relationship('infra', 'name'),
                SelectFilter::make('region')
                    ->relationship('region', 'name'),
                SelectFilter::make('folder')
                    ->label('Project')
                    ->relationship('folder', 'name'),
            ])
            ->recordActions([
                SecretCopy::copyAction(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
