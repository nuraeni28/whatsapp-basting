<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    public function sendMessage(Request $request)
    {
        $responses = [];
        $message = '';

        $input = $request->all();
        $message = $request->input('message');
        $phone = $request->input('phone');

        if (is_array($phone) && !empty($phone) && !empty($message)) {
            foreach ($phone as $p) {
                // send request to whatsapp js
                $response = Http::post('http://localhost:3000/send-message', [
                    'number' => $p,
                    'message' => $message,
                ]);

                // save response in array responses
                $responses[] = $response->json();
                $message = 'Succesfully send message';
            }
        } elseif (empty($phone || !is_array($phone))) {
            $responses[] = 'No phone numbers provided or phone numbers is not an array';
            $message = 'Error';
        } elseif (empty($message) && !empty($phone)) {
            $responses[] = 'Message cannot be empty';
            $message = 'Error';
        } else {
            $responses[] = 'Phone numbers and message cannot be empty';
            $message = 'Error';
        }

        return response()->json([
            'message' => $message,
            'responses' => $responses,
        ]);
    }
}
