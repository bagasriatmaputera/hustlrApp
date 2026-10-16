<?php

namespace App\Livewire\Portfolio\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Portfolio\PortfolioProject;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Portfolio - Projects')]
class Index extends Component
{
    use AuthorizesPermissions;

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');
    }

    /**
     * @return Collection<int, PortfolioProject>
     */
    #[Computed]
    public function projects(): Collection
    {
        return PortfolioProject::query()
            ->when($this->search, fn ($q) => $q->whereJsonContains('title->en', $this->search)
                ->orWhereJsonContains('title->id', $this->search))
            ->orderBy('order')
            ->get();
    }

    public function delete(string $projectId): void
    {
        $this->authorizePermission('PORTFOLIO - Can Manage Projects');

        PortfolioProject::findOrFail($projectId)->delete();
        Flux::toast(variant: 'success', text: __('Project deleted successfully.'));
    }

    public function render(): mixed
    {
        return view('livewire.portfolio.projects.index');
    }
}
