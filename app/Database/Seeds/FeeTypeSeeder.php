<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code'                         => 'B_FORM',
                'name'                         => 'Biaya Formulir',
                'description'                  => 'Biaya pembelian formulir pendaftaran awal.',
                'amount'                       => 150000.00,
                'billing_period'               => 'Satu Kali',
                'is_required'                  => 1,
                'is_active'                    => 1,
                'show_on_homepage'             => 1,
                'requires_payment_before_form' => 1,
                'auto_invoice'                 => 1,
                'icon'                         => 'file-text',
                'sort_order'                   => 10,
                'created_at'                   => date('Y-m-d H:i:s'),
                'updated_at'                   => date('Y-m-d H:i:s'),
            ],
            [
                'code'                         => 'B_REGISTRATION',
                'name'                         => 'Biaya Pendaftaran',
                'description'                  => 'Biaya administrasi seleksi masuk calon siswa.',
                'amount'                       => 250000.00,
                'billing_period'               => 'Satu Kali',
                'is_required'                  => 1,
                'is_active'                    => 1,
                'show_on_homepage'             => 1,
                'requires_payment_before_form' => 0,
                'auto_invoice'                 => 1,
                'icon'                         => 'user-plus',
                'sort_order'                   => 20,
                'created_at'                   => date('Y-m-d H:i:s'),
                'updated_at'                   => date('Y-m-d H:i:s'),
            ],
            [
                'code'                         => 'B_UNIFORM',
                'name'                         => 'Biaya Seragam',
                'description'                  => 'Paket seragam sekolah lengkap (5 stel).',
                'amount'                       => 1200000.00,
                'billing_period'               => 'Satu Kali',
                'is_required'                  => 1,
                'is_active'                    => 1,
                'show_on_homepage'             => 1,
                'requires_payment_before_form' => 0,
                'auto_invoice'                 => 0,
                'icon'                         => 'shirt',
                'sort_order'                   => 30,
                'created_at'                   => date('Y-m-d H:i:s'),
                'updated_at'                   => date('Y-m-d H:i:s'),
            ],
            [
                'code'                         => 'B_SPP',
                'name'                         => 'Biaya SPP Bulanan',
                'description'                  => 'Sumbangan Pembinaan Pendidikan bulanan.',
                'amount'                       => 350000.00,
                'billing_period'               => 'Bulanan',
                'is_required'                  => 1,
                'is_active'                    => 1,
                'show_on_homepage'             => 1,
                'requires_payment_before_form' => 0,
                'auto_invoice'                 => 0,
                'icon'                         => 'calendar',
                'sort_order'                   => 40,
                'created_at'                   => date('Y-m-d H:i:s'),
                'updated_at'                   => date('Y-m-d H:i:s'),
            ],
            [
                'code'                         => 'B_RE_REGISTRATION',
                'name'                         => 'Biaya Daftar Ulang',
                'description'                  => 'Biaya administrasi daftar ulang setelah dinyatakan lulus seleksi.',
                'amount'                       => 1500000.00,
                'billing_period'               => 'Satu Kali',
                'is_required'                  => 1,
                'is_active'                    => 1,
                'show_on_homepage'             => 1,
                'requires_payment_before_form' => 0,
                'auto_invoice'                 => 0,
                'icon'                         => 'check-square',
                'sort_order'                   => 50,
                'created_at'                   => date('Y-m-d H:i:s'),
                'updated_at'                   => date('Y-m-d H:i:s'),
            ],
        ];

        $db = \Config\Database::connect();
        foreach ($data as $row) {
            $existing = $db->table('fee_types')->where('code', $row['code'])->get()->getRow();
            if (!$existing) {
                $db->table('fee_types')->insert($row);
            }
        }
    }
}
