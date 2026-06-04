<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeUserEmailNullable extends Migration
{
    public function up(): void
    {
        $fields = [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }

    public function down(): void
    {
        $fields = [
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
        ];
        $this->forge->modifyColumn('users', $fields);
    }
}
