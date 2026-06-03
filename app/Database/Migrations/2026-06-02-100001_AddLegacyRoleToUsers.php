<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class AddLegacyRoleToUsers extends Migration
{
    public function up(): void
    {
        if (!in_array('role', $this->db->getFieldNames('users'), true)) {
            return;
        }

        $roleColumn = $this->db->getFieldData('users');
        foreach ($roleColumn as $field) {
            if ($field->name === 'role' && strtolower((string) $field->type) !== 'enum') {
                return;
            }
        }

        // Drop index lama pada kolom role jika ada (dibuat oleh CreateUsersTable migration)
        try {
            $this->db->query("ALTER TABLE users DROP INDEX `role`");
        } catch (DatabaseException) {
            // Index mungkin sudah tidak ada atau sudah di-drop sebelumnya; lanjutkan.
        }

        // Rename kolom role → legacy_role, tetap ENUM 'admin','operator','pendaftar',
        // dan tambahkan index baru pada kolom legacy_role
        $this->db->query("
            ALTER TABLE users 
            CHANGE COLUMN `role` `legacy_role` ENUM('admin','operator','pendaftar') 
                NOT NULL DEFAULT 'pendaftar',
            ADD INDEX `idx_legacy_role` (`legacy_role`)
        ");
    }

    public function down(): void
    {
        if (!in_array('legacy_role', $this->db->getFieldNames('users'), true)) {
            return;
        }

        // Drop index pada legacy_role, kembalikan nama kolom ke role,
        // dan tambahkan kembali index pada kolom role
        $this->db->query("
            ALTER TABLE users 
            DROP INDEX `idx_legacy_role`,
            CHANGE COLUMN `legacy_role` `role` ENUM('admin','operator','pendaftar') 
                NOT NULL DEFAULT 'pendaftar',
            ADD INDEX `role` (`role`)
        ");
    }
}

