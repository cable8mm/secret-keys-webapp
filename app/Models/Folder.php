<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'archived',
        'note',
        'url',
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
     * @return HasMany<Secret, $this>
     */
    public function secrets(): HasMany
    {
        return $this->hasMany(Secret::class);
    }

    /**
     * @return HasMany<InfraSecret, $this>
     */
    public function infraSecrets(): HasMany
    {
        return $this->hasMany(InfraSecret::class, 'folder_id');
    }
}
