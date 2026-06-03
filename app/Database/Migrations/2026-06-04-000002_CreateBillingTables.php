<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBillingTables extends Migration
{
    public function up(): void
    {
        $this->createPaymentMethodsTable();
        $this->createInvoicesTable();
        $this->createInvoiceItemsTable();
        $this->createPaymentsTable();
        $this->createPaymentLogsTable();
    }

    public function down(): void
    {
        $this->forge->dropTable('payment_logs', true);
        $this->forge->dropTable('payments', true);
        $this->forge->dropTable('invoice_items', true);
        $this->forge->dropTable('invoices', true);
        $this->forge->dropTable('payment_methods', true);
    }

    private function createPaymentMethodsTable(): void
    {
        if ($this->db->tableExists('payment_methods')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 60],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'account_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'account_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'instructions' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'constraint' => 10, 'default' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->createTable('payment_methods', true);
    }

    private function createInvoicesTable(): void
    {
        if ($this->db->tableExists('invoices')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 40],
            'registration_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'student_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'academic_year' => ['type' => 'VARCHAR', 'constraint' => 9],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'unpaid'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'discount_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'paid_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'balance_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'due_at' => ['type' => 'DATETIME', 'null' => true],
            'issued_at' => ['type' => 'DATETIME', 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'cancelled_at' => ['type' => 'DATETIME', 'null' => true],
            'cancellation_reason' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('invoice_number');
        $this->forge->addKey(['registration_id', 'status']);
        $this->forge->addKey(['academic_year', 'status']);
        $this->forge->createTable('invoices', true);
    }

    private function createInvoiceItemsTable(): void
    {
        if ($this->db->tableExists('invoice_items')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'fee_type_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'item_code' => ['type' => 'VARCHAR', 'constraint' => 60],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'quantity' => ['type' => 'INT', 'constraint' => 10, 'default' => 1],
            'unit_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'total_amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'is_required' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['invoice_id', 'item_code']);
        $this->forge->createTable('invoice_items', true);
    }

    private function createPaymentsTable(): void
    {
        if ($this->db->tableExists('payments')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'payment_method_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'proof_file' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'paid_at' => ['type' => 'DATETIME', 'null' => true],
            'verified_at' => ['type' => 'DATETIME', 'null' => true],
            'verified_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'rejection_reason' => ['type' => 'TEXT', 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['invoice_id', 'status']);
        $this->forge->createTable('payments', true);
    }

    private function createPaymentLogsTable(): void
    {
        if ($this->db->tableExists('payment_logs')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'invoice_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'payment_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'action' => ['type' => 'VARCHAR', 'constraint' => 80],
            'old_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'new_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'actor_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'meta' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['invoice_id', 'created_at']);
        $this->forge->createTable('payment_logs', true);
    }
}
