<?php

namespace App\Services;

use App\Libraries\RbacEngine;
use App\Models\RoleModel;
use App\Models\RolePermissionModel;

class PermissionService
{
    public function __construct(
        private ?RoleModel $roleModel = null,
        private ?RolePermissionModel $rolePermissionModel = null,
        private ?RbacEngine $rbacEngine = null
    ) {
        $this->roleModel ??= new RoleModel();
        $this->rolePermissionModel ??= new RolePermissionModel();
        $this->rbacEngine ??= service('rbacEngine');
    }

    public function roleForSlug(string $slug): ?array
    {
        return $this->roleModel->findBySlug($slug);
    }

    public function baseRoleFor(string $slug): string
    {
        $role = $this->roleForSlug($slug);

        return $role['base_role'] ?? $slug;
    }

    public function permissionKeysForSlug(string $slug): array
    {
        $role = $this->roleForSlug($slug);
        if (!$role) {
            return [];
        }

        return $this->rolePermissionModel->permissionKeysForRole((int) $role['id']);
    }

    public function has(string $roleSlug, string $permissionKey): bool
    {
        $baseRole = $this->baseRoleFor($roleSlug);
        if ($baseRole === 'admin') {
            return true;
        }

        return in_array($permissionKey, $this->permissionKeysForSlug($roleSlug), true);
    }

    public function hasForUser(int $userId, string $permissionKey): bool
    {
        return $this->rbacEngine->hasPermission($userId, $permissionKey);
    }

    public function hasAnyForUser(int $userId, array $permissionKeys): bool
    {
        return $this->rbacEngine->hasAnyPermission($userId, $permissionKeys);
    }

    public function effectivePermissionsForUser(int $userId): array
    {
        return $this->rbacEngine->getUserPermissions($userId);
    }

    public function effectiveRolesForUser(int $userId): array
    {
        return $this->rbacEngine->getRoles($userId);
    }

    public function invalidateUser(int $userId): void
    {
        $this->rbacEngine->invalidateUser($userId);
    }
}
