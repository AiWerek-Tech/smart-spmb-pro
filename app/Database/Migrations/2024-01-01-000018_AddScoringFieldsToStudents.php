<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class AddScoringFieldsToStudents extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('students')) {
            return;
        }

        $fields = [];

        if (! $this->columnExists('students', 'score_distance')) {
            $fields['score_distance'] = [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'is_dapodik_ready',
            ];
        }

        if (! $this->columnExists('students', 'score_achievement')) {
            $fields['score_achievement'] = [
                'type'       => 'INT',
                'constraint' => 10,
                'null'       => false,
                'default'    => 0,
                'after'      => 'score_distance',
            ];
        }

        if (! $this->columnExists('students', 'score_total')) {
            $fields['score_total'] = [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 0.00,
                'after'      => 'score_achievement',
            ];
        }

        if ($fields !== []) {
            try {
                $this->forge->addColumn('students', $fields);
            } catch (DatabaseException) {
            }
        }

        if (! $this->columnExists('students', 'score_distance')) {
            $this->db->query('ALTER TABLE students ADD COLUMN score_distance DECIMAL(6,2) NULL DEFAULT NULL AFTER is_dapodik_ready');
        }

        if (! $this->columnExists('students', 'score_achievement')) {
            $this->db->query('ALTER TABLE students ADD COLUMN score_achievement INT(10) NOT NULL DEFAULT 0 AFTER score_distance');
        }

        if (! $this->columnExists('students', 'score_total')) {
            $this->db->query('ALTER TABLE students ADD COLUMN score_total DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER score_achievement');
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('students')) {
            return;
        }

        foreach (['score_distance', 'score_achievement', 'score_total'] as $column) {
            if ($this->columnExists('students', $column)) {
                try {
                    $this->forge->dropColumn('students', $column);
                } catch (DatabaseException) {
                }
            }
        }
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
