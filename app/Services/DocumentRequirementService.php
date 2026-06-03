<?php

namespace App\Services;

use App\Models\DocumentRequirementModel;
use App\Models\StudentDocumentModel;

class DocumentRequirementService
{
    public const DEFAULTS = [
        'kk' => ['document_type' => 'kk', 'label' => 'Kartu Keluarga (KK)', 'is_required' => 1, 'allowed_extensions' => 'jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'description' => null, 'sort_order' => 10],
        'akta' => ['document_type' => 'akta', 'label' => 'Akta Kelahiran', 'is_required' => 1, 'allowed_extensions' => 'jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'description' => null, 'sort_order' => 20],
        'foto' => ['document_type' => 'foto', 'label' => 'Pas Foto 3x4 Calon Siswa', 'is_required' => 1, 'allowed_extensions' => 'jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'description' => null, 'sort_order' => 30],
        'raport' => ['document_type' => 'raport', 'label' => 'Raport Terakhir', 'is_required' => 0, 'allowed_extensions' => 'pdf,jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'description' => null, 'sort_order' => 40],
        'sertifikat' => ['document_type' => 'sertifikat', 'label' => 'Sertifikat Prestasi', 'is_required' => 0, 'allowed_extensions' => 'pdf,jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'description' => null, 'sort_order' => 50],
        'kip_kks' => ['document_type' => 'kip_kks', 'label' => 'KIP / KKS Pendukung', 'is_required' => 0, 'allowed_extensions' => 'pdf,jpg,jpeg,png,webp', 'max_size_kb' => 2048, 'requires_verification' => 1, 'description' => null, 'sort_order' => 60],
    ];

    public function __construct(private ?DocumentRequirementModel $requirementModel = null)
    {
        $this->requirementModel ??= new DocumentRequirementModel();
    }

    public function requirements(string $academicYear, ?int $jalurId = null): array
    {
        $rows = $this->requirementModel->activeForYear($academicYear, $jalurId);
        if ($rows === []) {
            return array_values(self::DEFAULTS);
        }

        usort($rows, static function ($a, $b): int {
            $scopeA = empty($a['jalur_id']) ? 0 : 1;
            $scopeB = empty($b['jalur_id']) ? 0 : 1;

            return ($scopeA <=> $scopeB)
                ?: (((int) ($a['sort_order'] ?? 100)) <=> ((int) ($b['sort_order'] ?? 100)));
        });

        $merged = [];
        foreach ($rows as $row) {
            $type = (string) $row['document_type'];
            $merged[$type] = $row;
        }

        uasort($merged, fn ($a, $b) => ((int) ($a['sort_order'] ?? 100)) <=> ((int) ($b['sort_order'] ?? 100)));

        return array_values($merged);
    }

    public function requirementsForUpload(string $academicYear, ?int $jalurId = null): array
    {
        if ($jalurId !== null) {
            return $this->requirements($academicYear, $jalurId);
        }

        $rows = $this->requirementModel
            ->where('academic_year', $academicYear)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        if ($rows === []) {
            return array_values(self::DEFAULTS);
        }

        $merged = [];
        foreach ($rows as $row) {
            $type = (string) $row['document_type'];
            if (!isset($merged[$type])) {
                $merged[$type] = $row;
                continue;
            }

            $merged[$type]['is_required'] = max((int) $merged[$type]['is_required'], (int) $row['is_required']);
            $merged[$type]['max_size_kb'] = max((int) $merged[$type]['max_size_kb'], (int) $row['max_size_kb']);
            $merged[$type]['allowed_extensions'] = $this->mergeExtensions(
                (string) ($merged[$type]['allowed_extensions'] ?? ''),
                (string) ($row['allowed_extensions'] ?? '')
            );
        }

        uasort($merged, fn ($a, $b) => ((int) ($a['sort_order'] ?? 100)) <=> ((int) ($b['sort_order'] ?? 100)));

        return array_values($merged);
    }

    public function requiredTypes(string $academicYear, ?int $jalurId = null): array
    {
        return array_values(array_map(
            fn ($row) => $row['document_type'],
            array_filter($this->requirements($academicYear, $jalurId), fn ($row) => (int) ($row['is_required'] ?? 0) === 1)
        ));
    }

    public function allTypes(string $academicYear, ?int $jalurId = null): array
    {
        return array_column($this->requirements($academicYear, $jalurId), 'document_type');
    }

    public function labels(string $academicYear, ?int $jalurId = null): array
    {
        $labels = [];
        foreach ($this->requirements($academicYear, $jalurId) as $row) {
            $labels[$row['document_type']] = $row['label'];
        }

        return $labels;
    }

    public function definition(string $academicYear, ?int $jalurId, string $documentType): ?array
    {
        foreach ($this->requirements($academicYear, $jalurId) as $row) {
            if ($row['document_type'] === $documentType) {
                return $row;
            }
        }

        return null;
    }

    public function uploadDefinition(string $academicYear, ?int $jalurId, string $documentType): ?array
    {
        foreach ($this->requirementsForUpload($academicYear, $jalurId) as $row) {
            if ($row['document_type'] === $documentType) {
                return $row;
            }
        }

        return null;
    }

    public function isCompleteUploaded(int $studentId, string $academicYear, ?int $jalurId = null): bool
    {
        $missing = $this->missingRequiredDocuments($studentId, $academicYear, $jalurId);

        return $missing === [];
    }

    public function missingRequiredDocuments(int $studentId, string $academicYear, ?int $jalurId = null): array
    {
        $documentModel = new StudentDocumentModel();
        $uploadedTypes = array_column(
            $documentModel->select('document_type')
                ->where('student_id', $studentId)
                ->where('academic_year', $academicYear)
                ->findAll(),
            'document_type'
        );

        return array_values(array_diff($this->requiredTypes($academicYear, $jalurId), $uploadedTypes));
    }

    public function approvedRequiredCount(int $studentId, string $academicYear, ?int $jalurId = null): int
    {
        $requiredTypes = $this->requiredTypes($academicYear, $jalurId);
        if ($requiredTypes === []) {
            return 0;
        }

        return (new StudentDocumentModel())
            ->where('student_id', $studentId)
            ->where('academic_year', $academicYear)
            ->where('status', 'approved')
            ->whereIn('document_type', $requiredTypes)
            ->countAllResults();
    }

    public function extensionList(array $definition): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) ($definition['allowed_extensions'] ?? '')))));
    }

    private function mergeExtensions(string ...$extensionGroups): string
    {
        $extensions = [];
        foreach ($extensionGroups as $group) {
            foreach (explode(',', $group) as $extension) {
                $extension = strtolower(trim($extension));
                if ($extension !== '') {
                    $extensions[$extension] = $extension;
                }
            }
        }

        return implode(',', array_values($extensions));
    }
}
