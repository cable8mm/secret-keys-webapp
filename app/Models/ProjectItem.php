<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $issued_date
 * @property Carbon|null $expires_at
 */
class ProjectItem extends Model
{
    protected $table = 'project_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'service',
        'environment',
        'kind',
        'tags',
        'value',
        'usage_location',
        'issued_date',
        'expires_at',
        'memo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expires_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
