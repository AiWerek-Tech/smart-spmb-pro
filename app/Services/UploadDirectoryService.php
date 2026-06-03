<?php

namespace App\Services;

class UploadDirectoryService
{
    public function __construct(private ?AcademicYearService $academicYearService = null)
    {
        $this->academicYearService ??= new AcademicYearService();
    }

    public function publicDirectory(string $category, ?string $year = null): array
    {
        $yearSegment = $this->academicYearService->pathSegment($year);
        $categoryPath = $this->sanitizePath($category);
        $relative = 'uploads/' . $yearSegment . '/' . $categoryPath . '/';
        $absolute = FCPATH . $relative;

        if (!is_dir($absolute)) {
            mkdir($absolute, 0775, true);
        }

        return ['absolute' => $absolute, 'relative' => $relative];
    }

    public function writableDirectory(string $category, ?string $year = null): array
    {
        $yearSegment = $this->academicYearService->pathSegment($year);
        $categoryPath = $this->sanitizePath($category);
        $relative = 'uploads/' . $yearSegment . '/' . $categoryPath . '/';
        $absolute = WRITEPATH . $relative;

        if (!is_dir($absolute)) {
            mkdir($absolute, 0775, true);
        }

        return ['absolute' => $absolute, 'relative' => $relative];
    }

    private function sanitizeSegment(string $segment): string
    {
        return preg_replace('/[^0-9A-Za-z_-]+/', '-', trim($segment)) ?: 'misc';
    }

    private function sanitizePath(string $path): string
    {
        $segments = preg_split('~[\\\\/]+~', $path) ?: [];
        $segments = array_values(array_filter(array_map(fn ($segment) => $this->sanitizeSegment($segment), $segments)));

        return $segments ? implode('/', $segments) : 'misc';
    }
}
