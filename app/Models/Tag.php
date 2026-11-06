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
     * @return BelongsToMany<ProjectItem, $this>
     */
    public function projectItems(): BelongsToMany
    {
        return $this->belongsToMany(ProjectItem::class, 'project_item_tag');
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
