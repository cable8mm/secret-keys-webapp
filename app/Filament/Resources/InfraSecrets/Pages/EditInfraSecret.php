<?php

namespace App\Filament\Resources\InfraSecrets\Pages;

use App\Filament\Resources\InfraSecrets\InfraSecretResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInfraSecret extends EditRecord
{
    protected static string $resource = InfraSecretResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
