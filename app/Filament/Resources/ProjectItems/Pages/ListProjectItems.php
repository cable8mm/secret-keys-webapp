<?php

namespace App\Filament\Resources\ProjectItems\Pages;

use App\Filament\Resources\ProjectItems\ProjectItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectItems extends ListRecords
{
    protected static string $resource = ProjectItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
