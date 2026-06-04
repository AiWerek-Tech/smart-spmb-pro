<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $db = \Config\Database::connect();

        // 1. Seed Provinces
        $provinces = [
            ['id' => '31', 'name' => 'DKI JAKARTA'],
            ['id' => '32', 'name' => 'JAWA BARAT'],
            ['id' => '33', 'name' => 'JAWA TENGAH'],
            ['id' => '35', 'name' => 'JAWA TIMUR'],
        ];

        foreach ($provinces as $prov) {
            $existing = $db->table('regions_provinces')->where('id', $prov['id'])->get()->getRow();
            if (!$existing) {
                $db->table('regions_provinces')->insert($prov);
            }
        }

        // 2. Seed Regencies
        $regencies = [
            ['id' => '3171', 'province_id' => '31', 'name' => 'KOTA JAKARTA SELATAN'],
            ['id' => '3172', 'province_id' => '31', 'name' => 'KOTA JAKARTA TIMUR'],
            ['id' => '3273', 'province_id' => '32', 'name' => 'KOTA BANDUNG'],
            ['id' => '3275', 'province_id' => '32', 'name' => 'KOTA BEKASI'],
        ];

        foreach ($regencies as $reg) {
            $existing = $db->table('regions_regencies')->where('id', $reg['id'])->get()->getRow();
            if (!$existing) {
                $db->table('regions_regencies')->insert($reg);
            }
        }

        // 3. Seed Districts (Kecamatan)
        $districts = [
            // Kebayoran Baru, Kebayoran Lama
            ['id' => '3171010', 'regency_id' => '3171', 'name' => 'KEBAYORAN BARU'],
            ['id' => '3171020', 'regency_id' => '3171', 'name' => 'KEBAYORAN LAMA'],
            // Jatinegara, Duren Sawit
            ['id' => '3172010', 'regency_id' => '3172', 'name' => 'JATINEGARA'],
            ['id' => '3172020', 'regency_id' => '3172', 'name' => 'DUREN SAWIT'],
        ];

        foreach ($districts as $dist) {
            $existing = $db->table('regions_districts')->where('id', $dist['id'])->get()->getRow();
            if (!$existing) {
                $db->table('regions_districts')->insert($dist);
            }
        }

        // 4. Seed Villages (Kelurahan/Desa)
        $villages = [
            // Kebayoran Baru
            ['id' => '3171010001', 'district_id' => '3171010', 'name' => 'SELONG'],
            ['id' => '3171010002', 'district_id' => '3171010', 'name' => 'MELAWAI'],
            // Jatinegara
            ['id' => '3172010001', 'district_id' => '3172010', 'name' => 'BALI MESTER'],
            ['id' => '3172010002', 'district_id' => '3172010', 'name' => 'KAMPUNG MELAYU'],
        ];

        foreach ($villages as $vil) {
            $existing = $db->table('regions_villages')->where('id', $vil['id'])->get()->getRow();
            if (!$existing) {
                $db->table('regions_villages')->insert($vil);
            }
        }
    }
}
