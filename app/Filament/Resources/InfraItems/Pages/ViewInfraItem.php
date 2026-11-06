<?php

namespace App\Filament\Resources\InfraItems\Pages;

use App\Filament\Resources\InfraItems\InfraItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInfraItem extends ViewRecord
{
    protected static string $resource = InfraItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
