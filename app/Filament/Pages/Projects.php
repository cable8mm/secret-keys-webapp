<?php

namespace App\Filament\Pages;

use App\Projects\ProjectManager;
use App\Records\ProjectSummary;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class Projects extends Page
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $slug = 'projects';

    protected string $view = 'filament.pages.projects';

    public string $projectName = '';

    /**
     * @var list<array{id: int, name: string, editedName: string}>
     */
    public array $projects = [];

    public ?string $errorCode = null;

    public ?string $errorMessage = null;

    public bool $projectsVisible = true;

    public ?int $pendingDeleteProjectId = null;

    public function mount(ProjectManager $projectManager): void
    {
        $this->loadProjects($projectManager);
    }

    public function createProject(ProjectManager $projectManager): void
    {
        $result = $projectManager->create($this->projectName);

        if (! $result->success) {
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return;
        }

        $this->projectName = '';
        $this->loadProjects($projectManager);
    }

    public function back(): RedirectResponse
    {
        return redirect()->to(Items::getUrl());
    }

    public function armDeleteProject(int $projectId): void
    {
        $this->pendingDeleteProjectId = $projectId;
    }

    public function cancelDeleteProject(): void
    {
        $this->pendingDeleteProjectId = null;
    }

    public function confirmDeleteProject(ProjectManager $projectManager): void
    {
        if ($this->pendingDeleteProjectId === null) {
            return;
        }

        $projectId = $this->pendingDeleteProjectId;

        $result = $projectManager->delete($projectId);

        if (! $result->success) {
            $this->pendingDeleteProjectId = $projectId;
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return;
        }

        $this->pendingDeleteProjectId = null;
        $this->loadProjects($projectManager);
    }

    public function renameProject(int $projectId, ProjectManager $projectManager): void
    {
        $editedName = null;

        foreach ($this->projects as $project) {
            if ($project['id'] === $projectId) {
                $editedName = $project['editedName'];
                break;
            }
        }

        if ($editedName === null) {
            $this->errorCode = 'ERR-007';
            $this->errorMessage = 'The Project could not be saved.';

            return;
        }

        $result = $projectManager->rename($projectId, $editedName);

        if (! $result->success) {
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return;
        }

        $this->loadProjects($projectManager);
    }

    private function loadProjects(ProjectManager $projectManager): void
    {
        $result = $projectManager->list();

        if (! $result->success) {
            $this->projects = [];
            $this->projectsVisible = false;
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return;
        }

        $this->projects = array_map(
            fn (ProjectSummary $project): array => [
                'id' => $project->id,
                'name' => $project->name,
                'editedName' => $project->name,
            ],
            $result->projects,
        );
        $this->projectsVisible = true;
        $this->errorCode = null;
        $this->errorMessage = null;
    }
}
