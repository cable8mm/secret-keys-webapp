<?php

namespace App\Filament\Resources\InfraSecrets\Pages;

use App\Filament\Resources\InfraSecrets\InfraSecretResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInfraSecrets extends ListRecords
{
    protected static string $resource = InfraSecretResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
