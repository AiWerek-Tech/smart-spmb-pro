<?php

namespace App\Controllers\Bendahara;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\PaymentMethodModel;
use App\Services\BillingService;

class PaymentController extends BaseController
{
    private InvoiceModel $invoiceModel;
    private PaymentMethodModel $paymentMethodModel;
    private BillingService $billingService;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->paymentMethodModel = new PaymentMethodModel();
        $this->billingService = new BillingService();
    }

    public function index()
    {
        $filters = [
            'status' => (string) $this->request->getGet('status'),
            'search' => (string) $this->request->getGet('search'),
        ];

        return view('bendahara/payments/index', [
            'title' => 'Pembayaran SPMB',
            'filters' => $filters,
            'summary' => $this->summary(),
            'invoices' => $this->invoiceRows($filters),
            'breadcrumbs' => [
                ['title' => 'Bendahara', 'url' => base_url('bendahara/dashboard')],
                ['title' => 'Pembayaran', 'url' => base_url('bendahara/invoices')],
            ],
        ]);
    }

    public function show(int $id)
    {
        $invoice = $this->invoiceDetail($id);
        if (! $invoice) {
            return redirect()->to('bendahara/invoices')->with('error', 'Invoice tidak ditemukan.');
        }

        return view('bendahara/payments/show', [
            'title' => 'Detail Invoice',
            'invoice' => $invoice,
            'items' => $this->invoiceItems($id),
            'payments' => $this->invoicePayments($id),
            'logs' => $this->invoiceLogs($id),
            'paymentMethods' => $this->paymentMethodModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'breadcrumbs' => [
                ['title' => 'Bendahara', 'url' => base_url('bendahara/dashboard')],
                ['title' => 'Pembayaran', 'url' => base_url('bendahara/invoices')],
                ['title' => $invoice['invoice_number'], 'url' => base_url('bendahara/invoices/' . $id)],
            ],
        ]);
    }

    public function recordPayment(int $id)
    {
        $rules = [
            'amount' => 'required|decimal|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $methodId = $this->request->getPost('payment_method_id');
        $result = $this->billingService->recordManualPayment(
            $id,
            (float) $this->request->getPost('amount'),
            $methodId !== null && $methodId !== '' ? (int) $methodId : null,
            (int) session()->get('user_id'),
            $this->request->getPost('notes') ?: null
        );

        return redirect()->to('bendahara/invoices/' . $id)->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function cancel(int $id)
    {
        $reason = trim((string) $this->request->getPost('reason'));
        if ($reason === '') {
            return redirect()->back()->withInput()->with('error', 'Alasan pembatalan wajib diisi.');
        }

        $result = $this->billingService->cancelInvoice($id, (int) session()->get('user_id'), $reason);

        return redirect()->to('bendahara/invoices/' . $id)->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function slip(int $id)
    {
        $invoice = $this->invoiceDetail($id);
        if (! $invoice) {
            return redirect()->to('bendahara/invoices')->with('error', 'Invoice tidak ditemukan.');
        }

        return view('bendahara/payments/slip', [
            'title' => 'Slip Pembayaran',
            'invoice' => $invoice,
            'items' => $this->invoiceItems($id),
            'payments' => $this->invoicePayments($id),
        ]);
    }

    public function export()
    {
        $rows = $this->invoiceRows([
            'status' => (string) $this->request->getGet('status'),
            'search' => (string) $this->request->getGet('search'),
        ]);

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Invoice', 'No Pendaftaran', 'Nama', 'Tahun Pelajaran', 'Status', 'Total', 'Dibayar', 'Sisa']);
        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['invoice_number'],
                $row['registration_number'],
                $row['full_name'],
                $row['academic_year'],
                $row['status'],
                $row['total_amount'],
                $row['paid_amount'],
                $row['balance_amount'],
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="rekap-pembayaran-spmb-' . date('Y-m-d') . '.csv"')
            ->setBody($csv);
    }

    private function invoiceRows(array $filters): array
    {
        $builder = $this->invoiceModel
            ->select('invoices.*, registrations.registration_number, students.full_name, jalur.name AS jalur_name')
            ->join('registrations', 'registrations.id = invoices.registration_id')
            ->join('students', 'students.id = invoices.student_id')
            ->join('jalur', 'jalur.id = registrations.jalur_id', 'left');

        if (($filters['status'] ?? '') !== '') {
            $builder->where('invoices.status', $filters['status']);
        }

        if (($filters['search'] ?? '') !== '') {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('invoices.invoice_number', $search)
                ->orLike('registrations.registration_number', $search)
                ->orLike('students.full_name', $search)
                ->groupEnd();
        }

        return $builder->orderBy('invoices.created_at', 'DESC')->findAll(100);
    }

    private function invoiceDetail(int $id): ?array
    {
        return $this->invoiceModel
            ->select('invoices.*, registrations.registration_number, registrations.status AS registration_status, students.full_name, students.nik, users.email AS user_email, jalur.name AS jalur_name')
            ->join('registrations', 'registrations.id = invoices.registration_id')
            ->join('students', 'students.id = invoices.student_id')
            ->join('users', 'users.id = invoices.user_id')
            ->join('jalur', 'jalur.id = registrations.jalur_id', 'left')
            ->where('invoices.id', $id)
            ->first();
    }

    private function invoiceItems(int $invoiceId): array
    {
        return \Config\Database::connect()
            ->table('invoice_items')
            ->where('invoice_id', $invoiceId)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function invoicePayments(int $invoiceId): array
    {
        return \Config\Database::connect()
            ->table('payments')
            ->select('payments.*, payment_methods.name AS method_name')
            ->join('payment_methods', 'payment_methods.id = payments.payment_method_id', 'left')
            ->where('payments.invoice_id', $invoiceId)
            ->orderBy('payments.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function invoiceLogs(int $invoiceId): array
    {
        return \Config\Database::connect()
            ->table('payment_logs')
            ->where('invoice_id', $invoiceId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function summary(): array
    {
        $rows = $this->invoiceModel
            ->select('status, COUNT(*) AS count, COALESCE(SUM(total_amount), 0) AS total, COALESCE(SUM(paid_amount), 0) AS paid')
            ->groupBy('status')
            ->findAll();

        $summary = ['count' => 0, 'total' => 0.0, 'paid' => 0.0, 'by_status' => []];
        foreach ($rows as $row) {
            $summary['count'] += (int) $row['count'];
            $summary['total'] += (float) $row['total'];
            $summary['paid'] += (float) $row['paid'];
            $summary['by_status'][$row['status']] = (int) $row['count'];
        }

        return $summary;
    }
}
