<?php

namespace App\Filament\Resources\Infras\Pages;

use App\Filament\Resources\Infras\InfraResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInfra extends EditRecord
{
    protected static string $resource = InfraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
