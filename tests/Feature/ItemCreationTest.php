<?php

use App\Filament\Pages\ItemForm;
use App\Filament\Pages\Items;
use App\Filament\Pages\Projects;
use App\Items\FingerprintDeriver;
use App\Items\ItemFinder;
use App\Items\ItemInput;
use App\Items\ItemManager;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Records\ProjectSummary;
use App\Records\RecordStore;
use App\Values\ValueProtector;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\Support\FakeRecordStore;

it('opens the create form empty with every field and no EL-114', function () {
    Livewire::test(ItemForm::class)
        ->assertSet('itemName', '')
        ->assertSet('projectId', '')
        ->assertSet('value', '')
        ->assertSet('revealed', false)
        ->assertSeeHtml('data-element="EL-101"')
        ->assertSeeHtml('data-element="EL-102"')
        ->assertSeeHtml('data-element="EL-103"')
        ->assertSeeHtml('data-element="EL-104"')
        ->assertSeeHtml('data-element="EL-105"')
        ->assertSeeHtml('data-element="EL-106"')
        ->assertSeeHtml('data-element="EL-108"')
        ->assertSeeHtml('data-element="EL-109"')
        ->assertSeeHtml('data-element="EL-110"')
        ->assertSeeHtml('data-element="EL-111"')
        ->assertSeeHtml('data-element="EL-112"')
        ->assertSeeHtml('data-element="EL-113"')
        ->assertDontSeeHtml('data-element="EL-114"')
        ->assertDontSeeHtml('data-element="EL-118"');
});

it('lists fixed containers in EL-102 and shows EL-117 when no user project exists', function () {
    Livewire::test(ItemForm::class)
        ->assertSee('Infra')
        ->assertSee('AI')
        ->assertSeeHtml('data-element="EL-117"');

    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->assertSee('Payments')
        ->assertDontSeeHtml('data-element="EL-117"');
});

it('opens the form from the items screen through EL-006', function () {
    $this->get(ItemForm::getUrl())->assertOk();
});

it('creates an item with the full value and lists its fingerprint', function () {
    $project = Project::create(['name' => 'Payments']);

    $value = "  first line\nsecond line  ";

    Livewire::test(ItemForm::class)
        ->set('itemName', ' API Key ')
        ->set('projectId', (string) $project->id)
        ->set('service', 'Stripe')
        ->set('environment', 'production')
        ->set('value', $value)
        ->set('usageLocation', 'Billing worker')
        ->set('issuedDate', '2026-11-05')
        ->set('memo', 'Rotated quarterly')
        ->call('save')
        ->assertRedirect(Items::getUrl());

    expect(ProjectItem::count())->toBe(1);

    $item = ProjectItem::first();
    expect($item->name)->toBe(' API Key ')
        ->and($item->service)->toBe('Stripe')
        ->and($item->environment)->toBe('production')
        ->and($item->usage_location)->toBe('Billing worker')
        ->and($item->issued_date?->toDateString())->toBe('2026-11-05')
        ->and($item->memo)->toBe('Rotated quarterly')
        ->and(app(ValueProtector::class)->unprotect($item->value))->toBe($value);

    Livewire::test(Items::class)
        ->assertSee(' API Key ')
        ->assertSee('Stripe')
        ->assertSee('Payments')
        ->assertSee('production')
        ->assertSee(app(FingerprintDeriver::class)->derive($value));
});

it('produces an empty fingerprint for short values', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', (string) $project->id)
        ->set('value', 'short')
        ->call('save');

    $this->app->forgetInstance(RecordStore::class);
    $this->app->forgetInstance(ItemFinder::class);

    $result = app(ItemFinder::class)->list();

    expect($result->success)->toBeTrue()
        ->and($result->rows)->toHaveCount(1)
        ->and($result->rows[0]->fingerprint)->toBe('');
});

it('shows raw text is not stored in the database', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', (string) $project->id)
        ->set('value', 'super-secret-value-1234')
        ->call('save');

    $raw = DB::table('project_items')->value('value');

    expect($raw)->not->toContain('super-secret-value-1234');
});

it('reports ERR-001 for an empty item name and keeps entered input', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', '   ')
        ->set('projectId', (string) $project->id)
        ->set('value', 'secret')
        ->call('save')
        ->assertSee('Item Name is required.')
        ->assertSeeHtml('data-element="EL-118"')
        ->assertSeeHtml('data-error-codes="ERR-001"');

    expect(ProjectItem::count())->toBe(0);
});

it('reports ERR-002 for an empty or whitespace-only value', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', (string) $project->id)
        ->set('value', '   ')
        ->call('save')
        ->assertSee('Value is required.')
        ->assertSeeHtml('data-error-codes="ERR-002"');

    expect(ProjectItem::count())->toBe(0);
});

it('reports ERR-003 when no project is selected', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', '')
        ->set('value', 'secret')
        ->call('save')
        ->assertSee('Project is required.')
        ->assertSeeHtml('data-error-codes="ERR-003"');

    expect(ProjectItem::count())->toBe(0);
});

it('reports ERR-003 for a project that no longer exists', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', '999')
        ->set('value', 'secret')
        ->call('save')
        ->assertSee('Project is required.')
        ->assertSeeHtml('data-error-codes="ERR-003"');

    expect(ProjectItem::count())->toBe(0);
});

it('reports every failing condition at once', function () {
    Livewire::test(ItemForm::class)
        ->set('itemName', '')
        ->set('value', '  ')
        ->set('projectId', '')
        ->call('save')
        ->assertSee('Item Name is required.')
        ->assertSee('Value is required.')
        ->assertSee('Project is required.')
        ->assertSeeHtml('data-error-codes="ERR-001,ERR-002,ERR-003"')
        ->assertSet('itemName', '')
        ->assertSet('value', '  ')
        ->assertSet('projectId', '');
});

it('reports ERR-007 when persistence fails', function () {
    $store = new FakeRecordStore;
    $store->projects[] = new ProjectSummary(1, 'Payments');
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', '1')
        ->set('value', 'secret')
        ->call('save')
        ->assertSee('The Item could not be saved.')
        ->assertSeeHtml('data-error-codes="ERR-007"');

    expect($store->itemCreateCalls)->toBe(1)
        ->and($store->items)->toBe([]);
});

it('toggles EL-105 between masked and unmasked', function () {
    Livewire::test(ItemForm::class)
        ->assertSet('revealed', false)
        ->call('toggleReveal')
        ->assertSet('revealed', true)
        ->call('toggleReveal')
        ->assertSet('revealed', false);
});

it('returns to the items screen without storing on cancel', function () {
    Livewire::test(ItemForm::class)
        ->call('cancel')
        ->assertRedirect(Items::getUrl());
});

it('opens project management when no project exists', function () {
    Livewire::test(ItemForm::class)
        ->call('openProjects')
        ->assertRedirect(Projects::getUrl());
});

it('persists an item through a fresh finder', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', (string) $project->id)
        ->set('value', 'secret')
        ->call('save');

    $this->app->forgetInstance(RecordStore::class);
    $this->app->forgetInstance(ItemFinder::class);

    $result = app(ItemFinder::class)->list();

    expect($result->success)->toBeTrue()
        ->and($result->rows)->toHaveCount(1)
        ->and($result->rows[0]->name)->toBe('API key');
});

it('shows items of archived projects in search results', function () {
    $project = Project::create(['name' => 'Payments', 'archived' => true]);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', (string) $project->id)
        ->set('value', 'super-secret-value-1234')
        ->call('save')
        ->assertRedirect(Items::getUrl());

    Livewire::test(Items::class)
        ->assertSee('API key')
        ->assertSee('Payments');
});

it('stores the selected kind and shows it in edit mode', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'Deploy Key')
        ->set('projectId', (string) $project->id)
        ->set('kind', 'pem')
        ->set('value', "line1\r\nline2")
        ->call('save')
        ->assertRedirect(Items::getUrl());

    $item = ProjectItem::query()->firstWhere('name', 'Deploy Key');

    expect($item->kind)->toBe('pem');

    Livewire::test(ItemForm::class, ['itemId' => $item->id])
        ->assertSet('kind', 'pem');
});

it('dispatches pem-normalized text when copying from the form', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'Deploy Key')
        ->set('projectId', (string) $project->id)
        ->set('kind', 'pem')
        ->set('value', "  line1\r\nline2  ")
        ->call('copyValue')
        ->assertDispatched('copy-prepared', text: "line1\nline2\n");
});

it('dispatches pem-normalized text when copying from a list row', function () {
    $project = Project::create(['name' => 'Payments']);

    $result = app(ItemManager::class)->create(new ItemInput(
        'Deploy Key',
        $project->id,
        '',
        '',
        "  line1\r\nline2  ",
        '',
        '',
        '',
        'pem',
    ));

    expect($result->success)->toBeTrue();

    $item = ProjectItem::query()->firstWhere('name', 'Deploy Key');

    Livewire::test(Items::class)
        ->call('copyValue', 'project', $item->id)
        ->assertDispatched('copy-prepared', text: "line1\nline2\n");
});

it('shows a copy error when the row item cannot be read', function () {
    Livewire::test(Items::class)
        ->call('copyValue', 'project', 999999)
        ->assertSee('Copying failed.')
        ->assertSeeHtml('data-element="EL-010"');
});

it('stores tags and expiry date and shows them in edit mode', function () {
    $project = Project::create(['name' => 'Payments']);

    Livewire::test(ItemForm::class)
        ->set('itemName', 'API key')
        ->set('projectId', (string) $project->id)
        ->set('value', 'secret-value-1234')
        ->set('tags', 'scope:deploy, server:prod-1')
        ->set('expiresAt', '2027-01-31')
        ->call('save')
        ->assertRedirect(Items::getUrl());

    $item = ProjectItem::query()->firstWhere('name', 'API key');

    expect($item->tags)->toBe('scope:deploy, server:prod-1')
        ->and($item->expires_at?->toDateString())->toBe('2027-01-31');

    Livewire::test(ItemForm::class, ['itemId' => $item->id])
        ->assertSet('tags', 'scope:deploy, server:prod-1')
        ->assertSet('expiresAt', '2027-01-31');
});

it('offers the fixed service suggestions and environment choices', function () {
    Livewire::test(ItemForm::class)
        ->assertSeeHtml('data-element="EL-120"')
        ->assertSeeHtml('data-element="EL-121"')
        ->assertSeeHtml('<option value="live">live</option>')
        ->assertSeeHtml('<option value="test">test</option>')
        ->assertSeeHtml('<option value="dev">dev</option>')
        ->assertSeeHtml('<option value="none">none</option>')
        ->assertSeeHtml('<datalist id="service-options">')
        ->assertSeeHtml('<option value="GitHub"></option>');
});
