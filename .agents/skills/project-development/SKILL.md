---
name: project-development
description: "Standard operating procedure and architectural blueprint for developing components in the Hustlr (Manage Side Hustle - MSH) project. Always activate this skill when creating, modifying, or refactoring Controllers, Livewire 4 Pages, Flux UI Components, Services, Repositories, Actions, Eloquent Models, Migrations, Seeders, Form Requests, Policies, RBAC Permissions, or PHPUnit Tests in Hustlr. Enforces strict typing (Larastan Level 7), Pint code formatting, UUID primary keys, and layer separation."
license: MIT
metadata:
  author: hustlr-team
  framework: "Laravel 13.x | Livewire 4.x | Flux UI 2.x | Tailwind CSS v4"
---

# Hustlr Project Development Standards & Blueprint

This skill governs all code generation, component scaffolding, and architectural patterns within the **Hustlr** codebase. Every developer and AI agent must strictly follow these rules to ensure consistency, type safety, testability, and high performance.

---

## 🏛️ Architectural Decision Matrix

When creating a new feature or component, use this matrix to choose the right layer:

| Layer | Location | When to Use | Responsibilities |
| :--- | :--- | :--- | :--- |
| **Livewire Page** | `app/Livewire/{Module}/` | Interactive web pages, forms, tables, modals | UI state, user interaction, rendering Flux UI components |
| **Controller** | `app/Http/Controllers/` | REST APIs, webhooks, file downloads, auth endpoints | HTTP request handling, delegating to Services, returning JSON Resources |
| **Service** | `app/Services/` | Complex business logic, multi-model transactions | Domain logic, database transactions (`DB::transaction`), orchestration |
| **Repository** | `app/Repositories/` | Complex data querying, filters, caching, data access | Encapsulating Eloquent queries, eager loading, pagination |
| **Action** | `app/Actions/` | Discrete single-purpose operations | Single Responsibility Principle (SRP) operations (e.g. Fortify, exports) |
| **Model** | `app/Models/` | Domain entity representation | Eloquent relations, UUID keys, `$fillable`, typed `casts()` |
| **Form Request** | `app/Http/Requests/` | Complex validation for HTTP controllers | Input validation, authorization gates, custom messages |
| **Policy / RBAC** | `app/Policies/` & `app/PermissionManager/` | Authorization & access control | Spatie permissions and Hustlr `PermissionManager` integration |

---

## ⚡ Core Rules & Invariants

1. **UUID Primary Keys**: Every domain model uses UUID primary keys:
   - Trait: `use HasUuids;`
   - Key: `protected $primaryKey = '{model}_id';`
   - Properties: `public $incrementing = false;` and `protected $keyType = 'string';`
2. **Strict PHP Typing & Larastan Level 7**:
   - All methods MUST declare explicit parameter types and return types.
   - Eloquent relations MUST include PHPStan generic PHPDocs:
     `/** @return BelongsTo<Client, $this> */`
   - Livewire computed queries MUST include generic annotations:
     `/** @return LengthAwarePaginator<int, Project> */`
3. **Pint Formatting**:
   - Always run `vendor/bin/pint --dirty --format agent` (or `composer run lint`) after creating or editing PHP files.
4. **Livewire 4 with Flux UI**:
   - Use `Route::livewire('path', Component::class)` for routing.
   - Use `#[Title('...')]`, `#[Url]`, and `#[Computed]` attributes.
   - Use `Flux::toast(...)` for user feedback.
5. **No Database Queries in Views**:
   - Always eager-load relations in the component or repository using `->with(['...'])`.

---

## 🛠️ Code Generation Blueprints

### 1. Eloquent Model Standard

```php
<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'status_id',
        'client_id',
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
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
```

### 2. Livewire 4 Component Standard

```php
<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Projects')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    /**
     * @return LengthAwarePaginator<int, Project>
     */
    #[Computed]
    public function projects(): LengthAwarePaginator
    {
        return Project::query()
            ->with(['client', 'status'])
            ->when($this->search, fn ($q) => $q->where('project_name', 'like', "%{$this->search}%"))
            ->latest('updated_at')
            ->paginate(10);
    }

    public function delete(string $projectId): void
    {
        Project::findOrFail($projectId)->delete();
        Flux::toast(variant: 'success', text: __('Project deleted successfully.'));
    }

    public function render()
    {
        return view('livewire.projects.index');
    }
}
```

### 3. Service Layer Standard (`app/Services`)

```php
<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectService
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function createProject(array $data, string $creatorId): Project
    {
        return DB::transaction(function () use ($data, $creatorId): Project {
            $data['created_by'] = $creatorId;
            $project = $this->projectRepository->create($data);

            Log::info('Project created', ['project_id' => $project->project_id]);

            return $project;
        });
    }
}
```

### 4. Repository Layer Standard (`app/Repositories`)

**Contract (`app/Repositories/Contracts/ProjectRepositoryInterface.php`):**
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
}
```

**Eloquent Implementation (`app/Repositories/Eloquent/ProjectRepository.php`):**
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
            ->latest('updated_at')
            ->paginate($perPage);
    }

    public function findById(string $id): ?Project
    {
        return Project::with(['status', 'client'])->find($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Project
    {
        return Project::create($data);
    }
}
```

### 5. Controller Standard (API & Web)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject(
            $request->validated(),
            $request->user()->id
        );

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
```

### 6. Action Standard (`app/Actions`)

```php
<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DuplicateProjectAction
{
    public function execute(Project $sourceProject, string $newCreatorId): Project
    {
        return DB::transaction(function () use ($sourceProject, $newCreatorId): Project {
            $clone = $sourceProject->replicate([
                'project_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

            $clone->project_name = $sourceProject->project_name . ' (Copy)';
            $clone->created_by = $newCreatorId;
            $clone->save();

            return $clone;
        });
    }
}
```

### 7. Form Request Standard

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
        ];
    }
}
```

### 8. PHPUnit 12 Test Standard

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

    public function test_user_can_view_project_index_page(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['project_name' => 'Alpha App']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Alpha App');
    }
}
```

---

## ⚡ CLI Artisan Generators Cheatsheet

```bash
# Model + Factory + Migration + Seeder
php artisan make:model Task -m -f -s

# Livewire 4 Component
php artisan make:livewire Tasks/Index

# Form Request
php artisan make:request StoreTaskRequest

# API Resource
php artisan make:resource TaskResource

# Feature Test (PHPUnit)
php artisan make:test TaskFeatureTest --phpunit

# Service / Repository / Action (Generic Class)
php artisan make:class Services/TaskService
php artisan make:class Repositories/Eloquent/TaskRepository
php artisan make:class Actions/Tasks/CreateTaskAction

# Quality Gates (Always run before commit)
composer run lint
composer run types:check
composer run test
```
