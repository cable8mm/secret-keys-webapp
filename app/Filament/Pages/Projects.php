<?php

namespace App\Filament\Pages;

use App\Projects\ProjectManager;
use App\Records\ProjectSummary;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class Projects extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'projects';

    protected string $view = 'filament.pages.projects';

    public string $projectName = '';

    /**
     * @var list<array{id: int, name: string}>
     */
    public array $projects = [];

    public ?string $errorCode = null;

    public ?string $errorMessage = null;

    public bool $projectsVisible = true;

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
            ],
            $result->projects,
        );
        $this->projectsVisible = true;
        $this->errorCode = null;
        $this->errorMessage = null;
    }
}
