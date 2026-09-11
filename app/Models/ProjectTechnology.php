<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTechnology extends Model
{
    use SoftDeletes;

    protected $primaryKey = ['project_id', 'tech_id'];

    public $incrementing = false;

    protected $fillable = [
        'project_id',
        'tech_id',
    ];

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * @return BelongsTo<Technology, $this>
     */
    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class, 'tech_id', 'tech_id');
    }
}
