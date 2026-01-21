<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl = 'https://api.fonnte.com/send';
    protected $token;
    protected $sellerPhone;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->sellerPhone = env('SELLER_WA');
    }

    /**
     * Send order notification to seller via WhatsApp
     *
     * @param array $orderData
     * @return array
     */
    public function sendOrderNotification($orderData)
    {
        try {
            $message = $this->formatSellerMessage($orderData);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $this->sellerPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp notification sent to seller', [
                    'kode_pesanan' => $orderData['kode_pesanan']
                ]);

                return [
                    'success' => true,
                    'message' => 'WhatsApp notification sent to seller'
                ];
            } else {
                Log::error('WhatsApp notification to seller failed', [
                    'kode_pesanan' => $orderData['kode_pesanan'],
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send WhatsApp notification to seller'
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp notification exception (seller)', [
                'kode_pesanan' => $orderData['kode_pesanan'] ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send order confirmation to customer via WhatsApp
     *
     * @param array $orderData
     * @return array
     */
    public function sendCustomerConfirmation($orderData)
    {
        try {
            $message = $this->formatCustomerMessage($orderData);
            $customerPhone = $orderData['customer_phone'];

            // Format nomor WA customer ke format internasional
            $customerPhone = $this->formatPhoneNumber($customerPhone);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $customerPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp confirmation sent to customer', [
                    'kode_pesanan' => $orderData['kode_pesanan'],
                    'customer_phone' => $customerPhone
                ]);

                return [
                    'success' => true,
                    'message' => 'WhatsApp confirmation sent to customer'
                ];
            } else {
                Log::error('WhatsApp confirmation to customer failed', [
                    'kode_pesanan' => $orderData['kode_pesanan'],
                    'customer_phone' => $customerPhone,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send WhatsApp confirmation to customer'
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp confirmation exception (customer)', [
                'kode_pesanan' => $orderData['kode_pesanan'] ?? null,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format (62xxx)
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any spaces, dashes, or special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert 08xxx to 628xxx
        if (substr($phone, 0, 2) === '08') {
            $phone = '62' . substr($phone, 1);
        }

        // If already starts with 62, keep it
        if (substr($phone, 0, 2) !== '62') {
            // If starts with 0, remove it and add 62
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }
        }

        return $phone;
    }

    /**
     * Format order message for seller (admin)
     *
     * @param array $orderData
     * @return string
     */
    protected function formatSellerMessage($orderData)
    {
        $message = "🔔 *PESANAN BARU*\n\n";
        $message .= "Kode Pesanan: *{$orderData['kode_pesanan']}*\n";
        $message .= "Nama: {$orderData['customer_name']}\n";
        $message .= "No. HP: {$orderData['customer_phone']}\n";
        $message .= "Tanggal: {$orderData['tanggal']}\n\n";
        
        $message .= "📋 *DETAIL PESANAN:*\n";
        
        foreach ($orderData['items'] as $index => $item) {
            $no = $index + 1;
            $message .= "{$no}. {$item['nama_menu']} x{$item['jumlah']} = Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n";
        }
        
        $message .= "\n💰 *TOTAL: Rp " . number_format($orderData['total'], 0, ',', '.') . "*";
        
        return $message;
    }

    /**
     * Format confirmation message for customer
     *
     * @param array $orderData
     * @return string
     */
    protected function formatCustomerMessage($orderData)
    {
        $message = "✅ *PESANAN DITERIMA (MENUNGGU KONFIRMASI)*\n\n";
        $message .= "Terima kasih atas pesanan Anda 🙏\n\n";
        $message .= "Kode Pesanan: *{$orderData['kode_pesanan']}*\n";
        $message .= "Nama: {$orderData['customer_name']}\n";
        $message .= "Tanggal: {$orderData['tanggal']}\n\n";
        
        $message .= "📋 *DETAIL PESANAN:*\n\n";
        
        foreach ($orderData['items'] as $item) {
            $message .= "{$item['nama_menu']} x{$item['jumlah']} = Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n\n";
        }
        
        $message .= "💰 *TOTAL: Rp " . number_format($orderData['total'], 0, ',', '.') . "*\n\n";
        $message .= "📌 *MOHON KONFIRMASI*\n";
        $message .= "Silakan balas chat ini dengan mengetik *OK* agar pesanan dapat segera kami proses.\n";
        $message .= "Pesanan akan diproses setelah konfirmasi diterima.\n\n";
        $message .= "Terima kasih atas kerja samanya 😊\n\n";
        $message .= "🏪 *Kedai Mily*\n\n";
        $message .= "Sent via fonnte.com";
        
        return $message;
    }
}
