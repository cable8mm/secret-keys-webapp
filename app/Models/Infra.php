<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Infra extends Model
{
    protected $table = 'infras';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'note',
        'archived',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'archived' => 'boolean',
        ];
    }

    /**
     * @return HasMany<InfraSecret, $this>
     */
    public function secrets(): HasMany
    {
        return $this->hasMany(InfraSecret::class, 'infra_id');
    }
}
