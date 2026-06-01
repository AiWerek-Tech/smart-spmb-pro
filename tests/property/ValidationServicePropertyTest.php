<?php

declare(strict_types=1);

namespace Tests\Property;

use App\Services\ValidationService;
use Eris\Generator;

/**
 * Property-based tests untuk ValidationService.
 *
 * **Validates: Requirements 9.4, 9.5, 9.6, 9.7**
 */
class ValidationServicePropertyTest extends BasePropertyTestCase
{
    private ValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ValidationService();
    }

    // =========================================================================
    // Property 4: Invariant Validasi NIK
    // =========================================================================

    /**
     * Property 4a: String acak yang bukan tepat 16 digit angka harus ditolak.
     *
     * Menggunakan generator string acak. Sebagian besar string acak tidak akan
     * memenuhi kriteria 16 digit angka, sehingga validateNik() harus false.
     *
     * **Validates: Requirements 9.4, 9.5**
     */
    public function testProperty4NikRejectsRandomStrings(): void
    {
        $this->forAll(
            Generator\string()
        )->then(function (string $input) {
            if (preg_match('/^\d{16}$/', $input)) {
                // Input kebetulan valid — harus diterima
                $this->assertTrue(
                    $this->service->validateNik($input),
                    "validateNik() harus true untuk input 16 digit angka: '{$input}'"
                );
            } else {
                // Input tidak valid — harus ditolak
                $this->assertFalse(
                    $this->service->validateNik($input),
                    "validateNik() harus false untuk input bukan 16 digit angka: '{$input}'"
                );
            }
        });
    }

    /**
     * Property 4b: String dengan panjang bukan 16 karakter harus selalu ditolak.
     *
     * **Validates: Requirements 9.4, 9.5**
     */
    public function testProperty4NikRejectsWrongLength(): void
    {
        // Test panjang 0-15 (terlalu pendek)
        $this->forAll(
            Generator\choose(0, 15)
        )->then(function (int $length) {
            $input = str_repeat('1', $length);
            $this->assertFalse(
                $this->service->validateNik($input),
                "validateNik() harus false untuk string {$length} digit: '{$input}'"
            );
        });
    }

    /**
     * Property 4c: String dengan panjang lebih dari 16 karakter harus selalu ditolak.
     *
     * **Validates: Requirements 9.4, 9.5**
     */
    public function testProperty4NikRejectsTooLong(): void
    {
        $this->forAll(
            Generator\choose(17, 30)
        )->then(function (int $length) {
            $input = str_repeat('1', $length);
            $this->assertFalse(
                $this->service->validateNik($input),
                "validateNik() harus false untuk string {$length} digit: '{$input}'"
            );
        });
    }

    /**
     * Property 4d: String 16 karakter yang mengandung non-digit harus ditolak.
     *
     * **Validates: Requirements 9.4, 9.5**
     */
    public function testProperty4NikRejectsNonDigitChars(): void
    {
        // Karakter non-digit yang umum
        $nonDigits = ['a', 'b', 'z', 'A', 'Z', ' ', '-', '.', '_', '!', '@', '#'];

        $this->forAll(
            Generator\elements(...$nonDigits),
            Generator\choose(0, 15)  // posisi karakter non-digit
        )->then(function (string $nonDigit, int $position) {
            // Buat string 16 karakter dengan satu karakter non-digit di posisi tertentu
            $digits = str_repeat('1', 16);
            $input = substr_replace($digits, $nonDigit, $position, 1);

            $this->assertFalse(
                $this->service->validateNik($input),
                "validateNik() harus false untuk string dengan non-digit '{$nonDigit}' di posisi {$position}: '{$input}'"
            );
        });
    }

    /**
     * Property 4e: Tepat 16 digit angka harus selalu diterima (if and only if).
     *
     * Menggunakan generator untuk menghasilkan string 16 digit angka yang valid.
     *
     * **Validates: Requirements 9.4, 9.5**
     */
    public function testProperty4NikAcceptsExactly16Digits(): void
    {
        $this->forAll(
            // Generate 16 digit angka: setiap digit adalah 0-9
            Generator\vector(16, Generator\choose(0, 9))
        )->then(function (array $digits) {
            $input = implode('', $digits);
            $this->assertTrue(
                $this->service->validateNik($input),
                "validateNik() harus true untuk tepat 16 digit angka: '{$input}'"
            );
        });
    }

    // =========================================================================
    // Property 5: Invariant Validasi NISN
    // =========================================================================

    /**
     * Property 5a: String acak yang bukan tepat 10 digit angka harus ditolak.
     *
     * **Validates: Requirements 9.6, 9.7**
     */
    public function testProperty5NisnRejectsRandomStrings(): void
    {
        $this->forAll(
            Generator\string()
        )->then(function (string $input) {
            if (preg_match('/^\d{10}$/', $input)) {
                // Input kebetulan valid — harus diterima
                $this->assertTrue(
                    $this->service->validateNisn($input),
                    "validateNisn() harus true untuk input 10 digit angka: '{$input}'"
                );
            } else {
                // Input tidak valid — harus ditolak
                $this->assertFalse(
                    $this->service->validateNisn($input),
                    "validateNisn() harus false untuk input bukan 10 digit angka: '{$input}'"
                );
            }
        });
    }

    /**
     * Property 5b: String dengan panjang bukan 10 karakter harus selalu ditolak.
     *
     * **Validates: Requirements 9.6, 9.7**
     */
    public function testProperty5NisnRejectsWrongLength(): void
    {
        // Test panjang 0-9 (terlalu pendek)
        $this->forAll(
            Generator\choose(0, 9)
        )->then(function (int $length) {
            $input = str_repeat('1', $length);
            $this->assertFalse(
                $this->service->validateNisn($input),
                "validateNisn() harus false untuk string {$length} digit: '{$input}'"
            );
        });
    }

    /**
     * Property 5c: String dengan panjang lebih dari 10 karakter harus selalu ditolak.
     *
     * **Validates: Requirements 9.6, 9.7**
     */
    public function testProperty5NisnRejectsTooLong(): void
    {
        $this->forAll(
            Generator\choose(11, 25)
        )->then(function (int $length) {
            $input = str_repeat('1', $length);
            $this->assertFalse(
                $this->service->validateNisn($input),
                "validateNisn() harus false untuk string {$length} digit: '{$input}'"
            );
        });
    }

    /**
     * Property 5d: String 10 karakter yang mengandung non-digit harus ditolak.
     *
     * **Validates: Requirements 9.6, 9.7**
     */
    public function testProperty5NisnRejectsNonDigitChars(): void
    {
        $nonDigits = ['a', 'b', 'z', 'A', 'Z', ' ', '-', '.', '_', '!', '@', '#'];

        $this->forAll(
            Generator\elements(...$nonDigits),
            Generator\choose(0, 9)  // posisi karakter non-digit
        )->then(function (string $nonDigit, int $position) {
            // Buat string 10 karakter dengan satu karakter non-digit di posisi tertentu
            $digits = str_repeat('1', 10);
            $input = substr_replace($digits, $nonDigit, $position, 1);

            $this->assertFalse(
                $this->service->validateNisn($input),
                "validateNisn() harus false untuk string dengan non-digit '{$nonDigit}' di posisi {$position}: '{$input}'"
            );
        });
    }

    /**
     * Property 5e: Tepat 10 digit angka harus selalu diterima (if and only if).
     *
     * **Validates: Requirements 9.6, 9.7**
     */
    public function testProperty5NisnAcceptsExactly10Digits(): void
    {
        $this->forAll(
            // Generate 10 digit angka: setiap digit adalah 0-9
            Generator\vector(10, Generator\choose(0, 9))
        )->then(function (array $digits) {
            $input = implode('', $digits);
            $this->assertTrue(
                $this->service->validateNisn($input),
                "validateNisn() harus true untuk tepat 10 digit angka: '{$input}'"
            );
        });
    }
}
