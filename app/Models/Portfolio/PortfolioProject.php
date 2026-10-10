<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioProject extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'project_id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'gif_image',
        'tech_stack',
        'live_url',
        'repo_url',
        'featured',
        'is_live',
        'is_repo',
        'order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'description' => 'array',
            'tech_stack' => 'array',
            'featured' => 'boolean',
            'is_live' => 'boolean',
            'is_repo' => 'boolean',
        ];
    }
}
