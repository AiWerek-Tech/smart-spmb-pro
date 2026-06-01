<?php

namespace App\Controllers\Pendaftar;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\StudentDocumentModel;
use App\Models\RegistrationModel;
use App\Services\ExportService;

class DocumentController extends BaseController
{
    protected StudentModel $studentModel;
    protected StudentDocumentModel $documentModel;
    protected RegistrationModel $registrationModel;
    protected ExportService $exportService;

    public function __construct()
    {
        $this->studentModel      = new StudentModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->registrationModel = new RegistrationModel();
        $this->exportService     = new ExportService();
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
        $documents = $this->documentModel->findByStudentId($studentId);

        $data = [
            'title'     => 'Unggah Dokumen Berkas',
            'student'   => $student,
            'documents' => $documents,
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

        $rules = [
            'document_type' => 'required|in_list[kk,akta,foto,raport,sertifikat,kip_kks]',
            'document_file' => 'uploaded[document_file]|is_image[document_file]|max_size[document_file,2048]',
        ];

        // Khususon untuk file raport/sertifikat yang bisa berformat PDF
        $docType = $this->request->getPost('document_type');
        if (in_array($docType, ['raport', 'sertifikat'], true)) {
            $rules['document_file'] = 'uploaded[document_file]|ext_in[document_file,pdf,jpg,jpeg,png]|max_size[document_file,2048]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Ukuran file melebihi batas maksimal 2 MB atau format file tidak diizinkan.');
        }

        $file = $this->request->getFile('document_file');
        if ($file->isValid() && !$file->hasMoved()) {
            if (!$this->isAllowedDocumentMime((string) $docType, (string) $file->getMimeType())) {
                return redirect()->back()->withInput()->with('error', 'Format file tidak sesuai dengan isi dokumen yang diunggah.');
            }

            $newName = $file->getRandomName();
            
            // Simpan ke storage writeable/uploads/documents/{user_id}/
            $subFolder = 'documents/' . $userId . '/';
            $targetDir = WRITEPATH . 'uploads/' . $subFolder;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if ($file->move($targetDir, $newName)) {
                $filePath = 'uploads/' . $subFolder . $newName;

                // Cek apakah dokumen sejenis sudah diunggah sebelumnya
                $existing = $this->documentModel->findByStudentAndType($studentId, $docType);
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
        $registration = $this->registrationModel->findByUserId($userId);
        if (!$registration || $registration['status'] === 'draft') {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Anda harus menyelesaikan pendaftaran terlebih dahulu.');
        }

        $result = $this->exportService->exportToPdfFpd($studentId);

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
        $registration = $this->registrationModel->findByUserId($userId);
        if (!$registration || !in_array($registration['status'], ['verified', 'accepted'], true)) {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Akses ditolak! Kartu Peserta hanya dapat diunduh jika berkas pendaftaran Anda telah disetujui (Lolos Verifikasi).');
        }

        $result = $this->exportService->exportToPdfKartu($studentId);

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
        $registration = $this->registrationModel->findByUserId($userId);
        if (!$registration || $registration['status'] !== 'accepted') {
            return redirect()->to('pendaftar/dashboard')->with('error', 'Akses ditolak! Surat Keterangan Lulus hanya tersedia jika Anda dinyatakan Diterima.');
        }

        $result = $this->exportService->exportToPdfSkl($studentId);

        if (!$result['success']) {
            return redirect()->to('pendaftar/dashboard')->with('error', $result['message']);
        }

        return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
    }

    private function isAllowedDocumentMime(string $documentType, string $mimeType): bool
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (in_array($documentType, ['raport', 'sertifikat'], true)) {
            $allowed[] = 'application/pdf';
        }

        return in_array($mimeType, $allowed, true);
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
