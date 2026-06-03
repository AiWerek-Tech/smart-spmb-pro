<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserRoleModel
 *
 * Model untuk tabel pivot `user_roles` dalam sistem RBAC berbasis kepanitiaan.
 * Mengelola assignment role ke user, termasuk query role aktif (belum expired
 * dan role berstatus aktif), serta pencabutan assignment.
 *
 * Tabel `user_roles` menggunakan composite key (user_id, role_id) tanpa
 * kolom auto-increment, sehingga `$useAutoIncrement = false` dan
 * `$primaryKey` dikosongkan.
 *
 * Validates: Requirements 3.6, 3.7, 6.1
 */
class UserRoleModel extends Model
{
    protected $table      = 'user_roles';
    protected $primaryKey = '';

    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'user_id',
        'role_id',
        'assigned_by',
        'assigned_at',
        'expires_at',
    ];

    protected $useTimestamps = false;

    // -------------------------------------------------------------------------
    // Query Methods
    // -------------------------------------------------------------------------

    /**
     * Ambil semua role aktif yang dimiliki user tertentu.
     *
     * Memfilter berdasarkan:
     * - `roles.is_active = 1` (role tidak dinonaktifkan)
     * - `user_roles.expires_at IS NULL OR user_roles.expires_at > NOW()` (assignment belum expired)
     *
     * Mengembalikan data role lengkap beserta metadata assignment.
     *
     * @param  int $userId
     * @return array
     */
    public function getActiveRolesForUser(int $userId): array
    {
        return $this->db
            ->table('user_roles ur')
            ->select("r.id, r.slug AS name, r.name AS display_name, r.description, '#6B7280' AS color_badge, r.is_system AS is_default, ur.assigned_at, ur.expires_at, ur.assigned_by", false)
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('r.is_active', 1)
            ->where('(ur.expires_at IS NULL OR ur.expires_at > ' . $this->db->escape(date('Y-m-d H:i:s')) . ')', null, false)
            ->orderBy('r.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Assign role ke user.
     *
     * Menyimpan entri baru ke tabel `user_roles`. Pastikan tidak ada duplikat
     * sebelum memanggil method ini (gunakan `hasRole()` atau cek di layer Service).
     *
     * @param  int         $userId      ID user yang akan diberi role
     * @param  int         $roleId      ID role yang akan di-assign
     * @param  int         $assignedBy  ID user yang melakukan assignment
     * @param  string|null $expiresAt   Tanggal kedaluwarsa assignment (format 'Y-m-d H:i:s'), null jika permanen
     * @return bool        true jika insert berhasil
     */
    public function assignRole(int $userId, int $roleId, int $assignedBy, ?string $expiresAt = null): bool
    {
        $data = [
            'user_id'     => $userId,
            'role_id'     => $roleId,
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s'),
            'expires_at'  => $expiresAt,
        ];

        return $this->db
            ->table($this->table)
            ->insert($data);
    }

    /**
     * Cabut role dari user.
     *
     * Menghapus entri assignment berdasarkan kombinasi `user_id` dan `role_id`.
     *
     * @param  int  $userId  ID user
     * @param  int  $roleId  ID role yang dicabut
     * @return bool true jika delete berhasil dan setidaknya satu baris terhapus
     */
    public function revokeRole(int $userId, int $roleId): bool
    {
        return $this->db
            ->table($this->table)
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->delete();
    }

    /**
     * Ambil semua user yang memiliki role tertentu.
     *
     * Menyertakan informasi user (nama, email) dan metadata assignment
     * (tanggal assign, tanggal kedaluwarsa, nama yang mengassign).
     * Tidak memfilter expired — mengembalikan semua assignment (aktif maupun expired).
     *
     * @param  int $roleId
     * @return array
     */
    public function getUsersForRole(int $roleId): array
    {
        return $this->db
            ->table('user_roles ur')
            ->select('ur.user_id, ur.assigned_at, ur.expires_at, u.name AS user_name, u.email AS user_email, ab.name AS assigned_by_name')
            ->join('users u', 'u.id = ur.user_id')
            ->join('users ab', 'ab.id = ur.assigned_by', 'left')
            ->where('ur.role_id', $roleId)
            ->orderBy('ur.assigned_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // -------------------------------------------------------------------------
    // Utility Methods
    // -------------------------------------------------------------------------

    /**
     * Cek apakah user sudah memiliki role tertentu (assignment masih ada,
     * terlepas dari status expired atau tidak).
     *
     * @param  int $userId
     * @param  int $roleId
     * @return bool
     */
    public function hasRole(int $userId, int $roleId): bool
    {
        return $this->db
            ->table($this->table)
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->countAllResults() > 0;
    }

    /**
     * Ambil semua assignment role untuk user tertentu (termasuk yang expired),
     * beserta metadata role.
     *
     * @param  int $userId
     * @return array
     */
    public function getAllRolesForUser(int $userId): array
    {
        return $this->db
            ->table('user_roles ur')
            ->select("r.id, r.slug AS name, r.name AS display_name, '#6B7280' AS color_badge, r.is_active AS role_is_active, ur.assigned_at, ur.expires_at, ur.assigned_by", false)
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->orderBy('ur.assigned_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil semua user_id yang memiliki role tertentu dan assignment-nya masih aktif.
     * Berguna untuk invalidasi cache massal saat role dinonaktifkan atau permission diubah.
     *
     * @param  int $roleId
     * @return int[]
     */
    public function getActiveUserIdsForRole(int $roleId): array
    {
        $rows = $this->db
            ->table($this->table)
            ->select('user_id')
            ->where('role_id', $roleId)
            ->where('(expires_at IS NULL OR expires_at > ' . $this->db->escape(date('Y-m-d H:i:s')) . ')', null, false)
            ->get()
            ->getResultArray();

        return array_column($rows, 'user_id');
    }
}
