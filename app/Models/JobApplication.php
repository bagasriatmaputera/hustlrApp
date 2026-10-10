<?php

namespace App\Models;

use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $job_application_id
 * @property int $user_id
 * @property string $company_name
 * @property string $position
 * @property string $contract_status
 * @property string $apply_status
 * @property string|null $salary
 * @property string|null $location
 * @property string $workplace_type
 * @property string|null $job_url
 * @property string|null $benefits
 * @property string|null $notes
 * @property Carbon|null $applied_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 */
class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'job_application_id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'job_application_id';
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'contract_status',
        'apply_status',
        'salary',
        'location',
        'workplace_type',
        'job_url',
        'benefits',
        'notes',
        'applied_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'applied_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Available Contract Statuses
     *
     * @return array<string, string>
     */
    public static function contractStatuses(): array
    {
        return [
            'contract' => 'Contract',
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'freelance' => 'Freelance',
            'internship' => 'Internship',
        ];
    }

    /**
     * Available Apply Statuses
     *
     * @return array<string, string>
     */
    public static function applyStatuses(): array
    {
        return [
            'applied' => 'Applied',
            'hr_review' => 'HR Review',
            'interview' => 'Interview',
            'interview_user' => 'Interview User',
            'offering_letter' => 'Offering Letter',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
        ];
    }

    /**
     * Available Workplace Types
     *
     * @return array<string, string>
     */
    public static function workplaceTypes(): array
    {
        return [
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            'onsite' => 'Onsite',
        ];
    }

    /**
     * Badge styles and labels for Apply Status
     *
     * @return array{label: string, bg: string, text: string, dot: string, icon: string}
     */
    public static function getApplyStatusConfig(string $status): array
    {
        return match ($status) {
            'hr_review' => [
                'label' => 'HR Review',
                'bg' => 'bg-amber-100 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/40',
                'text' => 'text-amber-700 dark:text-amber-300',
                'dot' => 'bg-amber-500',
                'icon' => 'clock',
            ],
            'interview' => [
                'label' => 'Interview',
                'bg' => 'bg-blue-100 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/40',
                'text' => 'text-blue-700 dark:text-blue-300',
                'dot' => 'bg-blue-500',
                'icon' => 'chat-bubble-left-right',
            ],
            'interview_user' => [
                'label' => 'Interview User',
                'bg' => 'bg-purple-100 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/40',
                'text' => 'text-purple-700 dark:text-purple-300',
                'dot' => 'bg-purple-500',
                'icon' => 'user-group',
            ],
            'offering_letter' => [
                'label' => 'Offering Letter',
                'bg' => 'bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/40',
                'text' => 'text-emerald-700 dark:text-emerald-300',
                'dot' => 'bg-emerald-500',
                'icon' => 'document-check',
            ],
            'accepted' => [
                'label' => 'Accepted',
                'bg' => 'bg-green-100 dark:bg-green-950/40 border border-green-200 dark:border-green-800/40',
                'text' => 'text-green-700 dark:text-green-300',
                'dot' => 'bg-green-500',
                'icon' => 'check-badge',
            ],
            'rejected' => [
                'label' => 'Rejected',
                'bg' => 'bg-rose-100 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/40',
                'text' => 'text-rose-700 dark:text-rose-300',
                'dot' => 'bg-rose-500',
                'icon' => 'x-circle',
            ],
            default => [
                'label' => 'Applied',
                'bg' => 'bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700',
                'text' => 'text-zinc-700 dark:text-zinc-300',
                'dot' => 'bg-zinc-500',
                'icon' => 'paper-airplane',
            ],
        };
    }

    /**
     * Badge styles and labels for Contract Status
     *
     * @return array{label: string, bg: string, text: string}
     */
    public static function getContractStatusConfig(string $status): array
    {
        return match ($status) {
            'contract' => [
                'label' => 'Contract',
                'bg' => 'bg-indigo-100 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/40',
                'text' => 'text-indigo-700 dark:text-indigo-300',
            ],
            'full_time' => [
                'label' => 'Full Time',
                'bg' => 'bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/40',
                'text' => 'text-emerald-700 dark:text-emerald-300',
            ],
            'part_time' => [
                'label' => 'Part Time',
                'bg' => 'bg-cyan-100 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800/40',
                'text' => 'text-cyan-700 dark:text-cyan-300',
            ],
            'freelance' => [
                'label' => 'Freelance',
                'bg' => 'bg-amber-100 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/40',
                'text' => 'text-amber-700 dark:text-amber-300',
            ],
            'internship' => [
                'label' => 'Internship',
                'bg' => 'bg-purple-100 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/40',
                'text' => 'text-purple-700 dark:text-purple-300',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', $status)),
                'bg' => 'bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700',
                'text' => 'text-zinc-700 dark:text-zinc-300',
            ],
        };
    }
}
