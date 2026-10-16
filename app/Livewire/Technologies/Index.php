<?php

namespace App\Livewire\Technologies;

use App\Concerns\AuthorizesPermissions;
use App\Models\Technology;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Technologies')]
class Index extends Component
{
    use AuthorizesPermissions, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $categoryFilter = '';

    // Modal Visibility
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    // Form Properties
    public ?string $selectedTechId = null;

    public string $techName = '';

    public string $category = 'Backend';

    public ?string $deleteTechName = null;

    public int $deleteProjectsCount = 0;

    public function mount(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Manage Technologies');
    }

    /**
     * @return list<string>
     */
    public function defaultCategories(): array
    {
        return [
            'Frontend',
            'Backend',
            'Mobile',
            'Database',
            'DevOps & Cloud',
            'UI & Styling',
            'Programming Language',
            'Testing & QA',
            'AI & Data Science',
            'Tools & Collaboration',
        ];
    }

    /**
     * @return LengthAwarePaginator<int, Technology>
     */
    #[Computed]
    public function technologies(): LengthAwarePaginator
    {
        return Technology::query()
            ->withCount('projects')
            ->when($this->search, function (Builder $query, string $search) {
                $query->where('tech_name', 'like', "%{$search}%");
            })
            ->when($this->categoryFilter, function (Builder $query, string $category) {
                $query->where('category', $category);
            })
            ->orderBy('category')
            ->orderBy('tech_name')
            ->paginate(15);
    }

    /**
     * @return Collection<int, string>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Technology::query()
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    #[Computed]
    public function totalTechnologies(): int
    {
        return Technology::count();
    }

    #[Computed]
    public function totalCategories(): int
    {
        return Technology::distinct('category')->count('category');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'categoryFilter']);
        $this->resetPage();
    }

    // --- Create Action ---

    public function openCreateModal(): void
    {
        $this->reset(['techName', 'selectedTechId']);
        $this->category = 'Backend';
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function createTechnology(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Manage Technologies');

        $this->validate([
            'techName' => ['required', 'string', 'max:100', 'unique:technologies,tech_name'],
            'category' => ['required', 'string', 'max:50'],
        ]);

        Technology::create([
            'tech_name' => trim($this->techName),
            'category' => trim($this->category),
        ]);

        $this->showCreateModal = false;
        $this->reset(['techName']);
        unset($this->technologies, $this->categories, $this->totalTechnologies, $this->totalCategories);

        Flux::toast(variant: 'success', text: __('Technology added successfully.'));
    }

    // --- Edit Action ---

    public function openEditModal(string $techId): void
    {
        $tech = Technology::findOrFail($techId);

        $this->selectedTechId = $tech->tech_id;
        $this->techName = $tech->tech_name;
        $this->category = $tech->category;
        $this->resetValidation();

        $this->showEditModal = true;
    }

    public function updateTechnology(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Manage Technologies');

        $this->validate([
            'techName' => [
                'required',
                'string',
                'max:100',
                Rule::unique('technologies', 'tech_name')->ignore($this->selectedTechId, 'tech_id'),
            ],
            'category' => ['required', 'string', 'max:50'],
        ]);

        $tech = Technology::findOrFail($this->selectedTechId);
        $tech->update([
            'tech_name' => trim($this->techName),
            'category' => trim($this->category),
        ]);

        $this->showEditModal = false;
        $this->reset(['techName', 'selectedTechId']);
        unset($this->technologies, $this->categories);

        Flux::toast(variant: 'success', text: __('Technology updated successfully.'));
    }

    // --- Delete Action ---

    public function confirmDelete(string $techId): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Manage Technologies');

        $tech = Technology::withCount('projects')->findOrFail($techId);

        $this->selectedTechId = $tech->tech_id;
        $this->deleteTechName = $tech->tech_name;
        $this->deleteProjectsCount = $tech->projects_count;

        $this->showDeleteModal = true;
    }

    public function deleteTechnology(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Manage Technologies');

        if (! $this->selectedTechId) {
            return;
        }

        $tech = Technology::findOrFail($this->selectedTechId);
        $tech->delete();

        $this->showDeleteModal = false;
        $this->reset(['selectedTechId', 'deleteTechName', 'deleteProjectsCount']);
        unset($this->technologies, $this->categories, $this->totalTechnologies, $this->totalCategories);

        Flux::toast(variant: 'success', text: __('Technology deleted successfully.'));
    }

    /**
     * Helper to get category styling colors
     *
     * @return array<string, string>
     */
    public static function categoryBadge(string $category): array
    {
        return match ($category) {
            'Backend' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-300'],
            'Frontend' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-300'],
            'Mobile' => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-700 dark:text-purple-300'],
            'Database' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-300'],
            'DevOps & Cloud' => ['bg' => 'bg-cyan-100 dark:bg-cyan-900/30', 'text' => 'text-cyan-700 dark:text-cyan-300'],
            'UI & Styling' => ['bg' => 'bg-pink-100 dark:bg-pink-900/30', 'text' => 'text-pink-700 dark:text-pink-300'],
            'Programming Language' => ['bg' => 'bg-indigo-100 dark:bg-indigo-900/30', 'text' => 'text-indigo-700 dark:text-indigo-300'],
            'Testing & QA' => ['bg' => 'bg-teal-100 dark:bg-teal-900/30', 'text' => 'text-teal-700 dark:text-teal-300'],
            'AI & Data Science' => ['bg' => 'bg-rose-100 dark:bg-rose-900/30', 'text' => 'text-rose-700 dark:text-rose-300'],
            'Tools & Collaboration' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30', 'text' => 'text-orange-700 dark:text-orange-300'],
            default => ['bg' => 'bg-zinc-100 dark:bg-zinc-800', 'text' => 'text-zinc-700 dark:text-zinc-300'],
        };
    }

    public function render(): View
    {
        return view('livewire.technologies.index');
    }
}
