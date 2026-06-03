<?php

namespace App\Models;

use CodeIgniter\Model;

class TeacherModel extends Model
{
    protected $table      = 'teachers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'academic_year',
        'name',
        'role',
        'photo',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'academic_year' => 'permit_empty|max_length[9]',
        'name'       => 'required|max_length[150]',
        'role'       => 'required|max_length[150]',
        'is_active'  => 'required|in_list[0,1]',
        'sort_order' => 'required|integer',
    ];

    public function activeOrdered(?string $academicYear = null): array
    {
        $query = $this->where('is_active', 1);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
