<?php

use App\Support\ProviderDefinition;

function awsDefinition(): array
{
    return [
        'schema_version' => 1,
        'id' => 'aws',
        'name' => 'Amazon Web Services',
        'description' => 'AWS access credentials',
        'category' => 'cloud',
        'credentials' => ['fields' => [
            ['key' => 'access_key_id', 'label' => 'Access Key ID', 'type' => 'text', 'required' => true, 'sensitive' => false],
            ['key' => 'secret_access_key', 'label' => 'Secret Access Key', 'type' => 'secret', 'required' => true, 'sensitive' => true],
            ['key' => 'region', 'label' => 'Region', 'type' => 'text', 'required' => false, 'sensitive' => false, 'placeholder' => 'ap-northeast-2'],
        ]],
    ];
}

it('accepts a valid definition and identifies its fields', function (): void {
    $definition = awsDefinition();

    ProviderDefinition::validate($definition);

    expect(ProviderDefinition::fields($definition))->toHaveCount(3)
        ->and(ProviderDefinition::sensitiveKeys($definition))->toBe(['secret_access_key']);
});

it('rejects invalid definitions with useful messages', function (array $definition, string $message): void {
    ProviderDefinition::validate($definition);
})->throws(InvalidArgumentException::class)->with([
    'missing schema version' => [
        ['id' => 'aws', 'name' => 'AWS', 'credentials' => ['fields' => [['key' => 'k', 'label' => 'K', 'type' => 'text', 'required' => true, 'sensitive' => false]]]],
        'schema_version',
    ],
    'unknown field type' => [
        ['schema_version' => 1, 'id' => 'aws', 'name' => 'AWS', 'credentials' => ['fields' => [['key' => 'k', 'label' => 'K', 'type' => 'bogus', 'required' => true, 'sensitive' => false]]]],
        'unknown type',
    ],
    'duplicate keys' => [
        ['schema_version' => 1, 'id' => 'aws', 'name' => 'AWS', 'credentials' => ['fields' => [
            ['key' => 'k', 'label' => 'First', 'type' => 'text', 'required' => true, 'sensitive' => false],
            ['key' => 'k', 'label' => 'Second', 'type' => 'text', 'required' => true, 'sensitive' => false],
        ]]],
        'duplicate',
    ],
    'select without options' => [
        ['schema_version' => 1, 'id' => 'aws', 'name' => 'AWS', 'credentials' => ['fields' => [['key' => 'k', 'label' => 'K', 'type' => 'select', 'required' => true, 'sensitive' => false]]]],
        'needs options',
    ],
]);

it('validates required fields', function (): void {
    $errors = ProviderDefinition::validateValues(awsDefinition(), ['access_key_id' => 'AKIA']);

    expect($errors)->toHaveKey('secret_access_key')
        ->not->toHaveKey('region');
});

it('accepts optional fields and type validation', function (): void {
    $definition = [
        'schema_version' => 1,
        'id' => 'example',
        'name' => 'Example',
        'credentials' => ['fields' => [
            ['key' => 'site', 'label' => 'Site', 'type' => 'url', 'required' => true, 'sensitive' => false, 'validation' => ['min' => 4, 'max' => 40]],
            ['key' => 'note', 'label' => 'Note', 'type' => 'text', 'required' => false, 'sensitive' => false],
        ]],
    ];

    ProviderDefinition::validate($definition);

    expect(ProviderDefinition::validateValues($definition, ['site' => 'not-a-url']))->toHaveKey('site')
        ->and(ProviderDefinition::validateValues($definition, ['site' => 'https://example.com']))->toBe([]);
});
