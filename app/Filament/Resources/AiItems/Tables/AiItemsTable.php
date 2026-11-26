<?php

namespace App\Filament\Resources\AiItems\Tables;

use App\Filament\Resources\SecretCopy;
use App\Models\AiItem;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('manifest_key')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                SecretCopy::copyAction()->hidden(fn (AiItem $record): bool => $record->manifest_key !== null),
                SecretCopy::copyKeyAction()->visible(fn (AiItem $record): bool => $record->manifest_key !== null),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
