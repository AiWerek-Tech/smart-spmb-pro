<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCoordinatesToStudentAddress extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('student_address')) {
            if (!$this->db->fieldExists('latitude', 'student_address')) {
                $this->forge->addColumn('student_address', [
                    'latitude' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '10,8',
                        'null'       => true,
                        'after'      => 'transport_mode',
                    ],
                    'longitude' => [
                        'type'       => 'DECIMAL',
                        'constraint' => '11,8',
                        'null'       => true,
                        'after'      => 'latitude',
                    ],
                ]);
            }
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('student_address')) {
            if ($this->db->fieldExists('latitude', 'student_address')) {
                $this->forge->dropColumn('student_address', 'latitude');
            }
            if ($this->db->fieldExists('longitude', 'student_address')) {
                $this->forge->dropColumn('student_address', 'longitude');
            }
        }
    }
}
