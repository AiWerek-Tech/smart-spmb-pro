<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentDocumentModel;

class DocumentController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentDocumentModel $documentModel;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->documentModel     = new StudentDocumentModel();
    }

    /**
     * Tampilkan halaman daftar dokumen calon peserta didik untuk diverifikasi.
     */
    public function index(int $registrationId)
    {
        $registration = $this->registrationModel->getRegistrationWithDetails($registrationId);

        if (!$registration) {
            return redirect()->to('operator/registrants')->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $documents = $this->documentModel->findByStudentId((int)$registration['student_id']);

        $data = [
            'title'        => 'Verifikasi Dokumen: ' . esc($registration['full_name']),
            'registration' => $registration,
            'documents'    => $documents,
        ];

        return view('operator/documents/index', $data);
    }

    /**
     * Verifikasi dokumen (Setujui / Tolak).
     */
    public function verify(int $registrationId)
    {
        $registration = $this->registrationModel->find($registrationId);
        if (!$registration) {
            return redirect()->to('operator/registrants')->with('error', 'Pendaftaran tidak ditemukan.');
        }

        $rules = [
            'document_id' => 'required|integer',
            'status'      => 'required|in_list[approved,rejected]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Parameter verifikasi tidak valid.');
        }

        $docId = (int)$this->request->getPost('document_id');
        $status = $this->request->getPost('status');
        $rejectionReason = $this->request->getPost('rejection_reason');
        $doc = $this->documentModel->find($docId);

        if (!$doc || (int) $doc['student_id'] !== (int) $registration['student_id']) {
            return redirect()->back()->with('error', 'Dokumen tidak sesuai dengan data pendaftar yang sedang diverifikasi.');
        }

        // Validasi: Jika ditolak, alasan penolakan wajib diisi (Req 14)
        if ($status === 'rejected' && empty(trim($rejectionReason))) {
            return redirect()->back()->with('error', 'Gagal memproses! Alasan penolakan berkas wajib diisi jika berkas ditolak.');
        }

        $operatorId = (int)session()->get('user_id');

        if ($this->documentModel->verifyDocument($docId, $status, $rejectionReason ?: null, $operatorId)) {
            
            // Cek apakah seluruh berkas wajib telah terverifikasi dan ubah status pendaftaran ke verified jika ya
            $studentId = (int)$registration['student_id'];
            $allDocs = $this->documentModel->findByStudentId($studentId);
            
            $allApproved = true;
            $requiredUploaded = 0;
            
            foreach ($allDocs as $doc) {
                if (in_array($doc['document_type'], ['kk', 'akta', 'foto'], true)) {
                    $requiredUploaded++;
                    if ($doc['status'] !== 'approved') {
                        $allApproved = false;
                    }
                }
            }

            // Jika seluruh 3 berkas wajib (KK, akta, foto) disetujui, ubah status pendaftaran ke verified otomatis
            if ($requiredUploaded === 3 && $allApproved) {
                $this->registrationModel->updateStatus($registrationId, 'verified');
            } else {
                // Jika ada berkas wajib yang ditolak atau di-revert, kembalikan ke submitted
                if ($registration['status'] === 'verified') {
                    $this->registrationModel->updateStatus($registrationId, 'submitted');
                }
            }

            // Kirim notifikasi WhatsApp ke calon siswa terkait status dokumen
            try {
                $studentModel = new \App\Models\StudentModel();
                $student = $studentModel->find($studentId);
                if ($student) {
                    $contactModel = new \App\Models\StudentContactModel();
                    $contact = $contactModel->findByStudentId($studentId);
                    if ($contact && !empty($contact['phone'])) {
                        $whatsappService = new \App\Services\WhatsappService();
                        $docTypeNames = [
                            'kk' => 'Kartu Keluarga',
                            'akta' => 'Akta Kelahiran',
                            'foto' => 'Pas Foto',
                            'raport' => 'Raport',
                            'sertifikat' => 'Sertifikat Prestasi',
                            'kip_kks' => 'Kartu KIP/KKS'
                        ];
                        $docName = $docTypeNames[$doc['document_type'] ?? ''] ?? 'Dokumen';
                        
                        if ($status === 'approved') {
                            $message = sprintf(
                                "Halo %s, berkas \"%s\" Anda telah disetujui oleh operator. Terima kasih.",
                                $student['full_name'],
                                $docName
                            );
                        } else {
                            $message = sprintf(
                                "Halo %s, berkas \"%s\" Anda ditolak oleh operator karena: %s. Silakan unggah kembali berkas yang valid.",
                                $student['full_name'],
                                $docName,
                                $rejectionReason
                            );
                        }
                        $whatsappService->sendNotification($contact['phone'], $message);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Failed to send WhatsApp notification on document verify: ' . $e->getMessage());
            }

            return redirect()->to('operator/documents/' . $registrationId)->with('success', 'Dokumen berhasil diverifikasi.');
        }

        return redirect()->back()->with('error', 'Gagal memverifikasi dokumen.');
    }

    /**
     * Tampilkan/Stream file dokumen secara aman dari server.
     */
    public function view(int $docId)
    {
        $doc = $this->documentModel->find($docId);

        if (!$doc) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $filePath = $this->resolveDocumentPath((string) $doc['file_path']);

        if (!file_exists($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File fisik dokumen tidak ditemukan di server.');
        }

        $mimeType = $this->safeMimeType((string) ($doc['mime_type'] ?: mime_content_type($filePath)));
        $fileName = str_replace(['"', "\r", "\n"], '', (string) $doc['file_name']);

        // Stream file ke browser
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->setBody(file_get_contents($filePath));
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

    private function safeMimeType(string $mimeType): string
    {
        $allowed = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
        ];

        return in_array($mimeType, $allowed, true) ? $mimeType : 'application/octet-stream';
    }
}
