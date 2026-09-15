# Hustlr Engineering & Architecture Standards

> **Comprehensive Architecture Guide, Code Blueprints, and Standard Operating Procedures for Hustlr (Manage Side Hustle - MSH)**  
> *Stack: Laravel 13.x | Livewire 4.x | Flux UI 2.x | Tailwind CSS v4 | PHP 8.3+ | Larastan Level 7*

---

## 📑 Table of Contents

1. [Architectural Principles & System Design](#1-architectural-principles--system-design)
2. [Component Directory Structure](#2-component-directory-structure)
3. [Domain Models & Database Architecture (UUID Standard)](#3-domain-models--database-architecture-uuid-standard)
4. [Livewire 4 & Flux UI Component Standards](#4-livewire-4--flux-ui-component-standards)
5. [Controller Standards (API & Web)](#5-controller-standards-api--web)
6. [Service Layer Standards](#6-service-layer-standards)
7. [Repository Layer Standards](#7-repository-layer-standards)
8. [Action Layer Standards](#8-action-layer-standards)
9. [Form Requests & Input Validation](#9-form-requests--input-validation)
10. [Role-Based Access Control (RBAC) & Permissions](#10-role-based-access-control-rbac--permissions)
11. [Testing Standards with PHPUnit 12](#11-testing-standards-with-phpunit-12)
12. [Code Quality, Type Safety & Static Analysis](#12-code-quality-type-safety--static-analysis)
13. [CLI Artisan Generators Cheatsheet](#13-cli-artisan-generators-cheatsheet)

---

## 1. Architectural Principles & System Design

Hustlr uses a layered, pragmatic architecture tailored for rapid development, maximum maintainability, and rock-solid type safety.

### Separation of Concerns (SoC)

```
┌────────────────────────────────────────────────────────┐
│                   Routing Layer                        │
│   (routes/web.php, routes/projects.php, routes/api.php) │
└───────────────────────┬────────────────────────────────┘
                        │
         ┌──────────────┴──────────────┐
         ▼                             ▼
┌──────────────────┐          ┌──────────────────┐
│  Livewire 4 UI   │          │  HTTP / API      │
│  (Interactive)   │          │  Controllers     │
└────────┬─────────┘          └────────┬─────────┘
         │                             │
         └──────────────┬──────────────┘
                        ▼
         ┌─────────────────────────────┐
         │     Service Layer / Action  │
         │   (Business Logic & Trx)    │
         └──────────────┬──────────────┘
                        ▼
         ┌─────────────────────────────┐
         │      Repository Layer       │
         │    (Data Access & Scopes)   │
         └──────────────┬──────────────┘
                        ▼
         ┌─────────────────────────────┐
         │    Eloquent Models (UUID)   │
         │       & Database Schema     │
         └─────────────────────────────┘
```

### Responsibility Matrix

- **Livewire Components**: Handle reactive presentation state, component UI events, and render Flux UI components. Delegate complex multi-step transactions to Services or Actions.
- **Controllers**: Handle stateless HTTP requests (JSON APIs, file streams, external webhooks, OAuth callbacks). Validate via Form Requests and transform responses via Eloquent API Resources.
- **Services**: Contain domain business rules, cross-entity orchestration, transactional safety (`DB::transaction`), and notifications.
- **Repositories**: Abstract database querying, encapsulate complex joins/eager-loads, and provide clean mockable interfaces for tests.
- **Actions**: Discrete, single-purpose command classes adhering strictly to the Single Responsibility Principle (SRP).
- **Models**: Represent schema entities with UUID keys, explicit mass-assignment protection (`$fillable`), type casts, and typed relationship methods.

---

## 2. Component Directory Structure

All components must strictly reside in their designated directories:

```text
app/
├── Actions/
│   ├── Fortify/                  # Fortify authentication actions
│   └── Projects/                 # Domain-specific action classes (e.g. DuplicateProjectAction)
├── Concerns/                     # Shared traits (e.g. HasUuids, Filterable)
├── Console/                      # Artisan console commands
├── Http/
│   ├── Controllers/
│   │   ├── Api/                  # RESTful JSON API controllers
│   │   └── Controller.php        # Base controller
│   ├── Middleware/               # HTTP middleware
│   ├── Requests/                 # Form Request validation classes
│   └── Resources/                # Eloquent API resource transformers
├── Livewire/
│   ├── Projects/                 # Feature-grouped Livewire components (Index, Create, Edit)
│   ├── Settings/                 # Profile, Appearance, Security components
│   └── Shared/                   # Reusable UI widgets & modal dialogs
├── Models/                       # Eloquent models (Project, Client, Tech, User, etc.)
├── PermissionManager/            # Role & Permission matrix definitions
│   └── IntegratedPersonalSystem/ # Module-scoped role permission sets
├── Providers/                    # Service providers (RepositoryServiceProvider, AppServiceProvider)
├── Repositories/
│   ├── Contracts/                # Repository interfaces (e.g. ProjectRepositoryInterface)
│   └── Eloquent/                 # Eloquent repository implementations
└── Services/                     # Domain services (e.g. ProjectService, BillingService)
```

---

## 3. Domain Models & Database Architecture (UUID Standard)

### UUID Primary Key Invariant
All domain models in Hustlr MUST use UUID primary keys to prevent enumeration attacks and simplify distributed data scaling:

1. Use `Illuminate\Database\Eloquent\Concerns\HasUuids`.
2. Explicitly define `$primaryKey = '{model}_id'`.
3. Set `public $incrementing = false;`.
4. Set `protected $keyType = 'string';`.
5. Annotate relationship methods with PHPStan generics to satisfy Larastan Level 7.

### Model Template

```php
<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'project_id';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_name',
        'description',
        'ownership_type',
        'link_testing',
        'link_prod',
        'status_id',
        'client_id',
        'created_by',
        'start_date',
        'end_date',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<ProjectStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id', 'status_id');
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members', 'project_id', 'user_id')
            ->withPivot('role_in_project', 'joined_at')
            ->withTimestamps();
    }
}
```

### Migration Standard

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('project_id')->primary();
            $table->string('project_name');
            $table->text('description')->nullable();
            $table->enum('ownership_type', ['Internal', 'Client', 'Freelance'])->default('Internal');
            $table->string('link_testing')->nullable();
            $table->string('link_prod')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->foreignUuid('status_id')
                ->constrained('project_statuses', 'status_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignUuid('client_id')
                ->nullable()
                ->constrained('clients', 'client_id')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status_id', 'ownership_type']);
            $table->index('project_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
```

---

## 4. Livewire 4 & Flux UI Component Standards

### Routing Convention
Always route full-page Livewire components using `Route::livewire(...)`:

```php
// routes/projects.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('projects', Index::class)->name('projects.index');
    Route::livewire('projects/create', Create::class)->name('projects.create');
    Route::livewire('projects/{project}/edit', Edit::class)->name('projects.edit');
});
```

### Component Class Blueprint

```php
<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectStatus;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Projects Directory')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    /**
     * @return LengthAwarePaginator<int, Project>
     */
    #[Computed]
    public function projects(): LengthAwarePaginator
    {
        return Project::query()
            ->with(['status', 'client', 'technologies', 'members'])
            ->when($this->search, fn ($q) => $q->where('project_name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status_id', $this->statusFilter))
            ->latest('updated_at')
            ->paginate(10);
    }

    /**
     * @return Collection<int, ProjectStatus>
     */
    #[Computed]
    public function statuses(): Collection
    {
        return ProjectStatus::orderBy('status_name')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteProject(string $projectId): void
    {
        $project = Project::findOrFail($projectId);
        $project->delete();

        Flux::toast(variant: 'success', text: __('Project moved to trash.'));
    }

    public function render(): View
    {
        return view('livewire.projects.index');
    }
}
```

### Blade Template with Flux UI Blueprint

```blade
<div>
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <flux:heading size="xl" level="1">Projects</flux:heading>
            <flux:subheading>Track deliverables, environments, and team assignments.</flux:subheading>
        </div>
        <div>
            <flux:button href="{{ route('projects.create') }}" variant="primary" icon="plus" wire:navigate>
                New Project
            </flux:button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search projects by name..." icon="magnifying-glass" clearable />
        </div>
        <div class="w-full sm:w-64">
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <flux:select.option value="">All Statuses</flux:select.option>
                @foreach ($this->statuses as $status)
                    <flux:select.option :value="$status->status_id">{{ $status->status_name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- Table / Cards --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>Project</flux:table.column>
            <flux:table.column>Client</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Timeline</flux:table.column>
            <flux:table.column align="end">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->projects as $project)
                <flux:table.row :key="$project->project_id">
                    <flux:table.cell variant="strong">
                        <a href="{{ route('projects.edit', $project) }}" class="hover:underline" wire:navigate>
                            {{ $project->project_name }}
                        </a>
                    </flux:table.cell>
                    <flux:table.cell>{{ $project->client?->client_name ?? '—' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm">{{ $project->status->status_name }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-zinc-500 text-xs">
                        {{ $project->start_date?->format('M d, Y') ?? '—' }}
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                            <flux:menu>
                                <flux:menu.item href="{{ route('projects.edit', $project) }}" icon="pencil-square" wire:navigate>Edit</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item wire:click="deleteProject('{{ $project->project_id }}')" wire:confirm="Are you sure you want to delete this project?" variant="danger" icon="trash">
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center py-12 text-zinc-500">
                        No projects found matching your search.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $this->projects->links() }}
    </div>
</div>
```

---

## 5. Controller Standards (API & Web)

Controllers are reserved for stateless HTTP operations, REST API endpoints, webhooks, or file downloads.

### Controller Blueprint (`app/Http/Controllers/Api/ProjectController.php`)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->projectService->getPaginatedList(
            filters: $request->only(['search', 'status_id']),
            perPage: (int) $request->input('per_page', 15)
        );

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject(
            data: $request->validated(),
            creatorId: (string) $request->user()?->getAuthIdentifier()
        );

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project): ProjectResource
    {
        $project->load(['status', 'client', 'technologies', 'members']);

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $updated = $this->projectService->updateProject($project, $request->validated());

        return new ProjectResource($updated);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->projectService->deleteProject($project);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
```

---

## 6. Service Layer Standards

Services encapsulate business logic, third-party integrations, multi-repository orchestration, and transaction isolation.

### Service Blueprint (`app/Services/ProjectService.php`)

```php
<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function getPaginatedList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->projectRepository->getPaginated($filters, $perPage);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createProject(array $data, string $creatorId): Project
    {
        return DB::transaction(function () use ($data, $creatorId): Project {
            $data['created_by'] = $creatorId;

            $technologies = $data['technologies'] ?? [];
            unset($data['technologies']);

            $project = $this->projectRepository->create($data);

            if (! empty($technologies)) {
                $project->technologies()->sync($technologies);
            }

            Log::info('Project created successfully', [
                'project_id' => $project->project_id,
                'creator_id' => $creatorId,
            ]);

            return $project->fresh(['status', 'client', 'technologies']);
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data): Project {
            $technologies = $data['technologies'] ?? null;
            unset($data['technologies']);

            $updated = $this->projectRepository->update($project, $data);

            if (is_array($technologies)) {
                $updated->technologies()->sync($technologies);
            }

            return $updated->fresh(['status', 'client', 'technologies']);
        });
    }

    public function deleteProject(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }
}
```

---

## 7. Repository Layer Standards

The repository layer isolates data access and prevents query leakage across controllers or Livewire components.

### Contract Blueprint (`app/Repositories/Contracts/ProjectRepositoryInterface.php`)

```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findById(string $id): ?Project;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Project;

    /**
     * @param array<string, mixed> $data
     */
    public function update(Project $project, array $data): Project;

    public function delete(Project $project): bool;
}
```

### Eloquent Implementation Blueprint (`app/Repositories/Eloquent/ProjectRepository.php`)

```php
<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     * @return LengthAwarePaginator<int, Project>
     */
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Project::query()
            ->with(['status', 'client', 'technologies'])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where('project_name', 'like', "%{$search}%");
            })
            ->when($filters['status_id'] ?? null, function ($q, $statusId) {
                $q->where('status_id', $statusId);
            })
            ->latest('updated_at')
            ->paginate($perPage);
    }

    public function findById(string $id): ?Project
    {
        return Project::with(['status', 'client', 'technologies'])->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Project
    {
        return Project::create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    public function delete(Project $project): bool
    {
        return (bool) $project->delete();
    }
}
```

### Binding in Service Provider (`app/Providers/AppServiceProvider.php`)

```php
public function register(): void
{
    $this->app->bind(
        \App\Repositories\Contracts\ProjectRepositoryInterface::class,
        \App\Repositories\Eloquent\ProjectRepository::class
    );
}
```

---

## 8. Action Layer Standards

Actions are single-purpose classes (SRP) used for discrete operations.

### Action Blueprint (`app/Actions/Projects/DuplicateProjectAction.php`)

```php
<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DuplicateProjectAction
{
    public function execute(Project $source, string $newCreatorId): Project
    {
        return DB::transaction(function () use ($source, $newCreatorId): Project {
            $clone = $source->replicate([
                'project_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $clone->project_name = "{$source->project_name} (Copy)";
            $clone->created_by = $newCreatorId;
            $clone->save();

            // Duplicate relation pivots
            $clone->technologies()->sync($source->technologies->pluck('tech_id'));

            return $clone;
        });
    }
}
```

---

## 9. Form Requests & Input Validation

### Form Request Blueprint (`app/Http/Requests/StoreProjectRequest.php`)

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('IPS - PROJECT - Can Create Projects') ?? false;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'ownership_type' => ['required', 'string', 'in:Internal,Client,Freelance'],
            'status_id' => ['required', 'uuid', 'exists:project_statuses,status_id'],
            'client_id' => ['nullable', 'uuid', 'exists:clients,client_id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'link_testing' => ['nullable', 'url', 'max:255'],
            'link_prod' => ['nullable', 'url', 'max:255'],
            'technologies' => ['nullable', 'array'],
            'technologies.*' => ['uuid', 'exists:technologies,tech_id'],
        ];
    }
}
```

---

## 10. Role-Based Access Control (RBAC) & Permissions

Hustlr uses `spatie/laravel-permission` structured through domain-level `PermissionManager` classes.

### Permission Structure

Permissions follow the naming convention:
`IPS - {MODULE} - Can {Action} {Resource}`

Example in `app/PermissionManager/IntegratedPersonalSystem/OwnerPermissionManager.php`:
- `IPS - PROJECT - Can List Projects`
- `IPS - PROJECT - Can Create Projects`
- `IPS - PROJECT - Can Update Projects`
- `IPS - PROJECT - Can Delete Projects`

### Authorizing in Code

**In Blade Views:**
```blade
@can('IPS - PROJECT - Can Create Projects')
    <flux:button href="{{ route('projects.create') }}">Create</flux:button>
@endcan
```

**In Livewire Actions:**
```php
public function delete(string $projectId): void
{
    $this->authorize('IPS - PROJECT - Can Delete Projects');
    // ...
}
```

---

## 11. Testing Standards with PHPUnit 12

Hustlr uses **PHPUnit 12** for automated verification. Every feature must include feature tests covering the happy path and edge cases.

### Livewire Component Feature Test Blueprint (`tests/Feature/ProjectIndexTest.php`)

```php
<?php

namespace Tests\Feature;

use App\Livewire\Projects\Index;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_projects_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('projects.index'));
        $response->assertOk();
    }

    public function test_livewire_can_filter_projects_by_search_query(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Project::factory()->create(['project_name' => 'Project Alpha']);
        Project::factory()->create(['project_name' => 'Project Omega']);

        Livewire::test(Index::class)
            ->assertSee('Project Alpha')
            ->assertSee('Project Omega')
            ->set('search', 'Alpha')
            ->assertSee('Project Alpha')
            ->assertDontSee('Project Omega');
    }

    public function test_user_can_delete_project_via_livewire(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        Livewire::test(Index::class)
            ->call('deleteProject', $project->project_id)
            ->assertStatus(200);

        $this->assertSoftDeleted('projects', [
            'project_id' => $project->project_id,
        ]);
    }
}
```

---

## 12. Code Quality, Type Safety & Static Analysis

Hustlr enforces strict quality checks. Code that fails Pint or Larastan must not be committed.

### Mandatory Quality Commands

```bash
# 1. Format code using Laravel Pint
composer run lint

# 2. Verify Pint styling without modifying files
composer run lint:check

# 3. Static Type Analysis with Larastan (Level 7)
composer run types:check

# 4. Run entire test suite
composer run test
```

---

## 13. CLI Artisan Generators Cheatsheet

Quickly generate new components conforming to project conventions:

```bash
# 1. Domain Model + UUID Migration + Factory + Seeder
php artisan make:model Task -m -f -s

# 2. Livewire 4 Component with Blade View
php artisan make:livewire Tasks/Index
php artisan make:livewire Tasks/Create
php artisan make:livewire Tasks/Edit

# 3. Service Layer Class
php artisan make:class Services/TaskService

# 4. Repository Interface and Implementation
php artisan make:class Repositories/Contracts/TaskRepositoryInterface
php artisan make:class Repositories/Eloquent/TaskRepository

# 5. Discrete Action Class
php artisan make:class Actions/Tasks/AssignTaskAction

# 6. Form Request & API Resource
php artisan make:request StoreTaskRequest
php artisan make:resource TaskResource

# 7. PHPUnit Feature Test
php artisan make:test TaskIndexTest --phpunit
```
