<?php

namespace App\Models;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table      = 'role_permissions';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'role_id',
        'permission_key',
        'created_at',
    ];

    protected $useTimestamps = false;

    public function permissionKeysForRole(int $roleId): array
    {
        return array_column(
            $this->select('permission_key')->where('role_id', $roleId)->findAll(),
            'permission_key'
        );
    }

    public function replacePermissions(int $roleId, array $permissionKeys): void
    {
        $this->where('role_id', $roleId)->delete();

        $rows = [];
        foreach (array_unique($permissionKeys) as $permissionKey) {
            if ($permissionKey === '') {
                continue;
            }

            $rows[] = [
                'role_id'        => $roleId,
                'permission_key' => $permissionKey,
                'created_at'     => date('Y-m-d H:i:s'),
            ];
        }

        if ($rows !== []) {
            $this->insertBatch($rows);
        }
    }
}
