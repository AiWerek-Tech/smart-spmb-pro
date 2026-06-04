<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFormOverrideToStudents extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('students', [
            'form_override' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'religion_subgroup_id',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('students', 'form_override');
    }
}
