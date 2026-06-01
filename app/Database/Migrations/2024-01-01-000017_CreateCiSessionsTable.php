<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Session table for CodeIgniter 4 DatabaseHandler.
 * Required when using session.driver = DatabaseHandler.
 */
class CreateCiSessionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => false,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => false,
            ],
            'timestamp' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'data' => [
                'type' => 'BLOB',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');

        $this->forge->createTable('ci_sessions', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('ci_sessions', true);
    }
}
