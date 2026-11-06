<?php

namespace App\Filament\Resources\InfraItems\Pages;

use App\Filament\Resources\InfraItems\InfraItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInfraItem extends EditRecord
{
    protected static string $resource = InfraItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
