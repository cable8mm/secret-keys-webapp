<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Panel;

class Items extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.items';

    public static function getSlug(?Panel $panel = null): string
    {
        return '';
    }

    public static function getRelativeRouteName(Panel $panel): string
    {
        return 'items';
    }
}
