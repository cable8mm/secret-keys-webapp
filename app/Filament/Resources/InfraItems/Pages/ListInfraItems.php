<?php

namespace App\Filament\Resources\InfraItems\Pages;

use App\Filament\Resources\InfraItems\InfraItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInfraItems extends ListRecords
{
    protected static string $resource = InfraItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
