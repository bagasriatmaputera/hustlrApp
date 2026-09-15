<?php

namespace App\PermissionManager;

abstract class PermissionManager
{
    public $permission = [];

    public $role;

    public function getRole()
    {
        return $this->role;
    }

    public function getPermissions()
    {
        return $this->permission;
    }
}
