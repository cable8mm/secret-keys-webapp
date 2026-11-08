<?php

namespace App\Filament\Resources\Secrets\Pages;

use App\Filament\Resources\Secrets\SecretResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSecret extends CreateRecord
{
    protected static string $resource = SecretResource::class;
}
