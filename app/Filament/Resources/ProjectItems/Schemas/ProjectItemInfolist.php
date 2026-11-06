<?php

namespace App\Filament\Resources\ProjectItems\Schemas;

use App\Filament\Resources\SecretCopy;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('project.name')
                    ->label('Project'),
                TextEntry::make('service.name'),
                TextEntry::make('environment')
                    ->badge(),
                TextEntry::make('kind')
                    ->badge(),
                TextEntry::make('value')
                    ->getStateUsing(fn ($record): ?string => SecretCopy::decryptForForm($record->value))
                    ->copyable()
                    ->columnSpanFull(),
                TextEntry::make('fingerprint')
                    ->getStateUsing(fn ($record): string => SecretCopy::fingerprint($record)),
                TextEntry::make('usage_location'),
                TextEntry::make('issued_date')
                    ->date(),
                TextEntry::make('expires_at')
                    ->date(),
                TextEntry::make('memo')
                    ->columnSpanFull(),
                TextEntry::make('tags')
                    ->columnSpanFull(),
            ]);
    }
}
