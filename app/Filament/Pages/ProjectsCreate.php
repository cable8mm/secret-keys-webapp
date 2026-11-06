<?php

namespace App\Filament\Pages;

use App\Projects\ProjectManager;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class ProjectsCreate extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'projects/create';

    protected string $view = 'filament.pages.projects-create';

    public string $projectName = '';

    public string $projectNote = '';

    public ?string $errorMessage = null;

    public ?string $errorCode = null;

    public function createProject(ProjectManager $projectManager): ?RedirectResponse
    {
        $result = $projectManager->create($this->projectName, $this->projectNote === '' ? null : $this->projectNote);

        if (! $result->success) {
            $this->errorCode = $result->errorCode;
            $this->errorMessage = $result->error;

            return null;
        }

        return redirect()->to('/projects');
    }
}
