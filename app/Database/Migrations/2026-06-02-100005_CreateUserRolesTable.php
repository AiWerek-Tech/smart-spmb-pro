<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
{
    public function up(): void
    {
        // Buat pivot table user_roles dengan:
        // - UNIQUE constraint pada (user_id, role_id)
        // - FK CASCADE DELETE ke users dan roles
        // - FK SET NULL untuk assigned_by → users.id
        // - Index pada expires_at untuk query filter expired roles
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `user_roles` (
                `user_id`     INT UNSIGNED NOT NULL,
                `role_id`     INT UNSIGNED NOT NULL,
                `assigned_by` INT UNSIGNED NULL DEFAULT NULL,
                `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `expires_at`  DATETIME NULL DEFAULT NULL,
                UNIQUE KEY `uq_user_role` (`user_id`, `role_id`),
                INDEX `idx_expires_at` (`expires_at`),
                CONSTRAINT `fk_ur_user_id`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `users` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT `fk_ur_role_id`
                    FOREIGN KEY (`role_id`)
                    REFERENCES `roles` (`id`)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT `fk_ur_assigned_by`
                    FOREIGN KEY (`assigned_by`)
                    REFERENCES `users` (`id`)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->forge->dropTable('user_roles', true);
    }
}
