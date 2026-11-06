<?php

namespace App\Models;

use App\Models\Concerns\HasSecretValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'service_id',
    ];

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
