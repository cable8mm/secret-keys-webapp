<?php

namespace App\Filament\Resources\InfraSecrets\Schemas;

use App\Filament\Resources\SecretCopy;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InfraSecretInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('infra.name'),
                TextEntry::make('value')
                    ->getStateUsing(fn ($record): ?string => SecretCopy::decryptForForm($record->value))
                    ->copyable()
                    ->columnSpanFull(),
                TextEntry::make('url')
                    ->url(fn ($record): ?string => $record->url, shouldOpenInNewTab: true),
            ]);
    }
}
