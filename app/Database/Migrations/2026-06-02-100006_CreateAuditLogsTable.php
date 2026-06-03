<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateAuditLogsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK ke users.id; SET NULL jika user dihapus',
            ],
            'user_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
                'comment'    => 'Snapshot nama user saat kejadian',
            ],
            'role_snapshot' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Daftar display_name role aktif user saat kejadian',
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'auth, users, rbac, pendaftaran, dokumen, seleksi, dapodik, laporan, sistem, keamanan',
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'login, logout, create_role, assign_role, approve_selection, dll.',
            ],
            'entity_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nama tabel atau tipe entitas: registrations, roles, users, dll.',
            ],
            'entity_id' => [
                'type'    => 'INT',
                'null'    => true,
                'comment' => 'ID entitas yang terpengaruh (nullable)',
            ],
            'old_data' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'State data sebelum perubahan',
            ],
            'new_data' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'State data setelah perubahan',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
                'comment'    => 'IPv4 atau IPv6 pengirim request',
            ],
            'user_agent' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'User-Agent string dari header HTTP',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'comment' => 'Tidak ada updated_at — log bersifat immutable',
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Index untuk query filtering dan sorting
        $this->forge->addKey('user_id');
        $this->forge->addKey('module');
        $this->forge->addKey('action');
        $this->forge->addKey('created_at');

        // Foreign key: user_id → users.id SET NULL (audit tetap ada jika user dihapus)
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'NO ACTION');

        $this->forge->createTable('audit_logs', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('audit_logs', true);
    }
}
