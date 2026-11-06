<?php

namespace App\Models;

use App\Models\Concerns\HasSecretValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $expires_at
 */
class ProjectItem extends Model
{
    use HasSecretValue;

    protected $table = 'project_items';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'name',
        'service_id',
        'environment',
        'kind',
        'tags',
        'value',
        'url',
        'expires_at',
        'memo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
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

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
