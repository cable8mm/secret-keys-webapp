<?php

namespace App\Filament\Resources\InfraItems\Tables;

use App\Filament\Resources\SecretCopy;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InfraItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('service.name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                SecretCopy::copyAction(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
