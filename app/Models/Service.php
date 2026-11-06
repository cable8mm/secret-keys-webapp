<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'is_project',
        'is_ai',
        'is_infra',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_project' => 'boolean',
            'is_ai' => 'boolean',
            'is_infra' => 'boolean',
        ];
    }

    /**
     * @return HasMany<ProjectItem, $this>
     */
    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }

    /**
     * @return HasMany<AiItem, $this>
     */
    public function aiItems(): HasMany
    {
        return $this->hasMany(AiItem::class);
    }

    /**
     * @return HasMany<InfraItem, $this>
     */
    public function infraItems(): HasMany
    {
        return $this->hasMany(InfraItem::class);
    }
}
