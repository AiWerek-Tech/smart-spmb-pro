<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Migration;

class CreateDocumentRequirementsTable extends Migration
{
    private array $defaultDocuments = [
        ['document_type' => 'kk', 'label' => 'Kartu Keluarga (KK)', 'is_required' => 1, 'allowed_extensions' => 'jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'sort_order' => 10],
        ['document_type' => 'akta', 'label' => 'Akta Kelahiran', 'is_required' => 1, 'allowed_extensions' => 'jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'sort_order' => 20],
        ['document_type' => 'foto', 'label' => 'Pas Foto 3x4 Calon Siswa', 'is_required' => 1, 'allowed_extensions' => 'jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'sort_order' => 30],
        ['document_type' => 'raport', 'label' => 'Raport Terakhir', 'is_required' => 0, 'allowed_extensions' => 'pdf,jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'sort_order' => 40],
        ['document_type' => 'sertifikat', 'label' => 'Sertifikat Prestasi', 'is_required' => 0, 'allowed_extensions' => 'pdf,jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'sort_order' => 50],
        ['document_type' => 'kip_kks', 'label' => 'KIP / KKS Pendukung', 'is_required' => 0, 'allowed_extensions' => 'pdf,jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'sort_order' => 60],
    ];

    public function up(): void
    {
        $this->makeStudentDocumentTypeConfigurable();
        $this->createTable();
        $this->seedDefaults();
        $this->seedPermission();
    }

    public function down(): void
    {
        $this->forge->dropTable('document_requirements', true);

        if ($this->db->tableExists('student_documents')) {
            try {
                $this->db->query("DELETE FROM student_documents WHERE document_type NOT IN ('kk','akta','foto','raport','sertifikat','kip_kks')");
                $this->forge->modifyColumn('student_documents', [
                    'document_type' => [
                        'type'       => 'ENUM',
                        'constraint' => ['kk', 'akta', 'foto', 'raport', 'sertifikat', 'kip_kks'],
                        'null'       => false,
                    ],
                ]);
            } catch (DatabaseException) {
            }
        }
    }

    private function makeStudentDocumentTypeConfigurable(): void
    {
        if (!$this->db->tableExists('student_documents')) {
            return;
        }

        try {
            $this->forge->modifyColumn('student_documents', [
                'document_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 60,
                    'null'       => false,
                ],
            ]);
        } catch (DatabaseException) {
        }
    }

    private function createTable(): void
    {
        if ($this->db->tableExists('document_requirements')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'academic_year' => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => false],
            'jalur_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'document_type' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'label' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => false],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_required' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'allowed_extensions' => ['type' => 'VARCHAR', 'constraint' => 120, 'default' => 'jpg,jpeg,png,webp'],
            'max_size_kb' => ['type' => 'INT', 'constraint' => 10, 'default' => 2048],
            'requires_verification' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'constraint' => 10, 'default' => 100],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['academic_year', 'jalur_id', 'document_type']);
        $this->forge->addKey(['academic_year', 'jalur_id', 'is_active']);
        $this->forge->createTable('document_requirements', true);
    }

    private function seedDefaults(): void
    {
        if (!$this->db->tableExists('document_requirements')) {
            return;
        }

        $activeYear = $this->db->table('settings')
            ->where('key', 'academic_year')
            ->get()
            ->getRowArray()['value'] ?? '2026/2027';

        foreach ($this->defaultDocuments as $document) {
            $exists = $this->db->table('document_requirements')
                ->where('academic_year', $activeYear)
                ->where('jalur_id', null)
                ->where('document_type', $document['document_type'])
                ->countAllResults();

            if ($exists === 0) {
                $this->db->table('document_requirements')->insert($document + [
                    'academic_year' => $activeYear,
                    'jalur_id'      => null,
                    'description'   => null,
                    'is_active'     => 1,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    private function seedPermission(): void
    {
        if (!$this->db->tableExists('permissions') || !$this->db->tableExists('roles') || !$this->db->tableExists('role_permissions')) {
            return;
        }

        $key = 'document_requirements.manage';
        $exists = $this->db->table('permissions')->where('permission_key', $key)->countAllResults();
        if ($exists === 0) {
            $this->db->table('permissions')->insert([
                'permission_key' => $key,
                'name'           => 'Kelola Syarat Dokumen',
                'group_name'     => 'Data SPMB',
                'description'    => 'Mengatur dokumen wajib/opsional per jalur dan tahun pelajaran.',
                'is_active'      => 1,
                'sort_order'     => 75,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        $admin = $this->db->table('roles')->where('slug', 'admin')->get()->getRowArray();
        if ($admin) {
            $grantExists = $this->db->table('role_permissions')
                ->where('role_id', $admin['id'])
                ->where('permission_key', $key)
                ->countAllResults();

            if ($grantExists === 0) {
                $this->db->table('role_permissions')->insert([
                    'role_id'        => $admin['id'],
                    'permission_key' => $key,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
