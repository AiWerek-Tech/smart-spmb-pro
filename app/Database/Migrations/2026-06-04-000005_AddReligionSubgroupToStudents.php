<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReligionSubgroupToStudents extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('students')) {
            // Check if column already exists
            if (!$this->db->fieldExists('religion_subgroup_id', 'students')) {
                $this->forge->addColumn('students', [
                    'religion_subgroup_id' => [
                        'type'       => 'INT',
                        'constraint' => 10,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'religion',
                    ],
                ]);

                // Add foreign key
                $this->db->query('ALTER TABLE `students` ADD CONSTRAINT `fk_students_religion_subgroup` FOREIGN KEY (`religion_subgroup_id`) REFERENCES `religion_subgroups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
            }
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('students')) {
            if ($this->db->fieldExists('religion_subgroup_id', 'students')) {
                // Drop foreign key first with fallback to ignore if not exists
                try {
                    $this->db->query('ALTER TABLE `students` DROP FOREIGN KEY `fk_students_religion_subgroup`');
                } catch (\Throwable $e) {
                    // Ignore
                }
                $this->forge->dropColumn('students', 'religion_subgroup_id');
            }
        }
    }
}
