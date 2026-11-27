<?php

use App\Filament\Resources\Credentials\CredentialResource;
use App\Filament\Resources\Folders\FolderResource;
use App\Filament\Resources\Infras\InfraResource;
use App\Filament\Resources\InfraSecrets\InfraSecretResource;
use App\Filament\Resources\Providers\ProviderResource;
use App\Filament\Resources\Tags\TagResource;
use App\Models\Credential;
use App\Models\Folder;
use App\Models\Infra;
use App\Models\InfraSecret;
use App\Models\Provider;
use App\Models\Tag;
use Filament\Facades\Filament;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('finds records by name across resources', function (): void {
    $provider = Provider::where('slug', 'aws')->firstOrFail();
    Provider::create([
        'slug' => 'global-search',
        'name' => 'GlobalSearchProvider',
        'definition' => '{}',
    ]);
    $folder = Folder::create(['name' => 'GlobalSearchFolder']);
    $infra = Infra::create(['name' => 'GlobalSearchInfra']);

    Credential::create([
        'provider_id' => $provider->id,
        'name' => 'GlobalSearchCredential',
        'credential_values' => '{}',
    ]);
    InfraSecret::create(['infra_id' => $infra->id, 'name' => 'GlobalSearchInfraSecret', 'value' => 'secret-value']);
    Tag::create(['name' => 'GlobalSearchTag']);

    foreach ([
        ProviderResource::class,
        CredentialResource::class,
        FolderResource::class,
        InfraResource::class,
        InfraSecretResource::class,
        TagResource::class,
    ] as $resource) {
        expect($resource::canGloballySearch())->toBeTrue()
            ->and($resource::getGlobalSearchResults('GlobalSearch'))->not->toBeEmpty();
    }
});

it('never searches credential values', function (): void {
    $provider = Provider::where('slug', 'aws')->firstOrFail();

    Credential::create([
        'provider_id' => $provider->id,
        'name' => 'NamedCredential',
        'credential_values' => '{"access_key_id":"unsearchable-needle"}',
    ]);

    expect(CredentialResource::getGlobalSearchResults('unsearchable-needle'))->toBeEmpty();
});
