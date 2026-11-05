<?php

namespace App\Filament\Pages;

use App\Items\ItemFinder;
use Filament\Pages\Page;
use Filament\Panel;

class Items extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.items';

    public ?string $errorMessage = null;

    /**
     * @var list<array{id: int, name: string, service: ?string, environment: ?string, projectName: string, fingerprint: string}>
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

    public function mount(ItemFinder $itemFinder): void
    {
        $this->loadItems($itemFinder);
    }

    private function loadItems(ItemFinder $itemFinder): void
    {
        $result = $itemFinder->list();

        if (! $result->success) {
            $this->items = [];
            $this->errorMessage = $result->error;

            return;
        }

        $this->items = array_map(
            fn ($row): array => [
                'id' => $row->id,
                'name' => $row->name,
                'service' => $row->service,
                'environment' => $row->environment,
                'projectName' => $row->projectName,
                'fingerprint' => $row->fingerprint,
            ],
            $result->rows,
        );
        $this->errorMessage = null;
    }
}
