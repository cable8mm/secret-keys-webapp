<?php

namespace App\Filament\Resources\Folders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FolderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('url'),
                TextEntry::make('note')
                    ->columnSpanFull(),
                TextEntry::make('archived')
                    ->badge(),
            ]);
    }
}
