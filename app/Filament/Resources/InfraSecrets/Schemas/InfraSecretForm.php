<?php

namespace App\Filament\Resources\InfraSecrets\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Models\Infra;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InfraSecretForm
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
                Select::make('infra_id')
                    ->relationship('infra', 'name')
                    ->default(fn (): ?string => request()->query('infra'))
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Infra::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Infra::query()->create([
                        'name' => $data['name'],
                    ])->getKey()),
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
            ]);
    }
}
