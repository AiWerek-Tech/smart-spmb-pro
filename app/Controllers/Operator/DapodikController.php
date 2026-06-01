<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentModel;
use App\Services\DapodikService;

class DapodikController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentModel $studentModel;
    protected DapodikService $dapodikService;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->studentModel      = new StudentModel();
        $this->dapodikService    = new DapodikService();
    }

    /**
     * Tampilkan laporan kelengkapan Dapodik pendaftar.
     */
    public function index()
    {
        $jalurId = $this->request->getGet('jalur');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $filters = [
            'jalur'  => !empty($jalurId) ? (int)$jalurId : null,
            'status' => !empty($status) ? $status : null,
            'search' => !empty($search) ? $search : null,
        ];

        // Saring pendaftar yang sudah submit
        $this->registrationModel->applyFilters($filters);
        $registrants = $this->registrationModel->whereNotIn('registrations.status', ['draft'])->findAll();

        $data = [
            'title'       => 'Validasi Dapodik Calon Siswa',
            'registrants' => $registrants,
            'jalurId'     => $jalurId,
            'status'      => $status,
            'search'      => $search,
        ];

        return view('operator/dapodik/index', $data);
    }

    /**
     * Tampilkan detail laporan validasi Dapodik untuk pendaftar tertentu.
     */
    public function show(int $registrationId)
    {
        $registration = $this->registrationModel->getRegistrationWithDetails($registrationId);

        if (!$registration) {
            return redirect()->to('operator/dapodik')->with('error', 'Pendaftar tidak ditemukan.');
        }

        $studentId = (int)$registration['student_id'];
        
        // Re-run status update to make sure database is perfectly in sync
        $this->dapodikService->updateDapodikStatus($studentId);
        
        // Get fresh details
        $registration = $this->registrationModel->getRegistrationWithDetails($registrationId);
        
        $missingFields = $this->dapodikService->getMissingFields($studentId);
        
        // Get detailed fields check for the UI checklist
        $student = $this->studentModel->find($studentId);
        $addressModel = new \App\Models\StudentAddressModel();
        $familyModel = new \App\Models\StudentFamilyModel();
        
        $address = $addressModel->findByStudentId($studentId) ?: [];
        $father = $familyModel->getFather($studentId) ?: [];
        $mother = $familyModel->getMother($studentId) ?: [];

        $fields = [
            ['label' => 'NIK (16 digit)', 'value' => $student['nik'] ?? null, 'is_filled' => !empty($student['nik'])],
            ['label' => 'NISN (10 digit)', 'value' => $student['nisn'] ?? null, 'is_filled' => !empty($student['nisn'])],
            ['label' => 'Agama', 'value' => $student['religion'] ?? null, 'is_filled' => !empty($student['religion'])],
            ['label' => 'Jenis Tempat Tinggal', 'value' => $address['residence_type'] ?? null, 'is_filled' => !empty($address['residence_type'])],
            ['label' => 'Moda Transportasi', 'value' => $address['transport_mode'] ?? null, 'is_filled' => !empty($address['transport_mode'])],
            ['label' => 'Jarak ke Sekolah (km)', 'value' => isset($address['distance_km']) ? $address['distance_km'] . ' km' : null, 'is_filled' => isset($address['distance_km']) && trim((string)$address['distance_km']) !== ''],
            ['label' => 'Pendidikan Ayah', 'value' => $father['education'] ?? null, 'is_filled' => !empty($father['education'])],
            ['label' => 'Pendidikan Ibu', 'value' => $mother['education'] ?? null, 'is_filled' => !empty($mother['education'])],
            ['label' => 'Pekerjaan Ayah', 'value' => $father['occupation'] ?? null, 'is_filled' => !empty($father['occupation'])],
            ['label' => 'Pekerjaan Ibu', 'value' => $mother['occupation'] ?? null, 'is_filled' => !empty($mother['occupation'])],
            ['label' => 'Kebutuhan Khusus', 'value' => $student['special_needs'] ?? null, 'is_filled' => !empty($student['special_needs'])],
        ];

        $data = [
            'title'         => 'Rincian Laporan Dapodik: ' . esc($registration['full_name']),
            'registration'  => $registration,
            'missingFields' => $missingFields,
            'fields'        => $fields,
        ];

        return view('operator/dapodik/show', $data);
    }
}
