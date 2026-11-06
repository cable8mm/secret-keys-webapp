<?php

namespace App\Filament\Resources\AiItems\Pages;

use App\Filament\Resources\AiItems\AiItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiItem extends EditRecord
{
    protected static string $resource = AiItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
