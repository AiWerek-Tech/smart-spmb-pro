<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\FeeTypeModel;
use App\Services\RegistrationGateService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class FormOverrideTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    private UserModel $userModel;
    private StudentModel $studentModel;
    private FeeTypeModel $feeTypeModel;
    private RegistrationGateService $gateService;

    protected function setUp(): void
    {
        parent::setUp();

        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        $this->userModel = new UserModel();
        $this->studentModel = new StudentModel();
        $this->feeTypeModel = new FeeTypeModel();
        $this->gateService = new RegistrationGateService();
    }

    protected function tearDown(): void
    {
        $db = \Config\Database::connect();
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        // Cleanup
        $db->table('payments')->where('1=1')->delete();
        $db->table('invoices')->where('1=1')->delete();
        $this->feeTypeModel->where('1=1')->delete();
        $this->studentModel->where('1=1')->delete();
        $this->userModel->where('1=1')->delete();

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
        parent::tearDown();
    }

    public function testPaymentGateRequiresPaymentByDefault(): void
    {
        // 1. Create a fee type requiring payment before form
        $this->feeTypeModel->insert([
            'code' => 'FEE_FORM',
            'name' => 'Biaya Formulir',
            'amount' => 100000,
            'is_required' => 1,
            'requires_payment_before_form' => 1,
            'is_active' => 1
        ]);

        // 2. Create User and Student without override
        $userId = $this->userModel->insert([
            'name'      => 'Test User Gate',
            'email'     => 'testgate@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $this->studentModel->insert([
            'user_id'       => $userId,
            'full_name'     => 'Test Student Gate',
            'gender'        => 'L',
            'birth_place'   => 'Jakarta',
            'birth_date'    => '2010-01-01',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '1234567890123456',
            'form_override' => 0
        ]);

        // 3. Evaluate gate status
        $status = $this->gateService->paymentGateStatus($userId, '2026/2027');

        $this->assertFalse($status['is_open']);
        $this->assertStringContainsString('Formulir pendaftaran terkunci', $status['message']);
    }

    public function testPaymentGateBypassedWithFormOverride(): void
    {
        // 1. Create a fee type requiring payment before form
        $this->feeTypeModel->insert([
            'code' => 'FEE_FORM',
            'name' => 'Biaya Formulir',
            'amount' => 100000,
            'is_required' => 1,
            'requires_payment_before_form' => 1,
            'is_active' => 1
        ]);

        // 2. Create User and Student with override = 1
        $userId = $this->userModel->insert([
            'name'      => 'Test User Gate Override',
            'email'     => 'testgateoverride@test.local',
            'password'  => password_hash('Password123', PASSWORD_BCRYPT),
            'role'      => 'pendaftar',
            'is_active' => 1,
        ]);

        $this->studentModel->insert([
            'user_id'       => $userId,
            'full_name'     => 'Test Student Gate Override',
            'gender'        => 'L',
            'birth_place'   => 'Jakarta',
            'birth_date'    => '2010-01-01',
            'religion'      => 'Islam',
            'citizenship'   => 'WNI',
            'family_status' => 'Anak Kandung',
            'nik'           => '1234567890123456',
            'form_override' => 1
        ]);

        // 3. Evaluate gate status
        $status = $this->gateService->paymentGateStatus($userId, '2026/2027');

        $this->assertTrue($status['is_open']);
        $this->assertStringContainsString('Akses formulir dibuka khusus', $status['message']);
    }
}
