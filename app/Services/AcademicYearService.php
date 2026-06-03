<?php

namespace App\Services;

use App\Models\AcademicYearModel;
use App\Models\SettingModel;

class AcademicYearService
{
    public function __construct(
        private ?SettingModel $settingModel = null,
        private ?AcademicYearModel $academicYearModel = null
    ) {
        $this->settingModel ??= new SettingModel();
        $this->academicYearModel ??= new AcademicYearModel();
    }

    public function activeYear(): string
    {
        $active = $this->academicYearModel->active();
        if ($active && !empty($active['year'])) {
            return (string) $active['year'];
        }

        return (string) $this->settingModel->getValue('academic_year', '2026/2027');
    }

    public function pathSegment(?string $year = null): string
    {
        $year ??= $this->activeYear();

        return preg_replace('/[^0-9A-Za-z_-]+/', '-', str_replace('/', '-', $year)) ?: 'active';
    }

    public function activate(string $year): void
    {
        $this->academicYearModel->builder()->where('id >', 0)->set('is_active', 0)->update();
        $row = $this->academicYearModel->where('year', $year)->first();

        if ($row) {
            $this->academicYearModel->update($row['id'], ['is_active' => 1, 'is_archived' => 0]);
        }

        $this->settingModel->setValue('academic_year', $year);
    }
}
