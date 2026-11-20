<?php

use App\Filament\Resources\AiItems\AiItemResource;
use App\Filament\Resources\Folders\FolderResource;
use App\Filament\Resources\Infras\InfraResource;
use App\Filament\Resources\InfraSecrets\InfraSecretResource;
use App\Filament\Resources\Secrets\SecretResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Tags\TagResource;
use App\Models\AiItem;
use App\Models\Folder;
use App\Models\Infra;
use App\Models\InfraSecret;
use App\Models\Secret;
use App\Models\Service;
use App\Models\Tag;
use Filament\Facades\Filament;
use Filament\Livewire\GlobalSearch;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('finds records by name across all containers', function (): void {
    $folder = Folder::create(['name' => 'GlobalSearchFolder']);
    $infra = Infra::create(['name' => 'GlobalSearchInfra']);

    Secret::create(['folder_id' => $folder->id, 'name' => 'GlobalSearchSecret', 'value' => 'secret-value']);
    InfraSecret::create(['infra_id' => $infra->id, 'name' => 'GlobalSearchInfraSecret', 'value' => 'secret-value']);
    AiItem::create(['name' => 'GlobalSearchAiItem', 'value' => 'secret-value']);
    Service::create(['name' => 'GlobalSearchService']);
    Tag::create(['name' => 'GlobalSearchTag']);

    foreach ([
        SecretResource::class,
        FolderResource::class,
        InfraResource::class,
        InfraSecretResource::class,
        AiItemResource::class,
        ServiceResource::class,
        TagResource::class,
    ] as $resource) {
        expect($resource::canGloballySearch())->toBeTrue()
            ->and($resource::getGlobalSearchResults('GlobalSearch'))->not->toBeEmpty();
    }
});

it('never searches secret values', function (): void {
    $folder = Folder::create(['name' => 'SearchableFolder']);
    $infra = Infra::create(['name' => 'SearchableInfra']);

    Secret::create(['folder_id' => $folder->id, 'name' => 'NamedSecret', 'value' => 'unsearchable-needle']);
    InfraSecret::create(['infra_id' => $infra->id, 'name' => 'NamedInfraSecret', 'value' => 'unsearchable-needle']);
    AiItem::create(['name' => 'NamedAiItem', 'value' => 'unsearchable-needle']);

    foreach ([SecretResource::class, InfraSecretResource::class, AiItemResource::class] as $resource) {
        expect($resource::getGlobalSearchResults('unsearchable-needle'))->toBeEmpty();
    }
});

it('opens the matching record from a result', function (): void {
    $folder = Folder::create(['name' => 'ResultFolder']);
    $secret = Secret::create(['folder_id' => $folder->id, 'name' => 'ResultSecret', 'value' => 'secret-value']);

    $result = SecretResource::getGlobalSearchResults('ResultSecret')->first();

    expect($result->url)->toBe(SecretResource::getUrl('view', ['record' => $secret]));
});

it('keeps secrets of archived folders searchable', function (): void {
    $folder = Folder::create(['name' => 'ArchivedFolder', 'archived' => true]);
    Secret::create(['folder_id' => $folder->id, 'name' => 'ArchivedFolderSecret', 'value' => 'secret-value']);

    expect(SecretResource::getGlobalSearchResults('ArchivedFolderSecret'))->not->toBeEmpty();
});

it('renders global search results in the panel', function (): void {
    Folder::create(['name' => 'UiSearchFolder']);

    Livewire::test(GlobalSearch::class)
        ->set('search', 'UiSearchFolder')
        ->assertSee('UiSearchFolder');
});
