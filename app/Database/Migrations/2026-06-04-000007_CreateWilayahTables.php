<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWilayahTables extends Migration
{
    public function up(): void
    {
        // 1. Provinces
        if (!$this->db->tableExists('regions_provinces')) {
            $this->forge->addField([
                'id'   => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
                'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('regions_provinces', true);
        }

        // 2. Regencies / Cities
        if (!$this->db->tableExists('regions_regencies')) {
            $this->forge->addField([
                'id'          => ['type' => 'CHAR', 'constraint' => 4, 'null' => false],
                'province_id' => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
                'name'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('province_id', 'regions_provinces', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('regions_regencies', true);
        }

        // 3. Districts (Kecamatan)
        if (!$this->db->tableExists('regions_districts')) {
            $this->forge->addField([
                'id'         => ['type' => 'CHAR', 'constraint' => 7, 'null' => false],
                'regency_id' => ['type' => 'CHAR', 'constraint' => 4, 'null' => false],
                'name'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('regency_id', 'regions_regencies', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('regions_districts', true);
        }

        // 4. Villages (Kelurahan)
        if (!$this->db->tableExists('regions_villages')) {
            $this->forge->addField([
                'id'          => ['type' => 'CHAR', 'constraint' => 10, 'null' => false],
                'district_id' => ['type' => 'CHAR', 'constraint' => 7, 'null' => false],
                'name'        => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('district_id', 'regions_districts', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('regions_villages', true);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('regions_villages', true);
        $this->forge->dropTable('regions_districts', true);
        $this->forge->dropTable('regions_regencies', true);
        $this->forge->dropTable('regions_provinces', true);
    }
}
