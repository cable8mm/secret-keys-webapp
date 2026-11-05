<?php

use App\Filament\Pages\Items;
use App\Filament\Pages\Projects;
use App\Models\Project;
use App\Projects\ProjectManager;
use App\Records\RecordStore;
use Livewire\Livewire;
use Tests\Support\FakeRecordStore;

it('shows the empty project state', function () {
    Livewire::test(Projects::class)
        ->assertSet('projectName', '')
        ->assertSeeHtml('data-element="EL-210"')
        ->assertDontSeeHtml('data-element="EL-203"')
        ->assertDontSeeHtml('data-element="EL-211"');
});

it('creates a project and lists it', function () {
    Livewire::test(Projects::class)
        ->set('projectName', 'alpha')
        ->call('createProject')
        ->assertSet('projectName', '')
        ->assertSee('alpha')
        ->assertSeeHtml('data-element="EL-203"')
        ->assertDontSeeHtml('data-element="EL-210"');

    expect(Project::query()->pluck('name')->all())->toBe(['alpha']);
});

it('orders listed projects case-insensitively by name', function () {
    Project::create(['name' => 'zulu']);
    Project::create(['name' => 'Alpha']);
    Project::create(['name' => 'beta']);

    Livewire::test(Projects::class)
        ->assertSeeInOrder(['Alpha', 'beta', 'zulu']);
});

it('rejects an empty project name without creating a project', function () {
    Livewire::test(Projects::class)
        ->call('createProject')
        ->assertSet('projectName', '')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-element="EL-211"')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect(Project::count())->toBe(0);
});

it('rejects a whitespace-only project name without creating a project', function () {
    Livewire::test(Projects::class)
        ->set('projectName', '   ')
        ->call('createProject')
        ->assertSet('projectName', '   ')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect(Project::count())->toBe(0);
});

it('keeps the entered name and shows a persistence error', function () {
    $store = new FakeRecordStore;
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(Projects::class)
        ->set('projectName', 'alpha')
        ->call('createProject')
        ->assertSet('projectName', 'alpha')
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-element="EL-211"')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect($store->createCalls)->toBe(1);
});

it('shows a retrieval error and hides the project list', function () {
    $store = new FakeRecordStore;
    $store->failRead = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(Projects::class)
        ->assertSee('Projects could not be loaded.')
        ->assertSeeHtml('data-element="EL-211"')
        ->assertDontSeeHtml('data-element="EL-203"');
});

it('returns to the items screen from the back control', function () {
    Livewire::test(Projects::class)
        ->call('back')
        ->assertRedirect(Items::getUrl());
});

it('lists persisted projects through a fresh manager', function () {
    Project::create(['name' => 'alpha']);

    $this->app->forgetInstance(RecordStore::class);
    $this->app->forgetInstance(ProjectManager::class);

    $result = app(ProjectManager::class)->list();

    expect($result->success)->toBeTrue()
        ->and(array_map(fn ($project): string => $project->name, $result->projects))
        ->toBe(['alpha']);
});
