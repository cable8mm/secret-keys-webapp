<?php

namespace App\Filament\Resources\AiItems\Schemas;

use App\Filament\Resources\SecretCopy;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AiItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('service_id')
                    ->relationship('service', 'name', modifyQueryUsing: fn ($query) => $query->where('is_ai', true))
                    ->searchable()
                    ->preload(),
                Textarea::make('value')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull()
                    ->dehydrateStateUsing(fn (?string $state): ?string => SecretCopy::encryptForStorage($state))
                    ->afterStateHydrated(function (Textarea $component, ?string $state): void {
                        $component->state(SecretCopy::decryptForForm($state));
                    }),
            ]);
    }
}
