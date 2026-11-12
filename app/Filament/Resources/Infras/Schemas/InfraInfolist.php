<?php

namespace App\Filament\Resources\Infras\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InfraInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('note')
                    ->columnSpanFull(),
                TextEntry::make('archived')
                    ->badge(),
            ]);
    }
}
