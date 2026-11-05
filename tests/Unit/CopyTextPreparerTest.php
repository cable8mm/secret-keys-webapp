<?php

use App\Values\CopyTextPreparer;

it('trims leading and trailing whitespace', function () {
    expect((new CopyTextPreparer)->prepare("  first line\nsecond line  "))->toBe("first line\nsecond line");
});

it('preserves line breaks and internal spaces', function () {
    expect((new CopyTextPreparer)->prepare("  a b \n c  "))->toBe("a b \n c");
});
