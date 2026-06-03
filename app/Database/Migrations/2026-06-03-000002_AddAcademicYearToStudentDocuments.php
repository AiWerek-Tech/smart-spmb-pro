<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class AddAcademicYearToStudentDocuments extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('student_documents')) {
            return;
        }

        if (! $this->columnExists('student_documents', 'academic_year')) {
            $activeYear = $this->activeYear();

            try {
                $this->forge->addColumn('student_documents', [
                    'academic_year' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 9,
                        'null'       => false,
                        'default'    => $activeYear,
                        'after'      => 'student_id',
                    ],
                ]);
            } catch (DatabaseException) {
            }

            if (! $this->columnExists('student_documents', 'academic_year')) {
                $this->db->query(
                    'ALTER TABLE student_documents ADD COLUMN academic_year VARCHAR(9) NOT NULL DEFAULT ' .
                    $this->db->escape($activeYear) . ' AFTER student_id'
                );
            }

            $this->backfillAcademicYear($activeYear);
        }

        if (! $this->columnExists('student_documents', 'academic_year')) {
            return;
        }

        if (! $this->indexExists('student_documents', 'idx_student_documents_year_type')) {
            $this->db->query(
                'ALTER TABLE student_documents ADD INDEX idx_student_documents_year_type (student_id, academic_year, document_type)'
            );
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('student_documents')) {
            return;
        }

        if ($this->indexExists('student_documents', 'idx_student_documents_year_type')) {
            try {
                $this->db->query('ALTER TABLE student_documents DROP INDEX idx_student_documents_year_type');
            } catch (DatabaseException) {
            }
        }

        if ($this->columnExists('student_documents', 'academic_year')) {
            try {
                $this->forge->dropColumn('student_documents', 'academic_year');
            } catch (DatabaseException) {
            }
        }
    }

    private function activeYear(): string
    {
        if ($this->db->tableExists('academic_years')) {
            $row = $this->db->table('academic_years')
                ->select('year')
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if (! empty($row['year'])) {
                return (string) $row['year'];
            }
        }

        if ($this->db->tableExists('settings')) {
            $row = $this->db->table('settings')
                ->select('value')
                ->where('key', 'academic_year')
                ->get()
                ->getRowArray();

            if (! empty($row['value'])) {
                return (string) $row['value'];
            }
        }

        return '2026/2027';
    }

    private function backfillAcademicYear(string $fallbackYear): void
    {
        if (! $this->db->tableExists('registrations')) {
            return;
        }

        $rows = $this->db->table('registrations')
            ->select('student_id, academic_year')
            ->where('academic_year IS NOT NULL', null, false)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $studentYears = [];
        foreach ($rows as $row) {
            $studentId = (int) ($row['student_id'] ?? 0);
            if ($studentId > 0 && ! isset($studentYears[$studentId])) {
                $studentYears[$studentId] = (string) ($row['academic_year'] ?: $fallbackYear);
            }
        }

        foreach ($studentYears as $studentId => $academicYear) {
            $this->db->table('student_documents')
                ->where('student_id', $studentId)
                ->update(['academic_year' => $academicYear]);
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $rows = $this->db->query('SHOW INDEX FROM ' . $this->db->escapeIdentifiers($table))->getResultArray();

        foreach ($rows as $row) {
            if (($row['Key_name'] ?? '') === $indexName) {
                return true;
            }
        }

        return false;
    }

    private function columnExists(string $table, string $column): bool
    {
        $row = $this->db->query(
            'SELECT COUNT(*) AS total
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        )->getRowArray();

        return (int) ($row['total'] ?? 0) > 0;
    }
}
