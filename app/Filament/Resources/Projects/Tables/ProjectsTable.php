<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
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
                EditAction::make(),
                Action::make('archive')
                    ->iconButton()
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->hidden(fn ($record): bool => (bool) $record->archived)
                    ->action(function ($record): void {
                        $record->update(['archived' => true]);

                        Notification::make()
                            ->title('Project archived.')
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
                            ->title('Project restored.')
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->iconButton()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        if ($record->items()->exists()) {
                            Notification::make()
                                ->title('The Project has Items.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Project deleted.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
