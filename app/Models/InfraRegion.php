<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfraRegion extends Model
{
    protected $table = 'infra_regions';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'infra_id',
        'name',
    ];

    /**
     * @return BelongsTo<Infra, $this>
     */
    public function infra(): BelongsTo
    {
        return $this->belongsTo(Infra::class, 'infra_id');
    }
}
