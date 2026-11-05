<?php

use App\Items\FingerprintDeriver;

it('returns the last four characters when at least eight remain', function () {
    $deriver = new FingerprintDeriver;

    expect($deriver->derive('abcdefgh1234'))->toBe('1234');
});

it('trims leading and trailing whitespace before deriving', function () {
    $deriver = new FingerprintDeriver;

    expect($deriver->derive('  abcdefgh  '))->toBe('efgh');
});

it('returns an empty fingerprint for fewer than eight characters', function () {
    $deriver = new FingerprintDeriver;

    expect($deriver->derive('abcdefgh'))->toBe('efgh')
        ->and($deriver->derive('abcdefg'))->toBe('');
});

it('preserves line breaks inside the value', function () {
    $deriver = new FingerprintDeriver;

    expect($deriver->derive("abcde\nfghij"))->toBe('ghij');
});
