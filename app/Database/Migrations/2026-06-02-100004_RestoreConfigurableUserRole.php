<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class RestoreConfigurableUserRole extends Migration
{
    public function up(): void
    {
        if (!$this->db->tableExists('users')) {
            return;
        }

        $fields = $this->db->getFieldNames('users');
        if (!in_array('role', $fields, true) && in_array('legacy_role', $fields, true)) {
            $this->forge->addColumn('users', [
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                    'default'    => 'pendaftar',
                    'after'      => 'password',
                ],
            ]);

            $this->db->query('UPDATE users SET role = legacy_role');
        }

        if (in_array('role', $this->db->getFieldNames('users'), true)) {
            try {
                $this->forge->modifyColumn('users', [
                    'role' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 50,
                        'null'       => false,
                        'default'    => 'pendaftar',
                    ],
                ]);
            } catch (DatabaseException) {
            }
        }
    }

    public function down(): void
    {
    }
}
