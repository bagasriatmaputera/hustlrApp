<?php

namespace App\Livewire\Portfolio\Experiences;

use App\Models\Portfolio\PortfolioExperience;
use App\Models\Portfolio\PortfolioJobDesc;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Portfolio - Add Experience')]
class Create extends Component
{
    public string $company_name = '';

    public string $position = '';

    public string $start_date = '';

    public string $end_date = '';

    public string $location = '';

    public string $workplace_type = 'On-site';

    public string $contract_type = 'Full-time';

    public int $order = 0;

    /** @var list<array{description: string}> */
    public array $job_descs = [
        ['description' => ''],
    ];

    public function addJobDesc(): void
    {
        $this->job_descs[] = ['description' => ''];
    }

    public function removeJobDesc(int $index): void
    {
        unset($this->job_descs[$index]);
        $this->job_descs = array_values($this->job_descs);
    }

    public function save(): void
    {
        $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'workplace_type' => ['required', 'string', 'max:100'],
            'contract_type' => ['required', 'string', 'max:100'],
            'order' => ['integer', 'min:0'],
            'job_descs' => ['array'],
            'job_descs.*.description' => ['required', 'string'],
        ]);

        $experience = PortfolioExperience::create([
            'company_name' => $this->company_name,
            'position' => $this->position,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'location' => $this->location,
            'workplace_type' => $this->workplace_type,
            'contract_type' => $this->contract_type,
            'order' => $this->order,
        ]);

        foreach ($this->job_descs as $jobDesc) {
            if (! empty($jobDesc['description'])) {
                PortfolioJobDesc::create([
                    'experience_id' => $experience->experience_id,
                    'description' => $jobDesc['description'],
                    'learning_outcomes' => null,
                ]);
            }
        }

        Flux::toast(variant: 'success', text: __('Experience created successfully.'));

        $this->redirectRoute('portfolio.experiences.index', navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.experiences.create');
    }
}
