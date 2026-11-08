<?php

namespace App\Filament\Resources\Secrets\Pages;

use App\Filament\Resources\Secrets\SecretResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSecret extends EditRecord
{
    protected static string $resource = SecretResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
