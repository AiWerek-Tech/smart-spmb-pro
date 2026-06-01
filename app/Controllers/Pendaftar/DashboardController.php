<?php

namespace App\Controllers\Pendaftar;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentModel;
use App\Models\SettingModel;
use App\Models\StudentDocumentModel;

class DashboardController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentModel $studentModel;
    protected SettingModel $settingModel;
    protected StudentDocumentModel $documentModel;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->studentModel      = new StudentModel();
        $this->settingModel      = new SettingModel();
        $this->documentModel     = new StudentDocumentModel();
    }

    /**
     * Tampilkan dashboard pendaftar.
     */
    public function index()
    {
        $userId = (int)session()->get('user_id');
        $academicYear = $this->settingModel->getValue('academic_year', '2026/2027');

        // Check if student profile exists; if not, create a default draft to prevent exceptions
        $student = $this->studentModel->findByUserId($userId);
        if (!$student) {
            $studentId = $this->studentModel->insert([
                'user_id'       => $userId,
                'full_name'     => session()->get('user_name') ?: 'Pendaftar Baru',
                'gender'        => 'L',
                'birth_place'   => 'Jakarta',
                'birth_date'    => '2012-01-01',
                'religion'      => 'Islam',
                'citizenship'   => 'WNI',
                'family_status' => 'Anak Kandung',
                'nik'           => '3200000000000000',
            ]);
            $student = $this->studentModel->find($studentId);
        }

        $studentId = (int)$student['id'];

        // Get registration info if already finalized
        $registration = $this->registrationModel->findByUserId($userId);

        $registrationDetails = [];
        if ($registration) {
            $registrationDetails = $this->registrationModel->getRegistrationWithDetails((int)$registration['id']);
        }

        // Gather document checklist count
        $uploadedDocs = $this->documentModel->findByStudentId($studentId);
        $mandatoryApprovedCount = 0;
        foreach ($uploadedDocs as $doc) {
            if (in_array($doc['document_type'], ['kk', 'akta', 'foto'], true) && $doc['status'] === 'approved') {
                $mandatoryApprovedCount++;
            }
        }

        $data = [
            'title'               => 'Panel Calon Siswa',
            'student'             => $student,
            'registration'        => $registrationDetails ?: $registration,
            'academicYear'        => $academicYear,
            'mandatoryApproved'   => $mandatoryApprovedCount,
            'totalUploaded'       => count($uploadedDocs),
            'breadcrumbs'  => [
                ['title' => 'Calon Siswa', 'url' => base_url('pendaftar/dashboard')],
                ['title' => 'Dashboard', 'url' => base_url('pendaftar/dashboard')],
            ],
        ];

        return view('pendaftar/dashboard', $data);
    }
}
