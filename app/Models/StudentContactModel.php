<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentContactModel extends Model
{
    protected $table      = 'student_contact';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'phone',
        'email',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'student_id' => 'required|integer',
        'phone'      => 'required|max_length[20]',
        'email'      => 'permit_empty|valid_email|max_length[150]',
    ];

    /**
     * Ambil data kontak berdasarkan student_id.
     */
    public function findByStudentId(int $studentId): ?array
    {
        return $this->where('student_id', $studentId)->first();
    }

    /**
     * Simpan atau perbarui data kontak (upsert berdasarkan student_id).
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
