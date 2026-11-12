<?php

namespace App\Filament\Resources\Folders\Tables;

use App\Filament\Resources\Secrets\SecretResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FoldersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('note')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('url')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('archived')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('archived'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('addSecret')
                    ->iconButton()
                    ->icon('heroicon-o-plus')
                    ->tooltip('New secret in this folder')
                    ->url(fn ($record): string => SecretResource::getUrl('create', ['folder' => $record->getKey()])),
                Action::make('archive')
                    ->iconButton()
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->hidden(fn ($record): bool => (bool) $record->archived)
                    ->action(function ($record): void {
                        $record->update(['archived' => true]);

                        Notification::make()
                            ->title('Folder archived.')
                            ->success()
                            ->send();
                    }),
                Action::make('restore')
                    ->iconButton()
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->hidden(fn ($record): bool => ! (bool) $record->archived)
                    ->action(function ($record): void {
                        $record->update(['archived' => false]);

                        Notification::make()
                            ->title('Folder restored.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        if ($record->secrets()->exists() || $record->infraSecrets()->exists()) {
                            Notification::make()
                                ->title('The Folder has Secrets or Infra secrets.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Folder deleted.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
