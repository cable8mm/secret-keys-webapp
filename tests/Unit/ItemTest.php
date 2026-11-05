<?php

use App\Models\Item;

it('casts the item issued date as a date', function () {
    expect((new Item)->getCasts())->toHaveKey('issued_date', 'date');
});
