<?php

namespace App\PermissionManager\IntegratedPersonalSystem;

use App\PermissionManager\PermissionManager;

class ManagementPermissionManager extends PermissionManager {
    public $role = 'Management Integrated Personal System';

    public $permission = [
        // Sidebar
        'IPS - PROJECT - Can View Dashboard in Sidebar',
        'IPS - PROJECT - Can View Projects in Sidebar',
        'IPS - PROJECT - Can View Tasks in Sidebar',
        'IPS - PROJECT - Can View Team in Sidebar',

        // Projects
        'IPS - PROJECT - Can List Projects',
        'IPS - PROJECT - Can Create Projects',
        'IPS - PROJECT - Can Show Projects',
        'IPS - PROJECT - Can Update Projects',

        // Tasks
        'IPS - PROJECT - Can List Tasks',
        'IPS - PROJECT - Can Create Tasks',
        'IPS - PROJECT - Can Show Tasks',
        'IPS - PROJECT - Can Update Tasks',
        'IPS - PROJECT - Can Delete Tasks',
        'IPS - PROJECT - Can Assign Tasks',

        // Team
        'IPS - PROJECT - Can List Team',
        'IPS - PROJECT - Can Invite Team Member',
    ];
}
