<?php

namespace App\Filament\Resources\InfraItems\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InfraItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('service_id')
                    ->relationship('service', 'name', modifyQueryUsing: fn ($query) => $query->where('is_infra', true))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(table: Service::class),
                    ])
                    ->createOptionUsing(fn (array $data): int => (int) Service::query()->create([
                        'name' => $data['name'],
                        'is_infra' => true,
                    ])->getKey()),
                Textarea::make('value')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->dehydrateStateUsing(fn (?string $state): ?string => SecretCopy::encryptForStorage($state))
                    ->afterStateHydrated(function (Textarea $component, ?string $state): void {
                        $component->state(SecretCopy::decryptForForm($state));
                    }),
                TextInput::make('url')
                    ->label('URL')
                    ->columnSpanFull(),
            ]);
    }
}
