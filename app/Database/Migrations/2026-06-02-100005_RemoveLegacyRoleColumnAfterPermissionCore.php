<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class RemoveLegacyRoleColumnAfterPermissionCore extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        if ($this->db->fieldExists('role', 'users') && $this->db->fieldExists('legacy_role', 'users')) {
            try {
                $this->forge->dropColumn('users', 'legacy_role');
            } catch (DatabaseException) {
            }
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('users')) {
            return;
        }

        if (!$this->db->fieldExists('legacy_role', 'users')) {
            try {
                $this->forge->addColumn('users', [
                    'legacy_role' => [
                        'type'       => 'ENUM',
                        'constraint' => ['admin', 'operator', 'pendaftar'],
                        'default'    => 'pendaftar',
                        'null'       => false,
                        'after'      => 'role',
                    ],
                ]);
            } catch (DatabaseException) {
            }
        }
    }
}
