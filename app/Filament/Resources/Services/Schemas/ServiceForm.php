<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('icon')
                    ->label('Icon URL')
                    ->columnSpanFull(),
                Toggle::make('is_project')
                    ->label('Show in Projects'),
                Toggle::make('is_ai')
                    ->label('Show in AI'),
                Toggle::make('is_infra')
                    ->label('Show in Infra'),
            ]);
    }
}
