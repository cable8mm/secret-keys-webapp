<?php

namespace App\Filament\Resources\Secrets\Schemas;

use App\Filament\Resources\SecretCopy;
use App\Support\CredentialSet;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SecretInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('folder.name')
                    ->label('Folder'),
                TextEntry::make('derivedFrom.name')
                    ->label('Source secret'),
                TextEntry::make('manifest_key'),
                TextEntry::make('environment')
                    ->badge(),
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
                TextEntry::make('url'),
                TextEntry::make('expires_at')
                    ->date(),
                TextEntry::make('memo')
                    ->columnSpanFull(),
                TextEntry::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->columnSpanFull(),
            ]);
    }
}
