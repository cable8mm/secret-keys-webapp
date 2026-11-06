<?php

use App\Filament\Pages\ItemForm;
use App\Filament\Pages\Items;
use App\Items\AiItemManager;
use App\Items\FingerprintDeriver;
use App\Items\InfraItemManager;
use App\Items\ItemInput;
use App\Items\ItemManager;
use App\Models\AiItem;
use App\Models\InfraItem;
use App\Models\Project;
use App\Values\ValueProtector;
use Livewire\Livewire;
use Tests\Support\FakeRecordStore;

function aiManagerWith(FakeRecordStore $store): AiItemManager
{
    return new AiItemManager($store, new ValueProtector, new FingerprintDeriver);
}

function infraManagerWith(FakeRecordStore $store): InfraItemManager
{
    return new InfraItemManager($store, new ValueProtector, new FingerprintDeriver);
}

it('creates ai and infra items with name, service, and value', function () {
    $ai = aiManagerWith(new FakeRecordStore);
    $infra = infraManagerWith(new FakeRecordStore);

    expect($ai->create('OpenAI Key', 'OpenAI', 'secret-value-1234')->success)->toBeTrue()
        ->and($infra->create('Coolify Token', 'Coolify', 'secret-value-1234')->success)->toBeTrue();
});

it('rejects an ai item with an empty name or value', function () {
    $manager = aiManagerWith(new FakeRecordStore);

    expect($manager->create('', 'OpenAI', 'secret')->success)->toBeFalse()
        ->and($manager->create('Key', 'OpenAI', '  ')->success)->toBeFalse();
});

it('copies an ai item value trimmed', function () {
    $store = new FakeRecordStore;
    $manager = aiManagerWith($store);

    expect($manager->create('OpenAI Key', 'OpenAI', '  secret-value-1234  ')->success)->toBeTrue()
        ->and($manager->copyText(1))->toBe('secret-value-1234');
});

it('lists ai and infra rows together with project rows', function () {
    $project = Project::create(['name' => 'Payments']);
    $infra = Project::query()->where('name', 'Infra')->firstOrFail();
    $ai = Project::query()->where('name', 'AI')->firstOrFail();

    app(AiItemManager::class)->create('OpenAI Key', 'OpenAI', 'ai-secret-1234');
    app(InfraItemManager::class)->create('Coolify Token', 'Coolify', 'infra-secret-1234');
    app(ItemManager::class)->create(new ItemInput('API key', $project->id, '', '', 'project-secret-1234', '', '', ''));

    Livewire::test(Items::class)
        ->assertSee('OpenAI Key')
        ->assertSee('Coolify Token')
        ->assertSee('API key')
        ->assertSee('Infra')
        ->assertSee('AI');
});

it('filters the list to only infra rows when the Infra project is selected', function () {
    app(AiItemManager::class)->create('OpenAI Key', 'OpenAI', 'ai-secret-1234');
    app(InfraItemManager::class)->create('Coolify Token', 'Coolify', 'infra-secret-1234');

    $infra = Project::query()->where('name', 'Infra')->firstOrFail();

    Livewire::test(Items::class)
        ->set('projectFilter', (string) $infra->id)
        ->assertSee('Coolify Token')
        ->assertDontSee('OpenAI Key');
});

it('creates an ai item through the form with only name, value, and service', function () {
    Livewire::test(ItemForm::class)
        ->set('itemType', 'ai')
        ->set('itemName', 'OpenAI Key')
        ->set('service', 'OpenAI')
        ->set('value', 'ai-secret-1234')
        ->call('save')
        ->assertRedirect(Items::getUrl());

    $item = AiItem::query()->firstWhere('name', 'OpenAI Key');

    expect($item)->not->toBeNull()
        ->and($item->service)->toBe('OpenAI');

    Livewire::test(ItemForm::class, ['itemId' => $item->id, 'itemType' => 'ai'])
        ->assertSet('itemName', 'OpenAI Key')
        ->assertSet('service', 'OpenAI');
});

it('copies an infra item from a list row', function () {
    app(InfraItemManager::class)->create('Coolify Token', 'Coolify', '  infra-secret-1234  ');

    $item = InfraItem::query()->firstWhere('name', 'Coolify Token');

    Livewire::test(Items::class)
        ->call('copyValue', 'infra', $item->id)
        ->assertDispatched('copy-prepared', text: 'infra-secret-1234');
});

it('deletes an ai item through the form', function () {
    app(AiItemManager::class)->create('OpenAI Key', 'OpenAI', 'ai-secret-1234');

    $item = AiItem::query()->firstWhere('name', 'OpenAI Key');

    Livewire::test(ItemForm::class, ['itemId' => $item->id, 'itemType' => 'ai'])
        ->call('armDelete')
        ->call('confirmDelete')
        ->assertRedirect(Items::getUrl());

    expect(AiItem::query()->whereKey($item->id)->exists())->toBeFalse();
});
