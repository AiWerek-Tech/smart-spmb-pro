<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentFamilyModel extends Model
{
    protected $table      = 'student_family';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'family_type',
        'full_name',
        'nik',
        'birth_place',
        'birth_date',
        'education',
        'occupation',
        'monthly_income',
        'phone',
        'relationship',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'student_id'  => 'required|integer',
        'family_type' => 'required|in_list[ayah,ibu,wali]',
        'full_name'   => 'required|max_length[100]',
    ];

    /**
     * Ambil semua data keluarga berdasarkan student_id.
     */
    public function findByStudentId(int $studentId): array
    {
        return $this->where('student_id', $studentId)
                    ->orderBy('family_type', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil data keluarga berdasarkan student_id dan tipe keluarga.
     */
    public function findByStudentAndType(int $studentId, string $familyType): ?array
    {
        return $this->where('student_id', $studentId)
                    ->where('family_type', $familyType)
                    ->first();
    }

    /**
     * Simpan atau perbarui data keluarga (upsert berdasarkan student_id + family_type).
     */
    public function saveOrUpdate(int $studentId, string $familyType, array $data): bool
    {
        $existing = $this->findByStudentAndType($studentId, $familyType);

        $data['student_id']  = $studentId;
        $data['family_type'] = $familyType;

        if ($existing !== null) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data) !== false;
    }

    /**
     * Ambil data ayah berdasarkan student_id.
     */
    public function getFather(int $studentId): ?array
    {
        return $this->findByStudentAndType($studentId, 'ayah');
    }

    /**
     * Ambil data ibu berdasarkan student_id.
     */
    public function getMother(int $studentId): ?array
    {
        return $this->findByStudentAndType($studentId, 'ibu');
    }

    /**
     * Ambil data wali berdasarkan student_id.
     */
    public function getGuardian(int $studentId): ?array
    {
        return $this->findByStudentAndType($studentId, 'wali');
    }
}
