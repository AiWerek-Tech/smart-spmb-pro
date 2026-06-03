<?php

namespace App\Services;

use App\Models\GelombangModel;
use DateTimeInterface;

class RegistrationGateService
{
    public function __construct(private ?GelombangModel $gelombangModel = null)
    {
        $this->gelombangModel ??= new GelombangModel();
    }

    public function status(?int $jalurId, string $academicYear, ?int $gelombangId = null, ?DateTimeInterface $today = null): array
    {
        $todayValue = $today?->format('Y-m-d') ?? date('Y-m-d');

        if ($gelombangId !== null) {
            $gelombang = $this->gelombangModel->find($gelombangId);
            return $this->evaluateSingleGelombang($gelombang, $academicYear, $jalurId, $todayValue);
        }

        $gelombangList = $this->activeGelombang($academicYear, $jalurId);
        if ($gelombangList === []) {
            return [
                'is_open'      => true,
                'status'       => 'unconfigured',
                'message'      => 'Jadwal pendaftaran belum dikonfigurasi. Pendaftaran tetap dibuka.',
                'gelombang'    => null,
                'gelombang_id' => null,
            ];
        }

        foreach ($gelombangList as $gelombang) {
            if ($this->isWithinWindow($gelombang, $todayValue)) {
                return [
                    'is_open'      => true,
                    'status'       => 'open',
                    'message'      => 'Pendaftaran sedang dibuka sampai ' . $this->formatDate($gelombang['close_date'] ?? ''),
                    'gelombang'    => $gelombang,
                    'gelombang_id' => (int) $gelombang['id'],
                ];
            }
        }

        $futureGelombang = array_values(array_filter(
            $gelombangList,
            static fn (array $gelombang): bool => (string) ($gelombang['open_date'] ?? '') > $todayValue
        ));

        if ($futureGelombang !== []) {
            $next = $futureGelombang[0];

            return [
                'is_open'      => false,
                'status'       => 'not_open',
                'message'      => 'Gelombang pendaftaran belum dibuka. Pendaftaran dapat dilakukan mulai ' . $this->formatDate($next['open_date'] ?? '') . '.',
                'gelombang'    => $next,
                'gelombang_id' => (int) $next['id'],
            ];
        }

        $latest = end($gelombangList) ?: null;

        return [
            'is_open'      => false,
            'status'       => 'closed',
            'message'      => 'Gelombang pendaftaran sudah ditutup pada ' . $this->formatDate($latest['close_date'] ?? '') . '.',
            'gelombang'    => $latest,
            'gelombang_id' => $latest ? (int) $latest['id'] : null,
        ];
    }

    private function evaluateSingleGelombang(?array $gelombang, string $academicYear, ?int $jalurId, string $todayValue): array
    {
        if (
            $gelombang === null
            || (int) ($gelombang['is_active'] ?? 0) !== 1
            || (string) ($gelombang['academic_year'] ?? '') !== $academicYear
            || ($jalurId !== null && (int) ($gelombang['jalur_id'] ?? 0) !== $jalurId)
        ) {
            return [
                'is_open'      => false,
                'status'       => 'inactive',
                'message'      => 'Gelombang pendaftaran tidak aktif untuk tahun pelajaran saat ini.',
                'gelombang'    => $gelombang,
                'gelombang_id' => $gelombang ? (int) $gelombang['id'] : null,
            ];
        }

        if ($this->isWithinWindow($gelombang, $todayValue)) {
            return [
                'is_open'      => true,
                'status'       => 'open',
                'message'      => 'Pendaftaran sedang dibuka sampai ' . $this->formatDate($gelombang['close_date'] ?? ''),
                'gelombang'    => $gelombang,
                'gelombang_id' => (int) $gelombang['id'],
            ];
        }

        if ((string) ($gelombang['open_date'] ?? '') > $todayValue) {
            return [
                'is_open'      => false,
                'status'       => 'not_open',
                'message'      => 'Gelombang pendaftaran belum dibuka. Pendaftaran dapat dilakukan mulai ' . $this->formatDate($gelombang['open_date'] ?? '') . '.',
                'gelombang'    => $gelombang,
                'gelombang_id' => (int) $gelombang['id'],
            ];
        }

        return [
            'is_open'      => false,
            'status'       => 'closed',
            'message'      => 'Gelombang pendaftaran sudah ditutup pada ' . $this->formatDate($gelombang['close_date'] ?? '') . '.',
            'gelombang'    => $gelombang,
            'gelombang_id' => (int) $gelombang['id'],
        ];
    }

    private function activeGelombang(string $academicYear, ?int $jalurId): array
    {
        $query = $this->gelombangModel
            ->where('academic_year', $academicYear)
            ->where('is_active', 1);

        if ($jalurId !== null) {
            $query->where('jalur_id', $jalurId);
        }

        return $query
            ->orderBy('open_date', 'ASC')
            ->findAll();
    }

    private function isWithinWindow(array $gelombang, string $todayValue): bool
    {
        return (string) ($gelombang['open_date'] ?? '') <= $todayValue
            && (string) ($gelombang['close_date'] ?? '') >= $todayValue;
    }

    private function formatDate(string $date): string
    {
        if ($date === '' || strtotime($date) === false) {
            return '-';
        }

        return date('d M Y', strtotime($date));
    }
}
