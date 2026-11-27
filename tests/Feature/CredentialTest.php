<?php

use App\Filament\Resources\Credentials\Pages\CreateCredential;
use App\Filament\Resources\Credentials\Pages\EditCredential;
use App\Filament\Resources\Credentials\Pages\ViewCredential;
use App\Filament\Resources\Providers\Pages\CreateProvider;
use App\Models\Credential;
use App\Models\Provider;
use App\Services\CredentialValues;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function definitionFor(string $slug): array
{
    /** @var array<string, mixed> $definition */
    $definition = json_decode((string) Provider::where('slug', $slug)->firstOrFail()->definition, true, 512, JSON_THROW_ON_ERROR);

    return $definition;
}

function storedValues(Credential $credential): array
{
    /** @var array<string, mixed> $decoded */
    $decoded = json_decode((string) DB::table('credentials')->where('id', $credential->id)->value('credential_values'), true, 512, JSON_THROW_ON_ERROR);

    return $decoded;
}

it('creates a multi-field credential and encrypts sensitive values', function (): void {
    $aws = Provider::where('slug', 'aws')->firstOrFail();

    Livewire::test(CreateCredential::class)
        ->fillForm([
            'provider_id' => $aws->id,
            'name' => 'AWS production',
            'values.access_key_id' => 'AKIA123',
            'values.secret_access_key' => 'topsecret-value',
            'values.region' => 'ap-northeast-2',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $credential = Credential::query()->firstOrFail();
    $raw = storedValues($credential);

    expect($raw['access_key_id'])->toBe('AKIA123')
        ->and($raw['secret_access_key'])->not->toBe('topsecret-value')
        ->and(app(CredentialValues::class)->decode(definitionFor('aws'), $credential->credential_values))->toBe([
            'access_key_id' => 'AKIA123',
            'secret_access_key' => 'topsecret-value',
            'region' => 'ap-northeast-2',
        ]);
});

it('creates a single-field credential', function (): void {
    $openai = Provider::where('slug', 'openai')->firstOrFail();

    Livewire::test(CreateCredential::class)
        ->fillForm([
            'provider_id' => $openai->id,
            'name' => 'OpenAI dev',
            'values.api_key' => 'sk-test-value',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(app(CredentialValues::class)->decode(definitionFor('openai'), Credential::query()->firstOrFail()->credential_values))
        ->toBe(['api_key' => 'sk-test-value']);
});

it('edits a credential while keeping sensitive values encrypted', function (): void {
    $aws = Provider::where('slug', 'aws')->firstOrFail();

    Livewire::test(CreateCredential::class)
        ->fillForm([
            'provider_id' => $aws->id,
            'name' => 'AWS edit',
            'values.access_key_id' => 'AKIA123',
            'values.secret_access_key' => 'topsecret-value',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $credential = Credential::query()->firstOrFail();

    Livewire::test(EditCredential::class, ['record' => $credential->getRouteKey()])
        ->fillForm(['values.region' => 'us-east-1'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(app(CredentialValues::class)->decode(definitionFor('aws'), $credential->fresh()->credential_values))->toBe([
        'access_key_id' => 'AKIA123',
        'secret_access_key' => 'topsecret-value',
        'region' => 'us-east-1',
    ]);
});

it('supports multiple credentials for one provider', function (): void {
    $aws = Provider::where('slug', 'aws')->firstOrFail();

    foreach (['First', 'Second'] as $name) {
        Livewire::test(CreateCredential::class)
            ->fillForm([
                'provider_id' => $aws->id,
                'name' => $name,
                'values.access_key_id' => "AKIA-{$name}",
                'values.secret_access_key' => "secret-{$name}",
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    expect(Credential::query()->where('provider_id', $aws->id)->count())->toBe(2);
});

it('rejects a credential with missing required fields', function (): void {
    $aws = Provider::where('slug', 'aws')->firstOrFail();

    Livewire::test(CreateCredential::class)
        ->fillForm([
            'provider_id' => $aws->id,
            'name' => 'Incomplete',
            'values.access_key_id' => 'AKIA123',
        ])
        ->call('create')
        ->assertHasFormErrors(['values.secret_access_key']);
});

it('adds a new provider through JSON without application code changes', function (): void {
    $definition = [
        'schema_version' => 1,
        'id' => 'newco',
        'name' => 'NewCo',
        'description' => 'Example provider',
        'category' => 'dev',
        'credentials' => ['fields' => [
            ['key' => 'token', 'label' => 'Token', 'type' => 'secret', 'required' => true, 'sensitive' => true],
        ]],
    ];

    Livewire::test(CreateProvider::class)
        ->fillForm([
            'slug' => 'newco',
            'name' => 'NewCo',
            'category' => 'dev',
            'definition' => json_encode($definition, JSON_THROW_ON_ERROR),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $provider = Provider::where('slug', 'newco')->firstOrFail();

    Livewire::test(CreateCredential::class)
        ->fillForm([
            'provider_id' => $provider->id,
            'name' => 'NewCo credential',
            'values.token' => 'newco-secret',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(app(CredentialValues::class)->decode(definitionFor('newco'), Credential::query()->firstOrFail()->credential_values))
        ->toBe(['token' => 'newco-secret']);
});

it('masks sensitive values on the view page', function (): void {
    $aws = Provider::where('slug', 'aws')->firstOrFail();

    Livewire::test(CreateCredential::class)
        ->fillForm([
            'provider_id' => $aws->id,
            'name' => 'AWS view',
            'values.access_key_id' => 'AKIA123',
            'values.secret_access_key' => 'topsecret-value',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(ViewCredential::class, ['record' => Credential::query()->firstOrFail()->getRouteKey()])
        ->assertSee('Access Key ID')
        ->assertSee('Secret Access Key')
        ->assertDontSee('topsecret-value');
});
