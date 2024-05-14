<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendWhatsappMessage;

class WhatsAppController extends Controller
{
    public function sendMessage(Request $request)
    {
        $responses = [];
        $success = true;
        $messageResponse = '';

        $requestData = $request->json()->all();

        foreach ($requestData as $data) {
            $message = $data['message'];
            $priority = $data['priority'];
            $phone = $data['phone'];
            // Validasi priority
            $priority = $priority === 'low' || $priority === 'high' ? $priority : 'low';

            if (is_array($phone) && !empty($phone) && !empty($message)) {
                foreach ($phone as $p) {
                    try {
                        // save data do db
                        $message = Message::create([
                            'phone' => $p,
                            'message' => $message,
                            'priority' => $priority,
                        ]);
                        $responses[] = $message;
                        $messageResponse = 'Successfully send message to queue';

                        dispatch(new SendWhatsappMessage($message->id));
                    } catch (\Exception $e) {
                        $messageResponse = 'Error sending message: ' . $e->getMessage();
                        $success = false; // Set success ke false jika ada error
                    }
                }
            } else {
                if (empty($phone) || !is_array($phone)) {
                    $messageResponse = 'No phone numbers provided or phone numbers is not an array';
                } elseif (empty($message) && !empty($phone)) {
                    $messageResponse = 'Message cannot be empty';
                } else {
                    $messageResponse = 'Phone numbers and message cannot be empty';
                }
                $success = false; // Set success ke false jika terdapat kesalahan input
            }
        }
        return response()->json([
            'message' => $messageResponse,
            'success' => $success,
            'data' => $responses,
        ]);
    }
}
