<?php

namespace App\Filament\Resources\ProjectItems\Pages;

use App\Filament\Resources\ProjectItems\ProjectItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectItem extends ViewRecord
{
    protected static string $resource = ProjectItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
