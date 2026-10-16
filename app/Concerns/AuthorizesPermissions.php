<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Auth;

trait AuthorizesPermissions
{
    /**
     * Abort with 403 unless the current user holds the given permission.
     */
    protected function authorizePermission(string $permission): void
    {
        if (! Auth::user()?->can($permission)) {
            abort(403, __('Unauthorized action.'));
        }
    }
}
