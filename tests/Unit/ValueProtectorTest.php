<?php

use App\Values\ValueProtector;

it('round-trips a value exactly', function () {
    $protector = new ValueProtector;
    $value = "  first line\nsecond line  ";

    expect($protector->unprotect($protector->protect($value)))->toBe($value);
});

it('produces ciphertext that does not contain the value', function () {
    $protector = new ValueProtector;

    expect($protector->protect('secret-value'))->not->toContain('secret');
});
