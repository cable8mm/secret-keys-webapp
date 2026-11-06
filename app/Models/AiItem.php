<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiItem extends Model
{
    protected $table = 'ai_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'value',
        'service',
    ];
}
