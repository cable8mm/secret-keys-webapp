<?php

namespace App\Filament\Pages;

use App\Projects\ProjectManager;
use App\Records\ProjectSummary;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class Projects extends Page
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'projects';

    protected string $view = 'filament.pages.projects';

    public string $projectName = '';

    public ?string $errorMessage = null;

    public ?string $errorCode = null;

    public ?string $successMessage = null;

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading('Delete project')
            ->modalDescription(function (array $arguments): string {
                $name = collect($this->projects)->firstWhere('id', (int) ($arguments['project'] ?? 0))['name'] ?? '';

                return $name === ''
                    ? 'Delete this project?'
                    : "Delete project \"{$name}\"?";
            })
            ->modalSubmitActionLabel('Delete')
            ->color('danger')
            ->action(function (array $arguments): void {
                $projectManager = app(ProjectManager::class);
                $result = $projectManager->delete((int) $arguments['project']);

                if (! $result->success) {
                    $this->errorCode = $result->errorCode;
                    $this->errorMessage = $result->error;

                    return;
                }

                $this->successMessage = 'Project deleted.';
                $this->errorCode = null;
                $this->errorMessage = null;

                $this->loadProjects($projectManager);
            });
    }

    /**
     * @var list<array{id: int, name: string}>
     */
    public array $projects = [];

    public function mount(ProjectManager $projectManager): void
    {
        $this->loadProjects($projectManager);
    }

    public function createProject(ProjectManager $projectManager): ?RedirectResponse
    {
        $result = $projectManager->create($this->projectName);

        if (! $result->success) {
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return null;
        }

        $this->projectName = '';
        $this->successMessage = 'Project created.';
        $this->errorCode = null;
        $this->errorMessage = null;

        $this->loadProjects($projectManager);

        return null;
    }

    private function loadProjects(ProjectManager $projectManager): void
    {
        $result = $projectManager->list();

        if (! $result->success) {
            $this->projects = [];
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return;
        }

        $this->projects = array_map(
            fn (ProjectSummary $project): array => [
                'id' => $project->id,
                'name' => $project->name,
            ],
            $result->projects,
        );
        $this->errorCode = null;
        $this->errorMessage = null;
    }
}
