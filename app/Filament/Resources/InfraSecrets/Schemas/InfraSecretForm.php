<?php

namespace App\Filament\Resources\InfraSecrets\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Models\Infra;
use App\Models\InfraRegion;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('region_id', null))
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Infra::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Infra::query()->create([
                        'name' => $data['name'],
                    ])->getKey()),
                Select::make('region_id')
                    ->label('Region')
                    ->relationship('region', 'name', modifyQueryUsing: fn ($query, Get $get) => $query->where('infra_id', $get('infra_id')))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data, Get $get): ?int {
                        $infraId = $get('infra_id');

                        if (blank($infraId)) {
                            return null;
                        }

                        return (int) InfraRegion::query()->firstOrCreate([
                            'infra_id' => (int) $infraId,
                            'name' => $data['name'],
                        ])->getKey();
                    })
                    ->createOptionAction(fn (Action $action, Get $get): Action => $action->hidden(blank($get('infra_id')))),
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
            ]);
    }
}
