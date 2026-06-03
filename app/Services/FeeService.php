<?php

namespace App\Services;

use App\Models\FeeTypeModel;

class FeeService
{
    public function __construct(private ?FeeTypeModel $feeTypeModel = null)
    {
        $this->feeTypeModel ??= new FeeTypeModel();
    }

    public function homepageSummary(): array
    {
        $rows = $this->feeTypeModel->activeHomepageFees();
        if ($rows === []) {
            return [
                'is_free'            => true,
                'title'              => 'Pendaftaran Gratis',
                'description'        => 'Pendaftaran tidak dipungut biaya apa pun bagi seluruh calon peserta didik.',
                'fees'               => [],
                'payment_faq_answer' => 'Pendaftaran ini sepenuhnya gratis dan tidak dipungut biaya apa pun bagi seluruh calon peserta didik.',
            ];
        }

        $fees = array_map(fn (array $row): array => [
            'id'                            => (int) $row['id'],
            'code'                          => (string) $row['code'],
            'name'                          => (string) $row['name'],
            'desc'                          => (string) ($row['description'] ?? ''),
            'amount'                        => $this->formatRupiah((float) $row['amount']),
            'raw_amount'                    => (float) $row['amount'],
            'period'                        => (string) ($row['billing_period'] ?? 'Satu Kali'),
            'is_required'                   => (int) ($row['is_required'] ?? 0) === 1,
            'requires_payment_before_form'  => (int) ($row['requires_payment_before_form'] ?? 0) === 1,
            'auto_invoice'                  => (int) ($row['auto_invoice'] ?? 0) === 1,
            'icon'                          => (string) (($row['icon'] ?? '') ?: 'wallet'),
        ], $rows);

        return [
            'is_free'            => false,
            'title'              => 'Rincian Biaya Pendidikan',
            'description'        => 'Rincian biaya aktif ditampilkan sesuai konfigurasi sekolah.',
            'fees'               => $fees,
            'payment_faq_answer' => $this->buildPaymentFaqAnswer($fees),
        ];
    }

    private function formatRupiah(float $amount): string
    {
        if ($amount <= 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function buildPaymentFaqAnswer(array $fees): string
    {
        $parts = array_map(
            fn (array $fee): string => $fee['name'] . ' sebesar ' . $fee['amount'],
            $fees
        );

        return 'Biaya aktif saat ini: ' . implode('; ', $parts) . '. Silakan pantau tagihan resmi pada panel pendaftar.';
    }
}
