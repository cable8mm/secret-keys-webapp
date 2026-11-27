<?php

namespace App\Filament\Resources\Credentials;

use App\Filament\Resources\Credentials\Pages\CreateCredential;
use App\Filament\Resources\Credentials\Pages\EditCredential;
use App\Filament\Resources\Credentials\Pages\ListCredentials;
use App\Filament\Resources\Credentials\Pages\ViewCredential;
use App\Models\Credential;
use App\Models\Provider;
use App\Services\CredentialValues;
use App\Support\ProviderDefinition;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CredentialResource extends Resource
{
    protected static ?string $model = Credential::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|\UnitEnum|null $navigationGroup = 'Vault';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_id')
                    ->label('Provider')
                    ->options(fn (): array => Provider::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->required()
                    ->live(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->rows(2),
                Group::make()
                    ->schema(function (Get $get): array {
                        $provider = Provider::find($get('provider_id'));

                        if ($provider === null) {
                            return [];
                        }

                        /** @var array<string, mixed> $definition */
                        $definition = json_decode((string) $provider->definition, true, 512, JSON_THROW_ON_ERROR);

                        $components = [];

                        foreach (ProviderDefinition::fields($definition) as $field) {
                            $key = $field['key'];
                            $path = "values.{$key}";

                            $component = match ($field['type']) {
                                'textarea' => Textarea::make($path)->rows(($field['multiline'] ?? false) ? 6 : 4),
                                'select' => Select::make($path)->options($field['options'] ?? []),
                                'url' => TextInput::make($path)->url(),
                                'email' => TextInput::make($path)->email(),
                                'number' => TextInput::make($path)->numeric(),
                                'secret' => TextInput::make($path)->password()->revealable(),
                                default => TextInput::make($path),
                            };

                            $components[] = $component
                                ->label($field['label'])
                                ->required((bool) ($field['required'] ?? false))
                                ->helperText($field['description'] ?? null)
                                ->placeholder($field['placeholder'] ?? null)
                                ->default($field['default'] ?? null);
                        }

                        return $components;
                    })
                    ->visible(fn (Get $get): bool => filled($get('provider_id'))),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('provider.name'),
                TextEntry::make('description'),
                Group::make()
                    ->schema(function (Credential $record): array {
                        $provider = Provider::find($record->provider_id);

                        if ($provider === null) {
                            return [];
                        }

                        /** @var array<string, mixed> $definition */
                        $definition = json_decode((string) $provider->definition, true, 512, JSON_THROW_ON_ERROR);
                        $values = app(CredentialValues::class)->decode($definition, $record->credential_values);

                        $entries = [];

                        foreach (ProviderDefinition::fields($definition) as $field) {
                            $entries[] = TextEntry::make($field['key'])
                                ->label($field['label'])
                                ->state(($field['sensitive'] ?? false) ? '••••••••' : ($values[$field['key']] ?? ''));
                        }

                        return $entries;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCredentials::route('/'),
            'create' => CreateCredential::route('/create'),
            'view' => ViewCredential::route('/{record}'),
            'edit' => EditCredential::route('/{record}/edit'),
        ];
    }
}
