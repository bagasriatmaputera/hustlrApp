<?php

namespace App\Models\Portfolio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $project_id
 * @property array{id?: string, en?: string}|null $title
 * @property array{id?: string, en?: string}|null $description
 * @property string|null $image
 * @property string|null $gif_image
 * @property list<array{image?: string, url?: string, caption?: array{id?: string, en?: string}, caption_en?: string, caption_id?: string}|string>|null $media_gallery
 * @property array{id?: string, en?: string}|null $problem_solution
 * @property array{id?: string, en?: string}|null $challenges_learnings
 * @property list<string>|null $tech_stack
 * @property string|null $live_url
 * @property string|null $repo_url
 * @property bool $featured
 * @property bool $is_live
 * @property bool $is_repo
 * @property int $order
 */
class PortfolioProject extends Model
{
    /** @use HasFactory<Factory<self>> */
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
        'media_gallery',
        'problem_solution',
        'challenges_learnings',
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
            'media_gallery' => 'array',
            'problem_solution' => 'array',
            'challenges_learnings' => 'array',
            'featured' => 'boolean',
            'is_live' => 'boolean',
            'is_repo' => 'boolean',
        ];
    }
}
