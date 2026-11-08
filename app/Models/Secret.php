<?php

namespace App\Models;

use App\Models\Concerns\HasSecretValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $expires_at
 */
class Secret extends Model
{
    use HasSecretValue;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'folder_id',
        'name',
        'service_id',
        'derived_from_id',
        'environment',
        'kind',
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

    protected static function booted(): void
    {
        static::saving(function (Secret $secret): void {
            if ($secret->derived_from_id !== null && (string) $secret->derived_from_id === (string) $secret->getKey()) {
                $secret->derived_from_id = null;
            }
        });
    }

    /**
     * @return BelongsTo<Folder, $this>
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * @return BelongsTo<Secret, $this>
     */
    public function derivedFrom(): BelongsTo
    {
        return $this->belongsTo(Secret::class, 'derived_from_id');
    }

    /**
     * @return HasMany<Secret, $this>
     */
    public function derivedSecrets(): HasMany
    {
        return $this->hasMany(Secret::class, 'derived_from_id');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'secret_tag');
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
