<?php

namespace App\Filament\Pages;

use App\Items\ItemInput;
use App\Items\ItemManager;
use App\Projects\ProjectManager;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class ItemForm extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'items/create';

    protected string $view = 'filament.pages.item-form';

    public string $itemName = '';

    public string $projectId = '';

    public string $service = '';

    public string $environment = '';

    public string $value = '';

    public string $usageLocation = '';

    public string $issuedDate = '';

    public string $memo = '';

    public bool $revealed = false;

    public string $fingerprint = '';

    /**
     * @var list<array{code: string, message: string}>
     */
    public array $formErrors = [];

    /**
     * @var list<array{id: int, name: string}>
     */
    public array $projects = [];

    public function mount(ProjectManager $projectManager): void
    {
        $result = $projectManager->list();

        if ($result->success) {
            $this->projects = array_map(
                fn ($project): array => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
                $result->projects,
            );
        } else {
            $this->formErrors = [[
                'code' => $result->errorCode,
                'message' => $result->error,
            ]];
        }
    }

    public function save(ItemManager $itemManager): ?RedirectResponse
    {
        $projectId = $this->projectId === '' ? null : (int) $this->projectId;

        $result = $itemManager->create(new ItemInput(
            $this->itemName,
            $projectId,
            $this->service,
            $this->environment,
            $this->value,
            $this->usageLocation,
            $this->issuedDate,
            $this->memo,
        ));

        if (! $result->success) {
            $this->formErrors = $result->errors;

            return null;
        }

        return redirect()->to(Items::getUrl());
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->to(Items::getUrl());
    }

    public function openProjects(): RedirectResponse
    {
        return redirect()->to(Projects::getUrl());
    }

    public function toggleReveal(): void
    {
        $this->revealed = ! $this->revealed;
    }

    public function getProjectsAvailableProperty(): bool
    {
        return $this->projects !== [];
    }
}
