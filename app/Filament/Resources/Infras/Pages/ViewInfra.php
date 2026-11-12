<?php

namespace App\Filament\Resources\Infras\Pages;

use App\Filament\Resources\Infras\InfraResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInfra extends ViewRecord
{
    protected static string $resource = InfraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
