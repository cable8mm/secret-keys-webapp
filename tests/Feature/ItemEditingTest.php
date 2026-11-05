<?php

use App\Filament\Pages\ItemForm;
use App\Filament\Pages\Items;
use App\Items\FingerprintDeriver;
use App\Models\Item;
use App\Models\Project;
use App\Records\ItemRecord;
use App\Records\ItemWrite;
use App\Records\ProjectSummary;
use App\Records\RecordStore;
use App\Values\ValueProtector;
use Livewire\Livewire;
use Tests\Support\FakeRecordStore;

beforeEach(function () {
    $this->project = Project::create(['name' => 'Payments']);
    $this->item = Item::create([
        'project_id' => $this->project->id,
        'name' => ' API key ',
        'service' => 'Stripe',
        'environment' => 'production',
        'value' => app(ValueProtector::class)->protect("  first line\nsecond line  "),
        'usage_location' => 'Billing worker',
        'issued_date' => '2026-11-05',
        'memo' => 'Rotated quarterly',
    ]);
});

it('opens an item in edit mode with every stored value and a masked value', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->assertSet('itemName', ' API key ')
        ->assertSet('projectId', (string) $this->project->id)
        ->assertSet('service', 'Stripe')
        ->assertSet('environment', 'production')
        ->assertSet('value', "  first line\nsecond line  ")
        ->assertSet('usageLocation', 'Billing worker')
        ->assertSet('issuedDate', '2026-11-05')
        ->assertSet('memo', 'Rotated quarterly')
        ->assertSet('revealed', false)
        ->assertSeeHtml('data-element="EL-108"')
        ->assertSeeHtml('data-element="EL-114"');
});

it('toggles the value between masked and unmasked twice', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->assertSet('revealed', false)
        ->call('toggleReveal')
        ->assertSet('revealed', true)
        ->call('toggleReveal')
        ->assertSet('revealed', false);
});

it('updates an item and changes its fingerprint', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('value', 'a brand new secret text')
        ->call('save')
        ->assertRedirect(Items::getUrl());

    $this->item->refresh();

    expect(app(ValueProtector::class)->unprotect($this->item->value))->toBe('a brand new secret text')
        ->and(app(FingerprintDeriver::class)->derive(app(ValueProtector::class)->unprotect($this->item->value)))->toBe('text');
});

it('keeps optional fields after an update', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('service', 'PayPal')
        ->set('environment', 'staging')
        ->set('usageLocation', 'Checkout')
        ->set('issuedDate', '2026-12-01')
        ->set('memo', 'Updated')
        ->call('save');

    $this->item->refresh();

    expect($this->item->service)->toBe('PayPal')
        ->and($this->item->environment)->toBe('staging')
        ->and($this->item->usage_location)->toBe('Checkout')
        ->and($this->item->issued_date?->toDateString())->toBe('2026-12-01')
        ->and($this->item->memo)->toBe('Updated');
});

it('clears the issued date when requested', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('issuedDate', '')
        ->call('save');

    $this->item->refresh();

    expect($this->item->issued_date)->toBeNull();
});

it('returns to the items screen without changing the item on cancel', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('itemName', 'changed')
        ->call('cancel')
        ->assertRedirect(Items::getUrl());

    $this->item->refresh();

    expect($this->item->name)->toBe(' API key ');
});

it('reports ERR-001 for an empty item name in edit mode', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('itemName', '   ')
        ->call('save')
        ->assertSee('Item Name is required.')
        ->assertSeeHtml('data-error-codes="ERR-001"');

    $this->item->refresh();

    expect($this->item->name)->toBe(' API key ');
});

it('reports ERR-002 for an empty value in edit mode', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('value', '   ')
        ->call('save')
        ->assertSee('Value is required.')
        ->assertSeeHtml('data-error-codes="ERR-002"');
});

it('reports ERR-003 for a missing project in edit mode', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('projectId', '999')
        ->call('save')
        ->assertSee('Project is required.')
        ->assertSeeHtml('data-error-codes="ERR-003"');
});

it('reports ERR-007 when persistence fails during update', function () {
    $store = app(FakeRecordStore::class);
    $store->projects[] = new ProjectSummary($this->project->id, 'Payments');
    foreach (Item::query()->get() as $model) {
        $store->items[] = new ItemRecord(
            $model->id,
            $model->project_id,
            $model->name,
            $model->service,
            $model->environment,
            'Payments',
            $model->value,
            $model->usage_location,
            $model->issued_date?->toDateString(),
            $model->memo,
        );
    }
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(ItemForm::class, ['itemId' => $this->item->id])
        ->set('itemName', 'changed')
        ->call('save')
        ->assertSee('The Item could not be saved.')
        ->assertSeeHtml('data-error-codes="ERR-007"');
});

it('shows a retrieval error when the item does not exist', function () {
    Livewire::test(ItemForm::class, ['itemId' => $this->item->id + 500])
        ->assertSee('Items could not be loaded.')
        ->assertDontSeeHtml('data-element="EL-101"');
});

it('shows a retrieval error when a value cannot be converted', function () {
    $this->app->instance(RecordStore::class, new class implements RecordStore
    {
        public function allProjects(): array
        {
            return [];
        }

        public function createProject(string $name): ProjectSummary
        {
            return new ProjectSummary(1, $name);
        }

        public function renameProject(int $id, string $name): void {}

        public function projectExists(int $id): bool
        {
            return true;
        }

        public function findItem(int $id): ?ItemRecord
        {
            return new ItemRecord(1, 1, 'name', null, null, 'Payments', 'not-a-valid-ciphertext', null, null, null);
        }

        public function allItems(): array
        {
            return [];
        }

        public function createItem(ItemWrite $write): int
        {
            return 1;
        }

        public function updateItem(int $id, ItemWrite $write): void {}

        public function deleteItem(int $id): void {}

        public function deleteProject(int $id): void {}
    });

    Livewire::test(ItemForm::class, ['itemId' => 1])
        ->assertSee('Items could not be loaded.')
        ->assertDontSeeHtml('data-element="EL-101"');
});
