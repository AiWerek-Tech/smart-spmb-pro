<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentRequirementModel;
use App\Models\JalurModel;
use App\Services\AcademicYearService;

class DocumentRequirementController extends BaseController
{
    private DocumentRequirementModel $requirementModel;
    private JalurModel $jalurModel;
    private AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->requirementModel = new DocumentRequirementModel();
        $this->jalurModel = new JalurModel();
        $this->academicYearService = new AcademicYearService();
    }

    public function index()
    {
        $activeYear = $this->academicYearService->activeYear();
        $jalurId = $this->request->getGet('jalur');
        $selectedJalurId = $jalurId !== null && $jalurId !== '' ? (int) $jalurId : null;

        return view('admin/document_requirements/index', [
            'title'          => 'Syarat Dokumen',
            'activeYear'     => $activeYear,
            'jalurOptions'   => $this->jalurModel->getActiveJalur(),
            'selectedJalurId'=> $selectedJalurId,
            'requirements'   => $this->requirementModel->scopedList($activeYear, $selectedJalurId),
        ]);
    }

    public function store()
    {
        $activeYear = $this->academicYearService->activeYear();
        $jalurId = $this->request->getPost('jalur_id');
        $jalurId = $jalurId !== null && $jalurId !== '' ? (int) $jalurId : null;

        $rules = [
            'document_type'      => 'required|alpha_dash|max_length[60]',
            'label'              => 'required|max_length[150]',
            'allowed_extensions' => 'required|max_length[120]',
            'max_size_kb'        => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $documentType = strtolower((string) $this->request->getPost('document_type'));
        $exists = $this->requirementModel
            ->where('academic_year', $activeYear)
            ->where('jalur_id', $jalurId)
            ->where('document_type', $documentType)
            ->first();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Jenis dokumen tersebut sudah ada pada scope ini.');
        }

        $this->requirementModel->insert($this->payload($activeYear, $jalurId, $documentType));

        return redirect()->to('admin/document-requirements' . ($jalurId ? '?jalur=' . $jalurId : ''))->with('success', 'Syarat dokumen berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $requirement = $this->requirementModel->find($id);
        if (!$requirement) {
            return redirect()->to('admin/document-requirements')->with('error', 'Syarat dokumen tidak ditemukan.');
        }

        $rules = [
            'label'              => 'required|max_length[150]',
            'allowed_extensions' => 'required|max_length[120]',
            'max_size_kb'        => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->requirementModel->update($id, [
            'label'                 => $this->request->getPost('label'),
            'description'           => $this->request->getPost('description'),
            'is_required'           => $this->request->getPost('is_required') !== null ? 1 : 0,
            'allowed_extensions'    => strtolower((string) $this->request->getPost('allowed_extensions')),
            'max_size_kb'           => (int) $this->request->getPost('max_size_kb'),
            'requires_verification' => $this->request->getPost('requires_verification') !== null ? 1 : 0,
            'is_active'             => $this->request->getPost('is_active') !== null ? 1 : 0,
            'sort_order'            => (int) ($this->request->getPost('sort_order') ?: 100),
        ]);

        $jalurId = $requirement['jalur_id'] ?? null;

        return redirect()->to('admin/document-requirements' . ($jalurId ? '?jalur=' . $jalurId : ''))->with('success', 'Syarat dokumen berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $requirement = $this->requirementModel->find($id);
        if (!$requirement) {
            return redirect()->to('admin/document-requirements')->with('error', 'Syarat dokumen tidak ditemukan.');
        }

        $this->requirementModel->delete($id);
        $jalurId = $requirement['jalur_id'] ?? null;

        return redirect()->to('admin/document-requirements' . ($jalurId ? '?jalur=' . $jalurId : ''))->with('success', 'Syarat dokumen berhasil dihapus.');
    }

    private function payload(string $activeYear, ?int $jalurId, string $documentType): array
    {
        return [
            'academic_year'         => $activeYear,
            'jalur_id'              => $jalurId,
            'document_type'         => $documentType,
            'label'                 => $this->request->getPost('label'),
            'description'           => $this->request->getPost('description'),
            'is_required'           => $this->request->getPost('is_required') !== null ? 1 : 0,
            'allowed_extensions'    => strtolower((string) $this->request->getPost('allowed_extensions')),
            'max_size_kb'           => (int) $this->request->getPost('max_size_kb'),
            'requires_verification' => $this->request->getPost('requires_verification') !== null ? 1 : 0,
            'is_active'             => 1,
            'sort_order'            => (int) ($this->request->getPost('sort_order') ?: 100),
        ];
    }
}
