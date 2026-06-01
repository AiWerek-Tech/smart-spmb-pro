<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder untuk 5 jalur pendaftaran bawaan sesuai Requirement 15.1.
 */
class JalurSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name'        => 'Domisili',
                'description' => 'Jalur pendaftaran berdasarkan domisili/tempat tinggal calon peserta didik yang berada dalam zona wilayah sekolah.',
                'quota'       => 30,
                'is_active'   => 1,
                'sort_order'  => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Prestasi',
                'description' => 'Jalur pendaftaran berdasarkan prestasi akademik maupun non-akademik yang diraih calon peserta didik.',
                'quota'       => 10,
                'is_active'   => 1,
                'sort_order'  => 2,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Afirmasi',
                'description' => 'Jalur pendaftaran untuk calon peserta didik dari keluarga tidak mampu yang memiliki KIP, KKS, atau PKH.',
                'quota'       => 15,
                'is_active'   => 1,
                'sort_order'  => 3,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Mutasi',
                'description' => 'Jalur pendaftaran untuk calon peserta didik yang pindah dari sekolah lain karena orang tua/wali pindah tugas.',
                'quota'       => 5,
                'is_active'   => 1,
                'sort_order'  => 4,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Umum',
                'description' => 'Jalur pendaftaran umum untuk calon peserta didik yang tidak memenuhi kriteria jalur lainnya.',
                'quota'       => 40,
                'is_active'   => 1,
                'sort_order'  => 5,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Only insert if table is empty to avoid duplicates
        $existing = $this->db->table('jalur')->countAllResults();
        if ($existing === 0) {
            $this->db->table('jalur')->insertBatch($data);
        }
    }
}
