<?php

namespace App\PermissionManager\IntegratedPersonalSystem;

use App\PermissionManager\PermissionManager;

class StaffPermissionManager extends PermissionManager
{
    public $role = 'Staff Integrated Personal System';

    public $permission = [
        // Sidebar
        'IPS - PROJECT - Can View Dashboard in Sidebar',
        'IPS - PROJECT - Can View Projects in Sidebar',
        'IPS - PROJECT - Can View Tasks in Sidebar',

        // Projects
        'IPS - PROJECT - Can List Projects',
        'IPS - PROJECT - Can Show Projects',

        // Tasks
        'IPS - PROJECT - Can List Tasks',
        'IPS - PROJECT - Can Create Tasks',
        'IPS - PROJECT - Can Show Tasks',
        'IPS - PROJECT - Can Update Tasks',

        // Team
        'IPS - PROJECT - Can List Team',
    ];
}
