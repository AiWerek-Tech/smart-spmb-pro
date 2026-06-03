<?php

namespace App\Models;

use CodeIgniter\Model;

class GelombangModel extends Model
{
    protected $table      = 'gelombang';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'academic_year',
        'jalur_id',
        'name',
        'open_date',
        'close_date',
        'announcement_date',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'academic_year' => 'permit_empty|max_length[9]',
        'jalur_id'   => 'required|integer',
        'name'       => 'required|max_length[100]',
        'open_date'  => 'required|valid_date',
        'close_date' => 'required|valid_date',
    ];

    /**
     * Ambil semua gelombang yang aktif.
     */
    public function getActiveGelombang(?string $academicYear = null): array
    {
        $query = $this->where('is_active', 1);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->orderBy('open_date', 'ASC')->findAll();
    }

    /**
     * Ambil gelombang beserta informasi jalur.
     */
    public function getGelombangWithJalur(?string $academicYear = null): array
    {
        $query = $this->select('gelombang.*, jalur.name AS jalur_name')
            ->join('jalur', 'jalur.id = gelombang.jalur_id');

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('gelombang.academic_year', $academicYear);
        }

        return $query->orderBy('gelombang.open_date', 'ASC')->findAll();
    }

    /**
     * Ambil gelombang berdasarkan jalur_id.
     */
    public function findByJalurId(int $jalurId, ?string $academicYear = null): array
    {
        $query = $this->where('jalur_id', $jalurId);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->orderBy('open_date', 'ASC')->findAll();
    }

    /**
     * Hitung jumlah gelombang aktif.
     */
    public function countActiveGelombang(?string $academicYear = null): int
    {
        $query = $this->where('is_active', 1);

        if ($academicYear !== null && $academicYear !== '') {
            $query->where('academic_year', $academicYear);
        }

        return $query->countAllResults();
    }

    /**
     * Validasi bahwa tanggal tutup lebih besar dari tanggal buka.
     */
    public function validateDates(string $openDate, string $closeDate): bool
    {
        return strtotime($closeDate) > strtotime($openDate);
    }

    /**
     * Toggle status aktif gelombang.
     */
    public function toggleActive(int $id): bool
    {
        $gelombang = $this->find($id);
        if ($gelombang === null) {
            return false;
        }

        return $this->update($id, ['is_active' => $gelombang['is_active'] ? 0 : 1]);
    }
}
