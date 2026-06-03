<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

class RbacEngine
{
    private const CACHE_TTL = 300;

    public function __construct(private ?BaseConnection $db = null)
    {
        $this->db ??= \Config\Database::connect();
    }

    public function hasPermission(int $userId, string $permissionKey): bool
    {
        $permissionKey = trim($permissionKey);

        if ($permissionKey === '') {
            return false;
        }

        return in_array($permissionKey, $this->getUserPermissions($userId), true);
    }

    public function hasAnyPermission(int $userId, array $permissionKeys): bool
    {
        $permissionKeys = array_values(array_filter(array_map('trim', $permissionKeys)));
        if ($permissionKeys === []) {
            return false;
        }

        return array_intersect($permissionKeys, $this->getUserPermissions($userId)) !== [];
    }

    public function hasAllPermissions(int $userId, array $permissionKeys): bool
    {
        $permissionKeys = array_values(array_unique(array_filter(array_map('trim', $permissionKeys))));
        if ($permissionKeys === []) {
            return false;
        }

        $effectivePermissions = $this->getUserPermissions($userId);

        foreach ($permissionKeys as $permissionKey) {
            if (! in_array($permissionKey, $effectivePermissions, true)) {
                return false;
            }
        }

        return true;
    }

    public function getUserPermissions(int $userId): array
    {
        $cached = $this->getCachedPayload($userId);
        if ($cached !== null) {
            return $cached['permissions'];
        }

        $roles = $this->queryActiveRoles($userId);
        $permissions = $this->queryPermissionsForRoles($roles);

        $this->setCachedPayload($userId, $roles, $permissions);

        return $permissions;
    }

    public function getRoles(int $userId): array
    {
        $cached = $this->getCachedPayload($userId);
        if ($cached !== null) {
            return $cached['roles'];
        }

        $roles = $this->queryActiveRoles($userId);
        $permissions = $this->queryPermissionsForRoles($roles);
        $this->setCachedPayload($userId, $roles, $permissions);

        return $roles;
    }

    public function invalidateUser(int $userId): void
    {
        $this->deleteCache($userId);

        if ((int) session()->get('user_id') === $userId) {
            session()->remove(['user_roles', 'user_permissions', 'rbac_cached_at']);
        }
    }

    public function invalidateRole(int $roleId): void
    {
        if (! $this->db->tableExists('user_roles')) {
            return;
        }

        $rows = $this->db->table('user_roles')
            ->select('user_id')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $this->invalidateUser((int) $row['user_id']);
        }
    }

    public function refreshSession(int $userId): void
    {
        $this->invalidateUser($userId);
        $roles = $this->getRoles($userId);
        $permissions = $this->getUserPermissions($userId);

        if ((int) session()->get('user_id') === $userId) {
            session()->set([
                'user_roles'       => $roles,
                'user_permissions' => $permissions,
                'rbac_cached_at'   => time(),
            ]);
        }
    }

    private function queryActiveRoles(int $userId): array
    {
        if (! $this->db->tableExists('users') || ! $this->db->tableExists('roles')) {
            return [];
        }

        $rolesById = [];
        $user = $this->db->table('users')->select('role')->where('id', $userId)->get()->getRowArray();

        if (! empty($user['role'])) {
            $role = $this->db->table('roles')
                ->select('id, slug, name, base_role, description, is_system, is_active, sort_order')
                ->where('slug', $user['role'])
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if ($role) {
                $role['assigned_at'] = null;
                $role['expires_at'] = null;
                $role['source'] = 'primary';
                $rolesById[(int) $role['id']] = $role;
            }
        }

        try {
            $rows = $this->db->table('user_roles ur')
                ->select('r.id, r.slug, r.name, r.base_role, r.description, r.is_system, r.is_active, r.sort_order, ur.assigned_at, ur.expires_at')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $userId)
                ->where('r.is_active', 1)
                ->where('(ur.expires_at IS NULL OR ur.expires_at > ' . $this->db->escape(date('Y-m-d H:i:s')) . ')', null, false)
                ->orderBy('r.sort_order', 'ASC')
                ->orderBy('r.name', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($rows as $role) {
                $role['source'] = 'assignment';
                $rolesById[(int) $role['id']] = $role;
            }
        } catch (\Throwable $e) {
            log_message('debug', 'RBAC user_roles query skipped: ' . $e->getMessage());
        }

        $roles = array_values($rolesById);
        usort($roles, static fn (array $a, array $b): int => ((int) $a['sort_order'] <=> (int) $b['sort_order']) ?: strcmp($a['name'], $b['name']));

        return $roles;
    }

    private function queryPermissionsForRoles(array $roles): array
    {
        if ($roles === [] || ! $this->db->tableExists('permissions')) {
            return [];
        }

        if ($this->hasSystemAdminRole($roles)) {
            $rows = $this->db->table('permissions')
                ->select('permission_key')
                ->where('is_active', 1)
                ->get()
                ->getResultArray();

            return $this->normalisePermissionRows($rows);
        }

        if (! $this->db->tableExists('role_permissions')) {
            return [];
        }

        $roleIds = array_values(array_unique(array_map(static fn (array $role): int => (int) $role['id'], $roles)));
        if ($roleIds === []) {
            return [];
        }

        $rows = $this->db->table('role_permissions rp')
            ->select('rp.permission_key')
            ->join('permissions p', 'p.permission_key = rp.permission_key')
            ->whereIn('rp.role_id', $roleIds)
            ->where('p.is_active', 1)
            ->get()
            ->getResultArray();

        return $this->normalisePermissionRows($rows);
    }

    private function hasSystemAdminRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if (($role['base_role'] ?? null) === 'admin' || in_array($role['slug'] ?? '', ['admin', 'super_admin'], true)) {
                return true;
            }
        }

        return false;
    }

    private function normalisePermissionRows(array $rows): array
    {
        $permissions = [];
        foreach ($rows as $row) {
            if (! empty($row['permission_key'])) {
                $permissions[] = (string) $row['permission_key'];
            }
        }

        $permissions = array_values(array_unique($permissions));
        sort($permissions);

        return $permissions;
    }

    private function getCachedPayload(int $userId): ?array
    {
        if ((int) session()->get('user_id') === $userId) {
            $cachedAt = (int) session()->get('rbac_cached_at');
            $permissions = session()->get('user_permissions');
            $roles = session()->get('user_roles');

            if ($cachedAt > 0 && (time() - $cachedAt) <= self::CACHE_TTL && is_array($permissions) && is_array($roles)) {
                return ['roles' => $roles, 'permissions' => $permissions];
            }
        }

        try {
            $payload = cache()->get($this->cacheKey($userId));
            if (is_array($payload) && isset($payload['roles'], $payload['permissions'])) {
                return $payload;
            }
        } catch (\Throwable $e) {
            log_message('debug', 'RBAC cache read skipped: ' . $e->getMessage());
        }

        return null;
    }

    private function setCachedPayload(int $userId, array $roles, array $permissions): void
    {
        $payload = ['roles' => $roles, 'permissions' => $permissions];

        if ((int) session()->get('user_id') === $userId) {
            session()->set([
                'user_roles'       => $roles,
                'user_permissions' => $permissions,
                'rbac_cached_at'   => time(),
            ]);
        }

        try {
            cache()->save($this->cacheKey($userId), $payload, self::CACHE_TTL);
        } catch (\Throwable $e) {
            log_message('debug', 'RBAC cache write skipped: ' . $e->getMessage());
        }
    }

    private function deleteCache(int $userId): void
    {
        try {
            cache()->delete($this->cacheKey($userId));
        } catch (\Throwable $e) {
            log_message('debug', 'RBAC cache delete skipped: ' . $e->getMessage());
        }
    }

    private function cacheKey(int $userId): string
    {
        return 'rbac_permissions_user_' . $userId;
    }
}
