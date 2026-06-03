<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AcademicYearModel;
use App\Services\AcademicYearService;

class AcademicYearController extends BaseController
{
    private AcademicYearModel $academicYearModel;
    private AcademicYearService $academicYearService;

    public function __construct()
    {
        $this->academicYearModel = new AcademicYearModel();
        $this->academicYearService = new AcademicYearService(academicYearModel: $this->academicYearModel);
    }

    public function index()
    {
        $years = $this->academicYearModel->ordered();
        $activeYear = $this->academicYearService->activeYear();
        $activeRow = null;
        $readyCount = 0;
        $archivedCount = 0;

        foreach ($years as $year) {
            if ((int) ($year['is_active'] ?? 0) === 1) {
                $activeRow = $year;
            }

            if ((int) ($year['is_archived'] ?? 0) === 1) {
                $archivedCount++;
            } else {
                $readyCount++;
            }
        }

        return view('admin/academic_years/index', [
            'title' => 'Tahun Pelajaran',
            'years' => $years,
            'activeYear' => $activeYear,
            'activeRow' => $activeRow,
            'yearSummary' => [
                'total' => count($years),
                'ready' => $readyCount,
                'archived' => $archivedCount,
            ],
        ]);
    }

    public function store()
    {
        $rules = [
            'year'      => 'required|max_length[9]|regex_match[/^[0-9]{4}\\/[0-9]{4}$/]|is_unique[academic_years.year]',
            'label'     => 'permit_empty|max_length[120]',
            'starts_at' => 'permit_empty|valid_date[Y-m-d]',
            'ends_at'   => 'permit_empty|valid_date[Y-m-d]',
            'notes'     => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi tahun pelajaran gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        $year = (string) $this->request->getPost('year');
        if (!$this->isSequentialAcademicYear($year)) {
            return redirect()->back()->withInput()->with('error', 'Tahun pelajaran harus berurutan, misalnya 2027/2028.');
        }

        $activate = (bool) $this->request->getPost('activate_now');

        $insertedId = $this->academicYearModel->insert([
            'year'        => $year,
            'label'       => $this->request->getPost('label') ?: 'Tahun Pelajaran ' . $year,
            'starts_at'   => $this->request->getPost('starts_at') ?: null,
            'ends_at'     => $this->request->getPost('ends_at') ?: null,
            'is_active'   => $activate ? 1 : 0,
            'is_archived' => 0,
            'notes'       => $this->request->getPost('notes'),
        ]);

        if (!$insertedId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan tahun pelajaran.');
        }

        if ($activate) {
            $this->academicYearService->activate($year);
        } elseif (!$this->academicYearModel->active()) {
            $this->academicYearService->activate($year);
        }

        return redirect()->to('admin/academic-years')->with('success', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function activate(int $id)
    {
        $year = $this->academicYearModel->find($id);
        if (!$year) {
            return redirect()->to('admin/academic-years')->with('error', 'Tahun pelajaran tidak ditemukan.');
        }

        $this->academicYearService->activate((string) $year['year']);

        return redirect()->to('admin/academic-years')->with('success', 'Tahun pelajaran aktif diperbarui ke ' . $year['year'] . '.');
    }

    public function update(int $id)
    {
        $year = $this->academicYearModel->find($id);
        if (!$year) {
            return redirect()->to('admin/academic-years')->with('error', 'Tahun pelajaran tidak ditemukan.');
        }

        $rules = [
            'label'     => 'permit_empty|max_length[120]',
            'starts_at' => 'permit_empty|valid_date[Y-m-d]',
            'ends_at'   => 'permit_empty|valid_date[Y-m-d]',
            'notes'     => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi pembaruan gagal: ' . implode(', ', $this->validator->getErrors()));
        }

        $this->academicYearModel->update($id, [
            'label'     => $this->request->getPost('label') ?: 'Tahun Pelajaran ' . $year['year'],
            'starts_at' => $this->request->getPost('starts_at') ?: null,
            'ends_at'   => $this->request->getPost('ends_at') ?: null,
            'notes'     => $this->request->getPost('notes'),
        ]);

        return redirect()->to('admin/academic-years')->with('success', 'Tahun pelajaran berhasil diperbarui.');
    }

    public function archive(int $id)
    {
        $year = $this->academicYearModel->find($id);
        if (!$year) {
            return redirect()->to('admin/academic-years')->with('error', 'Tahun pelajaran tidak ditemukan.');
        }

        if ((int) $year['is_active'] === 1) {
            return redirect()->to('admin/academic-years')->with('error', 'Tahun aktif tidak dapat diarsipkan. Aktifkan tahun lain terlebih dahulu.');
        }

        $this->academicYearModel->update($id, ['is_archived' => (int) !$year['is_archived']]);

        return redirect()->to('admin/academic-years')->with('success', 'Status arsip tahun pelajaran diperbarui.');
    }

    public function delete(int $id)
    {
        $year = $this->academicYearModel->find($id);
        if (!$year) {
            return redirect()->to('admin/academic-years')->with('error', 'Tahun pelajaran tidak ditemukan.');
        }

        if ($this->academicYearModel->countAll() <= 1) {
            return redirect()->to('admin/academic-years')->with('error', 'Minimal harus ada satu tahun pelajaran di sistem.');
        }

        $wasActive = (int) ($year['is_active'] ?? 0) === 1;
        if (!$this->academicYearModel->delete($id)) {
            return redirect()->to('admin/academic-years')->with('error', 'Gagal menghapus tahun pelajaran.');
        }

        if ($wasActive || !$this->academicYearModel->active()) {
            $fallback = $this->academicYearModel
                ->where('is_archived', 0)
                ->orderBy('year', 'DESC')
                ->first();

            if (!$fallback) {
                $fallback = $this->academicYearModel
                    ->orderBy('year', 'DESC')
                    ->first();
            }

            if ($fallback && !empty($fallback['year'])) {
                $this->academicYearService->activate((string) $fallback['year']);
            }
        }

        return redirect()->to('admin/academic-years')->with('success', 'Tahun pelajaran berhasil dihapus.');
    }

    private function isSequentialAcademicYear(string $year): bool
    {
        if (!preg_match('/^([0-9]{4})\\/([0-9]{4})$/', $year, $matches)) {
            return false;
        }

        return (int) $matches[2] === (int) $matches[1] + 1;
    }
}
