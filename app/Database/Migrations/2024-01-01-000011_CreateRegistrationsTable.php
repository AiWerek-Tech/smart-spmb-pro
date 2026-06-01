<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateRegistrationsTable extends Migration
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
            'user_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => false,
            ],
            'student_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => false,
            ],
            'jalur_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => false,
            ],
            'gelombang_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => true,
            ],
            'registration_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'SPMB-2026-0001',
            ],
            'academic_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 9,
                'null'       => false,
                'comment'    => '2026/2027',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'submitted', 'verified', 'accepted', 'rejected'],
                'null'       => false,
                'default'    => 'draft',
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('registration_number');
        $this->forge->addKey('user_id');
        $this->forge->addKey('jalur_id');
        $this->forge->addKey('status');
        $this->forge->addKey('academic_year');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('jalur_id', 'jalur', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('gelombang_id', 'gelombang', 'id', 'SET NULL', 'SET NULL');

        $this->forge->createTable('registrations', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('registrations', true);
    }
}
