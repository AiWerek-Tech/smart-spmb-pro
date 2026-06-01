<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStudentDocumentsTable extends Migration
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
            'student_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => false,
            ],
            'document_type' => [
                'type'       => 'ENUM',
                'constraint' => ['kk', 'akta', 'foto', 'raport', 'sertifikat', 'kip_kks'],
                'null'       => false,
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'file_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => false,
                'comment'    => 'path relatif dari storage/',
            ],
            'file_size' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'comment'  => 'dalam bytes',
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'null'       => false,
                'default'    => 'pending',
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'verified_by' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null'     => true,
                'comment'  => 'FK ke users.id (operator)',
            ],
            'verified_at' => [
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
        $this->forge->addKey('student_id');
        $this->forge->addKey('document_type');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'SET NULL');

        $this->forge->createTable('student_documents', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('student_documents', true);
    }
}
