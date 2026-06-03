<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk 55 permission atomik di 10 modul.
 *
 * Menggunakan updateOrInsert agar idempoten — aman dijalankan
 * berkali-kali tanpa menduplikasi data.
 *
 * Validates: Requirements 2.1, 2.15
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        /**
         * Daftar 55 permission dalam format:
         *   [name, display_name, module, description]
         */
        $permissions = [
            // ─────────────────────────────────────────
            // Sistem (6)
            // ─────────────────────────────────────────
            ['name' => 'manage_system',           'display_name' => 'Kelola Sistem',                'module' => 'Sistem',              'description' => 'Akses penuh ke pengaturan dan konfigurasi sistem'],
            ['name' => 'manage_users',             'display_name' => 'Kelola User',                  'module' => 'Sistem',              'description' => 'Membuat, mengedit, dan menonaktifkan akun pengguna'],
            ['name' => 'manage_roles',             'display_name' => 'Kelola Role & Permission',     'module' => 'Sistem',              'description' => 'Membuat, mengedit, dan menghapus role serta mengalokasikan permission'],
            ['name' => 'manage_admission_paths',   'display_name' => 'Kelola Jalur Pendaftaran',     'module' => 'Sistem',              'description' => 'Mengkonfigurasi jalur dan kuota pendaftaran'],
            ['name' => 'manage_academic_years',    'display_name' => 'Kelola Tahun Ajaran',          'module' => 'Sistem',              'description' => 'Membuka dan menutup periode tahun ajaran'],
            ['name' => 'manage_settings',          'display_name' => 'Kelola Pengaturan',            'module' => 'Sistem',              'description' => 'Mengubah pengaturan umum aplikasi'],

            // ─────────────────────────────────────────
            // Website Publik (5)
            // ─────────────────────────────────────────
            ['name' => 'manage_public_homepage',   'display_name' => 'Kelola Halaman Utama Publik',  'module' => 'Website Publik',      'description' => 'Mengedit konten halaman utama website publik sekolah'],
            ['name' => 'manage_announcements',     'display_name' => 'Kelola Pengumuman',            'module' => 'Website Publik',      'description' => 'Membuat dan mengedit pengumuman yang tampil di website'],
            ['name' => 'manage_news',              'display_name' => 'Kelola Berita',                'module' => 'Website Publik',      'description' => 'Membuat dan mengedit artikel berita sekolah'],
            ['name' => 'manage_faq',               'display_name' => 'Kelola FAQ',                   'module' => 'Website Publik',      'description' => 'Mengelola daftar pertanyaan dan jawaban yang sering diajukan'],
            ['name' => 'publish_content',          'display_name' => 'Publikasikan Konten',          'module' => 'Website Publik',      'description' => 'Mempublikasikan atau mencabut konten yang telah dibuat'],

            // ─────────────────────────────────────────
            // Pendaftaran (6)
            // ─────────────────────────────────────────
            ['name' => 'submit_registration',      'display_name' => 'Submit Pendaftaran (Pendaftar)', 'module' => 'Pendaftaran',       'description' => 'Mengisi dan mengirimkan formulir pendaftaran'],
            ['name' => 'view_registrants',         'display_name' => 'Lihat Daftar Pendaftar',       'module' => 'Pendaftaran',         'description' => 'Melihat seluruh daftar pendaftar dan detailnya'],
            ['name' => 'create_registrant',        'display_name' => 'Buat Data Pendaftar',          'module' => 'Pendaftaran',         'description' => 'Membuat data pendaftar baru atas nama calon peserta didik'],
            ['name' => 'edit_registrant',          'display_name' => 'Edit Data Pendaftar',          'module' => 'Pendaftaran',         'description' => 'Mengubah data pendaftar yang sudah ada'],
            ['name' => 'delete_registrant',        'display_name' => 'Hapus Data Pendaftar',         'module' => 'Pendaftaran',         'description' => 'Menghapus data pendaftar secara permanen'],
            ['name' => 'print_registration_card',  'display_name' => 'Cetak Kartu Pendaftaran',      'module' => 'Pendaftaran',         'description' => 'Mencetak kartu tanda peserta pendaftaran'],

            // ─────────────────────────────────────────
            // Dokumen (5)
            // ─────────────────────────────────────────
            ['name' => 'view_documents',           'display_name' => 'Lihat Dokumen',                'module' => 'Dokumen',             'description' => 'Melihat dokumen yang diunggah oleh pendaftar'],
            ['name' => 'download_documents',       'display_name' => 'Unduh Dokumen',                'module' => 'Dokumen',             'description' => 'Mengunduh dokumen pendaftar ke perangkat lokal'],
            ['name' => 'verify_documents',         'display_name' => 'Verifikasi Dokumen',           'module' => 'Dokumen',             'description' => 'Menandai dokumen pendaftar sebagai terverifikasi'],
            ['name' => 'reject_documents',         'display_name' => 'Tolak Dokumen',                'module' => 'Dokumen',             'description' => 'Menolak dokumen pendaftar yang tidak memenuhi syarat'],
            ['name' => 'request_document_revision','display_name' => 'Minta Revisi Dokumen',         'module' => 'Dokumen',             'description' => 'Meminta pendaftar untuk memperbaiki atau mengunggah ulang dokumen'],

            // ─────────────────────────────────────────
            // Jalur Pendaftaran (6)
            // ─────────────────────────────────────────
            ['name' => 'verify_domisili',          'display_name' => 'Verifikasi Jalur Domisili',    'module' => 'Jalur Pendaftaran',   'description' => 'Memverifikasi persyaratan domisili pendaftar jalur zonasi'],
            ['name' => 'verify_afirmasi',          'display_name' => 'Verifikasi Jalur Afirmasi',    'module' => 'Jalur Pendaftaran',   'description' => 'Memverifikasi kelayakan pendaftar jalur afirmasi'],
            ['name' => 'verify_prestasi',          'display_name' => 'Verifikasi Jalur Prestasi',    'module' => 'Jalur Pendaftaran',   'description' => 'Memverifikasi bukti prestasi pendaftar jalur prestasi'],
            ['name' => 'verify_mutasi',            'display_name' => 'Verifikasi Jalur Mutasi',      'module' => 'Jalur Pendaftaran',   'description' => 'Memverifikasi bukti mutasi/kepindahan orang tua pendaftar'],
            ['name' => 'score_achievement',        'display_name' => 'Input Nilai Prestasi',         'module' => 'Jalur Pendaftaran',   'description' => 'Menginput nilai atau poin prestasi untuk seleksi jalur prestasi'],
            ['name' => 'manage_jalur_config',      'display_name' => 'Konfigurasi Jalur',            'module' => 'Jalur Pendaftaran',   'description' => 'Mengkonfigurasi parameter dan aturan masing-masing jalur pendaftaran'],

            // ─────────────────────────────────────────
            // Seleksi (7)
            // ─────────────────────────────────────────
            ['name' => 'view_selection',                'display_name' => 'Lihat Data Seleksi',                   'module' => 'Seleksi', 'description' => 'Melihat data dan hasil seleksi pendaftar'],
            ['name' => 'calculate_ranking',             'display_name' => 'Hitung Ranking',                       'module' => 'Seleksi', 'description' => 'Menjalankan proses perhitungan peringkat pendaftar'],
            ['name' => 'simulate_selection',            'display_name' => 'Simulasi Seleksi',                     'module' => 'Seleksi', 'description' => 'Menjalankan simulasi hasil seleksi tanpa mengubah data resmi'],
            ['name' => 'set_selection_status',          'display_name' => 'Tetapkan Status Seleksi Sementara',    'module' => 'Seleksi', 'description' => 'Menetapkan status seleksi sementara (diterima/cadangan/tidak diterima)'],
            ['name' => 'submit_selection_to_chairman',  'display_name' => 'Kirim Hasil ke Kepala Sekolah',        'module' => 'Seleksi', 'description' => 'Mengirimkan hasil seleksi sementara untuk disetujui kepala sekolah'],
            ['name' => 'approve_selection',             'display_name' => 'Approve Hasil Seleksi Final',          'module' => 'Seleksi', 'description' => '⚠️ Menyetujui hasil seleksi secara final — hanya untuk Super Admin dan Kepala Sekolah'],
            ['name' => 'publish_selection',             'display_name' => 'Publikasikan Hasil Seleksi',           'module' => 'Seleksi', 'description' => '⚠️ Mempublikasikan hasil seleksi final kepada publik — hanya untuk Super Admin dan Kepala Sekolah'],

            // ─────────────────────────────────────────
            // Dapodik (5)
            // ─────────────────────────────────────────
            ['name' => 'view_dapodik_checklist',   'display_name' => 'Lihat Checklist Dapodik',      'module' => 'Dapodik',             'description' => 'Melihat daftar checklist kelengkapan data dapodik'],
            ['name' => 'validate_dapodik_data',    'display_name' => 'Validasi Data Dapodik',        'module' => 'Dapodik',             'description' => 'Memvalidasi dan memastikan data peserta siap untuk dapodik'],
            ['name' => 'export_dapodik_excel',     'display_name' => 'Ekspor Excel Dapodik',         'module' => 'Dapodik',             'description' => 'Mengekspor data peserta didik baru ke format Excel untuk dapodik'],
            ['name' => 'print_fpd',                'display_name' => 'Cetak FPD',                   'module' => 'Dapodik',             'description' => 'Mencetak Formulir Peserta Didik (FPD) untuk keperluan dapodik'],
            ['name' => 'mark_dapodik_ready',       'display_name' => 'Tandai Siap Dapodik',         'module' => 'Dapodik',             'description' => 'Menandai bahwa data peserta telah siap dientry ke sistem dapodik'],

            // ─────────────────────────────────────────
            // Laporan (3)
            // ─────────────────────────────────────────
            ['name' => 'view_reports',             'display_name' => 'Lihat Laporan',                'module' => 'Laporan',             'description' => 'Melihat berbagai laporan statistik dan rekap pendaftaran'],
            ['name' => 'export_reports',           'display_name' => 'Ekspor Laporan',               'module' => 'Laporan',             'description' => 'Mengekspor laporan ke format Excel atau PDF'],
            ['name' => 'print_reports',            'display_name' => 'Cetak Laporan',                'module' => 'Laporan',             'description' => 'Mencetak laporan secara langsung dari aplikasi'],

            // Pembayaran (4)
            ['name' => 'payments.view',             'display_name' => 'Lihat Pembayaran',             'module' => 'Pembayaran',          'description' => 'Melihat invoice dan pembayaran pendaftar'],
            ['name' => 'payments.verify',           'display_name' => 'Verifikasi Pembayaran',        'module' => 'Pembayaran',          'description' => 'Mencatat dan memverifikasi pembayaran manual'],
            ['name' => 'payments.cancel',           'display_name' => 'Batalkan Invoice',             'module' => 'Pembayaran',          'description' => 'Membatalkan invoice dengan alasan resmi'],
            ['name' => 'payments.export',           'display_name' => 'Ekspor Pembayaran',            'module' => 'Pembayaran',          'description' => 'Mengekspor rekap invoice dan pembayaran'],

            // ─────────────────────────────────────────
            // Bantuan (3)
            // ─────────────────────────────────────────
            ['name' => 'view_helpdesk',            'display_name' => 'Lihat Tiket Helpdesk',         'module' => 'Bantuan',             'description' => 'Melihat seluruh tiket bantuan yang masuk dari pendaftar'],
            ['name' => 'reply_helpdesk',           'display_name' => 'Balas Tiket Helpdesk',         'module' => 'Bantuan',             'description' => 'Membalas dan menyelesaikan tiket bantuan pendaftar'],
            ['name' => 'manage_support_templates', 'display_name' => 'Kelola Template Bantuan',      'module' => 'Bantuan',             'description' => 'Membuat dan mengelola template pesan untuk helpdesk'],

            // ─────────────────────────────────────────
            // Audit (2)
            // ─────────────────────────────────────────
            ['name' => 'view_audit_logs',          'display_name' => 'Lihat Audit Log',              'module' => 'Audit',               'description' => 'Melihat riwayat aktivitas dan perubahan data di sistem'],
            ['name' => 'export_audit_logs',        'display_name' => 'Ekspor Audit Log',             'module' => 'Audit',               'description' => 'Mengekspor data audit log ke format Excel'],
        ];

        foreach ($permissions as $permission) {
            $existing = $this->db->table('permissions')
                ->where('permission_key', $permission['name'])
                ->get()
                ->getRow();

            if ($existing === null) {
                // Insert baru
                $this->db->table('permissions')->insert([
                    'permission_key' => $permission['name'],
                    'name'           => $permission['display_name'],
                    'group_name'     => $permission['module'],
                    'description'    => $permission['description'],
                    'is_active'      => 1,
                    'sort_order'     => 100,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
            } else {
                // Update jika display_name, module, atau description berubah
                $this->db->table('permissions')
                    ->where('permission_key', $permission['name'])
                    ->update([
                        'name'        => $permission['display_name'],
                        'group_name'  => $permission['module'],
                        'description' => $permission['description'],
                        'is_active'   => 1,
                        'updated_at'  => $now,
                    ]);
            }
        }
    }
}
