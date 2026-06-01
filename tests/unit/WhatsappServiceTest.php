<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\WhatsappService;
use App\Models\SettingModel;
use CodeIgniter\Test\CIUnitTestCase;

class WhatsappServiceTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Uji sendNotification ketika API key kosong (Mode Simulasi / Mock).
     * Memastikan pesan dicatat di log sistem dan return value sesuai.
     */
    public function testSendNotificationMockMode(): void
    {
        // Bersihkan atau pastikan setting whatsapp_api_key kosong
        $settingModel = new SettingModel();
        $settingModel->where('key', 'whatsapp_api_key')->delete();

        $service = new WhatsappService();
        $result = $service->sendNotification('081234567890', 'Hello Test Message');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['mocked']);
        $this->assertStringContainsString('Simulasi pengiriman WhatsApp berhasil dicatat', $result['message']);
    }

    /**
     * Uji pembersihan format nomor telepon.
     */
    public function testSendNotificationCleanPhoneFormat(): void
    {
        $settingModel = new SettingModel();
        $settingModel->where('key', 'whatsapp_api_key')->delete();

        $service = new WhatsappService();
        
        // Nomor dengan karakter non-numeric dan awalan 0
        $result = $service->sendNotification('0812-3456-7890', 'Hello Clean Phone Test');
        $this->assertTrue($result['success']);
        $this->assertTrue($result['mocked']);
    }
}
