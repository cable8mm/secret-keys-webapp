<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Pages\Page;

class ProjectsShow extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'projects/{project}';

    protected string $view = 'filament.pages.projects-show';

    public ?Project $project = null;

    public function mount(Project $project): void
    {
        $this->project = $project;
    }
}
