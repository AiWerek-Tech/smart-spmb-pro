<?php

use App\Libraries\RbacEngine;
use App\Services\PermissionService;

if (! function_exists('rbac_engine')) {
    function rbac_engine(): RbacEngine
    {
        return service('rbacEngine');
    }
}

if (! function_exists('can')) {
    function can(string $permissionKey): bool
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return false;
        }

        $permissions = session()->get('user_permissions');
        if (is_array($permissions) && in_array($permissionKey, $permissions, true)) {
            return true;
        }

        return rbac_engine()->hasPermission($userId, $permissionKey);
    }
}

if (! function_exists('can_any')) {
    function can_any(array $permissionKeys): bool
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return false;
        }

        return rbac_engine()->hasAnyPermission($userId, $permissionKeys);
    }
}

if (! function_exists('can_all')) {
    function can_all(array $permissionKeys): bool
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return false;
        }

        return rbac_engine()->hasAllPermissions($userId, $permissionKeys);
    }
}

if (! function_exists('current_user_permissions')) {
    function current_user_permissions(): array
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return [];
        }

        return rbac_engine()->getUserPermissions($userId);
    }
}

if (! function_exists('current_user_roles')) {
    function current_user_roles(): array
    {
        $userId = (int) session()->get('user_id');
        if ($userId <= 0) {
            return [];
        }

        return rbac_engine()->getRoles($userId);
    }
}

if (! function_exists('legacyRoleCheck')) {
    function legacyRoleCheck(string $oldRole): bool
    {
        $userRole = (string) session()->get('user_role');
        $baseRole = (string) (session()->get('user_base_role') ?? '');

        if ($userRole === $oldRole || $baseRole === $oldRole) {
            return true;
        }

        if ($userRole === '') {
            return false;
        }

        return (new PermissionService())->baseRoleFor($userRole) === $oldRole;
    }
}
