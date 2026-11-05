<?php

use App\Models\Item;

it('casts the item value as encrypted', function () {
    expect((new Item)->getCasts())->toHaveKey('value', 'encrypted');
});
