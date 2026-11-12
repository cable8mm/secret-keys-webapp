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
                    ->extraAttributes(['style' => 'white-space: pre-wrap'])
                    ->columnSpanFull(),
                TextEntry::make('url')
                    ->url(fn ($record): ?string => $record->url, shouldOpenInNewTab: true),
            ]);
    }
}
