<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Services\ExportService;
use App\Models\RegistrationModel;

class ExportController extends BaseController
{
    protected ExportService $exportService;
    protected RegistrationModel $registrationModel;

    public function __construct()
    {
        $this->exportService     = new ExportService();
        $this->registrationModel = new RegistrationModel();
    }

    /**
     * Ekspor seluruh pendaftar yang disubmit ke Excel (.xlsx).
     */
    public function excel()
    {
        $jalurId = $this->request->getGet('jalur');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $filters = [
            'jalur'  => !empty($jalurId) ? (int)$jalurId : null,
            'status' => !empty($status) ? $status : null,
            'search' => !empty($search) ? $search : null,
        ];

        $result = $this->exportService->exportToExcel($filters);

        if (!$result['success']) {
            return redirect()->to('operator/registrants')->with('error', $result['message']);
        }

        // Return direct download response of excel
        return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
    }

    /**
     * Ekspor data satu pendaftar ke PDF F-PD (Dapodik-Ready).
     */
    public function fpd(int $registrationId)
    {
        $registration = $this->registrationModel->find($registrationId);

        if (!$registration) {
            return redirect()->to('operator/registrants')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $studentId = (int)$registration['student_id'];
        $result = $this->exportService->exportToPdfFpd($studentId);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        // Return direct stream download response of FPD PDF
        return $this->response->download($result['file_path'], null)->setFileName($result['filename']);
    }
}
