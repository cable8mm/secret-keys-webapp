<?php

namespace App\Filament\Resources\ProjectItems\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Models\Project;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('value')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->dehydrateStateUsing(fn (?string $state): ?string => SecretCopy::encryptForStorage($state))
                    ->afterStateHydrated(function (Textarea $component, ?string $state): void {
                        $component->state(SecretCopy::decryptForForm($state));
                    }),
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->default(fn (): ?string => request()->query('project'))
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Project::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Project::query()->create([
                        'name' => $data['name'],
                    ])->getKey()),
                Select::make('service_id')
                    ->relationship('service', 'name', modifyQueryUsing: fn ($query) => $query->where('is_project', true))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Service::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Service::query()->create([
                        'name' => $data['name'],
                        'is_project' => true,
                    ])->getKey()),
                Select::make('environment')
                    ->default('none')
                    ->options([
                        'live' => 'live',
                        'test' => 'test',
                        'dev' => 'dev',
                        'none' => 'none',
                    ]),
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
                DatePicker::make('expires_at'),
                Textarea::make('memo')
                    ->columnSpanFull(),
                Textarea::make('tags')
                    ->columnSpanFull(),
            ]);
    }
}
