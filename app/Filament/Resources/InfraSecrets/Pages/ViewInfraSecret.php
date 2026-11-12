<?php

namespace App\Filament\Resources\InfraSecrets\Pages;

use App\Filament\Resources\InfraSecrets\InfraSecretResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInfraSecret extends ViewRecord
{
    protected static string $resource = InfraSecretResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
