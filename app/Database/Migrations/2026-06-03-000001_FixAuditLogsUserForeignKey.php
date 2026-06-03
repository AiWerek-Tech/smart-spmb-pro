<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Throwable;

class FixAuditLogsUserForeignKey extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('audit_logs')) {
            return;
        }

        $this->dropExistingForeignKey();

        $this->addForeignKey('SET NULL', 'CASCADE');
    }

    public function down(): void
    {
        if (! $this->db->tableExists('audit_logs')) {
            return;
        }

        $this->dropExistingForeignKey();

        $this->addForeignKey('NO ACTION', 'SET NULL');
    }

    private function dropExistingForeignKey(): void
    {
        $constraints = $this->db->query("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'audit_logs'
              AND COLUMN_NAME = 'user_id'
              AND REFERENCED_TABLE_NAME = 'users'
        ")->getResultArray();

        foreach ($constraints as $constraint) {
            $name = $constraint['CONSTRAINT_NAME'] ?? '';
            if ($name !== '') {
                try {
                    $this->db->query("ALTER TABLE `audit_logs` DROP FOREIGN KEY `{$name}`");
                } catch (Throwable) {
                }
            }
        }
    }

    private function addForeignKey(string $onDelete, string $onUpdate): void
    {
        if (! $this->db->tableExists('users') || $this->hasUserForeignKey()) {
            return;
        }

        $this->db->query("
            ALTER TABLE `audit_logs`
            ADD CONSTRAINT `audit_logs_user_id_foreign`
            FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            ON DELETE {$onDelete}
            ON UPDATE {$onUpdate}
        ");
    }

    private function hasUserForeignKey(): bool
    {
        $constraints = $this->db->query("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'audit_logs'
              AND COLUMN_NAME = 'user_id'
              AND REFERENCED_TABLE_NAME = 'users'
        ")->getResultArray();

        return $constraints !== [];
    }
}
