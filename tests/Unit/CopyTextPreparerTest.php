<?php

use App\Values\CopyTextPreparer;

it('trims leading and trailing whitespace', function () {
    expect((new CopyTextPreparer)->prepare("  first line\nsecond line  "))->toBe("first line\nsecond line");
});

it('preserves line breaks and internal spaces', function () {
    expect((new CopyTextPreparer)->prepare("  a b \n c  "))->toBe("a b \n c");
});

it('normalizes a pem value with exactly one trailing newline', function () {
    expect((new CopyTextPreparer)->prepare("  -----BEGIN KEY-----\r\nabc\rdef  \r\n", 'pem'))
        ->toBe("-----BEGIN KEY-----\nabc\ndef\n");
});

it('keeps an existing single trailing newline on a pem value', function () {
    expect((new CopyTextPreparer)->prepare("line1\nline2\n", 'pem'))->toBe("line1\nline2\n");
});

it('uses the plain rule when kind is not pem', function () {
    expect((new CopyTextPreparer)->prepare("  abc  \n", 'token'))->toBe('abc')
        ->and((new CopyTextPreparer)->prepare("  abc  \n", null))->toBe('abc')
        ->and((new CopyTextPreparer)->prepare("  abc  \n", ''))->toBe('abc');
});
