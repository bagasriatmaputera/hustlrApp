<?php

namespace App\Livewire\Approvals;

use App\Models\User;
use App\PermissionManager\ModuleRegistry;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Access Approvals')]
class Index extends Component
{
    use WithPagination;

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->hasRole('Owner Integrated Personal System')) {
            abort(403, __('Only an Owner can manage access approvals.'));
        }
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function pendingUsers(): LengthAwarePaginator
    {
        return User::query()
            ->where('approval_status', 'pending')
            ->oldest('created_at')
            ->paginate(15);
    }

    #[Computed]
    public function pendingCount(): int
    {
        return User::where('approval_status', 'pending')->count();
    }

    /**
     * @return array<string, string>
     */
    #[Computed]
    public function moduleLabels(): array
    {
        return ModuleRegistry::all();
    }

    public function approve(int $userId): void
    {
        $this->authorizeOwner();

        $user = User::findOrFail($userId);

        $user->approval_status = 'approved';
        $user->save();

        $user->syncPermissions(
            ModuleRegistry::permissionsFor($user->requested_modules ?? [])
        );

        unset($this->pendingUsers, $this->pendingCount);

        Flux::toast(variant: 'success', text: __(':name approved.', ['name' => $user->full_name ?? $user->email]));
    }

    public function reject(int $userId): void
    {
        $this->authorizeOwner();

        $user = User::findOrFail($userId);
        $user->approval_status = 'rejected';
        $user->save();

        $user->syncPermissions([]);

        unset($this->pendingUsers, $this->pendingCount);

        Flux::toast(variant: 'success', text: __(':name rejected.', ['name' => $user->full_name ?? $user->email]));
    }

    private function authorizeOwner(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->hasRole('Owner Integrated Personal System')) {
            abort(403, __('Only an Owner can manage access approvals.'));
        }
    }

    public function render(): View
    {
        return view('livewire.approvals.index');
    }
}
