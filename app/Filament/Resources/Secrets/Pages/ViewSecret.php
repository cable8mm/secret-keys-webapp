<?php

namespace App\Filament\Resources\Secrets\Pages;

use App\Filament\Resources\Secrets\SecretResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSecret extends ViewRecord
{
    protected static string $resource = SecretResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
