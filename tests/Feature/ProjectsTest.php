<?php

use App\Filament\Pages\Items;
use App\Filament\Pages\Projects;
use App\Models\Project;
use App\Projects\ProjectManager;
use App\Records\ProjectSummary;
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

it('shows each project name in an editable row', function () {
    Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->assertSet('projects.0.editedName', 'alpha')
        ->assertSeeHtml('data-element="EL-204"')
        ->assertSeeHtml('data-element="EL-205"');
});

it('renames a project and lists the new name', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->set('projects.0.editedName', 'beta')
        ->call('renameProject', $project->id)
        ->assertSet('projects.0.name', 'beta')
        ->assertSet('projects.0.editedName', 'beta')
        ->assertDontSeeHtml('data-element="EL-211"');

    expect($project->fresh()?->name)->toBe('beta');
});

it('rejects an empty rename and keeps the stored name', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->set('projects.0.editedName', '')
        ->call('renameProject', $project->id)
        ->assertSet('projects.0.editedName', '')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect($project->fresh()?->name)->toBe('alpha');
});

it('rejects a whitespace-only rename and keeps the stored name', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->set('projects.0.editedName', '   ')
        ->call('renameProject', $project->id)
        ->assertSet('projects.0.editedName', '   ')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect($project->fresh()?->name)->toBe('alpha');
});

it('keeps the edited name and shows a persistence error when rename fails', function () {
    $store = new FakeRecordStore;
    $store->projects = [new ProjectSummary(1, 'alpha')];
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(Projects::class)
        ->set('projects.0.editedName', 'beta')
        ->call('renameProject', 1)
        ->assertSet('projects.0.editedName', 'beta')
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect($store->renameCalls)->toBe(1)
        ->and($store->projects[0]->name)->toBe('alpha');
});

it('shows a persistence error when the project no longer exists', function () {
    $alpha = Project::create(['name' => 'alpha']);
    Project::create(['name' => 'beta']);

    $component = Livewire::test(Projects::class);

    $alpha->delete();

    $component
        ->set('projects.0.editedName', 'renamed')
        ->call('renameProject', $alpha->id)
        ->assertSet('projects.0.editedName', 'renamed')
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect(Project::query()->whereKey($alpha->id)->exists())->toBeFalse();
});

it('shows a persistence error when the edited row no longer exists', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->call('renameProject', $project->id + 1)
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect($project->fresh()?->name)->toBe('alpha');
});

it('persists a renamed project through a fresh manager', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->set('projects.0.editedName', 'beta')
        ->call('renameProject', $project->id);

    $this->app->forgetInstance(RecordStore::class);
    $this->app->forgetInstance(ProjectManager::class);

    $result = app(ProjectManager::class)->list();

    expect($result->success)->toBeTrue()
        ->and(array_map(fn (ProjectSummary $project): string => $project->name, $result->projects))
        ->toBe(['beta']);
});
