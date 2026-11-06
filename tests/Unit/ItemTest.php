<?php

use App\Models\ProjectItem;

it('casts the project item issued date as a date', function () {
    expect((new ProjectItem)->getCasts())->toHaveKey('issued_date', 'date');
});
