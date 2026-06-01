<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\BackupService;

class BackupController extends BaseController
{
    protected BackupService $backupService;

    public function __construct()
    {
        $this->backupService = new BackupService();
    }

    /**
     * Tampilkan halaman pengelolaan Backup & Restore.
     */
    public function index()
    {
        $data = [
            'title' => 'Backup & Restore Database',
        ];

        return view('admin/backup/index', $data);
    }

    /**
     * Buat backup database dan unduh langsung.
     */
    public function create()
    {
        $sqlContent = $this->backupService->generateBackup();
        $fileName = 'backup_spmb_' . date('Y-m-d_H-i-s') . '.sql';

        // Kembalikan response download file SQL
        return $this->response
            ->setHeader('Content-Type', 'application/sql')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($sqlContent);
    }

    /**
     * Pulihkan database dari file SQL yang diunggah.
     */
    public function restore()
    {
        $confirm = $this->request->getPost('confirm');
        if ($confirm !== '1') {
            return redirect()->back()->with('error', 'Anda harus memberikan konfirmasi sebelum memulihkan database.');
        }

        $rules = [
            'backup_file' => 'uploaded[backup_file]|ext_in[backup_file,sql]|max_size[backup_file,10240]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'File backup tidak valid. Harus berupa file SQL dengan ukuran maksimal 10 MB.');
        }

        $file = $this->request->getFile('backup_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $sqlContent = file_get_contents($file->getTempName());

            $result = $this->backupService->restoreBackup($sqlContent);

            if ($result['success']) {
                return redirect()->to('admin/backup')->with('success', $result['message']);
            } else {
                return redirect()->to('admin/backup')->with('error', $result['message']);
            }
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file.');
    }
}
