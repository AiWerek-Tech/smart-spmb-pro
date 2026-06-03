<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * AuditLogModel
 *
 * Model untuk tabel `audit_logs` — bersifat append-only (immutable).
 * Tidak ada UPDATE atau DELETE; setiap entry adalah rekaman permanen.
 *
 * Menyediakan:
 * - `log(array $data)`         : insert satu entri audit
 * - `getFiltered(array $filters)` : ambil log dengan filter + paginasi (untuk index)
 * - `exportToArray(array $filters)` : ambil semua log dengan filter sebagai array penuh (untuk ekspor)
 * - `getRecent(int $limit)`    : ambil N entri terbaru (untuk widget dashboard)
 *
 * Validates: Requirements 11.2, 11.3, 11.6
 */
class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    /**
     * Semua kolom kecuali `id` — log bersifat append-only, tidak ada UPDATE/DELETE.
     * Requirement 11.6: entri log tidak dapat dihapus maupun diubah.
     */
    protected $allowedFields = [
        'user_id',
        'user_name',
        'role_snapshot',
        'module',
        'action',
        'entity_type',
        'entity_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * Tabel audit_logs hanya memiliki `created_at`, tidak ada `updated_at`.
     * Kita kelola `created_at` secara manual agar tidak dibiarkan NULL.
     */
    protected $useTimestamps = false;

    protected $validationRules = [
        'user_name' => 'required|max_length[150]',
        'module'    => 'required|max_length[50]',
        'action'    => 'required|max_length[100]',
    ];

    // -------------------------------------------------------------------------
    // Append-Only Guard: Override update & delete agar tidak bisa dipanggil
    // -------------------------------------------------------------------------

    /**
     * Dinonaktifkan — audit log bersifat immutable.
     * Requirement 11.6: tidak ada entri yang dapat dihapus atau diubah.
     *
     * @throws \RuntimeException
     */
    public function update($id = null, $data = null): bool
    {
        throw new \RuntimeException('AuditLogModel is append-only. Update is not allowed.');
    }

    /**
     * Dinonaktifkan — audit log bersifat immutable.
     *
     * @throws \RuntimeException
     */
    public function delete($id = null, bool $purge = false)
    {
        throw new \RuntimeException('AuditLogModel is append-only. Delete is not allowed.');
    }

    // -------------------------------------------------------------------------
    // Write Method
    // -------------------------------------------------------------------------

    /**
     * Catat satu entri audit log.
     *
     * Kolom JSON (`role_snapshot`, `old_data`, `new_data`) secara otomatis
     * di-encode jika diberikan sebagai array/object.
     *
     * Requirement 11.2 — kolom yang disimpan:
     *   user_id, user_name, role_snapshot (JSON), module, action,
     *   entity_type, entity_id, old_data (JSON), new_data (JSON),
     *   ip_address, user_agent, created_at.
     *
     * @param  array $data Asosiatif dengan kunci sesuai allowedFields.
     * @return bool        true jika insert berhasil.
     */
    public function log(array $data): bool
    {
        // Auto-encode kolom JSON jika diberikan sebagai array / object
        foreach (['role_snapshot', 'old_data', 'new_data'] as $jsonField) {
            if (isset($data[$jsonField]) && is_array($data[$jsonField])) {
                $data[$jsonField] = json_encode($data[$jsonField], JSON_UNESCAPED_UNICODE);
            }
        }

        // Pastikan created_at selalu terisi
        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data) !== false;
    }

    // -------------------------------------------------------------------------
    // Read Methods
    // -------------------------------------------------------------------------

    /**
     * Ambil daftar audit log dengan filter opsional dan paginasi bawaan CI4.
     *
     * Filter yang didukung (Requirement 11.3):
     *   - `date_from`  (string Y-m-d)   : filter created_at >= date_from 00:00:00
     *   - `date_to`    (string Y-m-d)   : filter created_at <= date_to 23:59:59
     *   - `user_name`  (string)         : LIKE search pada kolom user_name
     *   - `module`     (string)         : exact match pada kolom module
     *   - `action`     (string)         : exact match pada kolom action
     *
     * Hasil diurutkan berdasarkan `created_at` DESC.
     * Mengaktifkan paginasi CI4 sehingga Controller dapat memanggil `->paginate(N)`.
     *
     * @param  array $filters Opsional, kunci sesuai deskripsi di atas.
     * @return $this          Builder yang siap di-paginate oleh Controller.
     */
    public function getFiltered(array $filters = []): static
    {
        $this->applyFilters($filters);
        $this->orderBy('created_at', 'DESC');

        return $this;
    }

    /**
     * Ambil seluruh log yang sesuai filter sebagai array (tanpa paginasi).
     * Digunakan untuk ekspor Excel / CSV.
     *
     * Requirement 11.5 + 11.3 — ekspor harus melewati filter yang sama
     * seperti tampilan list.
     *
     * @param  array $filters Sama seperti getFiltered().
     * @return array
     */
    public function exportToArray(array $filters = []): array
    {
        $this->applyFilters($filters);
        $this->orderBy('created_at', 'DESC');

        return $this->findAll();
    }

    /**
     * Ambil N entri audit log terbaru.
     * Digunakan oleh widget dashboard Super Admin (Requirement 11.8).
     *
     * @param  int   $limit Jumlah entri yang dikembalikan (default: 10).
     * @return array
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Terapkan kondisi filter WHERE pada builder saat ini.
     * Dipanggil secara internal oleh getFiltered() dan exportToArray().
     *
     * @param  array $filters
     * @return void
     */
    private function applyFilters(array $filters): void
    {
        // Filter rentang tanggal
        if (! empty($filters['date_from'])) {
            $this->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (! empty($filters['date_to'])) {
            $this->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        // Filter nama user — pencarian sebagian (LIKE)
        if (! empty($filters['user_name'])) {
            $this->like('user_name', $filters['user_name']);
        }

        // Filter modul — exact match
        if (! empty($filters['module'])) {
            $this->where('module', $filters['module']);
        }

        // Filter aksi — exact match
        if (! empty($filters['action'])) {
            $this->where('action', $filters['action']);
        }
    }
}
