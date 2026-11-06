<?php

namespace App\Filament\Pages;

use App\Items\AiItemManager;
use App\Items\InfraItemManager;
use App\Items\ItemInput;
use App\Items\ItemManager;
use App\Projects\ProjectManager;
use App\Values\CopyTextPreparer;
use Filament\Pages\Page;
use Illuminate\Http\RedirectResponse;

class ItemForm extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'items/create';

    protected string $view = 'filament.pages.item-form';

    public ?int $itemId = null;

    public string $itemType = 'project';

    public string $itemName = '';

    public string $projectId = '';

    public string $service = '';

    public string $environment = '';

    public string $kind = '';

    public string $value = '';

    public string $usageLocation = '';

    public string $issuedDate = '';

    public string $expiresAt = '';

    public string $memo = '';

    public string $tags = '';

    public bool $revealed = false;

    public bool $hasUserProjects = false;

    public string $fingerprint = '';

    public ?string $copyMessage = null;

    public ?string $copyError = null;

    public bool $itemLoaded = true;

    public bool $loading = false;

    public bool $deleteArmed = false;

    /**
     * @var list<array{code: string, message: string}>
     */
    public array $formErrors = [];

    /**
     * @var list<array{id: int, name: string}>
     */
    public array $projects = [];

    public function mount(ProjectManager $projectManager, ItemManager $itemManager, AiItemManager $aiItemManager, InfraItemManager $infraItemManager, ?int $itemId = null, string $itemType = 'project'): void
    {
        if (! in_array($itemType, ['project', 'ai', 'infra'], true)) {
            $itemType = 'project';
        }

        $this->itemType = $itemType;

        if ($itemId !== null) {
            $this->itemId = $itemId;
        }

        $result = $projectManager->list();

        if ($result->success) {
            $this->projects = array_map(
                fn ($project): array => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
                $result->projects,
            );
            $this->hasUserProjects = collect($result->projects)->contains(fn ($project): bool => ! $project->isFixed);
        } else {
            $this->formErrors = [[
                'code' => $result->errorCode,
                'message' => $result->error,
            ]];
        }

        if ($this->itemId !== null) {
            $this->loading = true;
            $this->revealed = false;
            $loadResult = match ($this->itemType) {
                'ai' => $aiItemManager->load($this->itemId),
                'infra' => $infraItemManager->load($this->itemId),
                default => $itemManager->load($this->itemId),
            };

            if (! $loadResult->success) {
                $this->itemLoaded = false;
                $this->loading = false;
                $this->formErrors = [[
                    'code' => $loadResult->errorCode,
                    'message' => $loadResult->error,
                ]];

                return;
            }

            $detail = $loadResult->detail;

            $this->itemName = $detail->name;
            $this->projectId = (string) $detail->projectId;
            $this->service = $detail->service;
            $this->environment = $detail->environment;
            $this->kind = $detail->kind;
            $this->value = $detail->value;
            $this->usageLocation = $detail->usageLocation;
            $this->issuedDate = $detail->issuedDate;
            $this->expiresAt = $detail->expiresAt;
            $this->memo = $detail->memo;
            $this->tags = $detail->tags;
            $this->fingerprint = $detail->fingerprint;
            $this->loading = false;
            $this->itemLoaded = true;
        }
    }

    public function save(ItemManager $itemManager, AiItemManager $aiItemManager, InfraItemManager $infraItemManager): ?RedirectResponse
    {
        if ($this->itemId === null) {
            return $this->storeCreate($itemManager, $aiItemManager, $infraItemManager);
        }

        return $this->storeUpdate($itemManager, $aiItemManager, $infraItemManager);
    }

    private function storeCreate(ItemManager $itemManager, AiItemManager $aiItemManager, InfraItemManager $infraItemManager): ?RedirectResponse
    {
        $result = match ($this->itemType) {
            'ai' => $aiItemManager->create($this->itemName, $this->service, $this->value),
            'infra' => $infraItemManager->create($this->itemName, $this->service, $this->value),
            default => $itemManager->create($this->toInput()),
        };

        if (! $result->success) {
            $this->formErrors = $result->errors;

            return null;
        }

        return redirect()->to(Items::getUrl());
    }

    private function storeUpdate(ItemManager $itemManager, AiItemManager $aiItemManager, InfraItemManager $infraItemManager): ?RedirectResponse
    {
        $result = match ($this->itemType) {
            'ai' => $aiItemManager->update((int) $this->itemId, $this->itemName, $this->service, $this->value),
            'infra' => $infraItemManager->update((int) $this->itemId, $this->itemName, $this->service, $this->value),
            default => $itemManager->update((int) $this->itemId, $this->toInput()),
        };

        if (! $result->success) {
            $this->formErrors = $result->errors;

            return null;
        }

        return redirect()->to(Items::getUrl());
    }

    private function toInput(): ItemInput
    {
        return new ItemInput(
            $this->itemName,
            $this->projectId === '' ? null : (int) $this->projectId,
            $this->service,
            $this->environment,
            $this->value,
            $this->usageLocation,
            $this->issuedDate,
            $this->memo,
            $this->kind,
            $this->tags,
            $this->expiresAt,
        );
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->to(Items::getUrl());
    }

    public function openProjects(): RedirectResponse
    {
        return redirect()->to(Projects::getUrl());
    }

    public function copyValue(): void
    {
        if (trim($this->value) === '') {
            $this->copyError = 'Copying failed.';
            $this->copyMessage = null;

            return;
        }

        $this->copyError = null;
        $this->copyMessage = null;

        $this->dispatch('copy-prepared', text: (new CopyTextPreparer)->prepare($this->value, $this->kind === '' ? null : $this->kind));
    }

    public function reportCopyResult(bool $success): void
    {
        if ($success) {
            $this->copyError = null;
            $this->copyMessage = 'Copied.';
        } else {
            $this->copyError = 'Copying failed.';
            $this->copyMessage = null;
        }
    }

    public function toggleReveal(): void
    {
        $this->revealed = ! $this->revealed;
    }

    public function armDelete(): void
    {
        $this->deleteArmed = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteArmed = false;
    }

    public function confirmDelete(ItemManager $itemManager, AiItemManager $aiItemManager, InfraItemManager $infraItemManager): ?RedirectResponse
    {
        if ($this->itemId === null) {
            return null;
        }

        $result = match ($this->itemType) {
            'ai' => $aiItemManager->delete($this->itemId),
            'infra' => $infraItemManager->delete($this->itemId),
            default => $itemManager->delete($this->itemId),
        };

        if (! $result->success) {
            $this->deleteArmed = true;
            $this->formErrors = $result->errors;

            return null;
        }

        return redirect()->to(Items::getUrl());
    }

    public function isCreateModeProperty(): bool
    {
        return $this->itemId === null;
    }

    public function getProjectsAvailableProperty(): bool
    {
        return $this->projects !== [];
    }
}
