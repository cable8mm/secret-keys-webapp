<?php

namespace App\Filament\Resources\ProjectItems\Tables;

use App\Filament\Resources\SecretCopy;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProjectItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('service')
                    ->searchable(),
                TextColumn::make('environment')
                    ->badge(),
                TextColumn::make('kind')
                    ->badge(),
                TextColumn::make('fingerprint')
                    ->getStateUsing(fn ($record): string => SecretCopy::fingerprint($record))
                    ->label('Fingerprint'),
                TextColumn::make('expires_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'name'),
                SelectFilter::make('environment')
                    ->options([
                        'live' => 'live',
                        'test' => 'test',
                        'dev' => 'dev',
                        'none' => 'none',
                    ]),
            ])
            ->recordActions([
                SecretCopy::copyAction(),
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
