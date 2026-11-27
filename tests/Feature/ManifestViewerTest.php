<?php

use App\Filament\Pages\ManifestViewer;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('shows manifest services, sets, versions, and field definitions', function (): void {
    Livewire::test(ManifestViewer::class)
        ->assertSee('x/app')
        ->assertSee('X app')
        ->assertSee('API Secret')
        ->assertSee('Authenticates the app')
        ->assertSee('x/api_key')
        ->assertSee('openai/api_key')
        ->assertSee('custom/values');
});
