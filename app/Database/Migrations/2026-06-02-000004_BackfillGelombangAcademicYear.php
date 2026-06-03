<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class BackfillGelombangAcademicYear extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('gelombang') || !in_array('academic_year', $this->db->getFieldNames('gelombang'), true)) {
            return;
        }

        $activeYear = $this->db->table('settings')
            ->where('key', 'academic_year')
            ->get()
            ->getRowArray()['value'] ?? '2026/2027';

        try {
            $this->db->query(
                'UPDATE gelombang SET academic_year = ? WHERE academic_year IS NULL OR academic_year = ?',
                [$activeYear, '']
            );
        } catch (DatabaseException) {
            return;
        }
    }

    public function down()
    {
    }
}
