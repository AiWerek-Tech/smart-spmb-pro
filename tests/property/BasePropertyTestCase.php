<?php

declare(strict_types=1);

namespace Tests\Property;

use Eris\TestTrait;
use PHPUnit\Framework\TestCase;

/**
 * BasePropertyTestCase
 *
 * Kelas dasar untuk semua property-based test menggunakan Eris.
 * Menggunakan TestTrait dari Eris dan mengatur minimum 100 iterasi.
 *
 * Kompatibel dengan PHPUnit 10 dengan meng-override getTestCaseAnnotations()
 * dan menambahkan hasFailed() fallback.
 */
abstract class BasePropertyTestCase extends TestCase
{
    use TestTrait;

    /**
     * Jumlah minimum iterasi untuk setiap property test.
     * Nilai ini digunakan oleh Eris TestTrait.
     */
    protected int $times = 100;

    /**
     * Override untuk kompatibilitas dengan PHPUnit 10.
     *
     * PHPUnit 10 menghapus metode getAnnotations() dan
     * PHPUnit\Util\Test::parseTestMethodAnnotations(). Dengan mengembalikan
     * array kosong, Eris akan menggunakan nilai default (100 iterasi).
     *
     * @return array
     */
    public function getTestCaseAnnotations(): array
    {
        return [];
    }

    /**
     * Setup sebelum setiap test: inisialisasi Eris dengan minimum 100 iterasi.
     *
     * @before
     */
    public function setUpEris(): void
    {
        $this->erisSetup();
        $this->limitTo($this->times);
    }

    /**
     * Fallback hasFailed() untuk Eris TestTrait di PHPUnit 10+.
     */
    public function hasFailed(): bool
    {
        if (method_exists($this, 'status')) {
            $status = $this->status();
            return !$status->isSuccess();
        }
        return false;
    }
}
