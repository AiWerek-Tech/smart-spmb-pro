<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAddressModel extends Model
{
    protected $table      = 'student_address';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'address',
        'rt',
        'rw',
        'hamlet',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'residence_type',
        'distance_km',
        'transport_mode',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'student_id'     => 'required|integer',
        'address'        => 'required',
        'residence_type' => 'required|max_length[50]',
    ];

    /**
     * Ambil data alamat berdasarkan student_id.
     */
    public function findByStudentId(int $studentId): ?array
    {
        return $this->where('student_id', $studentId)->first();
    }

    /**
     * Simpan atau perbarui data alamat (upsert berdasarkan student_id).
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
