<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Libraries\RbacEngine;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class RbacEngineTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    private RbacEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        session()->destroy();
        cache()->clean();
        $this->engine = new RbacEngine();
    }

    public function testUnionPermissionsFromPrimaryAndAssignedRoles(): void
    {
        $db = \Config\Database::connect();
        $suffix = uniqid('', false);
        $viewPermission = 'rbac_test_view_' . $suffix;
        $editPermission = 'rbac_test_edit_' . $suffix;

        $this->insertPermission($viewPermission);
        $this->insertPermission($editPermission);

        $primaryRoleId = $this->insertRole('rbac_primary_' . $suffix, 'operator');
        $extraRoleId = $this->insertRole('rbac_extra_' . $suffix, 'operator');
        $userId = $this->insertUser('rbac-user-' . $suffix . '@test.local', 'rbac_primary_' . $suffix);

        $db->table('role_permissions')->insert(['role_id' => $primaryRoleId, 'permission_key' => $viewPermission, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('role_permissions')->insert(['role_id' => $extraRoleId, 'permission_key' => $editPermission, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => $extraRoleId, 'assigned_at' => date('Y-m-d H:i:s')]);
        $this->engine->invalidateUser($userId);
        cache()->delete('rbac_permissions_user_' . $userId);
        session()->remove(['user_id', 'user_roles', 'user_permissions', 'rbac_cached_at']);

        $effectivePermissions = $this->engine->getUserPermissions($userId);
        $roleRows = $db->table('user_roles')->where('user_id', $userId)->get()->getResultArray();
        $extraRoleRow = $db->table('roles')->where('id', $extraRoleId)->get()->getRowArray();
        $directJoinRows = $db->table('user_roles ur')
            ->select('r.id, r.slug')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.is_active', 1)
            ->where('(ur.expires_at IS NULL OR ur.expires_at > ' . $db->escape(date('Y-m-d H:i:s')) . ')', null, false)
            ->get()
            ->getResultArray();
        $activeRoles = $this->engine->getRoles($userId);

        $this->assertTrue(
            in_array($viewPermission, $effectivePermissions, true) && in_array($editPermission, $effectivePermissions, true),
            'Effective permissions: ' . json_encode($effectivePermissions) . '; user_roles: ' . json_encode($roleRows) . '; extra_role: ' . json_encode($extraRoleRow) . '; direct_join: ' . json_encode($directJoinRows) . '; active_roles: ' . json_encode($activeRoles)
        );
    }

    public function testExpiredRoleAssignmentIsIgnored(): void
    {
        $db = \Config\Database::connect();
        $suffix = uniqid('', false);
        $permission = 'rbac_expired_' . $suffix;

        $this->insertPermission($permission);
        $primaryRoleId = $this->insertRole('rbac_base_' . $suffix, 'operator');
        $expiredRoleId = $this->insertRole('rbac_expired_role_' . $suffix, 'operator');
        $userId = $this->insertUser('rbac-expired-' . $suffix . '@test.local', 'rbac_base_' . $suffix);

        $db->table('role_permissions')->insert(['role_id' => $expiredRoleId, 'permission_key' => $permission, 'created_at' => date('Y-m-d H:i:s')]);
        $db->table('user_roles')->insert([
            'user_id'     => $userId,
            'role_id'     => $expiredRoleId,
            'assigned_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'expires_at'  => date('Y-m-d H:i:s', strtotime('-1 day')),
        ]);

        $this->assertFalse($this->engine->hasPermission($userId, $permission));
        $this->assertFalse($this->engine->hasPermission($userId, 'missing_permission'));
        $this->assertNotEmpty($primaryRoleId);
    }

    public function testAdminBaseRoleReceivesAllActivePermissions(): void
    {
        $suffix = uniqid('', false);
        $permission = 'rbac_admin_' . $suffix;

        $this->insertPermission($permission);
        $this->insertRole('rbac_admin_role_' . $suffix, 'admin');
        $userId = $this->insertUser('rbac-admin-' . $suffix . '@test.local', 'rbac_admin_role_' . $suffix);

        $this->assertTrue($this->engine->hasPermission($userId, $permission));
    }

    private function insertPermission(string $permissionKey): void
    {
        \Config\Database::connect()->table('permissions')->insert([
            'permission_key' => $permissionKey,
            'name'           => $permissionKey,
            'group_name'     => 'RBAC Test',
            'description'    => 'Permission test RBAC',
            'is_active'      => 1,
            'sort_order'     => 999,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    private function insertRole(string $slug, string $baseRole): int
    {
        $db = \Config\Database::connect();
        $db->table('roles')->insert([
            'slug'        => $slug,
            'name'        => ucwords(str_replace('_', ' ', $slug)),
            'base_role'   => $baseRole,
            'description' => 'Role test RBAC',
            'is_system'   => 0,
            'is_active'   => 1,
            'sort_order'  => 999,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }

    private function insertUser(string $email, string $roleSlug): int
    {
        $db = \Config\Database::connect();
        $db->table('users')->insert([
            'name'       => 'RBAC Test User',
            'email'      => $email,
            'password'   => password_hash('Password123', PASSWORD_BCRYPT),
            'role'       => $roleSlug,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $db->insertID();
    }
}
