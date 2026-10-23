<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $experience_id
 * @property string $company_name
 * @property string $position
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $location
 * @property string $workplace_type
 * @property string $contract_type
 * @property int $order
 */
class PortfolioExperience extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'experience_id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'company_name',
        'position',
        'description',
        'start_date',
        'end_date',
        'location',
        'workplace_type',
        'contract_type',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'description' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * @return HasMany<PortfolioJobDesc, $this>
     */
    public function jobDescs(): HasMany
    {
        return $this->hasMany(PortfolioJobDesc::class, 'experience_id', 'experience_id');
    }
}
