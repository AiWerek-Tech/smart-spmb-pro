<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk konfigurasi settings default sesuai Requirement 18.7.
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Informasi Sekolah
            [
                'key'        => 'school_name',
                'value'      => 'SMP Nusantara Mandiri',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'school_logo',
                'value'      => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'tagline',
                'value'      => 'Unggul dalam Prestasi, Berkarakter Mulia',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'school_history',
                'value'      => 'Sekolah ini berdiri sejak tahun 1970 dan telah menghasilkan ribuan alumni berprestasi.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Tahun Ajaran
            [
                'key'        => 'academic_year',
                'value'      => '2026/2027',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Akreditasi
            [
                'key'        => 'accreditation',
                'value'      => 'A',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'accreditation_year',
                'value'      => '2023',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Visi & Misi
            [
                'key'        => 'vision',
                'value'      => 'Menjadi sekolah unggulan yang menghasilkan peserta didik beriman, berilmu, dan berkarakter.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'mission',
                'value'      => "1. Menyelenggarakan pembelajaran yang berkualitas dan inovatif.\n2. Membentuk karakter peserta didik yang berakhlak mulia.\n3. Mengembangkan potensi peserta didik secara optimal.\n4. Menjalin kerjasama yang harmonis dengan orang tua dan masyarakat.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Kontak
            [
                'key'        => 'address',
                'value'      => 'Jl. Pendidikan No. 1, Kota Nusantara',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'phone',
                'value'      => '(021) 1234567',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'email',
                'value'      => 'info@smpnusantaramandiri.sch.id',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'whatsapp',
                'value'      => config('AppInfo')->developerWhatsapp,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Google Maps
            [
                'key'        => 'maps_embed_url',
                'value'      => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'maps_lat',
                'value'      => '-6.200000',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'maps_lng',
                'value'      => '106.816666',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // SPMB Info
            [
                'key'        => 'spmb_description',
                'value'      => 'Penerimaan Murid Baru (SPMB) dilaksanakan secara online melalui sistem Smart SPMB Pro.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'spmb_requirements',
                'value'      => "1. Fotokopi Kartu Keluarga\n2. Akta Kelahiran\n3. Pas Foto 3x4 (2 lembar)\n4. Raport kelas 6 (semester 1-5)",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'school_facilities',
                'value'      => "Ruang kelas nyaman\nPerpustakaan\nLaboratorium komputer\nLaboratorium sains\nLapangan olahraga\nRuang ibadah",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'campus_title',
                'value'      => 'Lingkungan Belajar yang Aman dan Nyaman',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'campus_description',
                'value'      => 'Lingkungan sekolah dirancang untuk mendukung proses belajar yang tertib, ramah anak, dan kondusif. Setiap ruang belajar, area kegiatan, serta fasilitas pendukung dikelola agar peserta didik dapat bertumbuh secara akademik, sosial, dan spiritual.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'privacy_policy',
                'value'      => 'Smart SPMB Pro mengumpulkan dan memproses data calon murid hanya untuk kebutuhan administrasi penerimaan murid baru, verifikasi dokumen, seleksi, dan pelaporan sekolah. Data tidak dibagikan kepada pihak lain di luar kepentingan resmi sekolah kecuali diwajibkan oleh peraturan yang berlaku.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'terms_conditions',
                'value'      => 'Pengguna wajib mengisi data secara benar, lengkap, dan dapat dipertanggungjawabkan. Sekolah berhak memverifikasi, meminta perbaikan, menolak, atau membatalkan pendaftaran apabila ditemukan data atau dokumen yang tidak sesuai dengan ketentuan penerimaan murid baru.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'app_version',
                'value'      => config('AppInfo')->version,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'npsn',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'school_description',
                'value'      => 'Membentuk generasi cerdas, berkarakter, dan siap menghadapi tantangan global dengan sistem pendidikan inovatif.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'theme_color',
                'value'      => 'purple',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'developer_name',
                'value'      => config('AppInfo')->developer,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'developer_phone',
                'value'      => config('AppInfo')->developerPhone,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'developer_email',
                'value'      => config('AppInfo')->developerEmail,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'school_operational_mode',
                'value'      => 'small',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Media Sosial & Ketentuan Registrasi (Req 2.2, 2.3)
            [
                'key'        => 'social_facebook',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'social_instagram',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'social_youtube',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'social_tiktok',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'social_whatsapp',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'social_website',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'social_email',
                'value'      => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'key'        => 'registration_email_required',
                'value'      => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Use INSERT IGNORE or check existing to avoid duplicates
        foreach ($data as $row) {
            $existing = $this->db->table('settings')
                ->where('key', $row['key'])
                ->countAllResults();

            if ($existing === 0) {
                $this->db->table('settings')->insert($row);
            }
        }
    }
}
