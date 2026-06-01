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
                'key'        => 'school_tagline',
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
                'key'        => 'accreditation_grade',
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
                'value'      => '6281234567890',
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
                'key'        => 'app_version',
                'value'      => config('AppInfo')->version,
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
