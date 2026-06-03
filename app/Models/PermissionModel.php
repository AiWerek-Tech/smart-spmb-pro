<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table      = 'permissions';
    protected $primaryKey = 'permission_key';

    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'permission_key',
        'name',
        'group_name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'permission_key' => 'required|alpha_dash|max_length[80]',
        'name'           => 'required|max_length[120]',
        'group_name'     => 'required|max_length[80]',
    ];

    public function getGroupedByModule(): array
    {
        $permissions = $this->where('is_active', 1)
            ->orderBy('group_name', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['group_name']][] = $permission;
        }

        return $grouped;
    }

    public function activeGrouped(): array
    {
        return $this->getGroupedByModule();
    }

    public function findByNames(array $names): array
    {
        if ($names === []) {
            return [];
        }

        return $this->whereIn('permission_key', $names)->findAll();
    }

    public function getModules(): array
    {
        $rows = $this->db
            ->table($this->table)
            ->select('group_name')
            ->distinct()
            ->orderBy('group_name', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($rows, 'group_name');
    }

    public function getIdsByRole(int $roleId): array
    {
        $rows = $this->db
            ->table('role_permissions')
            ->select('permission_key')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        return array_column($rows, 'permission_key');
    }

    public function getGroupedByModuleForRole(int $roleId): array
    {
        $permissions = $this->db
            ->table('permissions p')
            ->select('p.*')
            ->join('role_permissions rp', 'rp.permission_key = p.permission_key')
            ->where('rp.role_id', $roleId)
            ->where('p.is_active', 1)
            ->orderBy('p.group_name', 'ASC')
            ->orderBy('p.sort_order', 'ASC')
            ->orderBy('p.name', 'ASC')
            ->get()
            ->getResultArray();

        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['group_name']][] = $permission;
        }

        return $grouped;
    }
}
