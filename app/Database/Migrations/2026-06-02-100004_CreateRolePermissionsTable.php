<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolePermissionsTable extends Migration
{
    public function up(): void
    {
        // Buat pivot table role_permissions dengan composite PRIMARY KEY
        // dan FK CASCADE DELETE ke roles dan permissions
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `role_permissions` (
                `role_id`       INT UNSIGNED NOT NULL,
                `permission_id` INT UNSIGNED NOT NULL,
                `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`role_id`, `permission_id`),
                CONSTRAINT `fk_rp_role_id`
                    FOREIGN KEY (`role_id`)
                    REFERENCES `roles` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT `fk_rp_permission_id`
                    FOREIGN KEY (`permission_id`)
                    REFERENCES `permissions` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->forge->dropTable('role_permissions', true);
    }
}
