<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioJobDesc extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'job_desc_id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'experience_id',
        'description',
        'learning_outcomes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'learning_outcomes' => 'array',
        ];
    }

    /**
     * @return BelongsTo<PortfolioExperience, $this>
     */
    public function experience(): BelongsTo
    {
        return $this->belongsTo(PortfolioExperience::class, 'experience_id', 'experience_id');
    }
}
