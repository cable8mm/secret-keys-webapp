<?php

namespace App\Filament\Resources\Folders\Schemas;

use App\Services\GitHubRepositoryService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Throwable;

class FolderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('github_repository')
                    ->label('Import from GitHub')
                    ->options(function (): array {
                        $service = app(GitHubRepositoryService::class);

                        if (! $service->isConfigured()) {
                            return [];
                        }

                        try {
                            $repositories = $service->repositories();
                        } catch (Throwable) {
                            return [];
                        }

                        return collect($repositories)
                            ->mapWithKeys(fn (array $repository): array => [
                                $repository['full_name'] => $repository['full_name'],
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->live()
                    ->dehydrated(false)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (blank($state)) {
                            return;
                        }

                        try {
                            $repository = app(GitHubRepositoryService::class)->repositories()[$state] ?? null;
                        } catch (Throwable) {
                            return;
                        }

                        if ($repository === null) {
                            return;
                        }

                        $set('name', $repository['full_name']);
                        $set('url', $repository['html_url']);
                        $set('note', $repository['description']);
                    })
                    ->helperText('Select a repository to fill Name, URL, and Note automatically.')
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('url')
                    ->placeholder('https://…')
                    ->columnSpanFull(),
                Textarea::make('note')
                    ->columnSpanFull(),
            ]);
    }
}
