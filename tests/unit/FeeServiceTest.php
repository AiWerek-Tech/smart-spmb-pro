<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\FeeTypeModel;
use App\Services\FeeService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class FeeServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    protected FeeTypeModel $feeTypeModel;
    protected FeeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feeTypeModel = new FeeTypeModel();
        $this->service = new FeeService($this->feeTypeModel);
    }

    protected function tearDown(): void
    {
        $this->feeTypeModel->where('1=1')->delete();

        parent::tearDown();
    }

    public function testHomepageFeesShowsFreeRegistrationWhenNoActiveFees(): void
    {
        $summary = $this->service->homepageSummary();

        $this->assertTrue($summary['is_free']);
        $this->assertSame('Pendaftaran Gratis', $summary['title']);
        $this->assertSame([], $summary['fees']);
    }

    public function testHomepageFeesUsesActiveVisibleFeeTypes(): void
    {
        $this->feeTypeModel->insert([
            'code'                       => 'formulir',
            'name'                       => 'Biaya Formulir',
            'description'                => 'Biaya administrasi formulir online.',
            'amount'                     => 250000,
            'billing_period'             => 'Satu Kali',
            'is_required'                => 1,
            'is_active'                  => 1,
            'show_on_homepage'           => 1,
            'requires_payment_before_form' => 1,
            'auto_invoice'               => 1,
            'sort_order'                 => 10,
        ]);
        $this->feeTypeModel->insert([
            'code'             => 'inactive',
            'name'             => 'Biaya Nonaktif',
            'description'      => 'Tidak tampil.',
            'amount'           => 999999,
            'billing_period'   => 'Satu Kali',
            'is_required'      => 1,
            'is_active'        => 0,
            'show_on_homepage' => 1,
            'auto_invoice'     => 1,
            'sort_order'       => 20,
        ]);

        $summary = $this->service->homepageSummary();

        $this->assertFalse($summary['is_free']);
        $this->assertSame('Rincian Biaya Pendidikan', $summary['title']);
        $this->assertCount(1, $summary['fees']);
        $this->assertSame('Biaya Formulir', $summary['fees'][0]['name']);
        $this->assertSame('Rp 250.000', $summary['fees'][0]['amount']);
        $this->assertStringContainsString('Biaya Formulir', $summary['payment_faq_answer']);
    }
}
