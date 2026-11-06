<?php

namespace App\Filament\Resources\AiItems\Tables;

use App\Filament\Resources\SecretCopy;
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
                TextColumn::make('service')
                    ->searchable(),
                TextColumn::make('fingerprint')
                    ->getStateUsing(fn ($record): string => SecretCopy::fingerprint($record))
                    ->label('Fingerprint'),
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
