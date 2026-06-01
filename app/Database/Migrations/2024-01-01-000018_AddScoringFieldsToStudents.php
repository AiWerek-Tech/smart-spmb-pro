<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScoringFieldsToStudents extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('students', [
            'score_distance' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'is_dapodik_ready',
            ],
            'score_achievement' => [
                'type'       => 'INT',
                'constraint' => 10,
                'null'       => false,
                'default'    => 0,
                'after'      => 'score_distance',
            ],
            'score_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 0.00,
                'after'      => 'score_achievement',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('students', ['score_distance', 'score_achievement', 'score_total']);
    }
}
