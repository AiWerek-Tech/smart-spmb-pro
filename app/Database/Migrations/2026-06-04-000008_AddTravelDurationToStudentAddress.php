<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTravelDurationToStudentAddress extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('student_address', [
            'travel_duration_minutes' => [
                'type'       => 'INT',
                'constraint' => 5,
                'null'       => true,
                'default'    => null,
                'after'      => 'distance_km',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('student_address', 'travel_duration_minutes');
    }
}
