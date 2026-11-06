<?php

namespace App\Filament\Resources\ProjectItems\Pages;

use App\Filament\Resources\ProjectItems\ProjectItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectItem extends EditRecord
{
    protected static string $resource = ProjectItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
