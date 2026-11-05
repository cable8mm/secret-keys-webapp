<?php

use App\Models\Project;
use Illuminate\Support\Facades\DB;

it('stores values encrypted at rest and returns them exactly', function () {
    $project = Project::create(['name' => 'Payments']);

    $value = "  s3cr3t-line-1\nline-2  ";

    $item = $project->items()->create([
        'name' => 'API key',
        'value' => $value,
    ]);

    $raw = DB::table('items')->where('id', $item->id)->value('value');

    expect($raw)->toBeString()
        ->and($raw)->not->toContain('s3cr3t')
        ->and($item->fresh()?->value)->toBe($value);
});

it('persists optional item fields and the project relation', function () {
    $project = Project::create(['name' => 'Payments']);

    $item = $project->items()->create([
        'name' => 'API key',
        'service' => 'Stripe',
        'environment' => 'production',
        'value' => 'secret-value',
        'usage_location' => 'Billing worker',
        'issued_date' => '2026-11-05',
        'memo' => 'Rotated quarterly',
    ]);

    $item->refresh();

    expect($item->project->is($project))->toBeTrue()
        ->and($item->service)->toBe('Stripe')
        ->and($item->environment)->toBe('production')
        ->and($item->usage_location)->toBe('Billing worker')
        ->and($item->issued_date?->toDateString())->toBe('2026-11-05')
        ->and($item->memo)->toBe('Rotated quarterly');
});
