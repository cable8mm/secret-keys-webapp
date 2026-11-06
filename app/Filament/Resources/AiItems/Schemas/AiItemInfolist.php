<?php

namespace App\Filament\Resources\AiItems\Schemas;

use App\Filament\Resources\SecretCopy;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AiItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('service.name'),
                TextEntry::make('value')
                    ->getStateUsing(fn ($record): ?string => SecretCopy::decryptForForm($record->value))
                    ->copyable()
                    ->columnSpanFull(),
                TextEntry::make('fingerprint')
                    ->getStateUsing(fn ($record): string => SecretCopy::fingerprint($record)),
            ]);
    }
}
