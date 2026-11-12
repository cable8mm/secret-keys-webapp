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
                TextEntry::make('infra.name')
                    ->label('Infra'),
                TextEntry::make('region.name')
                    ->label('Region'),
                TextEntry::make('value')
                    ->getStateUsing(fn ($record): ?string => SecretCopy::decryptForForm($record->value))
                    ->copyable()
                    ->extraAttributes(['style' => 'white-space: pre-wrap'])
                    ->columnSpanFull(),
                TextEntry::make('url')
                    ->url(fn ($record): ?string => $record->url, shouldOpenInNewTab: true),
            ]);
    }
}
