<?php

namespace App\Filament\Resources\AiItems\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Support\CredentialSet;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AiItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('manifest_key'),
                TextEntry::make('value')
                    ->getStateUsing(fn ($record): ?string => SecretCopy::decryptForForm($record->value))
                    ->copyable()
                    ->extraAttributes(['style' => 'white-space: pre-wrap'])
                    ->visible(fn ($record): bool => $record->manifest_key === null)
                    ->columnSpanFull(),
                TextEntry::make('credentialKeys')
                    ->label('Keys')
                    ->getStateUsing(fn ($record): string => implode(', ', array_keys(CredentialSet::decode($record->value))))
                    ->visible(fn ($record): bool => $record->manifest_key !== null)
                    ->columnSpanFull(),
                TextEntry::make('url')
                    ->url(fn ($record): ?string => $record->url, shouldOpenInNewTab: true),
            ]);
    }
}
