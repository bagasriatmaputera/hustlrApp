---
paths:
  - 'app/Livewire/**'
---

# Livewire

## Guard every Livewire action with authorizePermission
Route middleware only protects mounting a Livewire page. Action calls hit /livewire/update and bypass route middleware, so any user can invoke a public component method directly. Every component that mutates data must `use App\Concerns\AuthorizesPermissions;` and call `$this->authorizePermission('IPS - ...')` in mount() and in each mutating action (create/update/delete/etc.). Follow app/Livewire/UAM/Index.php as the reference pattern.
