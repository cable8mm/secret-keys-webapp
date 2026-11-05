<?php

use App\Projects\ProjectManager;
use App\Records\ProjectSummary;
use App\Support\TextComparison;
use Tests\Support\FakeRecordStore;

function projectManagerWith(FakeRecordStore $store): ProjectManager
{
    return new ProjectManager($store, new TextComparison);
}

it('rejects an empty project name', function () {
    $store = new FakeRecordStore;

    $result = projectManagerWith($store)->create('');

    expect($result->success)->toBeFalse()
        ->and($result->errorCode)->toBe('ERR-004')
        ->and($store->createCalls)->toBe(0);
});

it('rejects a whitespace-only project name', function () {
    $store = new FakeRecordStore;

    $result = projectManagerWith($store)->create("  \t\n ");

    expect($result->success)->toBeFalse()
        ->and($result->errorCode)->toBe('ERR-004')
        ->and($store->createCalls)->toBe(0);
});

it('creates a project with a non-empty name', function () {
    $store = new FakeRecordStore;

    $result = projectManagerWith($store)->create('alpha');

    expect($result->success)->toBeTrue()
        ->and($store->createCalls)->toBe(1)
        ->and($store->projects)->toHaveCount(1)
        ->and($store->projects[0]->name)->toBe('alpha');
});

it('orders projects by name with case-insensitive comparison', function () {
    $store = new FakeRecordStore;
    $store->projects = [
        new ProjectSummary(1, 'zulu'),
        new ProjectSummary(2, 'Alpha'),
        new ProjectSummary(3, 'beta'),
    ];

    $result = projectManagerWith($store)->list();

    expect($result->success)->toBeTrue()
        ->and(array_map(fn (ProjectSummary $project): string => $project->name, $result->projects))
        ->toBe(['Alpha', 'beta', 'zulu']);
});

it('returns a persistence failure when creation fails', function () {
    $store = new FakeRecordStore;
    $store->failWrite = true;

    $result = projectManagerWith($store)->create('alpha');

    expect($result->success)->toBeFalse()
        ->and($result->errorCode)->toBe('ERR-007')
        ->and($store->projects)->toBe([]);
});

it('returns a retrieval failure when listing fails', function () {
    $store = new FakeRecordStore;
    $store->failRead = true;

    $result = projectManagerWith($store)->list();

    expect($result->success)->toBeFalse()
        ->and($result->errorCode)->toBe('RETRIEVAL')
        ->and($result->projects)->toBe([]);
});
