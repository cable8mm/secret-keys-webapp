<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Projects\ProjectManager;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class ProjectsEdit extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'projects/{project}/edit';

    protected string $view = 'filament.pages.projects-edit';

    public ?Project $project = null;

    public string $projectName = '';

    public string $projectNote = '';

    public ?string $errorMessage = null;

    public ?string $errorCode = null;

    public function mount(Project $project, ProjectManager $projectManager): ?RedirectResponse
    {
        if ((bool) $project->is_fixed) {
            return redirect()->to(Projects::getUrl());
        }

        $this->project = $project;
        $this->projectName = $project->name;
        $this->projectNote = (string) ($project->note ?? '');

        return null;
    }

    public function updateProject(ProjectManager $projectManager): ?RedirectResponse
    {
        $projectId = $this->project?->id;

        if ($projectId === null) {
            $this->errorCode = 'ERR-007';
            $this->errorMessage = 'The Project could not be saved.';

            return null;
        }

        $result = $projectManager->rename($projectId, $this->projectName, $this->projectNote);

        if (! $result->success) {
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return null;
        }

        $this->project->refresh();
        $this->projectName = $this->project->name;

        return redirect()->to('/projects');
    }
}
