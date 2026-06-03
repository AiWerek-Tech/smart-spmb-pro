<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $allPermissions = array_column(
            $this->db->table('permissions')->select('permission_key')->get()->getResultArray(),
            'permission_key'
        );

        $roles = [
            'super_admin' => [
                'name' => 'Super Admin',
                'base_role' => 'admin',
                'description' => 'Akses penuh ke seluruh sistem.',
                'permissions' => $allPermissions,
            ],
            'kepala_sekolah' => [
                'name' => 'Kepala Sekolah',
                'base_role' => 'admin',
                'description' => 'Menyetujui hasil seleksi dan memantau laporan.',
                'permissions' => ['view_reports', 'export_reports', 'print_reports', 'approve_selection', 'publish_selection', 'view_registrants', 'view_selection', 'view_audit_logs'],
            ],
            'ketua_panitia' => [
                'name' => 'Ketua Panitia',
                'base_role' => 'admin',
                'description' => 'Memimpin proses SPMB dan memantau panitia.',
                'permissions' => ['view_registrants', 'view_documents', 'verify_documents', 'view_selection', 'submit_selection_to_chairman', 'view_reports', 'print_reports', 'manage_admission_paths', 'view_audit_logs'],
            ],
            'sekretaris_panitia' => [
                'name' => 'Sekretaris Panitia',
                'base_role' => 'operator',
                'description' => 'Mengelola administrasi dan dokumentasi pendaftaran.',
                'permissions' => ['view_registrants', 'edit_registrant', 'print_registration_card', 'view_documents', 'download_documents', 'export_reports', 'print_reports'],
            ],
            'petugas_pendaftaran' => [
                'name' => 'Petugas Pendaftaran',
                'base_role' => 'operator',
                'description' => 'Membantu proses pendaftaran calon peserta didik.',
                'permissions' => ['view_registrants', 'create_registrant', 'edit_registrant', 'view_documents', 'download_documents', 'print_registration_card'],
            ],
            'verifikator_dokumen' => [
                'name' => 'Verifikator Dokumen',
                'base_role' => 'operator',
                'description' => 'Memverifikasi kelengkapan dan validitas dokumen.',
                'permissions' => ['view_registrants', 'view_documents', 'verify_documents', 'reject_documents', 'request_document_revision', 'download_documents'],
            ],
            'verifikator_jalur' => [
                'name' => 'Verifikator Jalur',
                'base_role' => 'operator',
                'description' => 'Memverifikasi persyaratan khusus jalur pendaftaran.',
                'permissions' => ['view_registrants', 'view_documents', 'verify_domisili', 'verify_afirmasi', 'verify_prestasi', 'verify_mutasi', 'score_achievement'],
            ],
            'tim_seleksi' => [
                'name' => 'Tim Seleksi',
                'base_role' => 'operator',
                'description' => 'Menghitung ranking dan menetapkan hasil seleksi sementara.',
                'permissions' => ['view_registrants', 'view_selection', 'calculate_ranking', 'simulate_selection', 'set_selection_status', 'submit_selection_to_chairman'],
            ],
            'operator_dapodik' => [
                'name' => 'Operator Dapodik',
                'base_role' => 'operator',
                'description' => 'Menyiapkan dan mengekspor data Dapodik.',
                'permissions' => ['view_registrants', 'view_dapodik_checklist', 'validate_dapodik_data', 'export_dapodik_excel', 'print_fpd', 'mark_dapodik_ready'],
            ],
            'bendahara' => [
                'name' => 'Bendahara',
                'base_role' => 'operator',
                'description' => 'Mengelola invoice, pembayaran, verifikasi manual, dan laporan keuangan SPMB.',
                'permissions' => ['payments.view', 'payments.verify', 'payments.cancel', 'payments.export', 'view_registrants', 'view_reports', 'export_reports'],
            ],
            'tim_publikasi' => [
                'name' => 'Tim Publikasi',
                'base_role' => 'operator',
                'description' => 'Mengelola konten publik dan pengumuman.',
                'permissions' => ['manage_public_homepage', 'manage_announcements', 'manage_news', 'manage_faq', 'publish_content'],
            ],
            'helpdesk' => [
                'name' => 'Helpdesk',
                'base_role' => 'operator',
                'description' => 'Melayani pertanyaan dan bantuan pendaftar.',
                'permissions' => ['view_registrants', 'view_helpdesk', 'reply_helpdesk', 'manage_support_templates'],
            ],
            'pendaftar' => [
                'name' => 'Pendaftar',
                'base_role' => 'pendaftar',
                'description' => 'Calon peserta didik yang sedang mendaftar.',
                'permissions' => ['submit_registration', 'print_registration_card', 'view_documents'],
            ],
        ];

        foreach ($roles as $slug => $role) {
            $row = $this->db->table('roles')->where('slug', $slug)->get()->getRowArray();

            if (!$row) {
                $this->db->table('roles')->insert([
                    'slug'        => $slug,
                    'name'        => $role['name'],
                    'base_role'   => $role['base_role'],
                    'description' => $role['description'],
                    'is_system'   => 1,
                    'is_active'   => 1,
                    'sort_order'  => 200,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $roleId = (int) $this->db->insertID();
            } else {
                $roleId = (int) $row['id'];
                $this->db->table('roles')->where('id', $roleId)->update([
                    'name'        => $role['name'],
                    'base_role'   => $role['base_role'],
                    'description' => $role['description'],
                    'is_system'   => 1,
                    'updated_at'  => $now,
                ]);
            }

            $this->db->table('role_permissions')->where('role_id', $roleId)->delete();
            foreach (array_intersect(array_unique($role['permissions']), $allPermissions) as $permissionKey) {
                $this->db->table('role_permissions')->insert([
                    'role_id'        => $roleId,
                    'permission_key' => $permissionKey,
                    'created_at'     => $now,
                ]);
            }
        }
    }
}
