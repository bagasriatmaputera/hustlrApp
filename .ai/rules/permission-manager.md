---
paths:
  - 'app/PermissionManager/**'
---

# Permission Manager

## Self-registered users get module permissions, not roles
New registrations are approval_status=pending with requested_modules (project_management, job_tracker). Owner approves/rejects via app/Livewire/Approvals/Index.php. Approval grants permissions directly (no Spatie role) using App\PermissionManager\ModuleRegistry::permissionsFor(). Add new self-service modules to ModuleRegistry::all()/permissions() — and add any new permission name to the relevant *PermissionManager so the seeder creates it. Owner super-admin is role-based only (AppServiceProvider Gate::before); do not reintroduce id/email/role-column bypasses.
