<?php

namespace App\Filament\Resources\Secrets\Tables;

use App\Filament\Resources\SecretCopy;
use App\Models\Secret;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SecretsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folder.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('manifest_key')
                    ->searchable(),
                TextColumn::make('environment')
                    ->badge(),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('folder')
                    ->relationship('folder', 'name'),
                SelectFilter::make('environment')
                    ->options([
                        'live' => 'live',
                        'test' => 'test',
                        'dev' => 'dev',
                        'none' => 'none',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                SecretCopy::copyAction()->hidden(fn (Secret $record): bool => $record->manifest_key !== null),
                SecretCopy::copyKeyAction()->visible(fn (Secret $record): bool => $record->manifest_key !== null),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
