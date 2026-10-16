<?php

namespace App\Livewire\Projects;

use App\Concerns\AuthorizesPermissions;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Technology;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Add Project')]
class Create extends Component
{
    use AuthorizesPermissions;

    public string $project_name = '';

    public string $description = '';

    public string $ownership_type = '';

    public string $status_id = '';

    public ?string $client_id = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public ?string $link_testing = null;

    public ?string $link_prod = null;

    /** @var array<int, string> */
    public array $selectedTechnologies = [];

    public function mount(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Create Projects');
    }

    /**
     * @return Collection<int, ProjectStatus>
     */
    #[Computed]
    public function statuses(): Collection
    {
        return ProjectStatus::orderBy('status_name')->get();
    }

    /**
     * @return Collection<int, Client>
     */
    #[Computed]
    public function clients(): Collection
    {
        return Client::orderBy('client_name')->get();
    }

    /**
     * @return Collection<int, Technology>
     */
    #[Computed]
    public function technologies(): Collection
    {
        return Technology::orderBy('category')->orderBy('tech_name')->get();
    }

    public function removeTechnology(string $techId): void
    {
        $this->selectedTechnologies = array_values(array_diff($this->selectedTechnologies, [$techId]));
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'ownership_type' => ['required', 'string', 'in:Internal,Client,Freelance'],
            'status_id' => ['required', 'exists:project_statuses,status_id'],
            'client_id' => ['nullable', 'exists:clients,client_id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'link_testing' => ['nullable', 'url', 'max:255'],
            'link_prod' => ['nullable', 'url', 'max:255'],
            'selectedTechnologies' => ['nullable', 'array'],
            'selectedTechnologies.*' => ['exists:technologies,tech_id'],
        ];
    }

    public function save(): void
    {
        $this->authorizePermission('IPS - PROJECT - Can Create Projects');

        $validated = $this->validate();

        $project = Project::create([
            'project_name' => $validated['project_name'],
            'description' => $validated['description'],
            'ownership_type' => $validated['ownership_type'],
            'status_id' => $validated['status_id'],
            'client_id' => $validated['client_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'link_testing' => $validated['link_testing'],
            'link_prod' => $validated['link_prod'],
            'created_by' => Auth::id(),
        ]);

        if (! empty($validated['selectedTechnologies'])) {
            $project->technologies()->attach($validated['selectedTechnologies']);
        }

        $project->members()->attach(Auth::id(), [
            'role_in_project' => 'Owner',
            'joined_at' => now(),
        ]);

        Flux::toast(variant: 'success', text: __('Project created successfully.'));

        $this->redirect(route('projects.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.projects.create');
    }
}
