<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAchievementModel extends Model
{
    protected $table      = 'student_achievements';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'student_id',
        'achievement_type',
        'competition_name',
        'level',
        'rank',
        'year',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'student_id'       => 'required|integer',
        'achievement_type' => 'required|in_list[akademik,non-akademik]',
        'competition_name' => 'required|max_length[200]',
        'level'            => 'required|in_list[kecamatan,kabupaten,provinsi,nasional,internasional]',
        'rank'             => 'required|in_list[juara 1,juara 2,juara 3,harapan]',
        'year'             => 'required|integer|greater_than[1900]',
    ];

    /**
     * Ambil semua prestasi berdasarkan student_id.
     */
    public function findByStudentId(int $studentId): array
    {
        return $this->where('student_id', $studentId)
                    ->orderBy('year', 'DESC')
                    ->findAll();
    }

    /**
     * Hapus semua prestasi berdasarkan student_id.
     */
    public function deleteByStudentId(int $studentId): bool
    {
        return $this->where('student_id', $studentId)->delete();
    }

    /**
     * Simpan banyak prestasi sekaligus (bulk insert).
     * Menghapus data lama terlebih dahulu.
     */
    public function replaceAll(int $studentId, array $achievements): bool
    {
        $this->deleteByStudentId($studentId);

        if (empty($achievements)) {
            return true;
        }

        foreach ($achievements as &$achievement) {
            $achievement['student_id'] = $studentId;
        }

        return $this->insertBatch($achievements) !== false;
    }

    /**
     * Hitung jumlah prestasi berdasarkan student_id.
     */
    public function countByStudentId(int $studentId): int
    {
        return $this->where('student_id', $studentId)->countAllResults();
    }
}
