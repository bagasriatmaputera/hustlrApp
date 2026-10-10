<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
