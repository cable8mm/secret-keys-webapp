<?php

namespace App\Filament\Resources\Secrets\Schemas;

use App\Filament\Resources\SecretCopy;
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
                TextEntry::make('service.name'),
                TextEntry::make('environment')
                    ->badge(),
                TextEntry::make('value')
                    ->getStateUsing(fn ($record): ?string => SecretCopy::decryptForForm($record->value))
                    ->copyable()
                    ->extraAttributes(['style' => 'white-space: pre-wrap'])
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
