<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

/**
 * BackupService — Layanan ekspor dan impor database (backup & restore).
 */
class BackupService
{
    protected BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Menghasilkan file SQL dump dari database saat ini secara manual/programatis.
     * Dikarenakan utility backup bawaan CI4 tidak didukung oleh driver MySQLi secara default.
     *
     * @return string Isi SQL dump
     */
    public function generateBackup(): string
    {
        // Ambil semua tabel yang ada di database saat ini
        $query = $this->db->query('SHOW TABLES');
        $tables = [];
        foreach ($query->getResultArray() as $row) {
            $tables[] = reset($row);
        }

        $output = "-- Smart SPMB Pro Database Backup\n";
        $output .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            // Dapatkan struktur CREATE TABLE
            $createQuery = $this->db->query('SHOW CREATE TABLE ' . $this->db->escapeIdentifiers($table));
            $createResult = $createQuery->getRowArray();
            $createStatement = $createResult['Create Table'] ?? $createResult['Create View'] ?? '';

            if (empty($createStatement)) {
                continue;
            }

            $output .= "DROP TABLE IF EXISTS " . $this->db->escapeIdentifiers($table) . ";\n";
            $output .= $createStatement . ";\n\n";

            // Dapatkan semua baris data
            $rowsQuery = $this->db->query('SELECT * FROM ' . $this->db->escapeIdentifiers($table));
            $rows = $rowsQuery->getResultArray();

            if (!empty($rows)) {
                $columns = array_keys($rows[0]);
                $escapedColumns = array_map(function($col) {
                    return $this->db->escapeIdentifiers($col);
                }, $columns);

                $output .= "INSERT INTO " . $this->db->escapeIdentifiers($table) . " (" . implode(', ', $escapedColumns) . ") VALUES\n";

                $valueStrings = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = $this->db->escape($val);
                        }
                    }
                    $valueStrings[] = "(" . implode(', ', $rowValues) . ")";
                }

                $output .= implode(",\n", $valueStrings) . ";\n\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

        return $output;
    }

    /**
     * Memulihkan (restore) database dari string SQL dump.
     *
     * @param string $sqlContent Isi SQL dump
     * @return array ['success' => bool, 'message' => string]
     */
    public function restoreBackup(string $sqlContent): array
    {
        try {
            // Disable foreign key checks temporarily during restore
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Pisahkan statement SQL berdasarkan titik koma di akhir baris
            // we must be careful with multiline queries, but for standard CI4 dumps it is clean
            $queries = preg_split('/;(?:\s*\r?\n)/', $sqlContent);

            $executedCount = 0;
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $this->db->query($query);
                    $executedCount++;
                }
            }

            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            return [
                'success' => true,
                'message' => "Database telah berhasil dipulihkan ($executedCount query berhasil dijalankan).",
            ];
        } catch (\Throwable $e) {
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            return [
                'success' => false,
                'message' => 'Gagal memulihkan database: ' . $e->getMessage(),
            ];
        }
    }
}
