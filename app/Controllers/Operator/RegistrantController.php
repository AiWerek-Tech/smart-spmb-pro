<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Models\RegistrationModel;
use App\Models\StudentModel;
use App\Models\StudentAddressModel;
use App\Models\StudentContactModel;
use App\Models\StudentFamilyModel;
use App\Models\StudentPeriodicModel;
use App\Models\StudentAchievementModel;
use App\Models\StudentDocumentModel;
use App\Models\JalurModel;
use App\Services\AcademicYearService;

class RegistrantController extends BaseController
{
    protected RegistrationModel $registrationModel;
    protected StudentModel $studentModel;
    protected StudentAddressModel $addressModel;
    protected StudentContactModel $contactModel;
    protected StudentFamilyModel $familyModel;
    protected StudentPeriodicModel $periodicModel;
    protected StudentAchievementModel $achievementModel;
    protected StudentDocumentModel $documentModel;
    protected JalurModel $jalurModel;
    protected AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->registrationModel = new RegistrationModel();
        $this->studentModel      = new StudentModel();
        $this->addressModel      = new StudentAddressModel();
        $this->contactModel      = new StudentContactModel();
        $this->familyModel       = new StudentFamilyModel();
        $this->periodicModel     = new StudentPeriodicModel();
        $this->achievementModel  = new StudentAchievementModel();
        $this->documentModel     = new StudentDocumentModel();
        $this->jalurModel        = new JalurModel();
        $this->academicYearService = new AcademicYearService();
    }

    /**
     * Tampilkan daftar seluruh pendaftar.
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

        $activeYear = $this->academicYearService->activeYear();

        $this->registrationModel->applyFilters($filters);
        $registrants = $this->registrationModel
            ->where('registrations.academic_year', $activeYear)
            ->whereNotIn('registrations.status', ['draft'])
            ->findAll();

        $jalur = $this->jalurModel->findAll();

        $data = [
            'title'       => 'Daftar Calon Peserta Didik Baru',
            'registrants' => $registrants,
            'jalur'       => $jalur,
            'jalurId'     => $jalurId,
            'status'      => $status,
            'search'      => $search,
            'activeYear'  => $activeYear,
        ];

        return view('operator/registrants/index', $data);
    }

    /**
     * Detail pendaftar (Profil F-PD Lengkap).
     */
    public function show(int $registrationId)
    {
        $registration = $this->registrationModel->getRegistrationWithDetails(
            $registrationId,
            $this->academicYearService->activeYear()
        );

        if (!$registration) {
            return redirect()->to('operator/registrants')->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $studentId = $registration['student_id'];

        $address      = $this->addressModel->findByStudentId($studentId);
        $contact      = $this->contactModel->findByStudentId($studentId);
        $father       = $this->familyModel->findByStudentAndType($studentId, 'ayah');
        $mother       = $this->familyModel->findByStudentAndType($studentId, 'ibu');
        $guardian     = $this->familyModel->findByStudentAndType($studentId, 'wali');
        $periodic     = $this->periodicModel->findByStudentId($studentId);
        $achievements = $this->achievementModel->findByStudentId($studentId);
        $documents    = $this->documentModel->findByStudentId($studentId, (string) $registration['academic_year']);

        $data = [
            'title'        => 'Detail Profil: ' . esc($registration['full_name']),
            'registration' => $registration,
            'address'      => $address ?: [],
            'contact'      => $contact ?: [],
            'father'       => $father ?: [],
            'mother'       => $mother ?: [],
            'guardian'     => $guardian ?: [],
            'periodic'     => $periodic ?: [],
            'achievements' => $achievements ?: [],
            'documents'    => $documents ?: [],
        ];

        return view('operator/registrants/show', $data);
    }

    /**
     * Formulir edit data pendaftar (mengoreksi kesalahan data F-PD).
     */
    public function edit(int $registrationId)
    {
        $registration = $this->registrationModel->getRegistrationWithDetails(
            $registrationId,
            $this->academicYearService->activeYear()
        );

        if (!$registration) {
            return redirect()->to('operator/registrants')->with('error', 'Data pendaftar tidak ditemukan.');
        }

        $studentId = $registration['student_id'];

        $address  = $this->addressModel->findByStudentId($studentId);
        $contact  = $this->contactModel->findByStudentId($studentId);
        $father   = $this->familyModel->findByStudentAndType($studentId, 'ayah');
        $mother   = $this->familyModel->findByStudentAndType($studentId, 'ibu');
        $guardian = $this->familyModel->findByStudentAndType($studentId, 'wali');
        $periodic = $this->periodicModel->findByStudentId($studentId);

        $data = [
            'title'        => 'Edit Profil Pendaftar: ' . esc($registration['full_name']),
            'registration' => $registration,
            'address'      => $address ?: [],
            'contact'      => $contact ?: [],
            'father'       => $father ?: [],
            'mother'       => $mother ?: [],
            'guardian'     => $guardian ?: [],
            'periodic'     => $periodic ?: [],
        ];

        return view('operator/registrants/edit', $data);
    }

    /**
     * Simpan pembaruan koreksi data pendaftar.
     */
    public function update(int $registrationId)
    {
        $registration = $this->registrationModel
            ->where('academic_year', $this->academicYearService->activeYear())
            ->find($registrationId);

        if (!$registration) {
            return redirect()->to('operator/registrants')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $studentId = (int)$registration['student_id'];

        // Rules check
        $rules = [
            'full_name'   => 'required|min_length[3]|max_length[100]',
            'gender'      => 'required|in_list[L,P]',
            'birth_place' => 'required',
            'birth_date'  => 'required|valid_date',
            'religion'    => 'required|in_list[Islam,Kristen,Katolik,Hindu,Buddha,Konghucu]',
            'nik'         => 'required|exact_length[16]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 1. Update Student Table
        $this->studentModel->update($studentId, [
            'full_name'     => $this->request->getPost('full_name'),
            'gender'        => $this->request->getPost('gender'),
            'birth_place'   => $this->request->getPost('birth_place'),
            'birth_date'    => $this->request->getPost('birth_date'),
            'religion'      => $this->request->getPost('religion'),
            'citizenship'   => $this->request->getPost('citizenship') ?: 'WNI',
            'family_status' => $this->request->getPost('family_status') ?: '',
            'nik'           => $this->request->getPost('nik'),
            'nisn'          => $this->request->getPost('nisn') ?: null,
            'birth_cert_number' => $this->request->getPost('birth_cert_number') ?: null,
            'special_needs' => $this->request->getPost('special_needs') ?: 'Tidak Ada',
        ]);

        // 2. Update Student Address
        $addressData = [
            'student_id'     => $studentId,
            'street_address' => $this->request->getPost('street_address') ?: '',
            'rt'             => $this->request->getPost('rt') ?: null,
            'rw'             => $this->request->getPost('rw') ?: null,
            'hamlet'         => $this->request->getPost('hamlet') ?: '',
            'village'        => $this->request->getPost('village') ?: '',
            'subdistrict'    => $this->request->getPost('subdistrict') ?: '',
            'district'       => $this->request->getPost('district') ?: '',
            'province'       => $this->request->getPost('province') ?: '',
            'postal_code'    => $this->request->getPost('postal_code') ?: '',
            'residence_type' => $this->request->getPost('residence_type') ?: '',
            'distance_km'    => floatval($this->request->getPost('distance_km') ?: 0),
            'transport_mode' => $this->request->getPost('transport_mode') ?: '',
        ];
        $existingAddress = $this->addressModel->findByStudentId($studentId);
        if ($existingAddress) {
            $this->addressModel->update($existingAddress['id'], $addressData);
        } else {
            $this->addressModel->insert($addressData);
        }

        // 3. Update Contact info
        $contactData = [
            'student_id'   => $studentId,
            'phone_number' => $this->request->getPost('phone_number') ?: '',
            'email'        => $this->request->getPost('email') ?: '',
        ];
        $existingContact = $this->contactModel->findByStudentId($studentId);
        if ($existingContact) {
            $this->contactModel->update($existingContact['id'], $contactData);
        } else {
            $this->contactModel->insert($contactData);
        }

        // 4. Update Father Family Info
        $fatherData = [
            'student_id'   => $studentId,
            'family_type'  => 'ayah',
            'full_name'    => $this->request->getPost('father_name') ?: '',
            'nik'          => $this->request->getPost('father_nik') ?: null,
            'birth_date'   => $this->request->getPost('father_birth_date') ?: null,
            'education'    => $this->request->getPost('father_education') ?: null,
            'occupation'   => $this->request->getPost('father_occupation') ?: null,
            'income'       => $this->request->getPost('father_income') ?: null,
        ];
        $existingFather = $this->familyModel->findByStudentAndType($studentId, 'ayah');
        if ($existingFather) {
            $this->familyModel->update($existingFather['id'], $fatherData);
        } else {
            $this->familyModel->insert($fatherData);
        }

        // 5. Update Mother Family Info
        $motherData = [
            'student_id'   => $studentId,
            'family_type'  => 'ibu',
            'full_name'    => $this->request->getPost('mother_name') ?: '',
            'nik'          => $this->request->getPost('mother_nik') ?: null,
            'birth_date'   => $this->request->getPost('mother_birth_date') ?: null,
            'education'    => $this->request->getPost('mother_education') ?: null,
            'occupation'   => $this->request->getPost('mother_occupation') ?: null,
            'income'       => $this->request->getPost('mother_income') ?: null,
        ];
        $existingMother = $this->familyModel->findByStudentAndType($studentId, 'ibu');
        if ($existingMother) {
            $this->familyModel->update($existingMother['id'], $motherData);
        } else {
            $this->familyModel->insert($motherData);
        }

        // 6. Update Periodic Info
        $periodicData = [
            'student_id' => $studentId,
            'height_cm'  => $this->request->getPost('height_cm') ?: null,
            'weight_kg'  => $this->request->getPost('weight_kg') ?: null,
            'kip_number' => $this->request->getPost('kip_number') ?: null,
            'kks_number' => $this->request->getPost('kks_number') ?: null,
            'pkh_number' => $this->request->getPost('pkh_number') ?: null,
        ];
        $existingPeriodic = $this->periodicModel->findByStudentId($studentId);
        if ($existingPeriodic) {
            $this->periodicModel->update($existingPeriodic['id'], $periodicData);
        } else {
            $this->periodicModel->insert($periodicData);
        }

        // Re-run Dapodik validation completeness checks
        $dapodikService = new \App\Services\DapodikService();
        $dapodikService->updateDapodikStatus($studentId);

        return redirect()->to('operator/registrants/'.$registrationId)->with('success', 'Profil pendaftar berhasil dikoreksi.');
    }

    /**
     * Override akses formulir pendaftaran.
     */
    public function toggleOverride(int $registrationId)
    {
        $registration = $this->registrationModel->find($registrationId);
        if (!$registration) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        $student = $this->studentModel->find($registration['student_id']);
        if (!$student) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $newOverride = (int)($student['form_override'] ?? 0) === 1 ? 0 : 1;
        
        $this->studentModel->update($student['id'], [
            'form_override' => $newOverride
        ]);

        // Record audit log
        $auditLogService = new \App\Services\AuditLogService();
        $auditLogService->record('operator', 'TOGGLE_FORM_OVERRIDE', [
            'entity_type' => 'students',
            'entity_id'   => $student['id'],
            'old_data'    => ['form_override' => $student['form_override'] ?? 0],
            'new_data'    => ['form_override' => $newOverride],
        ]);

        $message = $newOverride === 1 
            ? 'Akses override formulir berhasil diaktifkan. Siswa dapat mengisi formulir tanpa lunas biaya pendaftaran.' 
            : 'Akses override formulir berhasil dinonaktifkan.';

        return redirect()->back()->with('success', $message);
    }
}

