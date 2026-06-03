<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeeTypesTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('fee_types')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'null' => false, 'default' => 0],
            'billing_period' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => 'Satu Kali'],
            'is_required' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'show_on_homepage' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'requires_payment_before_form' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 0],
            'auto_invoice' => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => 'wallet'],
            'sort_order' => ['type' => 'INT', 'constraint' => 10, 'null' => false, 'default' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey(['is_active', 'show_on_homepage', 'sort_order']);
        $this->forge->createTable('fee_types', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('fee_types', true);
    }
}
