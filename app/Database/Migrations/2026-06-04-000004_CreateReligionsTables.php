<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReligionsTables extends Migration
{
    public function up(): void
    {
        // 1. Create religions table
        if (!$this->db->tableExists('religions')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('name');
            $this->forge->createTable('religions', true);
        }

        // 2. Create religion_subgroups table
        if (!$this->db->tableExists('religion_subgroups')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'religion_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['religion_id', 'name']);
            $this->forge->addForeignKey('religion_id', 'religions', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('religion_subgroups', true);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('religion_subgroups', true);
        $this->forge->dropTable('religions', true);
    }
}
