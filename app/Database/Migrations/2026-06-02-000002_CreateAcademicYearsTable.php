<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class CreateAcademicYearsTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('academic_years')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'year' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 9,
                    'unique'     => true,
                ],
                'label' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 120,
                    'null'       => true,
                ],
                'starts_at' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'ends_at' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'is_active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'is_archived' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->createTable('academic_years', true);
        }

        $activeYear = $this->db->table('settings')
            ->where('key', 'academic_year')
            ->get()
            ->getRowArray()['value'] ?? '2026/2027';

        $exists = $this->db->table('academic_years')->where('year', $activeYear)->get()->getRowArray();
        if (!$exists) {
            $this->db->table('academic_years')->insert([
                'year'        => $activeYear,
                'label'       => 'Tahun Pelajaran ' . $activeYear,
                'is_active'   => 1,
                'is_archived' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        $this->addAcademicYearColumn('teachers', 'academic_year');
        $this->addAcademicYearColumn('gallery', 'academic_year');
    }

    public function down()
    {
        $this->dropAcademicYearColumn('teachers');
        $this->dropAcademicYearColumn('gallery');

        $this->forge->dropTable('academic_years', true);
    }

    private function addAcademicYearColumn(string $table, string $column): void
    {
        if (!$this->db->tableExists($table)) {
            return;
        }

        if (!$this->columnExists($table, $column)) {
            try {
                $this->forge->addColumn($table, [
                    $column => [
                        'type'       => 'VARCHAR',
                        'constraint' => 9,
                        'null'       => true,
                        'after'      => 'id',
                    ],
                ]);
            } catch (DatabaseException) {
            }
        }

        if (!$this->columnExists($table, $column)) {
            try {
                $this->db->query(
                    'ALTER TABLE ' . $this->db->escapeIdentifiers($table) .
                    ' ADD COLUMN ' . $this->db->escapeIdentifiers($column) .
                    ' VARCHAR(9) NULL AFTER id'
                );
            } catch (DatabaseException) {
                return;
            }
        }

        $activeYear = $this->db->table('settings')
            ->where('key', 'academic_year')
            ->get()
            ->getRowArray()['value'] ?? '2026/2027';

        try {
            $this->db->table($table)
                ->groupStart()
                ->where($column, null)
                ->orWhere($column, '')
                ->groupEnd()
                ->update([$column => $activeYear]);
        } catch (DatabaseException) {
            return;
        }
    }

    private function dropAcademicYearColumn(string $table): void
    {
        try {
            if (!$this->db->tableExists($table)) {
                return;
            }

            if (!$this->columnExists($table, 'academic_year')) {
                return;
            }

            $this->forge->dropColumn($table, 'academic_year');
        } catch (DatabaseException) {
            return;
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            $row = $this->db->query(
                'SELECT COUNT(*) AS total
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?',
                [$table, $column]
            )->getRowArray();

            return (int) ($row['total'] ?? 0) > 0;
        } catch (DatabaseException) {
            return false;
        }
    }
}
