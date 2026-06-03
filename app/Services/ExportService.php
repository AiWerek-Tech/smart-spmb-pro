<?php

namespace App\Services;

use App\Models\RegistrationModel;
use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentContactModel;
use App\Models\StudentFamilyModel;
use App\Models\StudentDocumentModel;
use CodeIgniter\Database\BaseConnection;

/**
 * ExportService — Orkestrasi ekspor data ke berbagai format.
 *
 * Mengelola ekspor Excel, PDF Formulir F-PD (Dapodik-Ready), dan PDF Kartu Peserta.
 *
 * Requirements: 20.1, 20.2, 20.3, 20.4, 20.5, 21.1, 21.2, 21.3, 21.4, 21.5,
 *               22.1, 22.2, 22.3, 22.4
 */
class ExportService
{
    protected RegistrationModel $registrationModel;
    protected StudentModel $studentModel;
    protected StudentAddressModel $addressModel;
    protected StudentContactModel $contactModel;
    protected StudentFamilyModel $familyModel;
    protected StudentDocumentModel $documentModel;
    protected BaseConnection $db;
    protected AcademicYearService $academicYearService;
    protected UploadDirectoryService $uploadDirectoryService;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->studentModel      = new StudentModel();
        $this->addressModel      = new StudentAddressModel();
        $this->contactModel      = new StudentContactModel();
        $this->familyModel       = new StudentFamilyModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->db                = \Config\Database::connect();
        $this->academicYearService = new AcademicYearService();
        $this->uploadDirectoryService = new UploadDirectoryService($this->academicYearService);
    }

    // -------------------------------------------------------------------------
    // Export Excel
    // -------------------------------------------------------------------------

    /**
     * Ekspor data pendaftar ke file Excel (.xlsx).
     *
     * Kolom: nomor pendaftaran, nama lengkap, NIK, NISN, tanggal lahir, jalur,
     * status verifikasi, nama ayah, nama ibu, alamat, status seleksi.
     *
     * Nama file: [NamaSekolah]_Data_Pendaftar_[YYYY-MM-DD].xlsx
     *
     * Requirements: 20.1, 20.2, 20.3, 20.4, 20.5
     *
     * @param  array $filters Filter untuk pencarian/filter data (search, jalur, status, seleksi)
     * @return array ['success' => bool, 'file_path' => string|null, 'filename' => string|null, 'message' => string]
     */
    public function exportToExcel(array $filters = []): array
    {
        try {
            // Ambil data pendaftar sesuai filter
            $activeYear = $this->academicYearService->activeYear();
            $registrations = $this->registrationModel
                ->applyFilters($filters)
                ->where('registrations.academic_year', $activeYear)
                ->findAll();

            if (empty($registrations)) {
                return [
                    'success'   => false,
                    'file_path' => null,
                    'filename'  => null,
                    'message'   => 'Tidak ada data yang sesuai untuk diekspor.',
                ];
            }

            // Buat workbook dengan PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet       = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Pendaftar');

            // Header row
            $headers = [
                'No Pendaftaran',
                'Nama Lengkap',
                'NIK',
                'NISN',
                'Tanggal Lahir',
                'Jalur',
                'Status Verifikasi',
                'Nama Ayah',
                'Nama Ibu',
                'Alamat',
                'Status Seleksi',
            ];

            $sheet->fromArray($headers, null, 'A1');

            // Style header row
            $headerStyle = [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ];
            $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

            // Populate data rows
            $row = 2;
            foreach ($registrations as $reg) {
                $studentId = $reg['student_id'];
                $father    = $this->familyModel->getFather($studentId);
                $mother    = $this->familyModel->getMother($studentId);
                $address   = $this->addressModel->findByStudentId($studentId);

                $sheet->setCellValue('A' . $row, $reg['registration_number'] ?? '');
                $sheet->setCellValue('B' . $row, $reg['full_name'] ?? '');
                $sheet->setCellValue('C' . $row, $reg['nik'] ?? '');
                $sheet->setCellValue('D' . $row, $reg['nisn'] ?? '');
                $sheet->setCellValue('E' . $row, $reg['birth_date'] ?? '');
                $sheet->setCellValue('F' . $row, $reg['jalur_name'] ?? '');
                $sheet->setCellValue('G' . $row, $reg['status'] ?? '');
                $sheet->setCellValue('H' . $row, $father['full_name'] ?? '');
                $sheet->setCellValue('I' . $row, $mother['full_name'] ?? '');
                $sheet->setCellValue('J' . $row, ($address['street_address'] ?? '') . ' ' . ($address['village'] ?? '') . ' ' . ($address['district'] ?? ''));
                $sheet->setCellValue('K' . $row, $reg['selection_status'] ?? '-');

                $row++;
            }

            // Auto-fit columns
            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Simpan file ke storage uploads/{tahun}/exports/excel/
            $filename    = $this->getSchoolName() . '_Data_Pendaftar_' . date('Y-m-d') . '.xlsx';
            $directory = $this->uploadDirectoryService->writableDirectory('exports/excel', $activeYear);

            $filePath = $directory['absolute'] . $filename;
            $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

            return [
                'success'   => true,
                'file_path' => $filePath,
                'filename'  => $filename,
                'message'   => 'Ekspor Excel berhasil.',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'ExportService::exportToExcel failed: ' . $e->getMessage());

            return [
                'success'   => false,
                'file_path' => null,
                'filename'  => null,
                'message'   => 'Ekspor Excel gagal. Silakan coba lagi.',
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Export PDF F-PD (Dapodik-Ready)
    // -------------------------------------------------------------------------

    /**
     * Ekspor data satu pendaftar ke PDF Formulir F-PD (Dapodik-Ready).
     *
     * Layout A4 portrait, tata letak sesuai standar F-PD Dapodik.
     * Sertakan QR code yang meng-encode nomor pendaftaran.
     *
     * Requirements: 21.1, 21.2, 21.3, 21.4, 21.5
     *
     * @param  int   $studentId
     * @return array ['success' => bool, 'file_path' => string|null, 'filename' => string|null, 'message' => string]
     */
    public function exportToPdfFpd(int $studentId, ?string $academicYear = null): array
    {
        try {
            $academicYear ??= $this->academicYearService->activeYear();
            $student      = $this->studentModel->find($studentId);
            $registration = $this->registrationModel->findByStudentId($studentId, $academicYear);
            $address      = $this->addressModel->findByStudentId($studentId);
            $father       = $this->familyModel->getFather($studentId);
            $mother       = $this->familyModel->getMother($studentId);

            if (! $student || ! $registration) {
                return [
                    'success'   => false,
                    'file_path' => null,
                    'filename'  => null,
                    'message'   => 'Data siswa atau pendaftaran tidak ditemukan.',
                ];
            }

            // Generate QR code
            $qrContent = $registration['registration_number'];
            $qrCode    = $this->generateQrCode($qrContent);

            // Render HTML untuk PDF
            $html = view('exports/fpd_template', [
                'student'      => $student,
                'registration' => $registration,
                'address'      => $address,
                'father'       => $father,
                'mother'       => $mother,
                'qrCode'       => $qrCode,
            ]);

            // Generate PDF menggunakan DomPDF
            $dompdf = new \Dompdf\Dompdf([
                'enable_remote' => false,
                'isPhpEnabled'  => true,
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Simpan file ke storage uploads/{tahun}/exports/pdf/
            $filename   = 'F-PD_' . ($student['full_name'] ?? 'Siswa') . '_' . date('Y-m-d-H-i-s') . '.pdf';
            $directory = $this->uploadDirectoryService->writableDirectory('exports/pdf', $registration['academic_year'] ?? null);

            $filePath = $directory['absolute'] . $filename;
            file_put_contents($filePath, $dompdf->output());

            return [
                'success'   => true,
                'file_path' => $filePath,
                'filename'  => $filename,
                'message'   => 'Ekspor PDF F-PD berhasil.',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'ExportService::exportToPdfFpd failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());

            return [
                'success'   => false,
                'file_path' => null,
                'filename'  => null,
                'message'   => 'Gagal membuat PDF F-PD. Silakan coba lagi.',
            ];
        }
    }

    // -------------------------------------------------------------------------
    // Export PDF Kartu Peserta
    // -------------------------------------------------------------------------

    /**
     * Ekspor data satu pendaftar ke PDF Kartu Peserta (A6 105×148mm).
     *
     * Isi: nomor pendaftaran, nama, pas foto (placeholder jika tidak ada), jalur, QR code.
     *
     * Requirements: 22.1, 22.2, 22.3, 22.4
     *
     * @param  int   $studentId
     * @return array ['success' => bool, 'file_path' => string|null, 'filename' => string|null, 'message' => string]
     */
    public function exportToPdfKartu(int $studentId, ?string $academicYear = null): array
    {
        try {
            $academicYear ??= $this->academicYearService->activeYear();
            $student      = $this->studentModel->find($studentId);
            $registration = $this->registrationModel->findByStudentId($studentId, $academicYear);

            if (! $student || ! $registration) {
                return [
                    'success'   => false,
                    'file_path' => null,
                    'filename'  => null,
                    'message'   => 'Data siswa atau pendaftaran tidak ditemukan.',
                ];
            }

            // Ambil foto siswa dari dokumen
            $photoDocument = $this->documentModel->where('student_id', $studentId)
                                                   ->where('academic_year', $academicYear)
                                                   ->where('document_type', 'foto')
                                                   ->first();
            $photoPath = $photoDocument ? $photoDocument['file_path'] : null;

            // Generate QR code
            $qrContent = $registration['registration_number'];
            $qrCode    = $this->generateQrCode($qrContent);

            // Render HTML untuk PDF
            $html = view('exports/kartu_template', [
                'student'      => $student,
                'registration' => $registration,
                'photoPath'    => $photoPath,
                'qrCode'       => $qrCode,
            ]);

            // Generate PDF menggunakan DomPDF dengan ukuran A6
            $dompdf = new \Dompdf\Dompdf([
                'enable_remote' => false,
                'isPhpEnabled'  => true,
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper([0, 0, 298.898, 419.528], 'portrait'); // A6 105×148mm
            $dompdf->render();

            // Simpan file ke storage uploads/{tahun}/exports/pdf/
            $filename   = 'Kartu_Peserta_' . ($student['full_name'] ?? 'Siswa') . '_' . date('Y-m-d-H-i-s') . '.pdf';
            $directory = $this->uploadDirectoryService->writableDirectory('exports/pdf', $registration['academic_year'] ?? null);

            $filePath = $directory['absolute'] . $filename;
            file_put_contents($filePath, $dompdf->output());

            return [
                'success'   => true,
                'file_path' => $filePath,
                'filename'  => $filename,
                'message'   => 'Ekspor Kartu Peserta berhasil.',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'ExportService::exportToPdfKartu failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());

            return [
                'success'   => false,
                'file_path' => null,
                'filename'  => null,
                'message'   => 'Gagal membuat Kartu Peserta. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Ekspor data satu pendaftar ke PDF Surat Keterangan Lulus (SKL).
     *
     * Layout A4 portrait, memuat pernyataan kelulusan seleksi.
     *
     * @param  int   $studentId
     * @return array ['success' => bool, 'file_path' => string|null, 'filename' => string|null, 'message' => string]
     */
    public function exportToPdfSkl(int $studentId, ?string $academicYear = null): array
    {
        try {
            $academicYear ??= $this->academicYearService->activeYear();
            $student      = $this->studentModel->find($studentId);
            $registration = $this->registrationModel->findByStudentId($studentId, $academicYear);

            if (! $student || ! $registration) {
                return [
                    'success'   => false,
                    'file_path' => null,
                    'filename'  => null,
                    'message'   => 'Data siswa atau pendaftaran tidak ditemukan.',
                ];
            }

            // Pastikan pendaftaran berstatus accepted
            if ($registration['status'] !== 'accepted') {
                return [
                    'success'   => false,
                    'file_path' => null,
                    'filename'  => null,
                    'message'   => 'Surat Keterangan Lulus hanya dapat diunduh jika status Anda dinyatakan Diterima.',
                ];
            }

            // Fetch school settings
            $schoolName    = $this->getSettingValue('school_name', 'Sekolah Mandiri');
            $schoolLogo    = $this->getSettingValue('school_logo', '');
            $schoolAddress = $this->getSettingValue('school_address', 'Alamat Sekolah Mandiri, Indonesia');
            $schoolPhone   = $this->getSettingValue('school_phone', '-');
            $schoolEmail   = $this->getSettingValue('school_email', '-');

            // Generate QR code
            $qrContent = $registration['registration_number'];
            $qrCode    = $this->generateQrCode($qrContent);

            // Render HTML untuk PDF
            $html = view('exports/skl_template', [
                'student'       => $student,
                'registration'  => $registration,
                'schoolName'    => $schoolName,
                'schoolLogo'    => $schoolLogo,
                'schoolAddress' => $schoolAddress,
                'schoolPhone'   => $schoolPhone,
                'schoolEmail'   => $schoolEmail,
                'qrCode'        => $qrCode,
            ]);

            // Generate PDF menggunakan DomPDF
            $dompdf = new \Dompdf\Dompdf([
                'enable_remote' => false,
                'isPhpEnabled'  => true,
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Simpan file ke storage uploads/{tahun}/exports/pdf/
            $filename   = 'SKL_' . ($student['full_name'] ?? 'Siswa') . '_' . date('Y-m-d-H-i-s') . '.pdf';
            $directory = $this->uploadDirectoryService->writableDirectory('exports/pdf', $registration['academic_year'] ?? null);

            $filePath = $directory['absolute'] . $filename;
            file_put_contents($filePath, $dompdf->output());

            return [
                'success'   => true,
                'file_path' => $filePath,
                'filename'  => $filename,
                'message'   => 'Ekspor SKL PDF berhasil.',
            ];
        } catch (\Throwable $e) {
            log_message('error', 'ExportService::exportToPdfSkl failed: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());

            return [
                'success'   => false,
                'file_path' => null,
                'filename'  => null,
                'message'   => 'Gagal membuat SKL PDF. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Ambil nilai pengaturan berdasarkan key.
     */
    protected function getSettingValue(string $key, string $default = ''): string
    {
        $settingModel = new \App\Models\SettingModel();
        $setting      = $settingModel->where('key', $key)->first();
        return $setting ? ($setting['value'] ?? $default) : $default;
    }

    // -------------------------------------------------------------------------
    // Helper: QR Code Generation
    // -------------------------------------------------------------------------

    /**
     * Generate QR code dalam format base64 data URI.
     *
     * @param  string $content Konten untuk QR code (biasanya nomor pendaftaran)
     * @return string Data URI base64 untuk img src
     */
    protected function generateQrCode(string $content): string
    {
        try {
            $qrCode = new \Endroid\QrCode\QrCode($content);
            $qrCode->setSize(300);
            $qrCode->setMargin(10);
            $qrCode->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'));
            $qrCode->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High);

            // Render ke PNG menggunakan PngWriter dan convert ke base64
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);

            return $result->getDataUri();
        } catch (\Throwable $e) {
            log_message('error', 'ExportService::generateQrCode failed: ' . $e->getMessage());

            // Return empty data URI jika gagal
            return 'data:image/png;base64,';
        }
    }

    // -------------------------------------------------------------------------
    // Helper: Get School Name
    // -------------------------------------------------------------------------

    /**
     * Ambil nama sekolah dari settings.
     */
    protected function getSchoolName(): string
    {
        $settingModel = new \App\Models\SettingModel();
        $setting      = $settingModel->where('key', 'school_name')->first();

        return $setting ? ($setting['value'] ?? 'Sekolah') : 'Sekolah';
    }
}
