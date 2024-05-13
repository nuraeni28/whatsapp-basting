<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function sendMessage($number, $message)
    {
        // Kirim permintaan ke endpoint API WhatsApp di Laravel
        $response = Http::post('http://localhost:8000/api/send-message', [
            'number' => $number,
            'message' => $message,
        ]);

        return $response->json();
    }
}
