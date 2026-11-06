<?php

namespace App\Filament\Resources\AiItems\Pages;

use App\Filament\Resources\AiItems\AiItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiItems extends ListRecords
{
    protected static string $resource = AiItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
