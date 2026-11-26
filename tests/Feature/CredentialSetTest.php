<?php

use App\Filament\Resources\Secrets\Pages\CreateSecret;
use App\Filament\Resources\Secrets\Pages\EditSecret;
use App\Filament\Resources\Secrets\Pages\ListSecrets;
use App\Models\Folder;
use App\Models\Secret;
use App\Support\CredentialSet;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('creates an X app set with three encrypted values', function (): void {
    $folder = Folder::create(['name' => 'XFolder']);
    Livewire::test(CreateSecret::class)
        ->fillForm([
            'name' => 'X @myapp',
            'folder_id' => $folder->id,
            'definition' => 'x/app',
            'environment' => 'live',
            'values.api_key' => 'key-123',
            'values.api_secret' => 'secret-456',
            'values.bearer_token' => 'bearer-789',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $secret = Secret::query()->firstOrFail();

    expect($secret->manifest_key)->toBe('x/app')
        ->and($secret->manifest_version)->toBe('1')
        ->and(CredentialSet::decode($secret->value))->toBe([
            'api_key' => 'key-123',
            'api_secret' => 'secret-456',
            'bearer_token' => 'bearer-789',
        ]);
});

it('creates a single-value X API key set', function (): void {
    $folder = Folder::create(['name' => 'ApiKeyFolder']);
    Livewire::test(CreateSecret::class)
        ->fillForm([
            'name' => 'X API key',
            'folder_id' => $folder->id,
            'definition' => 'x/api_key',
            'values.token' => 'single-key',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(CredentialSet::decode(Secret::query()->firstOrFail()->value))->toBe(['token' => 'single-key']);
});

it('creates a custom set with user-defined keys', function (): void {
    $folder = Folder::create(['name' => 'CustomFolder']);
    Livewire::test(CreateSecret::class)
        ->fillForm([
            'name' => 'Misc credentials',
            'folder_id' => $folder->id,
            'definition' => 'custom/values',
            'values' => ['foo' => 'bar', 'baz' => 'qux'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $secret = Secret::query()->firstOrFail();

    expect($secret->manifest_key)->toBe('custom/values')
        ->and(CredentialSet::decode($secret->value))->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('edits the whole set and keeps its definition reference', function (): void {
    $folder = Folder::create(['name' => 'EditFolder']);
    $secret = Secret::create([
        'folder_id' => $folder->id,
        'name' => 'X @old',
        'manifest_key' => 'x/app',
        'manifest_version' => '1',
        'value' => CredentialSet::encode([
            'api_key' => 'old-key',
            'api_secret' => 'old-secret',
            'bearer_token' => 'old-token',
        ]),
    ]);

    Livewire::test(EditSecret::class, ['record' => $secret->getRouteKey()])
        ->fillForm(['values.api_key' => 'new-key'])
        ->call('save')
        ->assertHasNoFormErrors();

    $secret->refresh();

    expect($secret->manifest_key)->toBe('x/app')
        ->and($secret->manifest_version)->toBe('1')
        ->and(CredentialSet::decode($secret->value))->toBe([
            'api_key' => 'new-key',
            'api_secret' => 'old-secret',
            'bearer_token' => 'old-token',
        ]);
});

it('copies one key without changing stored values', function (): void {
    $folder = Folder::create(['name' => 'CopyFolder']);
    $secret = Secret::create([
        'folder_id' => $folder->id,
        'name' => 'X @copy',
        'manifest_key' => 'x/app',
        'manifest_version' => '1',
        'value' => CredentialSet::encode([
            'api_key' => 'key-123',
            'api_secret' => 'secret-456',
            'bearer_token' => 'bearer-789',
        ]),
    ]);

    expect(CredentialSet::fieldLabels($secret))->toBe([
        'api_key' => 'API Key',
        'api_secret' => 'API Secret',
        'bearer_token' => 'Bearer Token',
    ]);

    Livewire::test(ListSecrets::class)
        ->callTableAction('copyKey', $secret, ['key' => 'api_key'])
        ->assertDispatched('copy-prepared', text: 'key-123');

    expect(CredentialSet::decode($secret->fresh()->value))->toBe([
        'api_key' => 'key-123',
        'api_secret' => 'secret-456',
        'bearer_token' => 'bearer-789',
    ]);
});
