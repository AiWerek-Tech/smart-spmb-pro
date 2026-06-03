<?php

namespace App\Services;

use App\Models\FeeTypeModel;
use App\Models\InvoiceItemModel;
use App\Models\InvoiceModel;
use App\Models\PaymentLogModel;
use App\Models\PaymentModel;
use App\Models\RegistrationModel;
use CodeIgniter\Database\BaseConnection;

class BillingService
{
    public function __construct(
        private ?RegistrationModel $registrationModel = null,
        private ?FeeTypeModel $feeTypeModel = null,
        private ?InvoiceModel $invoiceModel = null,
        private ?InvoiceItemModel $invoiceItemModel = null,
        private ?PaymentModel $paymentModel = null,
        private ?PaymentLogModel $paymentLogModel = null,
        private ?BaseConnection $db = null
    ) {
        $this->registrationModel ??= new RegistrationModel();
        $this->feeTypeModel ??= new FeeTypeModel();
        $this->invoiceModel ??= new InvoiceModel();
        $this->invoiceItemModel ??= new InvoiceItemModel();
        $this->paymentModel ??= new PaymentModel();
        $this->paymentLogModel ??= new PaymentLogModel();
        $this->db ??= \Config\Database::connect();
    }

    public function generateInvoiceForRegistration(int $registrationId, ?int $actorId = null): array
    {
        $registration = $this->registrationModel->find($registrationId);
        if (! $registration) {
            return ['success' => false, 'message' => 'Data pendaftaran tidak ditemukan.', 'invoice' => null];
        }

        $existing = $this->invoiceModel->activeForRegistration($registrationId);
        if ($existing) {
            return ['success' => true, 'message' => 'Invoice sudah tersedia.', 'invoice' => $existing];
        }

        $fees = $this->feeTypeModel
            ->where('is_active', 1)
            ->where('auto_invoice', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        if ($fees === []) {
            return ['success' => true, 'message' => 'Tidak ada biaya aktif yang perlu ditagihkan.', 'invoice' => null];
        }

        $subtotal = 0.0;
        foreach ($fees as $fee) {
            $subtotal += (float) ($fee['amount'] ?? 0);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->transBegin();

        try {
            $invoiceId = $this->invoiceModel->insert([
                'invoice_number'      => $this->nextInvoiceNumber(),
                'registration_id'     => $registrationId,
                'user_id'             => (int) $registration['user_id'],
                'student_id'          => (int) $registration['student_id'],
                'academic_year'       => (string) $registration['academic_year'],
                'status'              => $subtotal > 0 ? 'unpaid' : 'paid',
                'subtotal'            => $subtotal,
                'discount_amount'     => 0,
                'total_amount'        => $subtotal,
                'paid_amount'         => $subtotal > 0 ? 0 : $subtotal,
                'balance_amount'      => $subtotal > 0 ? $subtotal : 0,
                'issued_at'           => $now,
                'paid_at'             => $subtotal > 0 ? null : $now,
                'created_by'          => $actorId,
                'updated_by'          => $actorId,
            ]);

            if (! $invoiceId) {
                throw new \RuntimeException('Gagal membuat invoice.');
            }

            foreach ($fees as $fee) {
                $amount = (float) ($fee['amount'] ?? 0);
                $this->invoiceItemModel->insert([
                    'invoice_id'   => (int) $invoiceId,
                    'fee_type_id'  => (int) $fee['id'],
                    'item_code'    => (string) $fee['code'],
                    'name'         => (string) $fee['name'],
                    'description'  => $fee['description'] ?? null,
                    'quantity'     => 1,
                    'unit_amount'  => $amount,
                    'total_amount' => $amount,
                    'is_required'  => (int) ($fee['is_required'] ?? 1),
                ]);
            }

            $this->log((int) $invoiceId, null, 'invoice_created', null, $subtotal > 0 ? 'unpaid' : 'paid', $actorId, 'Invoice dibuat otomatis dari konfigurasi biaya.');
            $this->db->transCommit();

            return [
                'success' => true,
                'message' => 'Invoice berhasil dibuat.',
                'invoice' => $this->invoiceModel->find((int) $invoiceId),
            ];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'BillingService::generateInvoiceForRegistration failed: ' . $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage(), 'invoice' => null];
        }
    }

    public function recordManualPayment(int $invoiceId, float $amount, ?int $paymentMethodId = null, ?int $actorId = null, ?string $notes = null): array
    {
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Nominal pembayaran harus lebih dari nol.', 'invoice' => null];
        }

        $invoice = $this->invoiceModel->find($invoiceId);
        if (! $invoice) {
            return ['success' => false, 'message' => 'Invoice tidak ditemukan.', 'invoice' => null];
        }

        if (($invoice['status'] ?? '') === 'cancelled') {
            return ['success' => false, 'message' => 'Invoice sudah dibatalkan dan tidak dapat menerima pembayaran.', 'invoice' => $invoice];
        }

        if (($invoice['status'] ?? '') === 'paid') {
            return ['success' => false, 'message' => 'Invoice sudah lunas.', 'invoice' => $invoice];
        }

        $this->db->transBegin();

        try {
            $now = date('Y-m-d H:i:s');
            $paymentId = $this->paymentModel->insert([
                'invoice_id'          => $invoiceId,
                'payment_method_id'   => $paymentMethodId,
                'amount'              => $amount,
                'status'              => 'verified',
                'paid_at'             => $now,
                'verified_at'         => $now,
                'verified_by'         => $actorId,
                'notes'               => $notes,
                'created_by'          => $actorId,
            ]);

            if (! $paymentId) {
                throw new \RuntimeException('Gagal mencatat pembayaran.');
            }

            $updatedInvoice = $this->recalculateInvoice($invoice, $amount, $actorId);
            $this->log($invoiceId, (int) $paymentId, 'payment_verified', (string) $invoice['status'], (string) $updatedInvoice['status'], $actorId, $notes);
            $this->db->transCommit();

            return ['success' => true, 'message' => 'Pembayaran berhasil dicatat.', 'invoice' => $updatedInvoice, 'payment_id' => (int) $paymentId];
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'BillingService::recordManualPayment failed: ' . $e->getMessage());

            return ['success' => false, 'message' => $e->getMessage(), 'invoice' => $invoice];
        }
    }

    public function cancelInvoice(int $invoiceId, ?int $actorId, string $reason): array
    {
        $invoice = $this->invoiceModel->find($invoiceId);
        if (! $invoice) {
            return ['success' => false, 'message' => 'Invoice tidak ditemukan.', 'invoice' => null];
        }

        if (($invoice['status'] ?? '') === 'cancelled') {
            return ['success' => true, 'message' => 'Invoice sudah dibatalkan.', 'invoice' => $invoice];
        }

        $this->invoiceModel->update($invoiceId, [
            'status'              => 'cancelled',
            'cancelled_at'        => date('Y-m-d H:i:s'),
            'cancellation_reason' => $reason,
            'updated_by'          => $actorId,
        ]);

        $this->log($invoiceId, null, 'invoice_cancelled', (string) $invoice['status'], 'cancelled', $actorId, $reason);

        return ['success' => true, 'message' => 'Invoice berhasil dibatalkan.', 'invoice' => $this->invoiceModel->find($invoiceId)];
    }

    private function recalculateInvoice(array $invoice, float $paymentAmount, ?int $actorId): array
    {
        $total = (float) $invoice['total_amount'];
        $paid = min($total, (float) $invoice['paid_amount'] + $paymentAmount);
        $balance = max(0, $total - $paid);
        $status = $balance <= 0 ? 'paid' : 'partial';

        $this->invoiceModel->update((int) $invoice['id'], [
            'paid_amount'    => $paid,
            'balance_amount' => $balance,
            'status'         => $status,
            'paid_at'        => $status === 'paid' ? date('Y-m-d H:i:s') : null,
            'updated_by'     => $actorId,
        ]);

        return $this->invoiceModel->find((int) $invoice['id']);
    }

    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $sequence = $this->invoiceModel
            ->like('invoice_number', $prefix, 'after')
            ->countAllResults() + 1;

        do {
            $number = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while ($this->invoiceModel->where('invoice_number', $number)->first() !== null);

        return $number;
    }

    private function log(int $invoiceId, ?int $paymentId, string $action, ?string $oldStatus, ?string $newStatus, ?int $actorId, ?string $notes = null, array $meta = []): void
    {
        $this->paymentLogModel->insert([
            'invoice_id'  => $invoiceId,
            'payment_id'  => $paymentId,
            'action'      => $action,
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'actor_id'    => $actorId,
            'notes'       => $notes,
            'meta'        => $meta === [] ? null : json_encode($meta),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
