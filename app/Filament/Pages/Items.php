<?php

namespace App\Filament\Pages;

use App\Items\AiItemManager;
use App\Items\InfraItemManager;
use App\Items\ItemFinder;
use App\Items\ItemManager;
use App\Projects\ProjectManager;
use Filament\Pages\Page;
use Filament\Panel;

class Items extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.items';

    public ?string $errorMessage = null;

    public ?string $message = null;

    public string $search = '';

    public string $projectFilter = '';

    public string $environmentFilter = '';

    /**
     * @var list<array{id: int, name: string}>
     */
    public array $projects = [];

    /**
     * @var list<string>
     */
    public array $environments = [];

    /**
     * @var list<array{id: int, projectId: ?int, name: string, service: ?string, environment: ?string, projectName: string, fingerprint: string, usageLocation: ?string, memo: ?string, type: string}>
     */
    public array $items = [];

    public static function getSlug(?Panel $panel = null): string
    {
        return '';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'items';
    }

    public function loadPage(ItemFinder $itemFinder, ProjectManager $projectManager): void
    {
        $this->loadOptions($projectManager);
        $this->loadItems($itemFinder);
    }

    public function mount(ItemFinder $itemFinder, ProjectManager $projectManager): void
    {
        $this->loadOptions($projectManager);
        $this->loadItems($itemFinder);
    }

    public function updatedSearch(): void
    {
        $this->reload();
    }

    public function updatedProjectFilter(): void
    {
        $this->reload();
    }

    public function updatedEnvironmentFilter(): void
    {
        $this->reload();
    }

    public function copyValue(string $type, int $id): void
    {
        $text = match ($type) {
            'ai' => app(AiItemManager::class)->copyText($id),
            'infra' => app(InfraItemManager::class)->copyText($id),
            default => app(ItemManager::class)->copyText($id),
        };

        if ($text === null) {
            $this->errorMessage = 'Copying failed.';
            $this->message = null;

            return;
        }

        $this->errorMessage = null;
        $this->message = null;

        $this->dispatch('copy-prepared', text: $text);
    }

    public function reportCopyResult(bool $success): void
    {
        if ($success) {
            $this->errorMessage = null;
            $this->message = 'Copied.';
        } else {
            $this->errorMessage = 'Copying failed.';
            $this->message = null;
        }
    }

    private function reload(): void
    {
        $this->loadItems(app(ItemFinder::class));
    }

    private function loadOptions(ProjectManager $projectManager): void
    {
        $result = $projectManager->list();

        $this->projects = $result->success
            ? array_map(fn ($p): array => ['id' => $p->id, 'name' => $p->name], $result->projects)
            : [];

        $itemResult = app(ItemFinder::class)->list();
        $this->environments = app(ItemFinder::class)->distinctEnvironments();
    }

    private function loadItems(ItemFinder $itemFinder): void
    {
        $result = $itemFinder->list(
            $this->search,
            $this->projectFilter === '' ? null : (int) $this->projectFilter,
            $this->environmentFilter === '' ? null : $this->environmentFilter,
        );

        if (! $result->success) {
            $this->items = [];
            $this->errorMessage = $result->error;

            return;
        }

        $this->items = array_map(
            fn ($row): array => [
                'id' => $row->id,
                'projectId' => $row->projectId,
                'name' => $row->name,
                'service' => $row->service,
                'environment' => $row->environment,
                'projectName' => $row->projectName,
                'fingerprint' => $row->fingerprint,
                'usageLocation' => $row->usageLocation,
                'memo' => $row->memo,
                'type' => $row->type,
            ],
            $result->rows,
        );
        $this->errorMessage = null;
    }
}
