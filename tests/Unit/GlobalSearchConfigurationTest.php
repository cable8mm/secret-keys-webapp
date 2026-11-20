<?php

use App\Filament\Resources\AiItems\AiItemResource;
use App\Filament\Resources\Folders\FolderResource;
use App\Filament\Resources\Infras\InfraResource;
use App\Filament\Resources\InfraSecrets\InfraSecretResource;
use App\Filament\Resources\Secrets\SecretResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Tags\TagResource;

it('enables global search by name on every resource', function (string $resource): void {
    expect($resource::getRecordTitleAttribute())->toBe('name')
        ->and($resource::getGloballySearchableAttributes())->toBe(['name']);
})->with([
    SecretResource::class,
    FolderResource::class,
    InfraResource::class,
    InfraSecretResource::class,
    AiItemResource::class,
    ServiceResource::class,
    TagResource::class,
]);
