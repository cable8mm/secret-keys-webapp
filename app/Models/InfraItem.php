<?php

namespace App\Models;

use App\Models\Concerns\HasSecretValue;
use Illuminate\Database\Eloquent\Model;

class InfraItem extends Model
{
    use HasSecretValue;

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
