<?php

namespace App\Services;

use App\Models\RoleModel;
use App\Models\RolePermissionModel;
use App\Models\UserRoleModel;
use CodeIgniter\Database\BaseConnection;

class RbacService
{
    private const PROTECTED_PERMISSIONS = ['approve_selection', 'publish_selection'];
    private const PROTECTED_ROLE_SLUGS = ['admin', 'super_admin', 'kepala_sekolah'];

    public function __construct(
        private ?RoleModel $roleModel = null,
        private ?RolePermissionModel $rolePermissionModel = null,
        private ?UserRoleModel $userRoleModel = null,
        private ?BaseConnection $db = null,
        private ?AuditLogService $auditLogService = null
    ) {
        $this->roleModel ??= new RoleModel();
        $this->rolePermissionModel ??= new RolePermissionModel();
        $this->userRoleModel ??= new UserRoleModel();
        $this->db ??= \Config\Database::connect();
        $this->auditLogService ??= new AuditLogService();
    }

    public function createRole(array $data, array $permissionKeys): array
    {
        $slug = strtolower(trim((string) ($data['slug'] ?? '')));
        $permissionKeys = $this->normalisePermissions($permissionKeys);

        $boundary = $this->validatePermissionBoundary($slug, $permissionKeys);
        if (! $boundary['success']) {
            return $boundary;
        }

        $roleId = $this->roleModel->insert([
            'name'        => $data['name'] ?? '',
            'slug'        => $slug,
            'base_role'   => $data['base_role'] ?? 'operator',
            'description' => $data['description'] ?? null,
            'is_system'   => 0,
            'is_active'   => 1,
            'sort_order'  => $data['sort_order'] ?? 100,
        ]);

        if (! $roleId) {
            return ['success' => false, 'message' => 'Gagal membuat role baru.'];
        }

        $this->replaceRolePermissions((int) $roleId, $permissionKeys);
        $this->auditLogService->record('rbac', 'create_role', [
            'entity_type' => 'roles',
            'entity_id'   => (int) $roleId,
            'new_data'    => ['role' => $data, 'permissions' => $permissionKeys],
        ]);

        return ['success' => true, 'message' => 'Role baru berhasil dibuat.', 'role_id' => (int) $roleId];
    }

    public function updateRole(int $roleId, array $data, array $permissionKeys): array
    {
        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return ['success' => false, 'message' => 'Role tidak ditemukan.'];
        }

        $permissionKeys = $this->normalisePermissions($permissionKeys);
        $boundary = $this->validatePermissionBoundary((string) $role['slug'], $permissionKeys);
        if (! $boundary['success']) {
            return $boundary;
        }

        $update = [
            'name'        => $data['name'] ?? $role['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => ! empty($data['is_active']) ? 1 : 0,
        ];

        if (! (int) $role['is_system']) {
            $update['base_role'] = $data['base_role'] ?? $role['base_role'];
        }

        $this->roleModel->update($roleId, $update);
        $this->replaceRolePermissions($roleId, $permissionKeys);
        service('rbacEngine')->invalidateRole($roleId);

        $this->auditLogService->record('rbac', 'update_role', [
            'entity_type' => 'roles',
            'entity_id'   => $roleId,
            'old_data'    => $role,
            'new_data'    => ['role' => $update, 'permissions' => $permissionKeys],
        ]);

        return ['success' => true, 'message' => 'Role dan permission berhasil diperbarui.'];
    }

    public function duplicateRole(int $roleId): array
    {
        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return ['success' => false, 'message' => 'Role tidak ditemukan.'];
        }

        $baseSlug = substr($role['slug'] . '_copy', 0, 45);
        $slug = $baseSlug;
        $counter = 2;
        while ($this->roleModel->findBySlug($slug)) {
            $slug = substr($baseSlug, 0, 42) . '_' . $counter;
            $counter++;
        }

        return $this->createRole([
            'name'        => $role['name'] . ' Copy',
            'slug'        => $slug,
            'base_role'   => $role['base_role'],
            'description' => $role['description'],
            'sort_order'  => 100,
        ], $this->rolePermissionModel->permissionKeysForRole($roleId));
    }

    public function deleteRole(int $roleId): array
    {
        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return ['success' => false, 'message' => 'Role tidak ditemukan.'];
        }

        if ((int) $role['is_system']) {
            return ['success' => false, 'message' => 'Role default sistem tidak dapat dihapus.'];
        }

        $affectedUserIds = [];
        if ($this->db->tableExists('user_roles')) {
            $affectedUserIds = array_map(
                'intval',
                array_column(
                    $this->db->table('user_roles')->select('user_id')->where('role_id', $roleId)->get()->getResultArray(),
                    'user_id'
                )
            );
        }

        $this->db->table('user_roles')->where('role_id', $roleId)->delete();
        $this->rolePermissionModel->where('role_id', $roleId)->delete();
        $this->roleModel->delete($roleId);
        foreach ($affectedUserIds as $userId) {
            service('rbacEngine')->invalidateUser($userId);
        }

        $this->auditLogService->record('rbac', 'delete_role', [
            'entity_type' => 'roles',
            'entity_id'   => $roleId,
            'old_data'    => $role,
        ]);

        return ['success' => true, 'message' => 'Role berhasil dihapus.'];
    }

    public function assignRole(int $userId, int $roleId, int $assignedBy, ?string $expiresAt = null): array
    {
        $role = $this->roleModel->find($roleId);
        if (! $role || ! (int) $role['is_active']) {
            return ['success' => false, 'message' => 'Role tidak aktif atau tidak ditemukan.'];
        }

        if ($userId === $assignedBy && ! in_array($role['slug'], ['admin', 'super_admin'], true)) {
            return ['success' => false, 'message' => 'Assignment role ke akun sendiri tidak diizinkan.'];
        }

        $actorBaseRole = (string) (session()->get('user_base_role') ?? session()->get('user_role') ?? '');
        if (in_array($role['slug'], ['admin', 'super_admin'], true) && $actorBaseRole !== 'admin') {
            return ['success' => false, 'message' => 'Hanya Super Admin yang dapat memberikan akses Super Admin.'];
        }

        if ($this->userRoleModel->hasRole($userId, $roleId)) {
            return ['success' => false, 'message' => 'User sudah memiliki role ini.'];
        }

        $this->userRoleModel->assignRole($userId, $roleId, $assignedBy, $expiresAt);
        service('rbacEngine')->invalidateUser($userId);

        $this->auditLogService->record('rbac', 'assign_role', [
            'entity_type' => 'users',
            'entity_id'   => $userId,
            'new_data'    => ['role_id' => $roleId, 'expires_at' => $expiresAt],
        ]);

        return ['success' => true, 'message' => 'Role berhasil diberikan ke user.'];
    }

    public function revokeRole(int $userId, int $roleId): array
    {
        $this->userRoleModel->revokeRole($userId, $roleId);
        service('rbacEngine')->invalidateUser($userId);

        $this->auditLogService->record('rbac', 'revoke_role', [
            'entity_type' => 'users',
            'entity_id'   => $userId,
            'old_data'    => ['role_id' => $roleId],
        ]);

        return ['success' => true, 'message' => 'Role user berhasil dicabut.'];
    }

    public function validatePermissionBoundary(string $roleSlug, array $permissionKeys): array
    {
        $protected = array_intersect(self::PROTECTED_PERMISSIONS, $permissionKeys);
        if ($protected !== [] && ! in_array($roleSlug, self::PROTECTED_ROLE_SLUGS, true)) {
            $this->auditLogService->record('rbac', 'PRIVILEGE_ESCALATION_BLOCKED', [
                'entity_type' => 'roles',
                'new_data'    => ['role_slug' => $roleSlug, 'permissions' => array_values($protected)],
            ]);

            return [
                'success' => false,
                'message' => 'Permission approval dan publikasi seleksi hanya dapat diberikan ke Kepala Sekolah atau Super Admin.',
            ];
        }

        return ['success' => true, 'message' => 'OK'];
    }

    private function replaceRolePermissions(int $roleId, array $permissionKeys): void
    {
        $permissionKeys = $this->normalisePermissions($permissionKeys);
        $validPermissions = [];

        if ($permissionKeys !== []) {
            $rows = $this->db->table('permissions')
                ->select('permission_key')
                ->whereIn('permission_key', $permissionKeys)
                ->where('is_active', 1)
                ->get()
                ->getResultArray();

            $validPermissions = array_column($rows, 'permission_key');
        }

        $this->rolePermissionModel->replacePermissions($roleId, $validPermissions);
    }

    private function normalisePermissions(array $permissionKeys): array
    {
        $normalised = [];
        foreach ($permissionKeys as $permissionKey) {
            $permissionKey = trim((string) $permissionKey);
            if ($permissionKey !== '') {
                $normalised[] = $permissionKey;
            }
        }

        return array_values(array_unique($normalised));
    }
}
