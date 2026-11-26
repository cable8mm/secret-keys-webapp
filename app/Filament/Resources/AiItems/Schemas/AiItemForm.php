<?php

namespace App\Filament\Resources\AiItems\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Support\ServiceManifest;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AiItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('definition')
                    ->label('Credential set')
                    ->options(fn (): array => ServiceManifest::setOptions('ai_items'))
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('values', [])),
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
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
            ]);
    }
}
