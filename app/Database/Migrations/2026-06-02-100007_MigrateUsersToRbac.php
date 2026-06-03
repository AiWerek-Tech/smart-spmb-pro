<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

/**
 * MigrateUsersToRbac
 *
 * Konversi legacy_role → user_roles assignment:
 *   legacy_role='admin'     → role 'super_admin'
 *   legacy_role='operator'  → role 'petugas_pendaftaran'
 *   legacy_role='pendaftar' → role 'pendaftar'
 *
 * Error per-user dicatat ke writable/logs/rbac_migration_{date}.log,
 * proses lanjut ke user berikutnya (tidak crash).
 *
 * Validates: Requirements 1.7, 1.8, 15.3
 */
class MigrateUsersToRbac extends Migration
{
    /**
     * Peta legacy_role → role name di tabel roles.
     */
    private const ROLE_MAP = [
        'admin'     => 'super_admin',
        'operator'  => 'petugas_pendaftaran',
        'pendaftar' => 'pendaftar',
    ];

    public function up(): void
    {
        // Pastikan tabel-tabel yang diperlukan sudah ada
        if (
            ! $this->db->tableExists('users')
            || ! $this->db->tableExists('roles')
            || ! $this->db->tableExists('user_roles')
        ) {
            $this->logLine('SKIP: Tabel users, roles, atau user_roles belum tersedia.');
            return;
        }

        // Pastikan kolom legacy_role sudah ada
        if (! in_array('legacy_role', $this->db->getFieldNames('users'), true)) {
            $this->logLine('SKIP: Kolom legacy_role tidak ditemukan di tabel users.');
            return;
        }

        // Ambil semua user yang memiliki legacy_role
        $users = $this->db->table('users')
            ->select('id, name, legacy_role')
            ->whereIn('legacy_role', array_keys(self::ROLE_MAP))
            ->get()
            ->getResultArray();

        if (empty($users)) {
            $this->logLine('INFO: Tidak ada user dengan legacy_role yang perlu dimigrasi.');
            return;
        }

        // Cache role id agar tidak query berulang
        $roleIdCache = [];

        $countSuccess = 0;
        $countSkip    = 0;
        $countError   = 0;
        $now          = date('Y-m-d H:i:s');

        foreach ($users as $user) {
            $userId     = (int) $user['id'];
            $legacyRole = $user['legacy_role'];
            $userName   = $user['name'] ?? "user#{$userId}";

            // Pastikan ada peta untuk legacy_role ini
            if (! isset(self::ROLE_MAP[$legacyRole])) {
                $this->logLine("SKIP: User ID={$userId} ({$userName}) — legacy_role '{$legacyRole}' tidak ada dalam peta konversi.");
                $countSkip++;
                continue;
            }

            $targetRoleName = self::ROLE_MAP[$legacyRole];

            // Ambil role_id dari cache atau query
            if (! isset($roleIdCache[$targetRoleName])) {
                $roleRow = $this->db->table('roles')
                    ->select('id')
                    ->where('name', $targetRoleName)
                    ->get()
                    ->getRowArray();

                if (empty($roleRow)) {
                    $this->logLine("SKIP: User ID={$userId} ({$userName}) — role target '{$targetRoleName}' tidak ditemukan di tabel roles. Jalankan RoleSeeder terlebih dahulu.");
                    $countSkip++;
                    continue;
                }

                $roleIdCache[$targetRoleName] = (int) $roleRow['id'];
            }

            $roleId = $roleIdCache[$targetRoleName];

            try {
                // Cek apakah assignment sudah ada (idempoten)
                $exists = $this->db->table('user_roles')
                    ->where('user_id', $userId)
                    ->where('role_id', $roleId)
                    ->countAllResults();

                if ($exists > 0) {
                    $this->logLine("SKIP: User ID={$userId} ({$userName}) — sudah memiliki assignment role '{$targetRoleName}', dilewati.");
                    $countSkip++;
                    continue;
                }

                // Insert assignment
                $this->db->table('user_roles')->insert([
                    'user_id'     => $userId,
                    'role_id'     => $roleId,
                    'assigned_by' => null,
                    'assigned_at' => $now,
                    'expires_at'  => null,
                ]);

                $this->logLine("OK: User ID={$userId} ({$userName}) — legacy_role='{$legacyRole}' → role='{$targetRoleName}' (role_id={$roleId}).");
                $countSuccess++;
            } catch (DatabaseException $e) {
                $this->logLine("ERROR: User ID={$userId} ({$userName}) — " . $e->getMessage());
                $countError++;
                // Lanjutkan ke user berikutnya, tidak crash
            }
        }

        $this->logLine("SELESAI: {$countSuccess} berhasil, {$countSkip} dilewati, {$countError} error dari total " . count($users) . " user.");
    }

    public function down(): void
    {
        if (
            ! $this->db->tableExists('users')
            || ! $this->db->tableExists('roles')
            || ! $this->db->tableExists('user_roles')
        ) {
            return;
        }

        if (! in_array('legacy_role', $this->db->getFieldNames('users'), true)) {
            return;
        }

        // Hapus assignment yang dibuat oleh migrasi ini:
        // yaitu user_roles di mana user memiliki legacy_role yang termasuk dalam peta konversi
        // dan role_id sesuai dengan role target yang telah dimappingkan.
        foreach (self::ROLE_MAP as $legacyRole => $roleName) {
            try {
                $roleRow = $this->db->table('roles')
                    ->select('id')
                    ->where('name', $roleName)
                    ->get()
                    ->getRowArray();

                if (empty($roleRow)) {
                    continue;
                }

                $roleId = (int) $roleRow['id'];

                // Hapus assignment user_roles untuk user yang memiliki legacy_role ini
                // dan role_id yang sesuai (assignment yang dibuat oleh migrasi ini tidak
                // memiliki assigned_by — dibiarkan null)
                $this->db->query(
                    "DELETE ur FROM user_roles ur
                     INNER JOIN users u ON u.id = ur.user_id
                     WHERE ur.role_id = ?
                       AND u.legacy_role = ?
                       AND ur.assigned_by IS NULL",
                    [$roleId, $legacyRole]
                );
            } catch (DatabaseException $e) {
                // Catat error tapi tetap lanjut rollback role lain
                $this->logLine("DOWN ERROR untuk legacy_role='{$legacyRole}': " . $e->getMessage());
            }
        }
    }

    /**
     * Catat satu baris ke file log migrasi RBAC.
     * Format file: writable/logs/rbac_migration_{date}.log
     */
    private function logLine(string $message): void
    {
        $logDir  = WRITEPATH . 'logs';
        $logFile = $logDir . DIRECTORY_SEPARATOR . 'rbac_migration_' . date('Y-m-d') . '.log';
        $line    = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;

        // Pastikan direktori log ada
        if (! is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        // Gunakan FILE_APPEND | LOCK_EX agar aman saat proses concurrent
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
