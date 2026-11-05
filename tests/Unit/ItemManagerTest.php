<?php

use App\Items\ItemInput;
use App\Items\ItemManager;
use App\Records\ProjectSummary;
use App\Values\ValueProtector;
use Tests\Support\FakeRecordStore;

function itemManagerWith(FakeRecordStore $store): ItemManager
{
    return new ItemManager($store, new ValueProtector);
}

function managerStore(): FakeRecordStore
{
    $store = new FakeRecordStore;
    $store->projects[] = new ProjectSummary(1, 'Default');

    return $store;
}

it('creates an item with a valid input', function () {
    $store = managerStore();

    $result = itemManagerWith($store)->create(new ItemInput(
        'API key',
        1,
        'Stripe',
        'production',
        '  secret  ',
        'Billing worker',
        '2026-11-05',
        'Memo text',
    ));

    expect($result->success)->toBeTrue()
        ->and($store->itemCreateCalls)->toBe(1)
        ->and($store->items)->toHaveCount(1)
        ->and($store->items[0]->name)->toBe('API key')
        ->and(app(ValueProtector::class)->unprotect($store->items[0]->protectedValue))->toBe('  secret  ');
});

it('reports ERR-001 for an empty item name', function () {
    $store = managerStore();

    $result = itemManagerWith($store)->create(new ItemInput('', 1, '', '', 'secret', '', '', ''));

    expect($result->success)->toBeFalse()
        ->and($result->errors[0]['code'])->toBe('ERR-001')
        ->and($store->itemCreateCalls)->toBe(0);
});

it('reports ERR-002 for an empty or whitespace-only value', function () {
    $store = managerStore();

    $result = itemManagerWith($store)->create(new ItemInput('name', 1, '', '', '   ', '', '', ''));

    expect($result->success)->toBeFalse()
        ->and($result->errors[0]['code'])->toBe('ERR-002')
        ->and($store->itemCreateCalls)->toBe(0);
});

it('reports ERR-003 when no project is selected', function () {
    $store = managerStore();

    $result = itemManagerWith($store)->create(new ItemInput('name', null, '', '', 'secret', '', '', ''));

    expect($result->success)->toBeFalse()
        ->and($result->errors[0]['code'])->toBe('ERR-003')
        ->and($store->itemCreateCalls)->toBe(0);
});

it('reports ERR-003 for a project that does not exist', function () {
    $store = managerStore();

    $result = itemManagerWith($store)->create(new ItemInput('name', 999, '', '', 'secret', '', '', ''));

    expect($result->success)->toBeFalse()
        ->and($result->errors[0]['code'])->toBe('ERR-003')
        ->and($store->itemCreateCalls)->toBe(0);
});

it('reports every failing condition at once', function () {
    $store = managerStore();

    $result = itemManagerWith($store)->create(new ItemInput('', null, '', '', '  ', '', '', ''));

    $codes = array_column($result->errors, 'code');

    expect($codes)->toBe(['ERR-001', 'ERR-002', 'ERR-003']);
});

it('reports ERR-007 when persistence fails', function () {
    $store = managerStore();
    $store->failWrite = true;

    $result = itemManagerWith($store)->create(new ItemInput('name', 1, '', '', 'secret', '', '', ''));

    expect($result->success)->toBeFalse()
        ->and($result->errors[0]['code'])->toBe('ERR-007')
        ->and($store->itemCreateCalls)->toBe(1)
        ->and($store->items)->toBe([]);
});
