<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code'           => 'BANK_TRANSFER',
                'name'           => 'Transfer Bank (Manual)',
                'description'    => 'Pembayaran melalui transfer bank BSI atau Bank DKI dengan konfirmasi manual.',
                'account_name'   => 'Panitia SPMB Smart-SPMB-Pro',
                'account_number' => '7712345678 (BSI) / 10123456789 (Bank DKI)',
                'instructions'   => "1. Lakukan transfer ke salah satu rekening berikut:\n   - Bank Syariah Indonesia (BSI) No. Rekening: 7712345678 a.n Panitia SPMB Nusantara Mandiri\n   - Bank DKI No. Rekening: 10123456789 a.n SMP Nusantara Mandiri\n2. Simpan struk / bukti transfer Anda.\n3. Hubungi panitia via WhatsApp untuk mengirimkan bukti transfer Anda.",
                'is_active'      => 1,
                'sort_order'     => 10,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'code'           => 'CASH',
                'name'           => 'Tunai (Kasir Sekolah)',
                'description'    => 'Pembayaran langsung secara tunai di sekretariat panitia SPMB.',
                'account_name'   => 'Kasir Sekolah',
                'account_number' => '-',
                'instructions'   => "1. Datang langsung ke sekretariat pendaftaran pada jam kerja (Senin - Jumat, 08.00 - 15.00).\n2. Berikan bukti pendaftaran dan Nomor Invoice kepada petugas kasir.\n3. Kasir akan memproses pembayaran Anda secara langsung.",
                'is_active'      => 1,
                'sort_order'     => 20,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'code'           => 'QRIS',
                'name'           => 'QRIS (E-Wallet)',
                'description'    => 'Scan kode QRIS sekolah menggunakan aplikasi E-Wallet (GoPay, OVO, Dana, LinkAja, dll).',
                'account_name'   => 'Smart SPMB Pro QRIS',
                'account_number' => 'NMID102030405060',
                'instructions'   => "1. Hubungi panitia/bendahara untuk mendapatkan QR Code dinamis atau gunakan QR Code statis di loket.\n2. Scan menggunakan aplikasi E-Wallet Anda.\n3. Kirim bukti transfer sukses ke bendahara pendaftaran.",
                'is_active'      => 1,
                'sort_order'     => 30,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        $db = \Config\Database::connect();
        foreach ($data as $row) {
            $existing = $db->table('payment_methods')->where('code', $row['code'])->get()->getRow();
            if (!$existing) {
                $db->table('payment_methods')->insert($row);
            }
        }
    }
}
