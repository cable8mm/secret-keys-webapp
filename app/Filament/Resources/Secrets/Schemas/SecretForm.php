<?php

namespace App\Filament\Resources\Secrets\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Models\Folder;
use App\Models\Tag;
use App\Support\ServiceManifest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                Select::make('definition')
                    ->label('Credential set')
                    ->options(fn (): array => ServiceManifest::setOptions())
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('values', [])),
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
                    ])
                    ->visible(fn (Get $get): bool => blank($get('definition'))),
                Textarea::make('value')
                    ->required(fn (Get $get): bool => blank($get('definition')))
                    ->visible(fn (Get $get): bool => blank($get('definition')))
                    ->dehydrated(fn (Get $get): bool => blank($get('definition')))
                    ->rows(4)
                    ->columnSpanFull()
                    ->dehydrateStateUsing(fn (?string $state): ?string => SecretCopy::encryptForStorage($state))
                    ->afterStateHydrated(function (Textarea $component, ?string $state): void {
                        $component->state(SecretCopy::decryptForForm($state));
                    }),
                Group::make()
                    ->schema(function (Get $get): array {
                        $definitionKey = $get('definition');

                        if (blank($definitionKey)) {
                            return [];
                        }

                        $definition = ServiceManifest::definition((string) $definitionKey);

                        if ($definition === null) {
                            return [];
                        }

                        if ($definition['customKeys']) {
                            return [
                                KeyValue::make('values')
                                    ->keyLabel('Key')
                                    ->valueLabel('Value')
                                    ->addButtonLabel('Add key')
                                    ->columnSpanFull(),
                            ];
                        }

                        $fields = [];

                        foreach ($definition['fields'] as $field) {
                            $fields[] = TextInput::make("values.{$field['key']}")
                                ->label($field['label'])
                                ->helperText($field['purpose'])
                                ->password()
                                ->revealable();
                        }

                        return $fields;
                    })
                    ->visible(fn (Get $get): bool => filled($get('definition'))),
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
