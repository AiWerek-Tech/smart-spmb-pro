<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateStudentAchievementsTable extends Migration
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
            'achievement_type' => [
                'type'       => 'ENUM',
                'constraint' => ['akademik', 'non-akademik'],
                'null'       => false,
            ],
            'competition_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
            ],
            'level' => [
                'type'       => 'ENUM',
                'constraint' => ['kecamatan', 'kabupaten', 'provinsi', 'nasional', 'internasional'],
                'null'       => false,
            ],
            'rank' => [
                'type'       => 'ENUM',
                'constraint' => ['juara 1', 'juara 2', 'juara 3', 'harapan'],
                'null'       => false,
            ],
            'year' => [
                'type' => 'YEAR',
                'null' => false,
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
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('student_achievements', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('student_achievements', true);
    }
}
