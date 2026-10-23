<?php

namespace App\Livewire\Portfolio\Experiences;

use App\Concerns\AuthorizesPermissions;
use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioJobDesc;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Portfolio - Edit Experience')]
class Edit extends Component
{
    use AuthorizesPermissions;

    public PortfolioExperience $portfolioExperience;

    public string $company_name = '';

    public string $position = '';

    /** @var array{id: string, en: string} */
    public array $description = [
        'id' => '',
        'en' => '',
    ];

    public string $start_date = '';

    public string $end_date = '';

    public string $location = '';

    public string $workplace_type = 'On-site';

    public string $contract_type = 'Full-time';

    public int $order = 0;

    /** @var list<array{job_desc_id: string|null, description: string}> */
    public array $job_descs = [];

    public function mount(PortfolioExperience $portfolioExperience): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Experiences');

        $this->portfolioExperience = $portfolioExperience->load('jobDescs');
        $this->company_name = (string) ($portfolioExperience->company_name ?? '');
        $this->position = (string) ($portfolioExperience->position ?? '');
        $this->description = [
            'id' => (string) ($portfolioExperience->description['id'] ?? ''),
            'en' => (string) ($portfolioExperience->description['en'] ?? ''),
        ];
        $this->start_date = $portfolioExperience->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $portfolioExperience->end_date?->format('Y-m-d') ?? '';
        $this->location = (string) ($portfolioExperience->location ?? '');
        $this->workplace_type = (string) ($portfolioExperience->workplace_type ?? 'On-site');
        $this->contract_type = (string) ($portfolioExperience->contract_type ?? 'Full-time');
        $this->order = (int) ($portfolioExperience->order ?? 0);
        $this->job_descs = $portfolioExperience->jobDescs->map(fn ($jd) => [
            'job_desc_id' => $jd->job_desc_id,
            'description' => $jd->description,
        ])->toArray();

        if (empty($this->job_descs)) {
            $this->job_descs = [['job_desc_id' => null, 'description' => '']];
        }
    }

    public function addJobDesc(): void
    {
        $this->job_descs[] = ['job_desc_id' => null, 'description' => ''];
    }

    public function removeJobDesc(int $index): void
    {
        $jobDescId = $this->job_descs[$index]['job_desc_id'] ?? null;

        if ($jobDescId) {
            PortfolioJobDesc::find($jobDescId)?->delete();
        }

        unset($this->job_descs[$index]);
        $this->job_descs = array_values($this->job_descs);
    }

    public function save(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Experiences');

        $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'description' => ['array'],
            'description.id' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'workplace_type' => ['required', 'string', 'max:100'],
            'contract_type' => ['required', 'string', 'max:100'],
            'order' => ['integer', 'min:0'],
            'job_descs' => ['array'],
            'job_descs.*.description' => ['required', 'string'],
        ]);

        $this->portfolioExperience->update([
            'company_name' => $this->company_name,
            'position' => $this->position,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'location' => $this->location,
            'workplace_type' => $this->workplace_type,
            'contract_type' => $this->contract_type,
            'order' => $this->order,
        ]);

        foreach ($this->job_descs as $jobDescData) {
            if (empty($jobDescData['description'])) {
                continue;
            }

            if (! empty($jobDescData['job_desc_id'])) {
                PortfolioJobDesc::find($jobDescData['job_desc_id'])?->update([
                    'description' => $jobDescData['description'],
                ]);
            } else {
                PortfolioJobDesc::create([
                    'experience_id' => $this->portfolioExperience->experience_id,
                    'description' => $jobDescData['description'],
                    'learning_outcomes' => null,
                ]);
            }
        }

        Flux::toast(variant: 'success', text: __('Experience updated successfully.'));

        $this->redirectRoute('portfolio.experiences.index', navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.experiences.edit');
    }
}
