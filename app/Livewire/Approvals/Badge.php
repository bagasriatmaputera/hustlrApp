<?php

namespace App\Livewire\Approvals;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Badge extends Component
{
    public function render(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        $count = $user?->hasRole('Owner Integrated Personal System')
            ? User::where('approval_status', 'pending')->count()
            : 0;

        return view('livewire.approvals.badge', ['count' => $count]);
    }
}
