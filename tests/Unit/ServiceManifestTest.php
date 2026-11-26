<?php

use App\Support\ServiceManifest;

it('resolves services, sets, and versions from the manifest', function (): void {
    expect(ServiceManifest::setOptions())->toHaveKey('x/app')
        ->toHaveKey('x/api_key')
        ->toHaveKey('custom/values');

    $app = ServiceManifest::definition('x/app');

    expect($app)->not->toBeNull()
        ->and($app['key'])->toBe('x/app')
        ->and($app['version'])->toBe(1)
        ->and($app['fields'])->toHaveCount(3)
        ->and($app['customKeys'])->toBeFalse();

    expect(ServiceManifest::definition('x/api_key')['fields'])->toHaveCount(1);
    expect(ServiceManifest::definition('x/app', 99))->toBeNull();
});

it('filters sets by container', function (): void {
    $aiSets = ServiceManifest::setOptions('ai_items');

    expect($aiSets)->toHaveKey('openai/api_key')
        ->toHaveKey('custom/values')
        ->not->toHaveKey('x/app');

    expect(ServiceManifest::setOptions('secrets'))->toHaveKey('x/app')
        ->not->toHaveKey('openai/api_key');
});

it('rejects a manifest without labels', function (): void {
    ServiceManifest::validate([
        'x' => [
            'label' => '',
            'containers' => ['secrets'],
            'sets' => [],
        ],
    ]);
})->throws(InvalidArgumentException::class);

it('rejects a field with an unknown kind', function (): void {
    ServiceManifest::validate([
        'x' => [
            'label' => 'X',
            'containers' => ['secrets'],
            'sets' => [
                'app' => [
                    '1' => [
                        'label' => 'X app',
                        'version' => 1,
                        'fields' => [
                            ['key' => 'api_key', 'label' => 'API Key', 'purpose' => 'Identifies the app', 'kind' => 'bogus'],
                        ],
                    ],
                ],
            ],
        ],
    ]);
})->throws(InvalidArgumentException::class);

it('rejects a manifest that carries secret values', function (): void {
    ServiceManifest::validate([
        'x' => [
            'label' => 'X',
            'containers' => ['secrets'],
            'sets' => [
                'app' => [
                    '1' => [
                        'label' => 'X app',
                        'version' => 1,
                        'fields' => [
                            ['key' => 'api_key', 'label' => 'API Key', 'purpose' => 'Identifies the app', 'kind' => 'token', 'value' => 'secret'],
                        ],
                    ],
                ],
            ],
        ],
    ]);
})->throws(InvalidArgumentException::class);
