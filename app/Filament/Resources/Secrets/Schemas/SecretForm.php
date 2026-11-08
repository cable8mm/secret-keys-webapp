<?php

namespace App\Filament\Resources\Secrets\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Models\Folder;
use App\Models\Service;
use App\Models\Tag;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class SecretForm
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
                Select::make('folder_id')
                    ->relationship('folder', 'name')
                    ->default(fn (): ?string => request()->query('folder'))
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Folder::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Folder::query()->create([
                        'name' => $data['name'],
                    ])->getKey()),
                Select::make('service_id')
                    ->relationship('service', 'name', modifyQueryUsing: fn ($query) => $query->where('is_secret', true))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Service::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Service::query()->create([
                        'name' => $data['name'],
                        'is_secret' => true,
                    ])->getKey()),
                Select::make('derived_from_id')
                    ->label('Source secret')
                    ->relationship('derivedFrom', 'name', modifyQueryUsing: function ($query, Select $component) {
                        $record = $component->getRecord();

                        if ($record instanceof Model && $record->exists) {
                            $query->whereKeyNot($record->getKey());
                        }

                        return $query;
                    })
                    ->searchable()
                    ->preload(),
                Select::make('environment')
                    ->default('none')
                    ->options([
                        'live' => 'live',
                        'test' => 'test',
                        'dev' => 'dev',
                        'none' => 'none',
                    ]),
                Select::make('kind')
                    ->options([
                        'token' => 'token',
                        'password' => 'password',
                        'pem' => 'pem',
                        'other' => 'other',
                    ]),
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
                DatePicker::make('expires_at'),
                Textarea::make('memo')
                    ->columnSpanFull(),
                TagsInput::make('tags')
                    ->suggestions(fn (): array => Tag::query()->pluck('name')->all())
                    ->nestedRecursiveRules(['min:1', 'max:255'])
                    ->loadStateFromRelationshipsUsing(fn (TagsInput $component, $record): mixed => $component->state($record?->tags->pluck('name')->all() ?? []))
                    ->saveRelationshipsUsing(fn ($record, $state): mixed => $record->tags()->sync(Tag::idsForNames(is_array($state) ? $state : [])))
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ]);
    }
}
