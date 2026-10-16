<?php

namespace App\PermissionManager\IntegratedPersonalSystem;

use App\PermissionManager\PermissionManager;

class ManagementPermissionManager extends PermissionManager
{
    public $role = 'Management Integrated Personal System';

    public $permission = [
        // Sidebar
        'IPS - PROJECT - Can View Dashboard in Sidebar',
        'IPS - PROJECT - Can View Projects in Sidebar',
        'IPS - PROJECT - Can View Tasks in Sidebar',
        'IPS - PROJECT - Can View Team in Sidebar',
        'IPS - UAM - Can View Users in Sidebar',

        // UAM (User Access Management - Managerial)
        'IPS - UAM - Can List Users',
        'IPS - UAM - Can Create Users',
        'IPS - UAM - Can Show Users',
        'IPS - UAM - Can Update Users',
        'IPS - UAM - Can Assign Roles',

        // Projects
        'IPS - PROJECT - Can List Projects',
        'IPS - PROJECT - Can Create Projects',
        'IPS - PROJECT - Can Show Projects',
        'IPS - PROJECT - Can Update Projects',
        'IPS - PROJECT - Can Manage Technologies',

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
