<?php

namespace App\Models;

use App\Models\Concerns\HasSecretValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfraSecret extends Model
{
    use HasSecretValue;

    protected $table = 'infra_secrets';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'infra_id',
        'region_id',
        'folder_id',
        'name',
        'value',
        'url',
    ];

    /**
     * @return BelongsTo<Infra, $this>
     */
    public function infra(): BelongsTo
    {
        return $this->belongsTo(Infra::class, 'infra_id');
    }

    /**
     * @return BelongsTo<InfraRegion, $this>
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(InfraRegion::class, 'region_id');
    }

    /**
     * @return BelongsTo<Folder, $this>
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
}
