<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table      = 'roles';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'slug',
        'name',
        'base_role',
        'description',
        'is_system',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'slug'      => 'required|alpha_dash|max_length[50]',
        'name'      => 'required|max_length[120]',
        'base_role' => 'required|in_list[admin,operator,pendaftar]',
    ];

    public function activeOrdered(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getActive(): array
    {
        return $this->activeOrdered();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    public function findByName(string $name): ?array
    {
        return $this->findBySlug($name);
    }

    public function isActiveSlug(string $slug): bool
    {
        return $this->where('slug', $slug)->where('is_active', 1)->countAllResults() > 0;
    }

    public function getWithPermissionCount(): array
    {
        return $this->db
            ->table('roles r')
            ->select('r.*, COUNT(rp.permission_key) AS permission_count')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->groupBy('r.id')
            ->orderBy('r.sort_order', 'ASC')
            ->orderBy('r.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getWithUserCount(): array
    {
        return $this->db
            ->table('roles r')
            ->select('r.*, COUNT(u.id) AS user_count')
            ->join('users u', 'u.role = r.slug', 'left')
            ->groupBy('r.id')
            ->orderBy('r.sort_order', 'ASC')
            ->orderBy('r.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getWithCounts(): array
    {
        return $this->db
            ->table('roles r')
            ->select('r.*, COUNT(DISTINCT rp.permission_key) AS permission_count, COUNT(DISTINCT u.id) AS user_count')
            ->join('role_permissions rp', 'rp.role_id = r.id', 'left')
            ->join('users u', 'u.role = r.slug', 'left')
            ->groupBy('r.id')
            ->orderBy('r.sort_order', 'ASC')
            ->orderBy('r.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getAvailableForUser(int $userId): array
    {
        $user = (new UserModel())->find($userId);
        $builder = $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC');

        if ($user && !empty($user['role'])) {
            $builder->where('slug !=', $user['role']);
        }

        return $builder->findAll();
    }

    public function getUsersForRole(int $roleId): array
    {
        $role = $this->find($roleId);
        if (!$role) {
            return [];
        }

        return $this->db
            ->table('users')
            ->select('id AS user_id, name AS user_name, email AS user_email, created_at AS assigned_at, NULL AS expires_at', false)
            ->where('role', $role['slug'])
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $builder = $this->where('slug', $name);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
