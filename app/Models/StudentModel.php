<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table      = 'students';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'religion_subgroup_id',
        'form_override',
        'citizenship',
        'family_status',
        'nik',
        'nisn',
        'birth_cert_number',
        'special_needs',
        'dapodik_percentage',
        'is_dapodik_ready',
        'score_distance',
        'score_achievement',
        'score_total',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'              => 'required|integer',
        'full_name'            => 'required|max_length[100]',
        'gender'               => 'required|in_list[L,P]',
        'birth_place'          => 'required|max_length[100]',
        'birth_date'           => 'required|valid_date',
        'religion'             => 'required|in_list[Islam,Kristen,Katolik,Hindu,Buddha,Konghucu]',
        'religion_subgroup_id' => 'permit_empty|integer',
        'citizenship'          => 'required|in_list[WNI,WNA]',
        'family_status'        => 'required|max_length[50]',
        'nik'                  => 'required|exact_length[16]|numeric',
    ];

    /**
     * Ambil data siswa berdasarkan user_id.
     */
    public function findByUserId(int $userId): ?array
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Ambil data siswa beserta data terkait (join).
     */
    public function getStudentWithDetails(int $studentId): ?array
    {
        return $this->select('students.*, users.email, users.role')
                    ->join('users', 'users.id = students.user_id')
                    ->where('students.id', $studentId)
                    ->first();
    }

    /**
     * Perbarui persentase dan status Dapodik.
     */
    public function updateDapodikStatus(int $studentId, float $percentage, bool $isReady): bool
    {
        return $this->update($studentId, [
            'dapodik_percentage' => $percentage,
            'is_dapodik_ready'   => $isReady ? 1 : 0,
        ]);
    }

    /**
     * Cek apakah NIK sudah digunakan oleh siswa lain.
     */
    public function nikExists(string $nik, ?int $excludeStudentId = null): bool
    {
        $builder = $this->where('nik', $nik);

        if ($excludeStudentId !== null) {
            $builder->where('id !=', $excludeStudentId);
        }

        return $builder->countAllResults() > 0;
    }
}
