<?php

namespace App\Models;

use App\Models\Concerns\HasSecretValue;
use Illuminate\Database\Eloquent\Model;

class AiItem extends Model
{
    use HasSecretValue;

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
