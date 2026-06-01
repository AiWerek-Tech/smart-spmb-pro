<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateGelombangTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jalur_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => false,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'open_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'close_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'announcement_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('jalur_id');
        $this->forge->addKey('is_active');
        $this->forge->addForeignKey('jalur_id', 'jalur', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('gelombang', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);

        // Add CHECK constraint for close_date > open_date
        $this->db->query('ALTER TABLE gelombang ADD CONSTRAINT chk_dates CHECK (close_date > open_date)');
    }

    public function down(): void
    {
        $this->forge->dropTable('gelombang', true);
    }
}
