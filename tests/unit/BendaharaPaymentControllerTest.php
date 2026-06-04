<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class BendaharaPaymentControllerTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;
    protected $namespace = 'App';

    public function testAdminCanOpenBendaharaInvoiceList(): void
    {
        $result = $this->withSession([
            'user_id' => 1,
            'user_name' => 'Admin Test',
            'user_role' => 'admin',
            'user_base_role' => 'admin',
            'logged_in' => true,
        ])->get('/bendahara/invoices');

        $result->assertStatus(200);
        $result->assertSee('Pembayaran SPMB');
        $result->assertSee('Ekspor Excel');
    }

    public function testAdminCanExportPaymentsToExcel(): void
    {
        $result = $this->withSession([
            'user_id' => 1,
            'user_name' => 'Admin Test',
            'user_role' => 'admin',
            'user_base_role' => 'admin',
            'logged_in' => true,
        ])->get('/bendahara/export');

        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('rekap-pembayaran-spmb', $result->response()->getHeaderLine('Content-Disposition'));
    }
}
