<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany<Secret, $this>
     */
    public function secrets(): BelongsToMany
    {
        return $this->belongsToMany(Secret::class, 'secret_tag');
    }

    /**
     * @param  list<string>  $names
     * @return list<int>
     */
    public static function idsForNames(array $names): array
    {
        $ids = [];

        foreach ($names as $name) {
            $name = trim((string) $name);

            if ($name === '') {
                continue;
            }

            $ids[] = static::query()->firstOrCreate(['name' => $name])->getKey();
        }

        return array_values(array_unique($ids));
    }
}
