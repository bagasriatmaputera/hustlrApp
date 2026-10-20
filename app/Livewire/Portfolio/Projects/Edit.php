<?php

namespace App\Livewire\Portfolio\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Portfolio\PortfolioProject;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Portfolio - Edit Project')]
class Edit extends Component
{
    use AuthorizesPermissions;

    public PortfolioProject $portfolioProject;

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

    public function mount(PortfolioProject $portfolioProject): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');

        $this->portfolioProject = $portfolioProject;
        $this->title_id = $portfolioProject->title['id'] ?? '';
        $this->title_en = $portfolioProject->title['en'] ?? '';
        $this->description_id = $portfolioProject->description['id'] ?? '';
        $this->description_en = $portfolioProject->description['en'] ?? '';
        $this->image = $portfolioProject->image ?? '';
        $this->gif_image = $portfolioProject->gif_image ?? '';
        $this->tech_stack_raw = implode(', ', $portfolioProject->tech_stack ?? []);
        $this->live_url = $portfolioProject->live_url ?? '';
        $this->repo_url = $portfolioProject->repo_url ?? '';
        $this->featured = (bool) ($portfolioProject->featured ?? false);
        $this->is_live = (bool) ($portfolioProject->is_live ?? false);
        $this->is_repo = (bool) ($portfolioProject->is_repo ?? false);
        $this->order = (int) ($portfolioProject->order ?? 0);
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

        $this->portfolioProject->update([
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

        Flux::toast(variant: 'success', text: __('Project updated successfully.'));

        $this->redirectRoute('portfolio.projects.index', navigate: true);
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.projects.edit');
    }
}
