<?php

namespace App\Models;

use Database\Factories\TechnologyFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technology extends Model
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'tech_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tech_name',
        'category',
    ];

    /**
     * @return BelongsToMany<Project, $this>
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_technologies', 'tech_id', 'project_id')
            ->withTimestamps();
    }
}
