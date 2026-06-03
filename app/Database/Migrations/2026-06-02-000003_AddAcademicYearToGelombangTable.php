<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class AddAcademicYearToGelombangTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('gelombang')) {
            return;
        }

        if (!$this->hasAcademicYearColumn()) {
            $this->forge->addColumn('gelombang', [
                'academic_year' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 9,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        $activeYear = $this->db->table('settings')
            ->where('key', 'academic_year')
            ->get()
            ->getRowArray()['value'] ?? '2026/2027';

        if ($this->hasAcademicYearColumn()) {
            try {
                $this->db->table('gelombang')
                    ->groupStart()
                    ->where('academic_year', null)
                    ->orWhere('academic_year', '')
                    ->groupEnd()
                    ->update(['academic_year' => $activeYear]);
            } catch (DatabaseException) {
                return;
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('gelombang') && $this->hasAcademicYearColumn()) {
            try {
                $this->forge->dropColumn('gelombang', 'academic_year');
            } catch (DatabaseException) {
            }
        }
    }

    private function hasAcademicYearColumn(): bool
    {
        if (!$this->db->tableExists('gelombang')) {
            return false;
        }

        return $this->db
            ->query("SHOW COLUMNS FROM `gelombang` LIKE 'academic_year'")
            ->getNumRows() > 0;
    }
}
