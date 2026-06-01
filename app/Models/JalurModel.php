<?php

namespace App\Models;

use CodeIgniter\Model;

class JalurModel extends Model
{
    protected $table      = 'jalur';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'name',
        'description',
        'quota',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'  => 'required|max_length[100]',
        'quota' => 'required|integer|greater_than[0]',
    ];

    /**
     * Ambil semua jalur yang aktif, diurutkan berdasarkan sort_order.
     */
    public function getActiveJalur(): array
    {
        return $this->where('is_active', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil semua jalur beserta jumlah pendaftar saat ini.
     */
    public function getJalurWithRegistrantCount(): array
    {
        return $this->select('jalur.*, COUNT(registrations.id) AS registrant_count')
                    ->join('registrations', 'registrations.jalur_id = jalur.id AND registrations.status != "draft"', 'left')
                    ->groupBy('jalur.id')
                    ->orderBy('jalur.sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Hitung jumlah pendaftar aktif pada suatu jalur.
     */
    public function countRegistrants(int $jalurId): int
    {
        $db = \Config\Database::connect();

        return (int) $db->table('registrations')
                        ->where('jalur_id', $jalurId)
                        ->whereNotIn('status', ['draft'])
                        ->countAllResults();
    }

    /**
     * Cek apakah kuota jalur masih tersedia.
     */
    public function hasAvailableQuota(int $jalurId): bool
    {
        $jalur = $this->find($jalurId);
        if ($jalur === null) {
            return false;
        }

        $registrantCount = $this->countRegistrants($jalurId);

        return $registrantCount < (int) $jalur['quota'];
    }

    /**
     * Toggle status aktif jalur.
     */
    public function toggleActive(int $id): bool
    {
        $jalur = $this->find($id);
        if ($jalur === null) {
            return false;
        }

        return $this->update($id, ['is_active' => $jalur['is_active'] ? 0 : 1]);
    }

    /**
     * Validasi kuota baru tidak lebih kecil dari jumlah pendaftar terdaftar.
     */
    public function validateQuota(int $jalurId, int $newQuota): bool
    {
        $registrantCount = $this->countRegistrants($jalurId);

        return $newQuota >= $registrantCount;
    }
}
