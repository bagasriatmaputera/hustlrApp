<?php

namespace App\Livewire\UAM;

use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Title('User Access Management')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $roleFilter = '';

    // Modal state
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    // Form fields
    public ?int $selectedUserId = null;

    public string $fullName = '';

    public string $email = '';

    public string $password = '';

    public string $selectedRole = 'Staff Integrated Personal System';

    public ?string $userToDeleteName = null;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! $user->can('IPS - UAM - Can List Users')) {
            abort(403, __('You do not have permission to view User Access Management.'));
        }

        // Auto-assign Owner role to owner@gmail.com if it has no role assigned
        try {
            $ownerUser = User::where('email', 'owner@gmail.com')->first();
            if ($ownerUser && $ownerUser->roles()->count() === 0) {
                $ownerRole = Role::where('name', 'Owner Integrated Personal System')->first();
                if ($ownerRole) {
                    $ownerUser->assignRole($ownerRole);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if roles table not available
        }
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->when($this->search, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($this->roleFilter, function (Builder $query, string $role) {
                $query->whereHas('roles', fn (Builder $q) => $q->where('name', $role));
            })
            ->latest('created_at')
            ->paginate(10);
    }

    /**
     * @return Collection<int, Role>
     */
    #[Computed]
    public function availableRoles(): Collection
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        return Role::query()
            ->when(! $currentUser->hasRole('Owner Integrated Personal System'), function (Builder $q) {
                $q->where('name', '!=', 'Owner Integrated Personal System');
            })
            ->get();
    }

    #[Computed]
    public function totalUsersCount(): int
    {
        return User::count();
    }

    #[Computed]
    public function ownerCount(): int
    {
        return User::role('Owner Integrated Personal System')->count();
    }

    #[Computed]
    public function managementCount(): int
    {
        return User::role('Management Integrated Personal System')->count();
    }

    #[Computed]
    public function staffCount(): int
    {
        return User::role('Staff Integrated Personal System')->count();
    }

    public function openCreateModal(): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->can('IPS - UAM - Can Create Users')) {
            Flux::toast(variant: 'danger', text: __('You do not have permission to create users.'));

            return;
        }

        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function createUser(): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->can('IPS - UAM - Can Create Users')) {
            abort(403, __('Unauthorized action.'));
        }

        $allowedRoles = $this->availableRoles->pluck('name')->toArray();

        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults()],
            'selectedRole' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        $user = User::create([
            // 'name' => $validated['fullName'],
            'full_name' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['selectedRole']);

        $this->showCreateModal = false;
        $this->resetForm();

        Flux::toast(variant: 'success', text: __('User created successfully.'));
    }

    public function openEditModal(int $userId): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->can('IPS - UAM - Can Update Users')) {
            Flux::toast(variant: 'danger', text: __('You do not have permission to update users.'));

            return;
        }

        $user = User::with('roles')->findOrFail($userId);

        // Disallow non-owners from editing Owner accounts
        if ($user->hasRole('Owner Integrated Personal System') && ! $currentUser->hasRole('Owner Integrated Personal System')) {
            Flux::toast(variant: 'danger', text: __('Only an Owner can edit Owner accounts.'));

            return;
        }

        $this->selectedUserId = $user->id;
        $this->fullName = $user->full_name ?? $user->name ?? '';
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRole = $user->roles->first()?->name ?? 'Staff Integrated Personal System';

        $this->showEditModal = true;
    }

    public function updateUser(): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->can('IPS - UAM - Can Update Users')) {
            abort(403, __('Unauthorized action.'));
        }

        if (! $this->selectedUserId) {
            return;
        }

        $user = User::findOrFail($this->selectedUserId);

        if ($user->hasRole('Owner Integrated Personal System') && ! $currentUser->hasRole('Owner Integrated Personal System')) {
            abort(403, __('Only an Owner can edit Owner accounts.'));
        }

        $allowedRoles = $this->availableRoles->pluck('name')->toArray();

        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', Password::defaults()],
            'selectedRole' => ['required', 'string', Rule::in($allowedRoles)],
        ]);

        $user->name = $validated['fullName'];
        $user->full_name = $validated['fullName'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($currentUser->can('IPS - UAM - Can Assign Roles')) {
            $user->syncRoles([$validated['selectedRole']]);
        }

        $this->showEditModal = false;
        $this->resetForm();

        Flux::toast(variant: 'success', text: __('User updated successfully.'));
    }

    public function confirmDelete(int $userId): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->can('IPS - UAM - Can Delete Users')) {
            Flux::toast(variant: 'danger', text: __('You do not have permission to delete users.'));

            return;
        }

        if ($userId === $currentUser->id) {
            Flux::toast(variant: 'danger', text: __('You cannot delete your own account.'));

            return;
        }

        $user = User::findOrFail($userId);

        if ($user->hasRole('Owner Integrated Personal System') && ! $currentUser->hasRole('Owner Integrated Personal System')) {
            Flux::toast(variant: 'danger', text: __('Only an Owner can delete another Owner.'));

            return;
        }

        $this->selectedUserId = $user->id;
        $this->userToDeleteName = $user->full_name ?? $user->name ?? $user->email;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->can('IPS - UAM - Can Delete Users')) {
            abort(403, __('Unauthorized action.'));
        }

        if (! $this->selectedUserId || $this->selectedUserId === $currentUser->id) {
            $this->showDeleteModal = false;

            return;
        }

        $user = User::findOrFail($this->selectedUserId);
        $user->delete();

        $this->showDeleteModal = false;
        $this->resetForm();

        Flux::toast(variant: 'success', text: __('User deleted successfully.'));
    }

    private function resetForm(): void
    {
        $this->selectedUserId = null;
        $this->fullName = '';
        $this->email = '';
        $this->password = '';
        $this->selectedRole = 'Staff Integrated Personal System';
        $this->userToDeleteName = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.uam.index');
    }
}
