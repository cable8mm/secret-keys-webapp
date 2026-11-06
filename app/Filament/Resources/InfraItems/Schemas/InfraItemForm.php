<?php

namespace App\Filament\Resources\InfraItems\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Items\ServiceOptions;
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
                TextInput::make('service')
                    ->datalist(ServiceOptions::all()),
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
