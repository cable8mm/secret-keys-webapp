<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Folders\FolderResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->navigationGroups([
                NavigationGroup::make('Vault'),
                NavigationGroup::make('Data'),
            ])
            ->homeUrl(fn (): string => FolderResource::getUrl('index'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16rem')
            ->maxContentWidth(Width::Full)
            ->renderHook(PanelsRenderHook::TOPBAR_END, fn (): string => Blade::render('<x-filament-panels::theme-switcher />'))
            ->renderHook(PanelsRenderHook::BODY_END, fn (): string => Blade::render(<<<'BLADE'
                <script>
                    window.addEventListener('copy-prepared', async (event) => {
                        try {
                            await navigator.clipboard.writeText(event.detail.text);
                            new FilamentNotification().title('Copied.').success().send();
                        } catch (error) {
                            new FilamentNotification().title('Copying failed.').danger().send();
                        }
                    });
                    window.addEventListener('copy-failed', () => {
                        new FilamentNotification().title('Copying failed.').danger().send();
                    });
                </script>
                BLADE))
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([]);
    }
}
