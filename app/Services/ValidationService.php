<?php

declare(strict_types=1);

namespace App\Services;

/**
 * ValidationService
 *
 * Menyediakan metode validasi untuk NIK, NISN, email, format file, dan ukuran file.
 *
 * Requirements: 9.4, 9.5, 9.6, 9.7, 10.5, 10.6, 14.3, 14.4
 */
class ValidationService
{
    /**
     * Ukuran file maksimum yang diizinkan: 2 MB dalam bytes.
     */
    public const MAX_FILE_SIZE_BYTES = 2 * 1024 * 1024; // 2 MB

    /**
     * Ekstensi file yang diizinkan (whitelist).
     */
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    /**
     * Validasi NIK (Nomor Induk Kependudukan).
     *
     * Mengembalikan true jika dan hanya jika input terdiri dari tepat 16 digit angka.
     *
     * Requirements: 9.4, 9.5
     *
     * @param string $input NIK yang akan divalidasi
     * @return bool true jika valid (tepat 16 digit angka), false jika tidak
     */
    public function validateNik(string $input): bool
    {
        return (bool) preg_match('/^\d{16}$/', $input);
    }

    /**
     * Validasi NISN (Nomor Induk Siswa Nasional).
     *
     * Mengembalikan true jika dan hanya jika input terdiri dari tepat 10 digit angka.
     *
     * Requirements: 9.6, 9.7
     *
     * @param string $input NISN yang akan divalidasi
     * @return bool true jika valid (tepat 10 digit angka), false jika tidak
     */
    public function validateNisn(string $input): bool
    {
        return (bool) preg_match('/^\d{10}$/', $input);
    }

    /**
     * Validasi format email sesuai standar RFC 5322.
     *
     * Menggunakan filter_var dengan FILTER_VALIDATE_EMAIL yang mengikuti
     * standar RFC 5322 untuk validasi alamat email.
     *
     * Requirements: 10.5, 10.6
     *
     * @param string $input Alamat email yang akan divalidasi
     * @return bool true jika format email valid, false jika tidak
     */
    public function validateEmail(string $input): bool
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validasi format file berdasarkan ekstensi.
     *
     * Memeriksa apakah ekstensi yang diberikan termasuk dalam whitelist yang diizinkan:
     * jpg, jpeg, png, pdf (case-insensitive).
     *
     * Requirements: 14.3
     *
     * @param string $extension Ekstensi file (tanpa titik) yang akan divalidasi, misal: "jpg", "PDF"
     * @return bool true jika format file diizinkan, false jika tidak
     */
    public function validateFileFormat(string $extension): bool
    {
        return in_array(strtolower($extension), self::ALLOWED_EXTENSIONS, true);
    }

    /**
     * Validasi ekstensi/format file berdasarkan nama file lengkap.
     *
     * Memeriksa apakah ekstensi file termasuk dalam whitelist yang diizinkan:
     * jpg, jpeg, png, pdf (case-insensitive).
     *
     * Requirements: 14.3
     *
     * @param string $filename Nama file lengkap yang akan divalidasi
     * @return bool true jika format file diizinkan, false jika tidak
     */
    public function validateFileExtension(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    /**
     * Validasi ukuran file.
     *
     * Memeriksa apakah ukuran file tidak melebihi batas maksimum 2 MB.
     *
     * Requirements: 14.4
     *
     * @param int $fileSizeBytes Ukuran file dalam bytes
     * @return bool true jika ukuran file valid (≤ 2 MB), false jika melebihi batas
     */
    public function validateFileSize(int $fileSizeBytes): bool
    {
        return $fileSizeBytes <= self::MAX_FILE_SIZE_BYTES;
    }

    /**
     * Validasi file secara lengkap (format dan ukuran).
     *
     * Memeriksa format file (whitelist: jpg, jpeg, png, pdf) dan
     * ukuran file (maks. 2 MB) sekaligus.
     *
     * Requirements: 14.3, 14.4
     *
     * @param string $filename     Nama file yang akan divalidasi
     * @param int    $fileSizeBytes Ukuran file dalam bytes
     * @return array{valid: bool, error: string|null} Array dengan kunci 'valid' (bool)
     *                                                 dan 'error' (string|null)
     */
    public function validateFile(string $filename, int $fileSizeBytes): array
    {
        if (! $this->validateFileExtension($filename)) {
            return [
                'valid' => false,
                'error' => 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau PDF',
            ];
        }

        if (! $this->validateFileSize($fileSizeBytes)) {
            return [
                'valid' => false,
                'error' => 'Ukuran file melebihi batas maksimal 2 MB',
            ];
        }

        return [
            'valid' => true,
            'error' => null,
        ];
    }
}
