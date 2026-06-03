<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\FeeTypeModel;
use App\Models\JalurModel;
use App\Models\RegistrationModel;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Services\BillingService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class BillingServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $namespace = 'App';

    private UserModel $userModel;
    private StudentModel $studentModel;
    private JalurModel $jalurModel;
    private RegistrationModel $registrationModel;
    private FeeTypeModel $feeTypeModel;
    private BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->jalurModel = new JalurModel();
        $this->registrationModel = new RegistrationModel();
        $this->feeTypeModel = new FeeTypeModel();
        $this->billingService = new BillingService();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        foreach (['payment_logs', 'payments', 'invoice_items', 'invoices', 'payment_methods'] as $table) {
            if ($db->tableExists($table)) {
                $db->table($table)->truncate();
            }
        }

        $this->registrationModel->where('1=1')->delete();
        $this->feeTypeModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();
        $this->jalurModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function testGenerateInvoiceCreatesItemsFromActiveAutoInvoiceFees(): void
    {
        $registrationId = $this->createRegistration();
        $this->createFeeType('formulir', 'Biaya Formulir', 150000);
        $this->createFeeType('seragam', 'Seragam', 350000, ['auto_invoice' => 0]);

        $result = $this->billingService->generateInvoiceForRegistration($registrationId);

        $this->assertTrue($result['success'], $result['message'] ?? '');
        $this->assertSame(150000.0, (float) $result['invoice']['total_amount']);
        $this->assertSame('unpaid', $result['invoice']['status']);

        $items = \Config\Database::connect()
            ->table('invoice_items')
            ->where('invoice_id', $result['invoice']['id'])
            ->get()
            ->getResultArray();

        $this->assertCount(1, $items);
        $this->assertSame('formulir', $items[0]['item_code']);
    }

    public function testGenerateInvoiceIsIdempotentPerRegistration(): void
    {
        $registrationId = $this->createRegistration();
        $this->createFeeType('formulir', 'Biaya Formulir', 150000);

        $first = $this->billingService->generateInvoiceForRegistration($registrationId);
        $second = $this->billingService->generateInvoiceForRegistration($registrationId);

        $this->assertTrue($first['success']);
        $this->assertTrue($second['success']);
        $this->assertSame((int) $first['invoice']['id'], (int) $second['invoice']['id']);
        $this->assertSame(1, \Config\Database::connect()->table('invoices')->countAllResults());
    }

    public function testRecordVerifiedPaymentUpdatesInvoiceFromPartialToPaid(): void
    {
        $registrationId = $this->createRegistration();
        $this->createFeeType('formulir', 'Biaya Formulir', 150000);
        $invoice = $this->billingService->generateInvoiceForRegistration($registrationId)['invoice'];

        $partial = $this->billingService->recordManualPayment((int) $invoice['id'], 50000, null, 99, 'Cicilan awal');
        $this->assertTrue($partial['success'], $partial['message'] ?? '');
        $this->assertSame('partial', $partial['invoice']['status']);
        $this->assertSame(50000.0, (float) $partial['invoice']['paid_amount']);

        $paid = $this->billingService->recordManualPayment((int) $invoice['id'], 100000, null, 99, 'Pelunasan');
        $this->assertTrue($paid['success'], $paid['message'] ?? '');
        $this->assertSame('paid', $paid['invoice']['status']);
        $this->assertSame(150000.0, (float) $paid['invoice']['paid_amount']);
        $this->assertSame(0.0, (float) $paid['invoice']['balance_amount']);
    }

    public function testCancelInvoiceLocksFurtherPayments(): void
    {
        $registrationId = $this->createRegistration();
        $this->createFeeType('formulir', 'Biaya Formulir', 150000);
        $invoice = $this->billingService->generateInvoiceForRegistration($registrationId)['invoice'];

        $cancelled = $this->billingService->cancelInvoice((int) $invoice['id'], 99, 'Salah tagihan');
        $this->assertTrue($cancelled['success'], $cancelled['message'] ?? '');
        $this->assertSame('cancelled', $cancelled['invoice']['status']);

        $payment = $this->billingService->recordManualPayment((int) $invoice['id'], 150000, null, 99);
        $this->assertFalse($payment['success']);
        $this->assertStringContainsString('dibatalkan', $payment['message']);
    }

    private function createRegistration(): int
    {
        $userId = $this->userModel->insert([
            'name'      => 'Billing User',
            'email'     => 'billing' . uniqid() . '@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $studentId = $this->studentModel->insert([
            'user_id'       => $userId,
            'full_name'     => 'Billing Student',
            'gender'        => 'L',
            'birth_place'   => 'Jayapura',
            'birth_date'    => '2012-01-01',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '3200000000000001',
        ]);

        $jalurId = $this->jalurModel->insert([
            'name'      => 'Reguler',
            'quota'     => 100,
            'is_active' => 1,
        ]);

        return $this->registrationModel->insert([
            'user_id'             => $userId,
            'student_id'          => $studentId,
            'jalur_id'            => $jalurId,
            'registration_number' => 'SPMB-20262027-' . random_int(1000, 9999),
            'academic_year'       => '2026/2027',
            'status'              => 'submitted',
            'submitted_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    private function createFeeType(string $code, string $name, int $amount, array $overrides = []): int
    {
        return $this->feeTypeModel->insert(array_merge([
            'code'                         => $code,
            'name'                         => $name,
            'amount'                       => $amount,
            'billing_period'               => 'Satu Kali',
            'is_required'                  => 1,
            'is_active'                    => 1,
            'show_on_homepage'             => 1,
            'requires_payment_before_form' => 0,
            'auto_invoice'                 => 1,
            'icon'                         => 'wallet',
            'sort_order'                   => 100,
        ], $overrides));
    }
}
