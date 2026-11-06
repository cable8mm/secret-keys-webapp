<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfraItem extends Model
{
    protected $table = 'infra_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'value',
        'service',
    ];
}
