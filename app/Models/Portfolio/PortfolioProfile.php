<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioProfile extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'profile_id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'job_position',
        'summary',
        'image_path',
        'location',
        'resume_url',
        'available_for_work',
        'socials',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_for_work' => 'boolean',
            'socials' => 'array',
        ];
    }
}
