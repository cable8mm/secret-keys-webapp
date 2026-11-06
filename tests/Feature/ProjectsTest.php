<?php

use App\Filament\Pages\Projects;
use App\Filament\Pages\ProjectsCreate;
use App\Filament\Pages\ProjectsEdit;
use App\Filament\Pages\ProjectsShow;
use App\Models\Project;
use App\Projects\ProjectManager;
use App\Records\ProjectSummary;
use App\Records\RecordStore;
use Livewire\Livewire;
use Tests\Support\FakeRecordStore;

it('shows the empty project list with only fixed containers', function () {
    Livewire::test(Projects::class)
        ->assertSee('No projects yet.')
        ->assertSeeHtml('data-element="EL-210"')
        ->assertSeeHtml('data-element="EL-203"')
        ->assertSee('Infra')
        ->assertSee('AI')
        ->assertDontSeeHtml('data-element="EL-211"')
        ->assertDontSeeHtml('data-element="EL-206"');
});

it('creates a project and lists it', function () {
    Livewire::test(ProjectsCreate::class)
        ->set('projectName', 'alpha')
        ->call('createProject')
        ->assertRedirect();

    Livewire::test(Projects::class)
        ->assertSee('alpha')
        ->assertSeeHtml('data-element="EL-203"')
        ->assertDontSeeHtml('data-element="EL-210"');

    expect(Project::query()->where('is_fixed', false)->pluck('name')->all())->toBe(['alpha']);
});

it('orders listed projects case-insensitively by name', function () {
    Project::create(['name' => 'zulu']);
    Project::create(['name' => 'Alpha']);
    Project::create(['name' => 'beta']);

    Livewire::test(Projects::class)
        ->assertSeeInOrder(['Alpha', 'beta', 'zulu']);
});

it('rejects an empty project name without creating a project', function () {
    Livewire::test(ProjectsCreate::class)
        ->call('createProject')
        ->assertSet('projectName', '')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect(Project::query()->where('is_fixed', false)->count())->toBe(0);
});

it('rejects a whitespace-only project name without creating a project', function () {
    Livewire::test(ProjectsCreate::class)
        ->set('projectName', '   ')
        ->call('createProject')
        ->assertSet('projectName', '   ')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect(Project::query()->where('is_fixed', false)->count())->toBe(0);
});

it('keeps the entered name and shows a persistence error', function () {
    $store = new FakeRecordStore;
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(ProjectsCreate::class)
        ->set('projectName', 'alpha')
        ->call('createProject')
        ->assertSet('projectName', 'alpha')
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect($store->createCalls)->toBe(1);
});

it('deletes a project and removes it from the list', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->mountAction('delete', ['project' => $project->id])
        ->assertActionMounted('delete')
        ->callMountedAction()
        ->assertSee('Project deleted.')
        ->assertDontSee('alpha')
        ->assertDontSeeHtml('data-error-code="ERR-005"')
        ->assertDontSeeHtml('data-error-code="ERR-007"');

    expect(Project::query()->whereKey($project->id)->exists())->toBeFalse();
});

it('asks for confirmation before deleting a project', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->mountAction('delete', ['project' => $project->id])
        ->assertActionMounted('delete');

    expect(Project::query()->whereKey($project->id)->exists())->toBeTrue();
});

it('cancels deletion without deleting the project', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->mountAction('delete', ['project' => $project->id])
        ->assertActionMounted('delete')
        ->unmountAction()
        ->assertActionNotMounted()
        ->assertDontSee('Project deleted.')
        ->assertSee('alpha');

    expect(Project::query()->whereKey($project->id)->exists())->toBeTrue();
});

it('rejects deletion of a project that has items', function () {
    $project = Project::create(['name' => 'alpha']);
    $project->items()->create(['name' => 'API key', 'value' => 'secret-value']);

    Livewire::test(Projects::class)
        ->mountAction('delete', ['project' => $project->id])
        ->callMountedAction()
        ->assertSee('The Project has Items.')
        ->assertSeeHtml('data-error-code="ERR-005"');

    expect(Project::query()->whereKey($project->id)->exists())->toBeTrue();
});

it('shows a persistence error when deletion fails', function () {
    $store = new FakeRecordStore;
    $store->projects = [new ProjectSummary(1, 'alpha')];
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    Livewire::test(Projects::class)
        ->mountAction('delete', ['project' => 1])
        ->callMountedAction()
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect($store->deleteProjectCalls)->toBe(1);
});

it('renames a project and lists the new name', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(ProjectsEdit::class, ['project' => $project])
        ->set('projectName', 'beta')
        ->call('updateProject')
        ->assertRedirect()
        ->assertSet('projectName', 'beta');

    $project->refresh();
    expect($project->name)->toBe('beta');

    Livewire::test(Projects::class)
        ->assertSee('beta')
        ->assertDontSee('alpha');
});

it('rejects an empty rename and keeps the stored name', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(ProjectsEdit::class, ['project' => $project])
        ->set('projectName', '')
        ->call('updateProject')
        ->assertSet('projectName', '')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect($project->fresh()?->name)->toBe('alpha');
});

it('rejects a whitespace-only rename and keeps the stored name', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(ProjectsEdit::class, ['project' => $project])
        ->set('projectName', '   ')
        ->call('updateProject')
        ->assertSet('projectName', '   ')
        ->assertSee('Project Name is required.')
        ->assertSeeHtml('data-error-code="ERR-004"');

    expect($project->fresh()?->name)->toBe('alpha');
});

it('shows a persistence error when rename fails', function () {
    $store = new FakeRecordStore;
    $store->projects = [new ProjectSummary(1, 'alpha')];
    $store->failWrite = true;
    $this->app->instance(RecordStore::class, $store);

    $project = Project::create(['name' => 'alpha']);

    Livewire::test(ProjectsEdit::class, ['project' => $project])
        ->set('projectName', 'beta')
        ->call('updateProject')
        ->assertSet('projectName', 'beta')
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-element="EL-211"')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect($project->fresh()?->name)->toBe('alpha');
});

it('shows a persistence error when the project no longer exists on rename', function () {
    $project = Project::create(['name' => 'alpha']);
    $project->delete();

    Livewire::test(ProjectsEdit::class, ['project' => $project])
        ->set('projectName', 'renamed')
        ->call('updateProject')
        ->assertSee('The Project could not be saved.')
        ->assertSeeHtml('data-error-code="ERR-007"');

    expect(Project::query()->whereKey($project->id)->exists())->toBeFalse();
});

it('shows project details on the show page', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(ProjectsShow::class, ['project' => $project])
        ->assertSee('alpha')
        ->assertSee('Edit')
        ->assertSee('Back to list');
});

it('lists persisted projects through a fresh manager', function () {
    Project::create(['name' => 'alpha']);

    $this->app->forgetInstance(RecordStore::class);
    $this->app->forgetInstance(ProjectManager::class);

    $result = app(ProjectManager::class)->list();

    expect($result->success)->toBeTrue()
        ->and(array_map(fn ($project): string => $project->name, $result->projects))
        ->toBe(['AI', 'alpha', 'Infra']);
});

it('seeds the fixed Infra and AI containers', function () {
    $fixed = Project::query()->where('is_fixed', true)->orderBy('name')->pluck('name')->all();

    expect($fixed)->toBe(['AI', 'Infra']);

    Livewire::test(Projects::class)
        ->assertSee('Infra')
        ->assertSee('AI');
});

it('offers no rename or delete controls for fixed containers', function () {
    Livewire::test(Projects::class)
        ->assertDontSeeHtml('data-element="EL-206"')
        ->assertDontSee('Edit');
});

it('redirects away from editing a fixed container', function () {
    $infra = Project::query()->where('name', 'Infra')->firstOrFail();

    Livewire::test(ProjectsEdit::class, ['project' => $infra])
        ->assertRedirect('/projects');

    expect(Project::query()->whereKey($infra->getKey())->exists())->toBeTrue();
});

it('rejects renaming a fixed container', function () {
    $infra = Project::query()->where('name', 'Infra')->firstOrFail();

    $result = app(ProjectManager::class)->rename($infra->getKey(), 'Changed');

    expect($result->success)->toBeFalse()
        ->and($result->errorCode)->toBe('ERR-007')
        ->and(Project::query()->whereKey($infra->getKey())->value('name'))->toBe('Infra');
});

it('rejects deleting a fixed container', function () {
    $infra = Project::query()->where('name', 'Infra')->firstOrFail();

    $result = app(ProjectManager::class)->delete($infra->getKey());

    expect($result->success)->toBeFalse()
        ->and($result->errorCode)->toBe('ERR-007')
        ->and(Project::query()->whereKey($infra->getKey())->exists())->toBeTrue();
});

it('archives a project and hides it from the default list', function () {
    $project = Project::create(['name' => 'alpha']);

    Livewire::test(Projects::class)
        ->call('archiveProject', $project->id)
        ->assertSee('Project archived.')
        ->assertDontSeeHtml('data-element="EL-206"')
        ->assertSee('Archived')
        ->assertSee('alpha');

    expect(Project::query()->whereKey($project->id)->value('archived'))->toBeTrue();
});

it('restores an archived project to the default list', function () {
    $project = Project::create(['name' => 'alpha', 'archived' => true]);

    Livewire::test(Projects::class)
        ->call('restoreProject', $project->id)
        ->assertSee('Project restored.')
        ->assertSeeHtml('data-element="EL-206"');

    expect(Project::query()->whereKey($project->id)->value('archived'))->toBeFalse();
});

it('offers no archive control for fixed containers', function () {
    Livewire::test(Projects::class)
        ->assertDontSee('Archive')
        ->assertDontSee('Restore');
});
