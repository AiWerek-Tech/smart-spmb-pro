<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Gelombang
        $gelombangData = [];
        // Loop through jalurs (1 to 5)
        for ($jalurId = 1; $jalurId <= 5; $jalurId++) {
            $gelombangData[] = [
                'jalur_id' => $jalurId,
                'name' => 'Gelombang 1',
                'open_date' => '2026-05-01',
                'close_date' => '2026-06-30',
                'announcement_date' => '2026-07-05',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $gelombangData[] = [
                'jalur_id' => $jalurId,
                'name' => 'Gelombang 2',
                'open_date' => '2026-07-10',
                'close_date' => '2026-08-15',
                'announcement_date' => '2026-08-20',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $existingGelombang = $this->db->table('gelombang')->countAllResults();
        if ($existingGelombang === 0) {
            $this->db->table('gelombang')->insertBatch($gelombangData);
        }

        // Get admin user ID
        $admin = $this->db->table('users')->where('email', 'admin@smartspmbpro.sch.id')->get()->getRow();
        $adminId = $admin ? $admin->id : 1;

        // 2. Seed Announcements
        $announcementsData = [
            [
                'title' => 'Pendaftaran Smart SPMB Pro Resmi Dibuka!',
                'content' => '<p>Selamat datang calon siswa baru! Pendaftaran Penerimaan Siswa Baru online resmi dibuka hari ini. Silakan melakukan registrasi akun terlebih dahulu melalui link yang disediakan kemudian lengkapi berkas pendaftaran Anda di dashboard pendaftar.</p><p>Pastikan data yang Anda masukkan sudah valid dan sesuai dengan dokumen asli yang Anda miliki. Selamat berjuang!</p>',
                'published_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'status' => 'published',
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Panduan Pengisian Berkas dan Verifikasi Dokumen',
                'content' => '<p>Untuk mempermudah proses pendaftaran, panitia telah menyediakan panduan pengisian dokumen persyaratan. Silakan perhatikan format unggahan berkas (PDF/JPG) dengan ukuran maksimal 2MB per dokumen.</p><p>Dokumen yang wajib diunggah meliputi Kartu Keluarga, Akta Kelahiran, dan rapor kelas 6 semester 1-5.</p>',
                'published_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
                'status' => 'published',
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Jadwal Sosialisasi Alur Penerimaan Siswa Baru',
                'content' => '<p>Panitia akan mengadakan sosialisasi alur pendaftaran secara virtual (via Zoom) pada hari Sabtu ini pukul 09.00 WIB. Tautan pertemuan virtual akan dikirimkan melalui grup koordinasi pendaftar.</p>',
                'published_at' => date('Y-m-d H:i:s'),
                'status' => 'published',
                'created_by' => $adminId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $existingAnnouncements = $this->db->table('announcements')->countAllResults();
        if ($existingAnnouncements === 0) {
            $this->db->table('announcements')->insertBatch($announcementsData);
        }

        // 3. Seed FAQs
        $faqsData = [
            [
                'question' => 'Bagaimana cara melakukan pendaftaran online?',
                'answer' => 'Anda hanya perlu membuat akun baru di halaman registrasi, masuk ke dashboard, lalu ikuti langkah demi langkah pada formulir pendaftaran (Wizard 8 Langkah) yang disediakan.',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'question' => 'Dokumen apa saja yang wajib diunggah?',
                'answer' => 'Dokumen wajib antara lain Kartu Keluarga, Akta Kelahiran, Pas Foto 3x4 terbaru, dan Surat Keterangan Lulus (SKL) atau Rapor semester terakhir.',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'question' => 'Apakah pendaftaran ini dikenakan biaya?',
                'answer' => 'Pendaftaran ini sepenuhnya gratis dan tidak dipungut biaya apapun bagi seluruh calon peserta didik.',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $existingFaqs = $this->db->table('faqs')->countAllResults();
        if ($existingFaqs === 0) {
            $this->db->table('faqs')->insertBatch($faqsData);
        }
    }
}
