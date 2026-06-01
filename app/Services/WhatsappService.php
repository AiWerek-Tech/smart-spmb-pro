<?php

namespace App\Services;

/**
 * WhatsappService — Layanan integrasi pengiriman notifikasi WhatsApp.
 *
 * Mengirimkan pesan teks ke nomor calon siswa atau orang tua.
 * Menyediakan fallback logger jika API key/gateway tidak dikonfigurasi.
 */
class WhatsappService
{
    protected string $apiKey;
    protected string $senderNumber;
    protected string $gatewayUrl;

    public function __construct()
    {
        $settingModel = new \App\Models\SettingModel();
        
        // Ambil konfigurasi API dari settings (jika ada)
        $this->apiKey       = $settingModel->getValue('whatsapp_api_key', '');
        $this->senderNumber = $settingModel->getValue('whatsapp_sender', '');
        $this->gatewayUrl   = $settingModel->getValue('whatsapp_gateway_url', 'https://api.fonnte.com/send');
    }

    /**
     * Kirim notifikasi WhatsApp ke nomor penerima.
     *
     * @param  string $phone   Nomor telepon penerima (format bebas, akan dibersihkan)
     * @param  string $message Isi pesan
     * @return array  ['success' => bool, 'message' => string, 'mocked' => bool]
     */
    public function sendNotification(string $phone, string $message): array
    {
        // Bersihkan nomor telepon
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ubah format awal 08xx menjadi 628xx
        if (strpos($cleanPhone, '0') === 0) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        if (empty($cleanPhone)) {
            return [
                'success' => false,
                'message' => 'Nomor telepon tidak valid.',
                'mocked'  => false,
            ];
        }

        // Jika API Key tidak ada, jalankan mode simulasi (mock)
        if (empty($this->apiKey)) {
            $logMsg = sprintf("[WhatsApp Mock] Kirim ke %s: %s", $cleanPhone, $message);
            log_message('info', $logMsg);

            return [
                'success' => true,
                'message' => 'Simulasi pengiriman WhatsApp berhasil dicatat di log.',
                'mocked'  => true,
            ];
        }

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->gatewayUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $cleanPhone,
                    'message' => $message,
                    'countryCode' => '62', // Default Indonesia
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $this->apiKey
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                log_message('error', 'WhatsApp Gateway CURL Error: ' . $err);
                return [
                    'success' => false,
                    'message' => 'Gagal terhubung ke gateway WhatsApp: ' . $err,
                    'mocked'  => false,
                ];
            }

            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === true) {
                return [
                    'success' => true,
                    'message' => 'WhatsApp berhasil terkirim melalui gateway.',
                    'mocked'  => false,
                ];
            }

            $errMsg = $result['reason'] ?? 'Kesalahan dari server gateway.';
            log_message('warning', 'WhatsApp Send Failed: ' . $errMsg);
            
            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan: ' . $errMsg,
                'mocked'  => false,
            ];

        } catch (\Throwable $e) {
            log_message('error', 'WhatsApp Service Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'mocked'  => false,
            ];
        }
    }
}
