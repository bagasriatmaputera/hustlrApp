<?php

namespace App\Livewire\Portfolio\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Portfolio\PortfolioProject;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Portfolio - Add Project')]
class Create extends Component
{
    use AuthorizesPermissions;

    public string $title_id = '';

    public string $title_en = '';

    public string $description_id = '';

    public string $description_en = '';

    public string $image = '';

    public string $gif_image = '';

    public string $tech_stack_raw = '';

    public string $live_url = '';

    public string $repo_url = '';

    public bool $featured = false;

    public bool $is_live = false;

    public bool $is_repo = false;

    public int $order = 0;

    public function mount(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');
    }

    public function save(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');

        $this->validate([
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'description_id' => ['required', 'string'],
            'description_en' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:500'],
            'gif_image' => ['nullable', 'string', 'max:500'],
            'tech_stack_raw' => ['required', 'string'],
            'live_url' => ['nullable', 'url', 'max:500'],
            'repo_url' => ['nullable', 'url', 'max:500'],
            'featured' => ['boolean'],
            'is_live' => ['boolean'],
            'is_repo' => ['boolean'],
            'order' => ['integer', 'min:0'],
        ]);

        $techStack = array_values(array_filter(array_map('trim', explode(',', $this->tech_stack_raw))));

        PortfolioProject::create([
            'title' => ['id' => $this->title_id, 'en' => $this->title_en],
            'description' => ['id' => $this->description_id, 'en' => $this->description_en],
            'image' => $this->image ?: null,
            'gif_image' => $this->gif_image ?: null,
            'tech_stack' => $techStack,
            'live_url' => $this->live_url ?: null,
            'repo_url' => $this->repo_url ?: null,
            'featured' => $this->featured,
            'is_live' => $this->is_live,
            'is_repo' => $this->is_repo,
            'order' => $this->order,
        ]);

        Flux::toast(variant: 'success', text: __('Project created successfully.'));

        $this->redirectRoute('portfolio.projects.index', navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.projects.create');
    }
}
