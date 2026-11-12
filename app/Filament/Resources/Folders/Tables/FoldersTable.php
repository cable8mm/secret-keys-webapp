<?php

namespace App\Filament\Resources\Folders\Tables;

use App\Filament\Resources\Secrets\SecretResource;
use App\Models\Folder;
use App\Services\GitHubRepositoryService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Throwable;

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
            ->headerActions([
                Action::make('importFromGitHub')
                    ->label('Import from GitHub')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading('Import repositories from GitHub')
                    ->modalDescription('Selected repositories become folders named owner/repo.')
                    ->schema(function (): array {
                        $service = app(GitHubRepositoryService::class);

                        if (! $service->isConfigured()) {
                            return [
                                Placeholder::make('notConfigured')
                                    ->content('Set GITHUB_TOKEN in .env to import repositories.'),
                            ];
                        }

                        try {
                            $repositories = $service->repositories();
                        } catch (Throwable $exception) {
                            return [
                                Placeholder::make('failed')
                                    ->content('GitHub request failed: '.$exception->getMessage()),
                            ];
                        }

                        if ($repositories === []) {
                            return [
                                Placeholder::make('empty')
                                    ->content('No repositories found.'),
                            ];
                        }

                        return [
                            CheckboxList::make('repositories')
                                ->label('Repositories')
                                ->options(collect($repositories)
                                    ->mapWithKeys(fn (array $repository): array => [
                                        $repository['full_name'] => $repository['full_name'],
                                    ])
                                    ->all())
                                ->searchable()
                                ->bulkToggleable()
                                ->required()
                                ->columnSpanFull(),
                        ];
                    })
                    ->action(function (array $data): void {
                        $selected = array_values(array_unique($data['repositories'] ?? []));

                        if ($selected === []) {
                            return;
                        }

                        try {
                            $repositories = app(GitHubRepositoryService::class)->repositories();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('GitHub request failed.')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();

                            return;
                        }

                        $existingUrls = Folder::query()
                            ->whereIn('url', collect($selected)
                                ->map(fn (string $name): ?string => $repositories[$name]['html_url'] ?? null)
                                ->filter()
                                ->all())
                            ->pluck('url')
                            ->all();

                        $created = 0;
                        $skipped = 0;

                        foreach ($selected as $name) {
                            $repository = $repositories[$name] ?? null;

                            if (
                                ($repository === null) ||
                                (
                                    filled($repository['html_url']) &&
                                    in_array($repository['html_url'], $existingUrls, true)
                                )
                            ) {
                                $skipped++;

                                continue;
                            }

                            Folder::query()->create([
                                'name' => $repository['full_name'],
                                'url' => $repository['html_url'],
                                'note' => $repository['description'],
                            ]);

                            if (filled($repository['html_url'])) {
                                $existingUrls[] = $repository['html_url'];
                            }

                            $created++;
                        }

                        Notification::make()
                            ->title("Imported {$created} folder(s).")
                            ->body($skipped > 0 ? "Skipped {$skipped} existing folder(s)." : null)
                            ->success()
                            ->send();
                    }),
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
