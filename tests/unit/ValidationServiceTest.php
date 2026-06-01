<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ValidationService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit tests untuk ValidationService.
 *
 * **Validates: Requirements 9.4, 9.5, 9.6, 9.7, 10.5, 10.6, 14.3, 14.4**
 */
class ValidationServiceTest extends CIUnitTestCase
{
    protected ValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ValidationService();
    }

    // =========================================================================
    // Test NIK Validation
    // =========================================================================

    /**
     * Test NIK valid 16 digit — accepted.
     *
     * **Validates: Requirement 9.4**
     */
    public function testValidNikAccepted(): void
    {
        $validNik = '1234567890123456';
        $this->assertTrue($this->service->validateNik($validNik));
    }

    /**
     * Test NIK < 16 digit — rejected.
     *
     * **Validates: Requirement 9.5**
     */
    public function testNikTooShortRejected(): void
    {
        $shortNik = '123456789012345'; // 15 digits
        $this->assertFalse($this->service->validateNik($shortNik));
    }

    /**
     * Test NIK > 16 digit — rejected.
     */
    public function testNikTooLongRejected(): void
    {
        $longNik = '12345678901234567'; // 17 digits
        $this->assertFalse($this->service->validateNik($longNik));
    }

    /**
     * Test NIK dengan karakter non-digit — rejected.
     */
    public function testNikWithNonDigitsRejected(): void
    {
        $invalidNik = '1234567890123ABC';
        $this->assertFalse($this->service->validateNik($invalidNik));
    }

    // =========================================================================
    // Test NISN Validation
    // =========================================================================

    /**
     * Test NISN valid 10 digit — accepted.
     *
     * **Validates: Requirement 9.6**
     */
    public function testValidNisnAccepted(): void
    {
        $validNisn = '1234567890';
        $this->assertTrue($this->service->validateNisn($validNisn));
    }

    /**
     * Test NISN < 10 digit — rejected.
     *
     * **Validates: Requirement 9.7**
     */
    public function testNisnTooShortRejected(): void
    {
        $shortNisn = '123456789'; // 9 digits
        $this->assertFalse($this->service->validateNisn($shortNisn));
    }

    /**
     * Test NISN > 10 digit — rejected.
     */
    public function testNisnTooLongRejected(): void
    {
        $longNisn = '12345678901'; // 11 digits
        $this->assertFalse($this->service->validateNisn($longNisn));
    }

    /**
     * Test NISN dengan karakter non-digit — rejected.
     */
    public function testNisnWithNonDigitsRejected(): void
    {
        $invalidNisn = '123456789A';
        $this->assertFalse($this->service->validateNisn($invalidNisn));
    }

    // =========================================================================
    // Test Email Validation
    // =========================================================================

    /**
     * Test email valid — accepted.
     *
     * **Validates: Requirement 10.5**
     */
    public function testValidEmailAccepted(): void
    {
        $validEmails = [
            'user@example.com',
            'john.doe@example.co.id',
            'test+tag@domain.org',
            'user_name@sub.domain.com',
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(
                $this->service->validateEmail($email),
                "Email '{$email}' harus valid"
            );
        }
    }

    /**
     * Test email invalid — rejected.
     *
     * **Validates: Requirement 10.6**
     */
    public function testInvalidEmailRejected(): void
    {
        $invalidEmails = [
            'notanemail',
            '@example.com',
            'user@',
            'user @example.com',
            'user@example',
            'user..name@example.com',
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(
                $this->service->validateEmail($email),
                "Email '{$email}' harus invalid"
            );
        }
    }

    // =========================================================================
    // Test File Format Validation
    // =========================================================================

    /**
     * Test format file valid — accepted.
     *
     * **Validates: Requirement 14.3**
     */
    public function testValidFileFormatAccepted(): void
    {
        $validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

        foreach ($validExtensions as $ext) {
            $this->assertTrue(
                $this->service->validateFileFormat($ext),
                "Format '{$ext}' harus valid"
            );

            // Test case-insensitive
            $this->assertTrue(
                $this->service->validateFileFormat(strtoupper($ext)),
                "Format '" . strtoupper($ext) . "' harus valid (case-insensitive)"
            );
        }
    }

    /**
     * Test format file invalid — rejected.
     */
    public function testInvalidFileFormatRejected(): void
    {
        $invalidExtensions = ['doc', 'xls', 'txt', 'exe', 'zip', 'rar'];

        foreach ($invalidExtensions as $ext) {
            $this->assertFalse(
                $this->service->validateFileFormat($ext),
                "Format '{$ext}' harus invalid"
            );
        }
    }

    /**
     * Test file extension dari filename — valid.
     */
    public function testValidFileExtensionAccepted(): void
    {
        $validFiles = [
            'photo.jpg',
            'document.PDF',
            'image.png',
            'report.JPEG',
        ];

        foreach ($validFiles as $filename) {
            $this->assertTrue(
                $this->service->validateFileExtension($filename),
                "File '{$filename}' harus valid"
            );
        }
    }

    /**
     * Test file extension dari filename — invalid.
     */
    public function testInvalidFileExtensionRejected(): void
    {
        $invalidFiles = [
            'document.doc',
            'script.exe',
            'archive.zip',
        ];

        foreach ($invalidFiles as $filename) {
            $this->assertFalse(
                $this->service->validateFileExtension($filename),
                "File '{$filename}' harus invalid"
            );
        }
    }

    // =========================================================================
    // Test File Size Validation
    // =========================================================================

    /**
     * Test ukuran file <= 2 MB — accepted.
     *
     * **Validates: Requirement 14.4**
     */
    public function testValidFileSizeAccepted(): void
    {
        $sizes = [
            0,                           // 0 bytes
            1 * 1024 * 1024,            // 1 MB
            2 * 1024 * 1024,            // 2 MB (max)
        ];

        foreach ($sizes as $size) {
            $this->assertTrue(
                $this->service->validateFileSize($size),
                "Ukuran {$size} bytes harus valid"
            );
        }
    }

    /**
     * Test ukuran file > 2 MB — rejected.
     */
    public function testInvalidFileSizeRejected(): void
    {
        $sizes = [
            (2 * 1024 * 1024) + 1,      // 2 MB + 1 byte
            3 * 1024 * 1024,            // 3 MB
            10 * 1024 * 1024,           // 10 MB
        ];

        foreach ($sizes as $size) {
            $this->assertFalse(
                $this->service->validateFileSize($size),
                "Ukuran {$size} bytes harus invalid"
            );
        }
    }

    // =========================================================================
    // Test Combined File Validation
    // =========================================================================

    /**
     * Test validasi file lengkap (format + ukuran) — valid.
     */
    public function testValidFilePassesCombinedValidation(): void
    {
        $result = $this->service->validateFile('photo.jpg', 1024 * 1024); // 1 MB

        $this->assertTrue($result['valid']);
        $this->assertNull($result['error']);
    }

    /**
     * Test validasi file lengkap — format invalid.
     */
    public function testInvalidFormatFailsCombinedValidation(): void
    {
        $result = $this->service->validateFile('script.exe', 1024 * 1024);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Format file', $result['error']);
    }

    /**
     * Test validasi file lengkap — ukuran invalid.
     */
    public function testInvalidSizeFailsCombinedValidation(): void
    {
        $result = $this->service->validateFile(
            'photo.jpg',
            (2 * 1024 * 1024) + 1 // 2 MB + 1 byte
        );

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('melebihi', $result['error']);
    }
}
