<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

it('serves the product at the root without authentication', function () {
    $this->get('/')->assertOk();
});

it('configures the panel without a login page or authentication middleware', function () {
    $panel = Filament::getPanel('admin');

    expect($panel->getPath())->toBe('/')
        ->and($panel->hasLogin())->toBeFalse()
        ->and($panel->getAuthMiddleware())->toBe([]);
});

it('serves the product with the database session driver', function () {
    config(['session.driver' => 'database']);

    $this->get('/')->assertOk();
});

it('does not register a login route', function () {
    expect(Route::has('filament.admin.auth.login'))->toBeFalse()
        ->and(Route::has('login'))->toBeFalse();
});
