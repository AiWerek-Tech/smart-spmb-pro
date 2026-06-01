# Smart SPMB Pro

Smart SPMB Pro adalah sistem penerimaan murid baru berbasis web untuk sekolah, dengan dashboard Admin, Operator, dan Pendaftar.

## Rilis Produksi

- Versi aplikasi: `1.0.0`
- Developer: `AiWerek Tech`
- Kontak developer: `082190822641`
- Email developer: `aiwerek.tech@gmail.com`

## Fitur Utama

- Pendaftaran multi-jalur dan multi-gelombang.
- Formulir data calon murid yang mengikuti kebutuhan Dapodik.
- Verifikasi dokumen pendaftar oleh operator.
- Seleksi, ranking, pengumuman, dan status kelulusan.
- Ekspor Excel dan PDF: kartu peserta, FPD, dan SKL.
- Dashboard multi-role dengan proteksi autentikasi, role, dan CSRF.
- Tema visual, profil sekolah, FAQ, banner, statistik, dan konten publik yang dapat dikonfigurasi dari admin.

## Teknologi

- PHP 8.1+
- CodeIgniter 4
- MySQL atau MariaDB
- Bootstrap 5
- PHPUnit
- Dompdf, PhpSpreadsheet, Endroid QR Code

## Persiapan Produksi

1. Arahkan document root web server ke folder `public/`.
2. Salin `.env.production.example` menjadi `.env` di server produksi.
3. Isi `app.baseURL`, kredensial database, SMTP, dan `encryption.key` produksi.
4. Jalankan migrasi dan seeder:

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

5. Pastikan folder `writable/` dapat ditulis oleh user web server.
6. Aktifkan HTTPS, lalu gunakan `app.forceGlobalSecureRequests = true` dan `cookie.secure = true`.

## Pengujian

```bash
php vendor/bin/phpunit
```

Jika test yang memakai database gagal, pastikan MySQL/MariaDB aktif dan database test `smart_spmb_pro_test` tersedia sesuai `phpunit.xml`.

## Struktur Singkat

```text
smart-spmb-pro-web/
├── app/        # Controller, model, service, config, view
├── public/     # Entry point dan aset publik
├── tests/      # Unit, property, dan browser audit
├── vendor/     # Dependency Composer
└── writable/   # Cache, log, session, upload, export
```

Developed by **AiWerek Tech**.
