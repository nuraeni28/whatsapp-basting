<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
     public function sendMessage(Request $request)
    {
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string',
        ]);

        $number = $request->input('number');
        $message = $request->input('message');

        // Kirim permintaan ke WhatsApp Client
        $response = Http::post('http://localhost:3000/send-message', [
            'number' => $number,
            'message' => $message,
        ]);

        return $response->json();
    }
}
