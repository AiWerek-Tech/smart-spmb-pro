<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run()
    {
        // 1. Banners
        $bannerData = [
            [
                'title'      => 'Wujudkan Masa Depan Cemerlang',
                'subtitle'   => 'Portal pendaftaran resmi dengan sistem yang transparan, akuntabel, dan terintegrasi langsung dengan data nasional.',
                'image'      => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1200',
                'cta_text'   => 'Daftar Sekarang',
                'cta_url'    => 'auth/register',
                'is_active'  => 1,
                'sort_order' => 1,
            ],
            [
                'title'      => 'Sekolah Penggerak Kurikulum Merdeka',
                'subtitle'   => 'Kami berkomitmen mencetak generasi unggul yang berkarakter dan siap menghadapi tantangan global.',
                'image'      => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=1200',
                'cta_text'   => 'Lihat Profil',
                'cta_url'    => 'profil',
                'is_active'  => 1,
                'sort_order' => 2,
            ]
        ];
        $this->db->table('banners')->insertBatch($bannerData);

        // 2. Statistics
        $statData = [
            ['label' => 'Pendaftar', 'value' => '1.250+', 'icon' => 'users', 'is_active' => 1, 'sort_order' => 1],
            ['label' => 'Terverifikasi', 'value' => '98%', 'icon' => 'check-circle', 'is_active' => 1, 'sort_order' => 2],
            ['label' => 'Diterima', 'value' => '320', 'icon' => 'user-check', 'is_active' => 1, 'sort_order' => 3],
            ['label' => 'Akreditasi', 'value' => 'A', 'icon' => 'award', 'is_active' => 1, 'sort_order' => 4],
            ['label' => 'Guru Ahli', 'value' => '50+', 'icon' => 'graduation-cap', 'is_active' => 1, 'sort_order' => 5],
            ['label' => 'Jalur Aktif', 'value' => '4', 'icon' => 'map-pin', 'is_active' => 1, 'sort_order' => 6],
        ];
        $this->db->table('statistics')->insertBatch($statData);

        // 3. Gallery
        $galleryData = [
            ['title' => 'Gedung Utama', 'image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=800', 'category' => 'Fasilitas', 'is_active' => 1, 'sort_order' => 1],
            ['title' => 'Perpustakaan Digital', 'image' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=800', 'category' => 'Fasilitas', 'is_active' => 1, 'sort_order' => 2],
            ['title' => 'Laboratorium Sains', 'image' => 'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=800', 'category' => 'Fasilitas', 'is_active' => 1, 'sort_order' => 3],
        ];
        $this->db->table('gallery')->insertBatch($galleryData);

        // 4. Testimonials
        $testiData = [
            [
                'name'    => 'Budi Santoso',
                'role'    => 'Alumni 2022',
                'content' => 'Sistem pendaftaran di SMP Nusantara sangat mudah dan transparan. Saya sangat terbantu dengan fitur cek status real-time.',
                'rating'  => 5,
                'is_active' => 1
            ],
            [
                'name'    => 'Siti Aminah',
                'role'    => 'Orang Tua Siswa',
                'content' => 'Guru-gurunya sangat profesional dan komunikatif. Fasilitas sekolah juga sangat mendukung tumbuh kembang anak saya.',
                'rating'  => 5,
                'is_active' => 1
            ],
            [
                'name'    => 'Andi Wijaya',
                'role'    => 'Alumni 2020',
                'content' => 'Pengalaman belajar yang luar biasa. Kurikulumnya sangat relevan dengan kebutuhan masa depan.',
                'rating'  => 4,
                'is_active' => 1
            ],
        ];
        $this->db->table('testimonials')->insertBatch($testiData);
    }
}
