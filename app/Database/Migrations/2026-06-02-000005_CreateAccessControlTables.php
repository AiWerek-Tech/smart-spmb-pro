<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class CreateAccessControlTables extends Migration
{
    private array $defaultRoles = [
        ['slug' => 'admin', 'name' => 'Administrator', 'base_role' => 'admin', 'description' => 'Akses penuh untuk seluruh konfigurasi dan data sistem.', 'is_system' => 1, 'sort_order' => 10],
        ['slug' => 'operator', 'name' => 'Operator', 'base_role' => 'operator', 'description' => 'Mengelola data pendaftar, verifikasi, Dapodik, dan export operasional.', 'is_system' => 1, 'sort_order' => 20],
        ['slug' => 'pendaftar', 'name' => 'Pendaftar', 'base_role' => 'pendaftar', 'description' => 'Akses calon siswa untuk pendaftaran, dokumen, dan hasil seleksi.', 'is_system' => 1, 'sort_order' => 30],
    ];

    private array $defaultPermissions = [
        ['key' => 'admin.dashboard.view', 'name' => 'Lihat Dashboard Admin', 'group' => 'Dashboard', 'description' => 'Melihat ringkasan dan aktivitas admin.', 'sort_order' => 10],
        ['key' => 'users.manage', 'name' => 'Kelola Pengguna', 'group' => 'Sistem', 'description' => 'Menambah, mengubah, menonaktifkan, dan menghapus pengguna.', 'sort_order' => 20],
        ['key' => 'access.manage', 'name' => 'Kelola Mode & Hak Akses', 'group' => 'Sistem', 'description' => 'Mengatur mode sekolah, role, dan permission.', 'sort_order' => 30],
        ['key' => 'settings.manage', 'name' => 'Kelola Konfigurasi', 'group' => 'Sistem', 'description' => 'Mengatur profil sekolah, kontak, tema, dan konfigurasi umum.', 'sort_order' => 40],
        ['key' => 'academic_years.manage', 'name' => 'Kelola Tahun Pelajaran', 'group' => 'Data SPMB', 'description' => 'Mengatur tahun aktif dan arsip tahun pelajaran.', 'sort_order' => 50],
        ['key' => 'jalur.manage', 'name' => 'Kelola Jalur & Kuota', 'group' => 'Data SPMB', 'description' => 'Mengatur jalur pendaftaran dan kuota.', 'sort_order' => 60],
        ['key' => 'gelombang.manage', 'name' => 'Kelola Gelombang', 'group' => 'Data SPMB', 'description' => 'Mengatur jadwal dan gelombang pendaftaran.', 'sort_order' => 70],
        ['key' => 'selection.manage', 'name' => 'Kelola Hasil Seleksi', 'group' => 'Seleksi', 'description' => 'Menghitung ranking dan menentukan hasil seleksi.', 'sort_order' => 80],
        ['key' => 'public_content.manage', 'name' => 'Kelola Konten Publik', 'group' => 'Konten Publik', 'description' => 'Mengatur profil, galeri, banner, pengumuman, testimoni, statistik, dan FAQ.', 'sort_order' => 90],
        ['key' => 'backup.manage', 'name' => 'Backup & Restore', 'group' => 'Sistem', 'description' => 'Membuat dan memulihkan backup database.', 'sort_order' => 100],
        ['key' => 'operator.dashboard.view', 'name' => 'Lihat Dashboard Operator', 'group' => 'Operator', 'description' => 'Melihat ringkasan pekerjaan operator.', 'sort_order' => 110],
        ['key' => 'registrants.view', 'name' => 'Lihat Pendaftar', 'group' => 'Operator', 'description' => 'Melihat daftar dan detail pendaftar.', 'sort_order' => 120],
        ['key' => 'registrants.edit', 'name' => 'Koreksi Data Pendaftar', 'group' => 'Operator', 'description' => 'Mengubah data pendaftar untuk koreksi operasional.', 'sort_order' => 130],
        ['key' => 'documents.verify', 'name' => 'Verifikasi Dokumen', 'group' => 'Operator', 'description' => 'Memeriksa, menerima, atau menolak dokumen pendaftar.', 'sort_order' => 140],
        ['key' => 'dapodik.view', 'name' => 'Validasi Dapodik', 'group' => 'Operator', 'description' => 'Melihat status kelengkapan data Dapodik.', 'sort_order' => 150],
        ['key' => 'exports.download', 'name' => 'Export Data', 'group' => 'Operator', 'description' => 'Mengunduh export Excel dan PDF.', 'sort_order' => 160],
        ['key' => 'pendaftar.dashboard.view', 'name' => 'Lihat Dashboard Pendaftar', 'group' => 'Pendaftar', 'description' => 'Melihat ringkasan status pendaftaran pribadi.', 'sort_order' => 170],
        ['key' => 'registration.manage', 'name' => 'Isi Formulir Pendaftaran', 'group' => 'Pendaftar', 'description' => 'Mengisi dan mengirim formulir pendaftaran.', 'sort_order' => 180],
        ['key' => 'documents.manage_own', 'name' => 'Kelola Dokumen Sendiri', 'group' => 'Pendaftar', 'description' => 'Mengunggah dan menghapus dokumen sendiri.', 'sort_order' => 190],
        ['key' => 'results.view_own', 'name' => 'Lihat Hasil Seleksi Sendiri', 'group' => 'Pendaftar', 'description' => 'Melihat hasil seleksi pribadi.', 'sort_order' => 200],
    ];

    public function up(): void
    {
        $this->createRolesTable();
        $this->createPermissionsTable();
        $this->createRolePermissionsTable();
        $this->makeUserRoleConfigurable();
        $this->seedDefaults();
        $this->seedOperationalMode();
    }

    public function down(): void
    {
        $this->forge->dropTable('role_permissions', true);
        $this->forge->dropTable('permissions', true);
        $this->forge->dropTable('roles', true);

        if ($this->db->tableExists('users')) {
            try {
                $this->db->query("UPDATE users SET role = 'operator' WHERE role NOT IN ('admin', 'operator', 'pendaftar')");
                $this->forge->modifyColumn('users', [
                    'role' => [
                        'type'       => 'ENUM',
                        'constraint' => ['admin', 'operator', 'pendaftar'],
                        'null'       => false,
                        'default'    => 'pendaftar',
                    ],
                ]);
            } catch (DatabaseException) {
            }
        }
    }

    private function createRolesTable(): void
    {
        if ($this->db->tableExists('roles')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => false],
            'base_role' => ['type' => 'ENUM', 'constraint' => ['admin', 'operator', 'pendaftar'], 'null' => false, 'default' => 'operator'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_system' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'constraint' => 10, 'default' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('base_role');
        $this->forge->createTable('roles', true);
    }

    private function createPermissionsTable(): void
    {
        if ($this->db->tableExists('permissions')) {
            return;
        }

        $this->forge->addField([
            'permission_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'group_name' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false, 'default' => 'Umum'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'constraint' => 10, 'default' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('permission_key', true);
        $this->forge->addKey('group_name');
        $this->forge->createTable('permissions', true);
    }

    private function createRolePermissionsTable(): void
    {
        if ($this->db->tableExists('role_permissions')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'role_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'permission_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['role_id', 'permission_key']);
        $this->forge->addKey('role_id');
        $this->forge->createTable('role_permissions', true);
    }

    private function makeUserRoleConfigurable(): void
    {
        if (!$this->db->tableExists('users')) {
            return;
        }

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

    private function seedDefaults(): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($this->defaultRoles as $role) {
            $exists = $this->db->table('roles')->where('slug', $role['slug'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('roles')->insert($role + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        foreach ($this->defaultPermissions as $permission) {
            $exists = $this->db->table('permissions')->where('permission_key', $permission['key'])->countAllResults();
            if ($exists === 0) {
                $this->db->table('permissions')->insert([
                    'permission_key' => $permission['key'],
                    'name'           => $permission['name'],
                    'group_name'     => $permission['group'],
                    'description'    => $permission['description'],
                    'sort_order'     => $permission['sort_order'],
                    'is_active'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            }
        }

        $this->grantDefaults('admin', array_column($this->defaultPermissions, 'key'));
        $this->grantDefaults('operator', [
            'operator.dashboard.view',
            'registrants.view',
            'registrants.edit',
            'documents.verify',
            'dapodik.view',
            'exports.download',
        ]);
        $this->grantDefaults('pendaftar', [
            'pendaftar.dashboard.view',
            'registration.manage',
            'documents.manage_own',
            'results.view_own',
        ]);
    }

    private function grantDefaults(string $roleSlug, array $permissionKeys): void
    {
        $role = $this->db->table('roles')->where('slug', $roleSlug)->get()->getRowArray();
        if (!$role) {
            return;
        }

        foreach ($permissionKeys as $permissionKey) {
            $exists = $this->db->table('role_permissions')
                ->where('role_id', $role['id'])
                ->where('permission_key', $permissionKey)
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('role_permissions')->insert([
                    'role_id'        => $role['id'],
                    'permission_key' => $permissionKey,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function seedOperationalMode(): void
    {
        if (!$this->db->tableExists('settings')) {
            return;
        }

        $exists = $this->db->table('settings')->where('key', 'school_operational_mode')->countAllResults();
        if ($exists === 0) {
            $this->db->table('settings')->insert([
                'key'        => 'school_operational_mode',
                'value'      => 'small',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
