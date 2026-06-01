<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentPeriodicModel extends Model
{
    protected $table      = 'student_periodic';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'height_cm',
        'weight_kg',
        'has_kip',
        'kip_number',
        'has_kks',
        'kks_number',
        'pkh_number',
        'special_condition',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'student_id'        => 'required|integer',
        'special_condition' => 'required|max_length[100]',
    ];

    /**
     * Ambil data periodik berdasarkan student_id.
     */
    public function findByStudentId(int $studentId): ?array
    {
        return $this->where('student_id', $studentId)->first();
    }

    /**
     * Simpan atau perbarui data periodik (upsert berdasarkan student_id).
     */
    public function saveOrUpdate(int $studentId, array $data): bool
    {
        $existing = $this->findByStudentId($studentId);

        $data['student_id'] = $studentId;

        if ($existing !== null) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data) !== false;
    }
}
