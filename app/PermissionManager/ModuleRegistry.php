<?php

namespace App\PermissionManager;

use App\Models\User;

/**
 * Registry of self-service modules a user may request at registration.
 *
 * Approving a module grants the user these permissions directly (no role).
 */
class ModuleRegistry
{
    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'project_management' => 'Project Management',
            'job_tracker' => 'Job Tracker',
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return list<string>
     */
    public static function permissions(string $module): array
    {
        return match ($module) {
            'project_management' => [
                'IPS - PROJECT - Can View Dashboard in Sidebar',
                'IPS - PROJECT - Can View Projects in Sidebar',
                'IPS - PROJECT - Can View Tasks in Sidebar',
                'IPS - PROJECT - Can View Team in Sidebar',
                'IPS - PROJECT - Can List Projects',
                'IPS - PROJECT - Can Create Projects',
                'IPS - PROJECT - Can Show Projects',
                'IPS - PROJECT - Can Update Projects',
                'IPS - PROJECT - Can List Tasks',
                'IPS - PROJECT - Can Create Tasks',
                'IPS - PROJECT - Can Show Tasks',
                'IPS - PROJECT - Can Update Tasks',
                'IPS - PROJECT - Can Delete Tasks',
                'IPS - PROJECT - Can Assign Tasks',
                'IPS - PROJECT - Can List Team',
                'IPS - PROJECT - Can Invite Team Member',
                'IPS - PROJECT - Can Manage Technologies',
            ],
            'job_tracker' => [
                'IPS - JOBTRACKER - Can View Job Tracker in Sidebar',
                'IPS - JOBTRACKER - Can List Job Tracker',
                'IPS - JOBTRACKER - Can Create Job Tracker',
                'IPS - JOBTRACKER - Can Update Job Tracker',
                'IPS - JOBTRACKER - Can Delete Job Tracker',
            ],
            default => [],
        };
    }

    /**
     * Permission set granted when a user is added as a project member.
     *
     * Derived from the member role. Does NOT include global ownership.
     *
     * @return list<string>
     */
    public static function projectMemberPermissions(string $role): array
    {
        return match (strtolower($role)) {
            'owner' => [
                'IPS - PROJECT - Can View Projects in Sidebar',
                'IPS - PROJECT - Can View Tasks in Sidebar',
                'IPS - PROJECT - Can View Team in Sidebar',
                'IPS - PROJECT - Can List Projects',
                'IPS - PROJECT - Can Create Projects',
                'IPS - PROJECT - Can Show Projects',
                'IPS - PROJECT - Can Update Projects',
                'IPS - PROJECT - Can Delete Projects',
                'IPS - PROJECT - Can List Tasks',
                'IPS - PROJECT - Can Create Tasks',
                'IPS - PROJECT - Can Show Tasks',
                'IPS - PROJECT - Can Update Tasks',
                'IPS - PROJECT - Can Delete Tasks',
                'IPS - PROJECT - Can Assign Tasks',
                'IPS - PROJECT - Can List Team',
                'IPS - PROJECT - Can Invite Team Member',
                'IPS - PROJECT - Can Remove Team Member',
                'IPS - PROJECT - Can Manage Technologies',
            ],
            'manager' => [
                'IPS - PROJECT - Can View Projects in Sidebar',
                'IPS - PROJECT - Can View Tasks in Sidebar',
                'IPS - PROJECT - Can View Team in Sidebar',
                'IPS - PROJECT - Can List Projects',
                'IPS - PROJECT - Can Show Projects',
                'IPS - PROJECT - Can Create Projects',
                'IPS - PROJECT - Can Update Projects',
                'IPS - PROJECT - Can List Tasks',
                'IPS - PROJECT - Can Create Tasks',
                'IPS - PROJECT - Can Show Tasks',
                'IPS - PROJECT - Can Update Tasks',
                'IPS - PROJECT - Can Assign Tasks',
                'IPS - PROJECT - Can List Team',
                'IPS - PROJECT - Can Invite Team Member',
                'IPS - PROJECT - Can Manage Technologies',
            ],
            default => [
                'IPS - PROJECT - Can View Projects in Sidebar',
                'IPS - PROJECT - Can View Tasks in Sidebar',
                'IPS - PROJECT - Can List Projects',
                'IPS - PROJECT - Can Show Projects',
                'IPS - PROJECT - Can List Tasks',
                'IPS - PROJECT - Can Create Tasks',
                'IPS - PROJECT - Can Show Tasks',
                'IPS - PROJECT - Can Update Tasks',
            ],
        };
    }

    /**
     * Whether the user is missing a project-participation permission.
     */
    public static function isMissingProjectAccess(User $user): bool
    {
        return ! $user->can('IPS - PROJECT - Can Show Projects');
    }

    public static function permissionsFor(array $modules): array
    {
        return collect($modules)
            ->flatMap(fn (string $module): array => self::permissions($module))
            ->unique()
            ->values()
            ->all();
    }
}
