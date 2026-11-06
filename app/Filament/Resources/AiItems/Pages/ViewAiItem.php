<?php

namespace App\Filament\Resources\AiItems\Pages;

use App\Filament\Resources\AiItems\AiItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAiItem extends ViewRecord
{
    protected static string $resource = AiItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
