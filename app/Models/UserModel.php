<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'     => 'required|max_length[100]',
        'email'    => 'required|valid_email|max_length[150]',
        'password' => 'required|min_length[8]',
        'role'     => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah terdaftar. Gunakan email lain atau masuk.',
        ],
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findActiveByEmail(string $email): ?array
    {
        return $this->where('email', $email)
            ->where('is_active', 1)
            ->first();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $builder = $this->where('email', $email);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function toggleActive(int $id): bool
    {
        $user = $this->find($id);
        if ($user === null) {
            return false;
        }

        return $this->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);
    }

    public function getUsers(?string $role = null, ?int $isActive = null): array
    {
        $builder = $this->builder();

        if ($role !== null) {
            $builder->where('role', $role);
        }

        if ($isActive !== null) {
            $builder->where('is_active', $isActive);
        }

        $builder->orderBy('created_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}
