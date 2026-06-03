<?php

namespace App\Controllers\Pendaftar;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\StudentDocumentModel;
use App\Models\RegistrationModel;
use App\Services\AcademicYearService;
use App\Services\DocumentRequirementService;
use App\Services\ExportService;
use App\Services\UploadDirectoryService;

class DocumentController extends BaseController
{
    protected StudentModel $studentModel;
    protected StudentDocumentModel $documentModel;
    protected RegistrationModel $registrationModel;
    protected ExportService $exportService;
    protected AcademicYearService $academicYearService;
    protected DocumentRequirementService $documentRequirementService;
    protected UploadDirectoryService $uploadDirectoryService;

    public function __construct()
    {
        $this->studentModel      = new StudentModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->registrationModel = new RegistrationModel();
        $this->exportService     = new ExportService();
        $this->academicYearService = new AcademicYearService();
        $this->documentRequirementService = new DocumentRequirementService();
        $this->uploadDirectoryService = new UploadDirectoryService($this->academicYearService);
    }

    /**
     * Tampilkan halaman kelola dokumen pendaftar.
     */
    public function index()
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Lengkapi profil Anda terlebih dahulu.');
        }

        $studentId = (int)$student['id'];
        $academicYear = $this->academicYearService->activeYear();
        $registration = $this->registrationModel->findByUserId($userId, $academicYear);
        $documents = $this->documentModel->findByStudentId($studentId, $academicYear);
        $jalurId = isset($registration['jalur_id']) ? (int) $registration['jalur_id'] : null;
        $requirements = $this->documentRequirementService->requirementsForUpload($academicYear, $jalurId);

        $data = [
            'title'        => 'Unggah Dokumen Berkas',
            'student'      => $student,
            'documents'    => $documents,
            'requirements' => $requirements,
            'academicYear' => $academicYear,
        ];

        return view('pendaftar/documents/index', $data);
    }

    /**
     * Proses unggah dokumen berkas baru/overwrite.
     */
    public function upload()
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Siswa tidak ditemukan.');
        }

        $studentId = (int)$student['id'];
        $academicYear = $this->academicYearService->activeYear();
        $registration = $this->registrationModel->findByUserId($userId, $academicYear);
        $jalurId = isset($registration['jalur_id']) ? (int) $registration['jalur_id'] : null;
        $docType = (string) $this->request->getPost('document_type');
        $definition = $this->documentRequirementService->uploadDefinition($academicYear, $jalurId, $docType);

        if (!$definition) {
            return redirect()->back()->withInput()->with('error', 'Jenis dokumen tidak tersedia untuk tahun pelajaran atau jalur pendaftaran ini.');
        }

        $allowedExtensions = implode(',', $this->documentRequirementService->extensionList($definition));
        $maxSizeKb = (int) ($definition['max_size_kb'] ?? 2048);

        $rules = [
            'document_type' => 'required|alpha_dash|max_length[60]',
            'document_file' => 'uploaded[document_file]|ext_in[document_file,' . $allowedExtensions . ']|max_size[document_file,' . $maxSizeKb . ']',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Ukuran file melebihi batas maksimal atau format file tidak diizinkan untuk jenis dokumen ini.');
        }

        $file = $this->request->getFile('document_file');
        if ($file->isValid() && !$file->hasMoved()) {
            if (!$this->isAllowedDocumentMime($this->documentRequirementService->extensionList($definition), (string) $file->getMimeType())) {
                return redirect()->back()->withInput()->with('error', 'Format file tidak sesuai dengan isi dokumen yang diunggah.');
            }

            $newName = $file->getRandomName();

            $directory = $this->uploadDirectoryService->writableDirectory('documents/' . $userId, $academicYear);

            if ($file->move($directory['absolute'], $newName)) {
                $filePath = $directory['relative'] . $newName;

                // Cek apakah dokumen sejenis sudah diunggah sebelumnya
                $existing = $this->documentModel->findByStudentAndType($studentId, $docType, $academicYear);
                if ($existing) {
                    // Hapus file fisik lama
                    $oldPhysical = $this->resolveDocumentPath($existing['file_path']);
                    if (file_exists($oldPhysical)) {
                        unlink($oldPhysical);
                    }

                    // Update data di database
                    $this->documentModel->update($existing['id'], [
                        'file_name' => $file->getClientName(),
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'status'    => 'pending', // Reset status verifikasi saat upload ulang
                        'rejection_reason' => null,
                    ]);
                } else {
                    // Insert data baru
                    $this->documentModel->insert([
                        'student_id'    => $studentId,
                        'academic_year' => $academicYear,
                        'document_type' => $docType,
                        'file_name'     => $file->getClientName(),
                        'file_path'     => $filePath,
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getMimeType(),
                        'status'        => 'pending',
                    ]);
                }

                // Perbarui status kesiapan Dapodik
                $dapodikService = new \App\Services\DapodikService();
                $dapodikService->updateDapodikStatus($studentId);

                return redirect()->to('pendaftar/dokumen')->with('success', 'Dokumen berhasil diunggah dan sedang menanti peninjauan.');
            }
        }

        return redirect()->back()->with('error', 'Gagal memindahkan file.');
    }

    /**
     * Hapus dokumen berkas.
     */
    public function delete(int $docId)
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Siswa tidak ditemukan.');
        }

        $studentId = (int)$student['id'];

        // Ambil data berkas dan pastikan milik siswa yang bersangkutan
        $doc = $this->documentModel->find($docId);
        if (!$doc || (int)$doc['student_id'] !== $studentId) {
            return redirect()->to('pendaftar/dokumen')->with('error', 'Dokumen tidak ditemukan.');
        }

        if (($doc['academic_year'] ?? '') !== $this->academicYearService->activeYear()) {
            return redirect()->to('pendaftar/dokumen')->with('error', 'Dokumen arsip tahun pelajaran lain tidak dapat dihapus dari tahun aktif.');
        }

        // Hapus file fisik
        $physicalPath = $this->resolveDocumentPath($doc['file_path']);
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }

        // Hapus record di database
        if ($this->documentModel->delete($docId)) {
            // Perbarui status kesiapan Dapodik
            $dapodikService = new \App\Services\DapodikService();
            $dapodikService->updateDapodikStatus($studentId);

            return redirect()->to('pendaftar/dokumen')->with('success', 'Dokumen berhasil dihapus.');
        }

        return redirect()->to('pendaftar/dokumen')->with('error', 'Gagal menghapus dokumen.');
    }

    /**
     * Unduh/Cetak Bukti Pendaftaran (Formulir F-PD).
     */
    public function printBukti()
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Siswa tidak ditemukan.');
        }

        $studentId = (int)$student['id'];
        
        // Pastikan sudah finalisasi pendaftaran
        $academicYear = $this->academicYearService->activeYear();
        $registration = $this->registrationModel->findByUserId($userId, $academicYear);
        if (!$registration || $registration['status'] === 'draft') {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Anda harus menyelesaikan pendaftaran terlebih dahulu.');
        }

        $result = $this->exportService->exportToPdfFpd($studentId, $academicYear);

        if (!$result['success']) {
            return redirect()->to('pendaftar/dashboard')->with('error', $result['message']);
        }

        return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
    }

    /**
     * Unduh/Cetak Kartu Peserta SPMB.
     */
    public function printKartu()
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Siswa tidak ditemukan.');
        }

        $studentId = (int)$student['id'];
        
        // Pastikan sudah lolos verifikasi berkas (status verified atau accepted)
        $academicYear = $this->academicYearService->activeYear();
        $registration = $this->registrationModel->findByUserId($userId, $academicYear);
        if (!$registration || !in_array($registration['status'], ['verified', 'accepted'], true)) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Akses ditolak! Kartu Peserta hanya dapat diunduh jika berkas pendaftaran Anda telah disetujui (Lolos Verifikasi).');
        }

        $result = $this->exportService->exportToPdfKartu($studentId, $academicYear);

        if (!$result['success']) {
            return redirect()->to('pendaftar/dashboard')->with('error', $result['message']);
        }

        return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
    }

    /**
     * Unduh/Cetak Surat Keterangan Lulus (SKL) PDF.
     */
    public function printSkl()
    {
        $userId = (int)session()->get('user_id');
        $student = $this->studentModel->findByUserId($userId);

        if (!$student) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Siswa tidak ditemukan.');
        }

        $studentId = (int)$student['id'];
        
        // Pastikan status diterima (accepted)
        $academicYear = $this->academicYearService->activeYear();
        $registration = $this->registrationModel->findByUserId($userId, $academicYear);
        if (!$registration || $registration['status'] !== 'accepted') {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Akses ditolak! Surat Keterangan Lulus hanya tersedia jika Anda dinyatakan Diterima.');
        }

        $result = $this->exportService->exportToPdfSkl($studentId, $academicYear);

        if (!$result['success']) {
            return redirect()->to('pendaftar/dashboard')->with('error', $result['message']);
        }

        return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
    }

    private function isAllowedDocumentMime(array $extensions, string $mimeType): bool
    {
        $extensionMimeMap = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'pdf'  => 'application/pdf',
        ];

        $allowed = [];
        foreach ($extensions as $extension) {
            if (isset($extensionMimeMap[$extension])) {
                $allowed[] = $extensionMimeMap[$extension];
            }
        }

        return in_array($mimeType, array_unique($allowed), true);
    }

    private function resolveDocumentPath(string $storedPath): string
    {
        $candidate = realpath(WRITEPATH . ltrim($storedPath, '\\/'));
        $uploadsRoot = realpath(WRITEPATH . 'uploads');

        if ($candidate === false || $uploadsRoot === false || !str_starts_with($candidate, $uploadsRoot . DIRECTORY_SEPARATOR)) {
            return '';
        }

        return $candidate;
    }
}
